<?php

namespace App\Models;

use Jenssegers\Date\Date;

class Task
{
    public string $uuid;

    public string $taskType;

    public string $source;

    public string $label;

    public ?string $category;

    public ?string $taskContext;

    public ?string $nature;

    public ?Date $dateOfLastExposure;

    public string $communication;

    public bool $informedByIndex;

    public ?array $answers;

    // Filled upon submit, indicates which questionnaire the user filled
    public ?string $questionnaireUuid;

    public int $progress = 0;

}