<?php

namespace App\Repositories;

use App\Models\Answer;
use App\Models\ClassificationDetailsAnswer;
use App\Models\ContactDetailsAnswer;
use App\Models\Eloquent\EloquentAnswer;
use App\Models\SimpleAnswer;
use Illuminate\Support\Collection;

class DbAnswerRepository implements AnswerRepository
{
    public function getAllAnswersByCase(string $caseUuid): Collection
    {
        $dbAnswers = EloquentAnswer::where('case_uuid', $caseUuid)
            ->select('answer.*', 'question.question_type')
            ->join('task', 'answer.task_uuid', '=', 'task.uuid')
            ->join('question', 'answer.question_uuid', '=', 'question.uuid')->get();

        $answers = array();

        foreach($dbAnswers as $dbAnswer) {
            $answers[] = $this->answerFromEloquentModel($dbAnswer);
        };

        return collect($answers);
    }

    public function answerFromEloquentModel(EloquentAnswer $dbAnswer): Answer
    {
        $answer = null;
        switch($dbAnswer->question_type) {
            case 'contactdetails':
                $answer = new ContactDetailsAnswer();
                $answer->firstname = $dbAnswer->ctd_firstname;
                $answer->lastname = $dbAnswer->ctd_lastname;
                $answer->email = $dbAnswer->ctd_email;
                $answer->phonenumber = $dbAnswer->ctd_phonenumber;
                break;
            case 'classificationdetails':
                $answer = new ClassificationDetailsAnswer();
                $answer->distanceRisk = ($dbAnswer->cfd_distancerisk == 1);
                $answer->livedTogetherRisk = ($dbAnswer->cfd_livedtogetherrisk == 1);
                $answer->durationRisk = ($dbAnswer->cfd_durationrisk == 1);
                $answer->otherRisk = ($dbAnswer->cfd_otherrisk == 1);
                break;
            default:
                $answer = new SimpleAnswer();
                $answer->value = $dbAnswer->spv_value;
        }

        $answer->uuid = $dbAnswer->uuid;
        $answer->taskUuid = $dbAnswer->task_uuid;
        $answer->questionUuid = $dbAnswer->question_uuid;

        return $answer;
    }
}