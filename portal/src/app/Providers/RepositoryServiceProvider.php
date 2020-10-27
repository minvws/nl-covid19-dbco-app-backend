<?php

namespace App\Providers;

use App\Repositories\AnswerRepository;
use App\Repositories\ApiPairingRepository;
use App\Repositories\CaseRepository;
use App\Repositories\DbAnswerRepository;
use App\Repositories\DbCaseRepository;
use App\Repositories\DbQuestionnaireRepository;
use App\Repositories\DbQuestionRepository;
use App\Repositories\PairingRepository;
use App\Repositories\QuestionnaireRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\TaskRepository;
use App\Repositories\DbTaskRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CaseRepository::class, DbCaseRepository::class);
        $this->app->bind(TaskRepository::class, DbTaskRepository::class);
        $this->app->bind(PairingRepository::class, ApiPairingRepository::class);
        $this->app->bind(QuestionnaireRepository::class, DbQuestionnaireRepository::class);
        $this->app->bind(AnswerRepository::class, DbAnswerRepository::class);
        $this->app->bind(QuestionRepository::class, DbQuestionRepository::class);
    }

}
