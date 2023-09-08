<?php

declare(strict_types=1);

namespace Tests\Unit\Services\SecureMail;

use App\Services\SecureMail\SecureMailMessage;
use App\Services\SecureMail\SecureMailStatusUpdate;
use App\Services\SecureMail\SecureMailV1Client;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use MinVWS\DBCO\Enum\Models\MessageStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Helpers\ConfigHelper;
use Tests\TestCase;

use function json_decode;
use function sprintf;
use function str_contains;

#[Group('secure-mail-send-message')]
#[Group('message')]
final class SecureMailV1ClientTest extends TestCase
{
    public function testSecureMailMessageCreation(): void
    {
        CarbonImmutable::setTestNow('2020');
        $expiryInDays = 30;

        $secureMailMessage = $this->createSecureMailMessage();

        ConfigHelper::set('misc.secure_mail.default_expiry_in_days', $expiryInDays);

        $baseUrl = 'https://secure-mail.local/api/v1/';
        Http::fake([
            'https://secure-mail.local/api/v1/messages' => Http::response([
                'id' => '80ee022d-90d1-4d85-aa09-f3af1528af18',
            ]),
        ]);
        $client = new SecureMailV1Client($baseUrl, 'portal_jwt_secret');
        $messageId = $client->postMessage($secureMailMessage);

        $this->assertEquals('80ee022d-90d1-4d85-aa09-f3af1528af18', $messageId);

        Http::assertSent(static function (Request $request) use ($baseUrl, $expiryInDays) {
            return $request->url() === $baseUrl . 'messages' &&
                $request['aliasId'] === 'aliasId' &&
                $request['fromName'] === 'fromName' &&
                $request['fromEmail'] === 'fromEmail' &&
                $request['toName'] === 'toName' &&
                $request['toEmail'] === 'toEmail' &&
                $request['phoneNumber'] === 'phoneNumber' &&
                $request['subject'] === 'subject' &&
                $request['text'] === 'text' &&
                $request['footer'] === 'footer' &&
                $request['type'] === 'secure' &&
                $request['expiresAt'] === CarbonImmutable::now()->addDays($expiryInDays)->format('c') &&
                $request['identityRequired'] === false &&
                $request['pseudoBsnToken'] === 'pseudoBsnToken';
        });
    }

    public function testSecureMailRetrieveMessages(): void
    {
        $baseUrl = 'https://secure-mail.local/api/v1/';
        Http::fake([
            'https://secure-mail.local/api/v1/messages/statusupdates*' => Http::response(
                json_decode(
                    '{"total":1,"count":1,"messages":[{"id":"32e5aa16-1bbd-4cea-a69a-081bfc0a7f39","notificationSentAt":null,"status":"draft"}]}',
                    true,
                ),
            ),
        ]);
        $client = new SecureMailV1Client($baseUrl, 'portal_jwt_secret');
        $secureMailCollection = $client->getSecureMailStatusUpdates(CarbonImmutable::now());

        $this->assertEquals(1, $secureMailCollection->total);
        $this->assertEquals(1, $secureMailCollection->count);
        $this->assertEquals(
            new SecureMailStatusUpdate('32e5aa16-1bbd-4cea-a69a-081bfc0a7f39', null, MessageStatus::draft()),
            $secureMailCollection->secureMailStatusUpdates[0],
        );

        Http::assertSent(static function (Request $request) use ($baseUrl) {
            return str_contains($request->url(), $baseUrl . 'messages/statusupdates');
        });
    }

    public function testSecureMailRetrieveMessagesWithInccorrectEnumValue(): void
    {
        $baseUrl = 'https://secure-mail.local/api/v1/';
        Http::fake([
            'https://secure-mail.local/api/v1/messages/statusupdates*' => Http::response(
                json_decode(
                    '{"total":1,"count":1,"messages":[{"id":"32e5aa16-1bbd-4cea-a69a-081bfc0a7f39","notificationSentAt":null,"status":"incorrect"}]}',
                    true,
                ),
            ),
        ]);
        $client = new SecureMailV1Client($baseUrl, 'portal_jwt_secret');

        $this->expectException(InvalidArgumentException::class);
        $client->getSecureMailStatusUpdates(CarbonImmutable::now());
    }

    #[DataProvider('baseUrlDataProvider')]
    public function testSecureMailBearerTokenGeneration(string $baseUrl): void
    {
        Http::fake([
            'https://secure-mail.local/api/v1/messages' => Http::response([
                'id' => '80ee022d-90d1-4d85-aa09-f3af1528af18',
            ]),
        ]);

        CarbonImmutable::setTestNow('2021-01-01T00:00:00+000');
        $client = new SecureMailV1Client($baseUrl, 'portal_jwt_secret');

        $secureMail = $this->createSecureMailMessage();
        $client->postMessage($secureMail);

        Http::assertSent(static function (Request $request) {
            return $request->hasHeader(
                'Authorization',
                'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImtpZCI6InBvcnRhbCJ9.eyJpYXQiOjE2MDk0NTkyMDAsImV4cCI6MTYwOTQ1OTI2MH0.GGVevk3BuQNeORZvlCxTP2knL1Z0W7OZnVRGOmZ306w',
            );
        });
    }

    #[DataProvider('baseUrlDataProvider')]
    public function testSecureMailBaseUrl(string $baseUrl): void
    {
        Http::fake([
            'https://secure-mail.local/api/v1/messages' => Http::response([
                'id' => '80ee022d-90d1-4d85-aa09-f3af1528af18',
            ]),
        ]);

        $client = new SecureMailV1Client($baseUrl, 'portal_jwt_secret');
        $secureMail = $this->createSecureMailMessage();
        $client->postMessage($secureMail);

        Http::assertSent(static function (Request $request) {
            $uri = $request->toPsrRequest()->getUri();
            return
                $uri->getScheme() === 'https' &&
                $uri->getHost() === 'secure-mail.local' &&
                $uri->getPath() === '/api/v1/messages';
        });
    }

    #[DataProvider('baseUrlDataProvider')]
    public function testDelete(string $baseUrl): void
    {
        Http::fake();
        $uuid = $this->faker->uuid();

        $client = new SecureMailV1Client($baseUrl, 'portal_jwt_secret');
        $client->delete($uuid);

        Http::assertSent(static function (Request $request) use ($uuid) {
            $uri = $request->toPsrRequest()->getUri();
            return
                $uri->getScheme() === 'https' &&
                $uri->getHost() === 'secure-mail.local' &&
                $uri->getPath() === sprintf('/api/v1/messages/%s', $uuid);
        });
    }

    public static function baseUrlDataProvider(): array
    {
        return [
            'with trailing slash' => ['https://secure-mail.local/api/v1/'],
            'without trailing slash' => ['https://secure-mail.local/api/v1'],
        ];
    }

    private function createSecureMailMessage(): SecureMailMessage
    {
        return SecureMailMessage::new(
            'aliasId',
            'fromName',
            'fromEmail',
            'toName',
            'toEmail',
            'phoneNumber',
            'subject',
            'text',
            'footer',
            true,
            CarbonImmutable::now()->addDays(30),
            false,
            'pseudoBsnToken',
        );
    }
}
