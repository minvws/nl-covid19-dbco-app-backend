<?php
declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Responses\RegisterCaseResponse;
use App\Application\Services\CaseService;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

/**
 * Register a new DBCO case.
 *
 * @package App\Application\Actions
 */
class RegisterCaseAction extends Action
{
    protected CaseService $caseService;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger
     * @param CaseService     $caseService
     */
    public function __construct(
        LoggerInterface $logger,
        CaseService $caseService
    )
    {
        parent::__construct($logger);
        $this->caseService = $caseService;
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();

        $errors = [];

        $caseId = $body['caseId'] ?? null;
        if (empty($caseId)) {
            $errors[] = ValidationError::body('isRequired', 'caseId is required', ['caseId']);
        }

        $caseExpiresAt = $body['caseExpiresAt'] ?? null;
        if (empty($caseExpiresAt)) {
            $errors[] = ValidationError::body('isRequired', 'caseExpiresAt is required', ['caseExpiresAt']);
        } else {
            $caseExpiresAt = new DateTime($caseExpiresAt);
        }

        // TODO: validate timestamp format

        if (count($errors) > 0) {
            throw new ValidationException($this->request, $errors);
        }

        try {
            $pairing = $this->caseService->registerCase($caseId, $caseExpiresAt);
            return $this->respond(new RegisterCaseResponse($pairing));
        } catch (Exception $e) {
            throw new ActionException('registrationFailed', 'Case registration failed', ActionException::INTERNAL_SERVER_ERROR, $e);
        }
    }
}
