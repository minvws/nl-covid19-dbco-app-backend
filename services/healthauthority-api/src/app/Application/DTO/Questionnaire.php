<?php
declare(strict_types=1);

namespace DBCO\HealthAuthorityAPI\Application\DTO;

use DBCO\HealthAuthorityAPI\Application\Models\Questionnaire as QuestionnaireModel;
use JsonSerializable;

/**
 * Questionnaire DTO.
 *
 * @package DBCO\HealthAuthorityAPI\Application\DTO
 */
class Questionnaire implements JsonSerializable
{
    /**
     * @var QuestionnaireModel $questionnaire
     */
    private QuestionnaireModel $questionnaire;

    /**
     * Constructor.
     *
     * @param QuestionnaireModel $questionnaire
     */
    public function __construct(QuestionnaireModel $questionnaire)
    {
        $this->questionnaire = $questionnaire;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'uuid' => $this->questionnaire->uuid,
            'taskType' => $this->questionnaire->taskType,
            'questions' => array_map(fn($q) => new Question($q), $this->questionnaire->questions)
        ];
    }
}
