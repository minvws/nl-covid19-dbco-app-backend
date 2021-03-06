<?php

namespace App\Models;

class Question
{
    public string $uuid;

    public string $group;

    public string $questionType;

    public string $label;

    public ?string $header;

    public ?string $description;

    public array $relevantForCategories;

    public ?array $answerOptions; // only used for questionType = multiplechoice

    public function getAnswerValidationRules()
    {
        return [];
    }
}
