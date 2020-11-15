<?php

namespace App\Repositories;

use App\Models\BCOUser;
use App\Models\Eloquent\EloquentCase;
use App\Models\CovidCase;
use Illuminate\Pagination\LengthAwarePaginator;

class DbCaseRepository implements CaseRepository
{
    /**
     * Returns the case and its task list.
     *
     * @param string $caseUuid Case identifier.
     *
     * @return CovidCase The found case (or null if not found)
     */
    public function getCase(string $caseUuid): ?CovidCase
    {
        $dbCase = $this->getCaseFromDb($caseUuid);
        return $dbCase != null ? $this->caseFromEloquentModel($dbCase): null;
    }

    /**
     * @param string $caseUuid
     * @return EloquentCase|null
     */
    private function getCaseFromDb(string $caseUuid): ?EloquentCase
    {
        $cases = EloquentCase::where('uuid', $caseUuid)->get();
        return $cases->first();
    }

    /**
     * Returns all the cases of a specicic user
     * @return LengthAwarePaginator
     */
    public function getCasesByAssignedUser(BCOUser $user): LengthAwarePaginator
    {
        $paginator = EloquentCase::where('assigned_uuid', $user->uuid)->orderBy('covidcase.updated_at', 'desc')->paginate(config('view.rowsPerPage'));

        $cases = array();

        foreach($paginator->items() as $dbCase) {
            $case = $this->caseFromEloquentModel($dbCase);
            $cases[] = $case;
        };

        $paginator->setCollection(collect($cases));
        return $paginator;
    }

    public function getCasesByOrganisation(BCOUser $user): LengthAwarePaginator
    {
        $paginator = EloquentCase::where('user_organisation.user_uuid', $user->uuid)
                ->select('covidcase.*')
                ->join('user_organisation', 'user_organisation.organisation_uuid', '=', 'covidcase.organisation_uuid')
                ->orderBy('covidcase.updated_at', 'desc')->paginate(config('view.rowsPerPage'));

        $cases = array();

        foreach($paginator->items() as $dbCase) {
            $case = $this->caseFromEloquentModel($dbCase);
            $cases[] = $case;
        };

        $paginator->setCollection(collect($cases));

        return $paginator;
    }

    /**
     * Create a new, empty case.
     *
     * @return CovidCase
     */
    public function createCase(BCOUser $owner, string $initialStatus, ?BCOUser $assignedTo=null): CovidCase
    {
        $dbCase = new EloquentCase();

        $dbCase->owner = $owner->uuid;
        $dbCase->status = $initialStatus;
        $dbCase->organisation_uuid = $owner->organisations[0]->uuid; // TODO fix me: what if user has 2 orgs?

        if ($assignedTo != null) {
            $dbCase->assigned_uuid = $assignedTo->uuid;
        }

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
        // TODO fixme: this retrieves the object from the db, again; but eloquent won't let us easily instantiate
        // an object directly from a CovidCase.
        $dbCase = $this->getCaseFromDb($case->uuid);
        $dbCase->case_id = $case->caseId;
        $dbCase->name = $case->name;
        $dbCase->status = $case->status;
        $dbCase->date_of_symptom_onset = $case->dateOfSymptomOnset != null ? $case->dateOfSymptomOnset->toDateTimeImmutable() : null;
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

}
