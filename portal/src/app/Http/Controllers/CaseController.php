<?php

namespace App\Http\Controllers;

use App\Models\CovidCase;
use App\Models\Task;
use App\Services\AuthenticationService;
use App\Services\CaseService;
use App\Services\QuestionnaireService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Jenssegers\Date\Date;

class CaseController extends Controller
{
    private CaseService $caseService;
    private QuestionnaireService $questionnaireService;
    private AuthenticationService $authService;

    public function __construct(CaseService $caseService,
                                QuestionnaireService $questionnaireService,
                                AuthenticationService $authService)
    {
        $this->caseService = $caseService;
        $this->questionnaireService = $questionnaireService;
        $this->authService = $authService;
    }

    public function newCase()
    {
        // Because we want to show the new case immediately, we create a draft case.
        $case = $this->caseService->createDraftCase();

        return redirect()->route('case-edit', [$case->uuid]);
    }

    public function editCase($caseUuid)
    {
        $case = $this->caseService->getCase($caseUuid);

        if ($case !== null && $this->caseService->canAccess($case)) {
            $case->tasks[] = new Task(); // one empty placeholder
            return view('editcase', [
                'action' => $case->status === CovidCase::STATUS_DRAFT ? 'new' : 'edit',
                'case' => $case,
                'tasks' => $case->tasks
            ]);
        } else {
            return redirect()->route('cases-list');
        }
    }

    public function viewCase($caseUuid)
    {
        $case = $this->caseService->getCase($caseUuid, true);

        if ($case !== null && $this->caseService->canAccess($case)) {

            $taskgroups = array();
            foreach ($case->tasks as $task) {
                $taskgroups[$task->communication][] = $task;
            }

            return view('viewcase', [
                'case' => $case,
                'taskgroups' => $taskgroups
            ]);
        } else {
            return redirect()->route('cases-list');
        }
    }

    public function dumpCase($caseUuid)
    {
        $case = $this->caseService->getCase($caseUuid);
        $user = $this->authService->getAuthenticatedUser();

        if ($case !== null && $this->caseService->canAccess($case)) {
            $tasksPerCategory = $this->questionnaireService->getExportFriendlyTaskExport($caseUuid);

            // TODO: Replace these getCopyData methods by ascii templates
            $copydata['user'] = $this->authService->getCopyData($user);
            $copydata['case'] = $this->caseService->getCopyDataCase($case);
            $copydata['index'] = $this->caseService->getCopyDataIndex($case);

            $groupTitles = [
                '1' => ['title' => '1 - Huisgenoten', 'postfix' => 'van de huisgenoot'],
                '2a' => ['title' => '2a - Nauwe contacten', 'postfix' => 'van het nauwe contact'],
                '2b' => ['title' => '2b - Nauwe contacten', 'postfix' => 'van het nauwe contact'],
                '3' => ['title' =>'3 - Overige contacten', 'postfix' => 'van het overig contact']
            ];

            $fieldLabels = [
                'lastname' => ['label' => 'Achternaam', 'postfix' => true],
                'firstname' => ['label' => 'Voornaam'],
                'email' => ['label' => 'E-mailadres'],
                'phonenumber' => ['label' => 'Telefoonnummer'],
                'label' => ['label' => 'Naam', 'postfix' => true],
            ];

            $copydata['contacts'] = $this->questionnaireService->getCopyData($tasksPerCategory, $groupTitles, $fieldLabels);


            return view('dumpcase', [
                'groupTitles' => $groupTitles,
                'fieldlabels' => $fieldLabels,
                'user' => $user,
                'case' => $case,
                'copydata' => $copydata,
                'taskcategories' => $tasksPerCategory
            ]);
        } else {
            return redirect()->route('cases-list');
        }
    }

    public function listCases()
    {
        if ($this->authService->hasPlannerRole()) {
            $cases = $this->caseService->organisationCases();
        } else {
            $cases = $this->caseService->myCases();
        }

        // Enrich data with some view level helper data
        foreach ($cases as $case) {
            $case->editCommand = $case->status === CovidCase::STATUS_DRAFT
                ? route('case-edit', [$case->uuid])
                : route('case-view', [$case->uuid])
            ;
        }

        return view('caseoverview', ['cases' => $cases]);
    }

    public function saveCase(Request $request)
    {
        $caseUuid = $request->input('caseUuid');

        $case = $this->caseService->getCase($caseUuid);

        if ($case != null && $this->caseService->canAccess($case)) {

            $validatedData = $request->validate([
                'action' => 'required|in:new,edit',
                'name' => 'required|max:255',
                'caseId' => 'max:255',
                'dateOfSymptomOnset' => 'required',
                'pairafteropen' => 'required_if:action,new|in:ja,nee',
                'addtasksnow' => 'nullable|in:ja,nee',
                'tasks.*.uuid' => 'nullable',
                'tasks.*.label' => 'nullable',
                'tasks.*.category' => 'required_with:tasks.*.label',
                'tasks.*.dateOfLastExposure' => 'required_with:tasks.*.label'
            ]);

            $case->name = $validatedData['name'];
            $case->caseId = $validatedData['caseId'];
            $case->dateOfSymptomOnset = Date::parse($validatedData['dateOfSymptomOnset']);
            $pairafteropen = $validatedData['pairafteropen'];

            $this->caseService->updateCase($case);

            $keep = array();
            foreach ($request->input('tasks') as $rawTask) {
                if (!empty($rawTask['label'])) { // skip empty auto-added table row
                    $this->caseService->createOrUpdateTask($caseUuid, $rawTask);

                    if (!empty($rawTask['uuid'])) {
                        $keep[] = $rawTask['uuid'];
                    }
                }
            }

            // Delete tasks that are no longer in the posted form
            $this->caseService->deleteRemovedTasks($caseUuid, $keep);
        }

        if ($case->status == 'draft' && $pairafteropen === 'ja') {
            // For draft cases go to the secondary screen to pair the case.
            return redirect()->route('case-pair', [$caseUuid]);
        } else {
            // For existing cases, go to the case's detail page
            return redirect()->route('case-view', [$caseUuid]);
        }
    }

    /**
     * Start pairing process.
     *
     * @param $caseUuid
     */
    public function pairCase($caseUuid)
    {
        $case = $this->caseService->getCase($caseUuid);

        if ($case != null && $this->caseService->canAccess($case)) {
            $pairingCode = $this->caseService->createPairingCodeForCase($case);
            $isDraftCase = $case->caseStatus() == CovidCase::STATUS_DRAFT;

            // When we show the pairingcode for a new case, we mark the case as 'open'.
            if ($isDraftCase) {
                $this->caseService->openCase($case);
            }
            return view('paircase', ['case' => $case, 'pairingCode' => $pairingCode, 'includeQuestionNumber' => $isDraftCase]);
        }
        return redirect()->route('cases-list');
    }

    /**
     * Trigger healthauthority_api to export case data.
     * Not to be confused with exporting case data to HPZone.
     *
     * @param $caseUuid
     * @return RedirectResponse
     */
    public function notifyCaseUpdate($caseUuid): RedirectResponse
    {
        $case = $this->caseService->getCase($caseUuid);

        if ($case === null || !$this->caseService->canAccess($case)) {
            // This is not the CovidCase you are looking for
            return redirect()->intended('/');
        }

        if ($this->caseService->notifyCaseUpdate($case)) {
            request()->session()->flash('message', 'Case klaargezet voor index');
        } else {
            request()->session()->flash('message', 'Fout bij klaarzetten case voor index');
        }

        return redirect()->intended('/case/' . $caseUuid);
    }
}
