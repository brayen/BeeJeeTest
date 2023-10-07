<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\Todo;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;


class TodoController extends Controller
{
    protected static ApiKey $apiKey;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        self::$apiKey = new ApiKey();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $tasks = Todo::with('user')
            ->where([
                ['parent_id', 0],
                ['deleted', 0]
            ])
            ->orderBy('id', 'desc')
            ->paginate(2);

        return view('task.list', compact('tasks'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function showTaskForm($id=null)
    {

        $parent_id = $id;

        if (null !== $parent_id) {
            $task = $this->getTaskById($parent_id);

            if (false === $this->checkAccess($task->user_id, false)) {
                return view('403');
            }
        }

        return view('task.form', compact('parent_id'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function showTask($id): \Illuminate\Foundation\Application|View|Factory|Application
    {
        $canComplete = true;

        $task = Todo::with(['user', 'subtask'])->find($id);
        $subtasks = $task->subtask;

        $allSubtask = $task->childs;

        if (count($allSubtask) > 0) {
            $subtaskIds = $this->allSubtask($allSubtask);
            $canComplete = $this->canComplete($subtaskIds);
        }

        return view('task.show', compact('task', 'subtasks', 'canComplete'));
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function createTask(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|Application|ResponseFactory
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;

        $result = Todo::create($data);

        return response(['status' => true, 'id' => $result->id], 200);
    }

    /**
     * @param $id
     * @param Request $request
     * @return false|Application|ResponseFactory|Factory|View|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function editTask($id, Request $request): Factory|View|\Illuminate\Foundation\Application|\Illuminate\Http\Response|bool|Application|ResponseFactory
    {
        $task = $this->getTaskById($id ?? $request->get('id'));

        if (false === $this->checkAccess($task->user_id, false)) {
            return view('403');
        }

        if ('GET' == $request->method()) {
            return view('task.form', compact('task'));
        }

        if ('POST' == $request->method()) {
            $data = $request->all();

            $result = $task->update($data);

            return response(['status' => $result]);
        }

        return false;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function completeTask(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|Application|ResponseFactory
    {
        $canComplete = true;
        $task = $this->getTaskById($request->get('id'));

        $subtask = $task->childs;

        if (count($subtask) > 0) {
            $subtaskIds = $this->allSubtask($subtask);
            $canComplete = $this->canComplete($subtaskIds);
        }

        if (false === $canComplete) {
            return response([
                'status'=>false,
                'message' => "You can not complete this task. \nThis task have uncompleted subtask."
            ]);
        }

        $result = $task->update([
            'status'=>1,
            'completedAt'=>Carbon::now()->format('Y-m-d H:i:s')
        ]);

        return response(['status' => $result]);
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function deleteTask(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|Application|ResponseFactory
    {
        $canDelete = true;
        $task = $this->getTaskById($request->get('id'));

        $subtask = $task->childs;

        if (count($subtask) > 0) {
            $subtaskIds = $this->allSubtask($subtask);
            $canDelete = $this->canDelete($subtaskIds);
        }

        if (false === $canDelete) {
            return response([
                'status'=>false,
                'message' => "You can not delete this task. \nThis task have completed subtask."
            ]);
        }

        $result = $task->update(['deleted'=>1]);

        return response(['status' => $result]);
    }

    public function apiKey()
    {
        $data = self::$apiKey::getApiKey();

        return view('api.key', compact('data'));
    }

    protected function generateApiKey()
    {
        return response()->json(['api_key' => self::$apiKey::createApiKey()]);
    }

    /**
     * @param $tasks
     * @param int $level
     * @param array $subtaskIds
     * @return array
     */
    protected function allSubtask($tasks, $level = 0, &$subtaskIds = []): array
    {
        foreach ($tasks as $task) {
            $subtaskIds[] = $task->id;

            if ($task->childs->isNotEmpty()) {
                $this->allSubtask($task->childs, $level + 1, $subtaskIds);
            }
        }

        return collect(Arr::sort($subtaskIds))->values()->toArray();
    }

    /**
     * @param $allSubtasks
     * @return bool
     */
    protected function canComplete($allSubtasks): bool
    {
        $subtasks = Todo::whereIn('id', $allSubtasks)->get();

        return collect($subtasks)->pluck('status')->min() == 1;
    }

    /**
     * @param $allSubtasks
     * @return bool
     */
    protected function canDelete($allSubtasks): bool
    {
        $subtasks = Todo::whereIn('id', $allSubtasks)->get();

        return collect($subtasks)->pluck('status')->max() == 0;
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function getTaskById($id): mixed
    {
        $task = Todo::find($id);

        $this->checkAccess($task->user_id);

        return $task;
    }

    /**
     * @param $user_id
     * @param $ajax
     * @return bool|Application|ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    protected function checkAccess($user_id, $ajax=true): \Illuminate\Foundation\Application|\Illuminate\Http\Response|bool|Application|ResponseFactory
    {
        if ($user_id !== Auth::user()->id) {
            return $ajax === true ? response('You can\'t do this action with this task', 403) : false;
        }

        return true;
    }

}
