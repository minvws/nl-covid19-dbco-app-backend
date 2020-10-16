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
            "uuid": "3fa85f64-5717-4562-b3fc-2c963f66afa6",
            "taskType": "contact",
            "questions": [
                {
                    "uuid": "37d818ed-9499-4b9a-9771-725467368387",
                    "group": "classification",
                    "questionType": "classificationdetails",
                    "label": "Vragen over jullie ontmoeting",
                    "description": null,
                    "relevantForCategories": [{
                        "category": "1", 
                    },{
                        "category": "2a"
                    },{
                        "category": "2b"
                    },{
                        "category": "3"
                    }]
                },
                {
                    "uuid": "37d818ed-9499-4b9a-9770-725467368388",
                    "group": "contactdetails",
                    "questionType": "contactdetails",
                    "label": "Contactgegevens",
                    "description": null,
                    "relevantForCategories": [{
                        "category": "1", 
                    },{
                        "category": "2a"
                    },{
                        "category": "2b"
                    },{
                        "category": "3"
                    }]
                },
                {
                    "uuid": "37d818ed-9499-4b9a-9771-725467368388",
                    "group": "contactdetails",
                    "questionType": "date",
                    "label": "Geboortedatum",
                    "description": null,
                    "relevantForCategories": [{
                        "category": "1" 
                    }]
                },
                {
                    "uuid": "37d818ed-9499-4b9a-9771-725467368389",
                    "group": "contactdetails",
                    "questionType": "open",
                    "label": "Beroep",
                    "description": null,
                    "relevantForCategories": [{
                        "category": "1" 
                    }]
                },
                {
                    "uuid": "37d818ed-9499-4b9a-9771-725467368391",
                    "group": "contactdetails",
                    "questionType": "multiplechoice",
                    "label": "Waar ken je deze persoon van?",
                    "description": null,
                    "relevantForCategories": [{
                        "category": "2a"
                    },{
                        "category": "2b"
                    }],
                    "answerOptions": [
                        {
                            "label": "Ouder",
                            "value": "Ouder"
                        },
                        {
                            "label": "Kind",
                            "value": "Kind"
                        },
                        {
                            "label": "Broer of zus",
                            "value": "Broer of zus"
                        },
                        {
                            "label": "Partner",
                            "value": "Partner"
                        },
                        {
                            "label": "Familielid (overig)",
                            "value": "Familielid (overig)"
                        },
                        {
                            "label": "Huisgenoot",
                            "value": "Huisgenoot"
                        },
                        {
                            "label": "Vriend of kennis",
                            "value": "Vriend of kennis"
                        },
                        {
                            "label": "Medestudent of leerling",
                            "value": "Medestudent of leerling"
                        },
                        {
                            "label": "Collega",
                            "value": "Collega"
                        },
                        {
                            "label": "Gezondheidszorg medewerker",
                            "value": "Gezondheidszorg medewerker"
                        },
                        {
                            "label": "Ex-partner",
                            "value": "Ex-partner"
                        },
                        {
                            "label": "Overig",
                            "value": "Overig"
                        }
                    ]

                },
                {
                    "uuid": "37d818ed-9499-4b9a-9771-725467368392",
                    "group": "contactdetails",
                    "questionType": "multiplechoice",
                    "label": "Is een of meerdere onderstaande zaken van toepassing voor deze persoon?",
                    "description": "* Is student\n* 70 jaar of ouder\n* Heeft gezondheidsklachten of loopt extra gezondheidsrisico's\n* Woont in een asielzoekerscentrum\n* Spreekt slecht of geen Nederlands",
                    "relevantForCategories": [{
                        "category": "1", 
                    },{
                        "category": "2a"
                    },{
                        "category": "2b"
                    }],
                    "answerOptions": [
                        {
                            "label": "Ja, één of meerdere dingen",
                            "value": "Ja",
                            "trigger": "communication_staff"
                        },
                        {
                            "label": "Nee, ik denk het niet",
                            "value": "Nee",
                            "trigger": "communication_index"
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
