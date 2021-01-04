<?php

namespace App\Http\Controllers;

use App\Repositories\StateRepository;
use App\Services\CaseService;
use App\Services\QuestionnaireService;
use App\Services\TaskService;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    private TaskService $taskService;
    private QuestionnaireService $questionnaireService;

    public function __construct(TaskService $taskService,
                                QuestionnaireService $questionnaireService)
    {
        $this->taskService = $taskService;
        $this->questionnaireService = $questionnaireService;
    }

    public function linkTaskToExport(Request $request)
    {
        $exportId = trim($request->input('exportId'));
        if (empty($exportId)) {
            return response()->json(['error' => "Export ID is invalid"], Response::HTTP_BAD_REQUEST);
        }

        $taskUuid = $request->input('taskId');
        $task = $this->taskService->getTask($taskUuid);

        if ($task === null) {
            return response()->json(['error' => "Task $taskUuid is invalid"], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->taskService->canAccess($task)) {
            return response()->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $this->taskService->linkTaskToExport($task, $exportId);

        return response()->json(['success' => 'success'], Response::HTTP_OK);
    }

    public function viewTaskQuestionnaire($taskUuid)
    {
        $task = $this->taskService->getTask($taskUuid);
        if (!$this->taskService->canAccess($task)) {
            return "access denied";
        }

        $questionnaireUuid = $task->questionnaireUuid;
        if ($task->questionnaireUuid === null) {
            // Not yet filled by user, get the latest questionnaire.
            $questionnaire = $this->questionnaireService->getLatestQuestionnaire($task->taskType);
            $answers = $this->taskService->getAllAnswersByTask($taskUuid);
        } else {
            $questionnaire = $this->questionnaireService->getQuestionnaire($questionnaireUuid);
            $answers = [];
        }

        $answersByQuestionUuid = [];
        foreach ($answers as $answer) {
            $answersByQuestionUuid[$answer->questionUuid] = $answer->toFormValue();
        }

        return view('taskquestionnaire', [
            'task' => (array)$task,
            'questions' => $questionnaire->questions,
            'answers' => $answersByQuestionUuid
        ]);
    }

    public function saveTaskQuestionnaire(Request $request): Response
    {
        $validatedData = $request->validate([
            'taskUuid' => 'required|uuid'
            ]);

        $taskUuid = $request->input('taskUuid');
        $task = $this->taskService->getTask($taskUuid);
        if ($task === null || !$this->taskService->canAccess($task)) {
            return response('access denied', 403);
        }

        // Validate questionnaire answers

        // Persist questionnaire answers

        // Close Task for further editing by index

         return redirect()->route('case-view', [$task->caseUuid]);
    }
}
