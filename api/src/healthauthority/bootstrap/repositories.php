<?php
declare(strict_types=1);

use App\Application\Repositories\CaseTaskRepository;
use App\Application\Repositories\StubCaseTaskRepository;
use App\Application\Repositories\StubGeneralTaskRepository;
use App\Application\Repositories\StubQuestionnaireRepository;
use App\Application\Repositories\GeneralTaskRepository;
use App\Application\Repositories\QuestionnaireRepository;

use DI\ContainerBuilder;
use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        QuestionnaireRepository::class => autowire(StubQuestionnaireRepository::class),
        GeneralTaskRepository::class => autowire(StubGeneralTaskRepository::class),
        CaseTaskRepository::class => autowire(StubCaseTaskRepository::class),
    ]);
};
