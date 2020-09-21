<?php
namespace App\Application\Services;

use App\Application\Managers\TransactionManager;
use App\Application\Models\Example;
use App\Application\Repositories\ExampleRepository;
use Exception;
use Psr\Log\LoggerInterface;

class ExampleService
{
    /**
     * @var ExampleRepository
     */
    private ExampleRepository $exampleRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var \App\Application\Managers\TransactionManager
     */
    private TransactionManager $transactionManager;

    /**
     * Constructor.
     *
     * @param ExampleRepository $$exampleRepository
     * @param LoggerInterface   $logger
     */
    public function __construct(
        ExampleRepository $exampleRepository,
        LoggerInterface $logger,
        TransactionManager $transactionManager
    )
    {
        $this->exampleRepository = $exampleRepository;
        $this->logger = $logger;
        $this->transactionManager = $transactionManager;
    }

    /**
     * Run the example.
     *
     * @return Example
     *
     * @throws Exception
     */
    public function example(): Example
    {
        $this->logger->debug('Run example code');
        return $this->transactionManager->run(function () {

            $example = $this->exampleRepository->createExample();
            // ...
            $this->exampleRepository->markExampleAsPrepared($example);

            return $example;
        });
    }
}
