<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CovidCase\Communication;
use App\Models\Versions\CovidCase\Communication\CommunicationV3Up;
use MinVWS\DBCO\Enum\Models\IsolationAdvice;
use MinVWS\DBCO\Enum\Models\YesNoUnknown;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\FeatureTestCase;

use function sprintf;

#[Group('case-fragment')]
#[Group('case-fragment-communication')]
class ApiCaseCommunicationV3UpControllerTest extends FeatureTestCase
{
    public function testGet(): void
    {
        /** @var CommunicationV3Up $communication */
        $communication = Communication::getSchema()->getVersion(3)->newInstance();
        $communication->isolationAdviceGiven = [
            IsolationAdvice::liveSeperatedExplained()->value,
            IsolationAdvice::testAdviceHousematesExplained()->value,
        ];
        $communication->conditionalAdviceGiven = 'some conditional advice';
        $communication->otherAdviceGiven = 'some other advice';
        $communication->particularities = 'some particularities';
        $communication->scientificResearchConsent = YesNoUnknown::yes();
        $communication->remarksRivm = $remarks = $this->faker->text(500);

        $user = $this->createUser();
        $case = $this->createCaseForUser($user, ['communication' => $communication, 'schema_version' => 5]);

        $response = $this->be($user)->get(sprintf('/api/cases/%s/fragments/communication', $case->uuid));
        $response->assertStatus(200);

        $expectedValue = [
            'data' => [
                'schemaVersion' => 3,
                'isolationAdviceGiven' => [IsolationAdvice::liveSeperatedExplained()->value, IsolationAdvice::testAdviceHousematesExplained()->value],
                'conditionalAdviceGiven' => 'some conditional advice',
                'otherAdviceGiven' => 'some other advice',
                'particularities' => 'some particularities',
                'scientificResearchConsent' => 'yes',
                'remarksRivm' => $remarks,
            ],
        ];
        $response->assertJson($expectedValue);
    }

    #[DataProvider('postValidDataProvider')]
    public function testPostValid(array $postData): void
    {
        /** @var Communication $communication */
        $communication = Communication::getSchema()->getCurrentVersion()->newInstance();

        $user = $this->createUser();
        $case = $this->createCaseForUser($user, [
            'communication' => $communication,
        ]);

        // check minimum fields required for storage
        $response = $this->be($user)->putJson(sprintf('/api/cases/%s/fragments/communication', $case->uuid), $postData);
        $response->assertStatus(200);
    }

    public static function postValidDataProvider(): array
    {
        return [
            'all set' => [
                [
                    'isolationAdviceGiven' => [
                        IsolationAdvice::liveSeperatedExplained()->value,
                        IsolationAdvice::testAdviceHousematesExplained()->value,
                    ],
                    'conditionalAdviceGiven' => 'some conditional advice',
                    'otherAdviceGiven' => 'some other advice',
                    'particularities' => 'some particularities',
                    'scientificResearchConsent' => 'yes',
                ],
            ],
            'partial' => [
                [
                    'isolationAdviceGiven' => [
                        IsolationAdvice::liveSeperatedExplained()->value,
                    ],
                    'otherAdviceGiven' => null,
                    'particularities' => 'some particularities',
                ],
            ],
            'empty' => [
                [],
            ],
        ];
    }

    #[DataProvider('postInvalidDataProvider')]
    public function testPostInvalid(array $postData, string $expectedError): void
    {
        /** @var Communication $communication */
        $communication = Communication::getSchema()->getCurrentVersion()->newInstance();

        $user = $this->createUser();
        $case = $this->createCaseForUser($user, [
            'communication' => $communication,
        ]);

        // check minimum fields required for storage
        $response = $this->be($user)->putJson(sprintf('/api/cases/%s/fragments/communication', $case->uuid), $postData);
        $response->assertStatus(400);
        $json = $response->json();
        $this->assertArrayHasKey($expectedError, $json['validationResult']['fatal']['errors']);
    }

    public static function postInvalidDataProvider(): array
    {
        return [
            'invalid liveSeperatedExplained' => [
                [
                    'isolationAdviceGiven' => [
                        'liveSeperatedExplained' => 'foo',
                    ],
                ],
                'isolationAdviceGiven.liveSeperatedExplained',
            ],
            'invalid value in isolationAdviceGiven' => [
                [
                    'isolationAdviceGiven' => [
                        'foo',
                    ],
                ],
                'isolationAdviceGiven.0',
            ],
            'invalid conditionalAdviceGiven' => [
                [
                    'conditionalAdviceGiven' => ['foo'],
                ],
                'conditionalAdviceGiven',
            ],
            'invalid otherAdviceGiven' => [
                [
                    'otherAdviceGiven' => ['foo'],
                ],
                'otherAdviceGiven',
            ],
            'invalid particularities' => [
                [
                    'particularities' => ['foo'],
                ],
                'particularities',
            ],
            'invalid scientificResearchConsent' => [
                [
                    'scientificResearchConsent' => ['foo'],
                ],
                'scientificResearchConsent',
            ],
        ];
    }
}
