<?php

namespace App\Repositories;

use App\Models\Eloquent\EloquentCase;
use App\Models\CovidCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

class DbCaseRepository implements CaseRepository
{
    /**
     * Returns the case and its task list.
     *
     * @param string $caseId Case identifier.
     *
     * @return CovidCase The found case (or null if not found)
     */
    public function getCase(string $caseId): ?CovidCase
    {
        $cases = EloquentCase::where('uuid', $caseId)->get();
        $dbCase = $cases->first();
        return $dbCase != null ? $this->caseFromEloquentModel($dbCase): null;
    }

    /**
     * Returns all the cases of the current user
     * @return Collection
     */
    public function myCases(): Collection
    {
        $user = Session::get('user');

        $dbCases = EloquentCase::where('owner', $user->id)->orderBy('updated_at', 'desc')->get();

        $cases = array();

        foreach($dbCases as $dbCase) {
            $cases[] = $this->caseFromEloquentModel($dbCase);
        };

        return collect($cases);
    }

    /**
     * Create a new, empty case in draft status.
     *
     * @return CovidCase
     */
    public function createDraftCase(): CovidCase
    {
        $dbCase = new EloquentCase();

        // TODO: this isn't strictly 'db case specific', where should this go? abstract case repository?
        $user = Session::get('user');
        $dbCase->owner = $user->id;

        $dbCase->status = CovidCase::STATUS_DRAFT;

        $dbCase->save();
        return $this->caseFromEloquentModel($dbCase);
    }

    /**
     * Update case.
     *
     * @param CovidCase $case Case entity
     */
    public function updateCase(CovidCase $case)
    {
        $dbCase = $this->eloquentModelFromCase($case);
        $dbCase->save();
    }

    private function caseFromEloquentModel(EloquentCase $dbCase): CovidCase
    {
        $case = new CovidCase();
        $case->uuid = $dbCase->uuid;
        $case->caseId = $dbCase->case_id;
        $case->dateOfSymptomOnset = $dbCase->date_of_symptom_onset != NULL ? new Date($dbCase->date_of_symptom_onset) : null;
        $case->name = $dbCase->name;
        $case->owner = $dbCase->owner;
        $case->status = $dbCase->status;
        $case->updatedAt = new Date($dbCase->updated_at);

        return $case;
    }

    private function eloquentModelFromCase(CovidCase $case): EloquentCase
    {
        $dbCase = new EloquentCase();
        $dbCase->uuid = $case->uuid;
        $dbCase->case_id = $case->caseId;
        $dbCase->date_of_symptom_onset = new \DateTimeImmutable($dbCase->dateOfSymptomOnset);
        $dbCase->name = $case->name;
        $dbCase->owner = $case->owner;
        $dbCase->status = $case->status;

        // Don't set updatedAt, will be auto updated by eloquent.

        return $dbCase;
    }

    /**
     * @param CovidCase $case
     * @return bool True if the currently logged in user is the owner of this case.
     */
    public function isOwner(CovidCase $case): bool
    {
        // TODO: this isn't strictly 'db case specific', where should this go? abstract case repository?
        $user = Session::get('user');
        return $user->id == $case->owner;
    }
}
