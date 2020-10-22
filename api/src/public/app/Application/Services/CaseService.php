<?php
namespace DBCO\PublicAPI\Application\Services;

use DBCO\PublicAPI\Application\Models\CovidCase;
use DBCO\PublicAPI\Application\Models\GeneralTaskList;
use DBCO\PublicAPI\Application\Repositories\CaseRepository;
use DBCO\PublicAPI\Application\Repositories\GeneralTaskRepository;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Responsible for listing tasks.
 *
 * @package DBCO\PublicAPI\Application\Services
 */
class CaseService
{
    /**
     * @var GeneralTaskRepository
     */
    private GeneralTaskRepository $generalTaskRepository;

    /**
     * @var CaseRepository
     */
    private CaseRepository $caseRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor.
     *
     * @param GeneralTaskRepository $generalTaskRepository
     * @param CaseRepository        $caseRepository
     * @param LoggerInterface       $logger
     */
    public function __construct(
        GeneralTaskRepository $generalTaskRepository,
        CaseRepository $caseRepository,
        LoggerInterface $logger
    )
    {
        $this->generalTaskRepository = $generalTaskRepository;
        $this->caseRepository = $caseRepository;
        $this->logger = $logger;
    }

    /**
     * Returns the general task list.
     *
     * @return GeneralTaskList
     *
     * @throws Exception
     */
    public function getGeneralTasks(): GeneralTaskList
    {
        return $this->generalTaskRepository->getGeneralTasks();
    }

    /**
     * Returns the case and its task list.
     *
     * @param string $caseId Case identifier.
     *
     * @return CovidCase
     */
    public function getCase(string $caseId): CovidCase
    {
        // TODO: verify access to case using signed otp
        return $this->caseRepository->getCase($caseId);
    }

    /**
     * Submit case tasks.
     *
     * @param string $caseId Case identifier.
     * @param string $body   Encrypted body.
     */
    public function submitCase(string $caseId, string $body): void
    {
        // TODO: verify signature and access
        $this->caseRepository->submitCase($caseId, $body);
    }
}
