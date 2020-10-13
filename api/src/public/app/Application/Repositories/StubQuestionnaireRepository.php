<?php
namespace App\Application\Repositories;

use App\Application\Models\QuestionnaireList;

/**
 * Used for retrieving questionnaires.
 *
 * Stub implementation.
 *
 * @package App\Application\Repositories
 */
class StubQuestionnaireRepository implements QuestionnaireRepository
{
    /**
     * Returns the questionnaire list.
     *
     * @return QuestionnaireList
     */
    public function getQuestionnaires(): QuestionnaireList
    {
        $body = <<<'EOD'
{
  "questionnaires": [
    {
      "id": "3fa85f64-5717-4562-b3fc-2c963f66afa6",
      "taskType": "contact",
      "questions": [
          {
              "id": "37d818ed-9499-4b9a-9771-725467368387",
              "group": "context",
              "questionType": "classificationdetails",
              "label": "Vragen over jullie ontmoeting",
              "description": null,
              "relevantForCategories": [ "1", "2a", "2b", "3" ]
          },
          {
              "id": "37d818ed-9499-4b9a-9771-725467368388",
              "group": "contactdetails",
              "questionType": "date",
              "label": "Geboortedatum",
              "description": null,
              "relevantForCategories": [ "1" ]
          },
          {
              "id": "37d818ed-9499-4b9a-9771-725467368389",
              "group": "contactdetails",
              "questionType": "open",
              "label": "Beroep",
              "description": null,
              "relevantForCategories": [ "1" ]
          },
          {
              "id": "37d818ed-9499-4b9a-9771-725467368391",
              "group": "contactdetails",
              "questionType": "multiplechoice",
              "label": "Waar ken je deze persoon van?",
              "description": null,
              "relevantForCategories": [ "2a", "2b" ],
              "answerOptions": [
                  {
                      "label": "Vriend of kennis",
                      "value": "Vriend of kennis"
                  },
                  {
                      "label": "Collega",
                      "value": "Collega"
                  },
                  {
                      "label": "Overig",
                      "value": "Overig"
                  }
              ]
              
          },
          {
              "id": "37d818ed-9499-4b9a-9771-725467368392",
              "group": "contactdetails",
              "questionType": "multiplechoice",
              "label": "Is een of meerdere onderstaande zaken van toepassing voor deze persoon?",
              "description": "<ul><li>Is student<li>70 jaar of ouder<li>Heeft gezondheidsklachten of loopt extra gezondheidsrisico's<li>Woont in een asielzoekerscentrum<li>Spreekt slecht of geen Nederlands</ul>",
              "relevantForCategories": [ "1", "2a", "2b" ],
              "answerOptions": [
                  {
                      "label": "Ja, één of meerdere dingen",
                      "value": "Ja"
                  },
                  {
                      "label": "Nee, ik denk het niet",
                      "value": "Nee"
                  }
              ]
          }
      ]
    }
  ]
}
EOD;

        return new QuestionnaireList([], $body);
    }
}
