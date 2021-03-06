<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Support\Collection;
use Jenssegers\Date\Date;

interface TaskRepository
{
    /**
     * Returns task list.
     *
     * @param string $caseUuid Case identifier.
     *
     * @return Collection List of tasks
     */
    public function getTasks(string $caseUuid): Collection;

    /**
     * Returns single task.
     *
     * @param string $uuid Task identifier.
     *
     * @return Task The task (or null if not found)
     */
    public function getTask(string $taskUuid): ?Task;

    /**
     * Update a task in the db
     *
     * @param Task $task The updated task
     */
    public function updateTask(Task $task);

    /**
     * Create a new task
     *
     * @return Task
     */
    public function createTask(string $caseUuid, string $label, ?string $context, string $category, string $communication, ?Date $dateOfLastExposure): Task;

    /**
     * @param string $caseUuid Case to clean up
     * @param array $keep Task uuids to keep
     */
    public function deleteRemovedTasks(string $caseUuid, array $keep);
}
