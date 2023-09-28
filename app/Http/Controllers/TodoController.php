<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;


class TodoController extends Controller
{
    /**
     * @var Todo
     */
    public $model;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->model = new Todo();
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
                ['pid', 0],
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
        $pid = $id;

        if (null !== $pid) {
            $task = $this->model->getTaskById($pid);

            if (false === $this->checkAccess($task->uid, false)) {
                return view('403');
            }
        }

        return view('task.form', compact('pid'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function showTask($id)
    {
        $canComplete = true;

        $task       = Todo::with(['user', 'subtask'])->find($id);
        $subtasks   = $task->subtask;
        $allSubtask = $task->childs;

        if (count($allSubtask) > 0) {
            $subtaskIds  = $this->model->allSubtask($allSubtask);
            $canComplete = $this->model->canComplete($subtaskIds);
        }

        return view('task.show', compact('task', 'subtasks', 'canComplete'));
    }

    /**
     * @param null $id
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function createTask(Request $request)
    {
        $data        = $request->all();
        $data['uid'] = Auth::user()->id;
        $result      = Todo::create($data);

        return response(['status' => true, 'id' => $result->id], 200);
    }

    public function editTask($id, Request $request)
    {
        $task = $this->model->getTaskById($id ?? $request->get('id'));

        if (false === $this->checkAccess($task->uid, false)) {
            return view('403');
        }

        if ('GET' == $request->method()) {
            return view('task.form', compact('task'));
        }

        if ('POST' == $request->method()) {
            $data   = $request->all();
            $result = $task->update($data);

            return response(['status' => $result]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function completeTask(Request $request)
    {
        $canComplete = true;
        $task        = $this->model->getTaskById($request->get('id'));
        $subtask     = $task->childs;

        if (count($subtask) > 0) {
            $subtaskIds  = $this->model->allSubtask($subtask);
            $canComplete = $this->model->canComplete($subtaskIds);
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function deleteTask(Request $request)
    {
        $canDelete = true;
        $task      = $this->model->getTaskById($request->get('id'));
        $subtask   = $task->childs;

        if (count($subtask) > 0) {
            $subtaskIds = $this->model->allSubtask($subtask);
            $canDelete  = $this->model->canDelete($subtaskIds);
        }

        if (false === $canDelete) {
            return response([
                'status'  => false,
                'message' => "You can not delete this task. \nThis task have completed subtask."
            ]);
        }

        $result = $task->update(['deleted'=>1]);

        return response(['status' => $result]);
    }

    /**
     * @param $uid
     * @param bool $ajax
     * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    protected function checkAccess($uid, $ajax=true)
    {
        if ($uid !== Auth::user()->id) {
            return $ajax === true ? response('You can\'t do this action with this task', 403) : false;
        }

        return true;
    }

}
