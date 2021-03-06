<?php
declare(strict_types=1);

use DBCO\Worker\Application\Commands\StatusCommand;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Predis\Client as PredisClient;
use Psr\Log\LoggerInterface;
use function DI\autowire;
use function DI\get;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            'logger.handlers' => [
                autowire(StreamHandler::class)->constructor(get('logger.path'), get('logger.level'))
            ],
            'logger.processors' => [
                autowire(UidProcessor::class)
            ],
            LoggerInterface::class =>
                autowire(Logger::class)
                    ->constructor(
                        get('logger.name'),
                        get('logger.handlers'),
                        get('logger.processors')
                    ),
            PredisClient::class =>
                autowire(PredisClient::class)
                    ->constructor(get('redis.parameters'), get('redis.options')),
            'healthAuthorityGuzzleClient' =>
                autowire(GuzzleHttp\Client::class)
                    ->constructor(get('healthAuthorityAPI')),
            StatusCommand::class => autowire(StatusCommand::class)
                ->constructorParameter(
                    'healthAuthorityGuzzleClient',
                    get('healthAuthorityGuzzleClient')
                )
        ]
    );
};
