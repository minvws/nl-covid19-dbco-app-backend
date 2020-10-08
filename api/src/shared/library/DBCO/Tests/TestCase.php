<?php
declare(strict_types=1);

namespace DBCO\Tests;

use Exception;
use PDO;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Slim\App;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

/**
 * Test case base.
 *
 * @package Tests
 */
class TestCase extends PHPUnit_TestCase
{
    /**
     * @var App
     */
    protected ?App $app = null;


    /**
     * Returns the per test app instance.
     *
     * @return App
     *
     * @throws Exception
     */
    protected function getAppInstance(): App
    {
        return $this->app;
    }

    /**
     * Create a new app instance.
     *
     * @return App
     *
     * @throws Exception
     */
    protected function createAppInstance(): App
    {
        $app = require APP_ROOT . '/bootstrap/application.php';
        $container = $app->getContainer();
        $container->set(LoggerInterface::class, new NullLogger());
        return $app;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $headers
     * @param array  $cookies
     * @param array  $serverParams
     * @return Request
     */
    protected function createRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = []
    ): Request {
        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }

    /**
     * Assert that the given date is an ISO8601 zulu date.
     *
     * @param string $date
     */
    protected function assertISO8601ZuluDate(string $date)
    {
        $this->assertEquals(1, preg_match('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?[zZ])?)?$/', $date));
    }

    /**
     * Set up.
     *
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app = $this->createAppInstance();

        // start transaction
        /** @var $db PDO */
        $db = $this->app->getContainer()->get(PDO::class);
        $db->beginTransaction();
    }

    /**
     * Clean up.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // rollback transaction
        /** @var $db PDO */
        $db = $this->app->getContainer()->get(PDO::class);
        $db->rollBack();

        $this->app = null;
    }
}
