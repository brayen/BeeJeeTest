<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * @var int
     */
    private $user_id;

    /**
     * @var array
     */
    private $credentials;

    /**
     * @var array
     */
    private $validation;

    /**
     * @var
     */
    protected $noId;

    /**
     * @var Todo
     */
    protected $model;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->model       = new Todo();
        $this->request     = $request;
        $this->credentials = [
            'email'    => $this->request->header('email'),
            'password' => $this->request->header('password')
        ];
    }

    public function create()
    {
        if (true !== $this->access()) return $this->validation;

        if (null !== $parent_id =$this->request->get('parent_id')) {

            $parent = $this->getTaskById($parent_id);

            if (!$parent) return response(['status' => 'error', 'message' => 'You can\'t create subtask with this parentID']);
        }

        $validator = Validator::make($this->request->all(), [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required'],
            'priority'      => ['required', 'integer']
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->messages()], 401);
        }

        $data = $this->request->all();
        $data['user_id'] = $this->user_id;

        return response(['status' => (bool)($this->model)::create($data)]);
    }

    public function read()
    {
        if (true !== $this->access()) return $this->validation;

        // Init query with filters
        $q = ($this->model)::select([
            'id',
            'title',
            'description',
            'status',
            'priority',
            'createdAt',
            'completedAt'
        ])
            ->where('user_id', $this->user_id);

        // Filters
        if ($this->request->has('status')) {
            $q->status($this->request->get('status'));
        }

        if ($this->request->has('priority')) {
            $q->priority($this->request->get('priority'));
        }

        // Ordering options
        if ($this->request->has('order') && null !== $order = $this->request->get('order')) {
            $sort = $this->request->get('sort') ?? 'ASC';
            $q->order([
                'order' => $order,
                'sort'  => $sort
            ]);
        }

        // Search in titles
        if ($this->request->has('title')) {
            $q->title($this->request->get('title'));
        }

        return response(['status' => 'ok', 'list' => $q->get()]);
    }

    public function update()
    {
        if (true !== $this->access()) return $this->validation;

        $id = $this->request->get('id');

        if (null !== $this->hasId($id)) return $this->noId;

        $task = $this->getTaskById($id);

        if (!$task) return response(['status' => 'error', 'message' => 'You don\'t have task with this ID']);

        return response(['status' => $task->update($this->request->all())]);

    }

    public function complete()
    {
        if (true !== $this->access()) return $this->validation;

        $id = $this->request->get('id');

        if (null !== $this->hasId($id)) return $this->noId;

        $canComplete = true;
        $task = $this->getTaskById($id);

        if (!$task) return response(['status' => 'error', 'message' => 'You don\'t have task with this ID']);

        $subtask = $task->childs;

        if (count($subtask) > 0) {
            $subtaskIds = $this->model->allSubtask($subtask);
            $canComplete = $this->model->canComplete($subtaskIds);
        }

        if (false === $canComplete) {
            return response([
                'status'  => 'error',
                'message' => 'You can not complete this task. This task have uncompleted subtask.'
            ]);
        }

        $result = $task->update([
            'status'=>1,
            'completedAt'=>Carbon::now()->format('Y-m-d H:i:s')
        ]);

        return response(['result' => $result]);
    }

    public function delete()
    {
        if (true !== $this->access()) return $this->validation;

        $id = $this->request->get('id');

        if (null !== $this->hasId($id)) return $this->noId;

        $canDelete = true;
        $task = $this->getTaskById($id);

        if (!$task) return response(['status' => 'error', 'message' => 'You don\'t have task with this ID']);

        $subtask = $task->childs ?? [];

        if (count($subtask) > 0) {
            $subtaskIds = $this->model->allSubtask($subtask);
            $canDelete  = $this->model->canDelete($subtaskIds);
        }

        if (false === $canDelete) {
            return response([
                'status'  => false,
                'message' => "You can not delete this task. This task have completed subtask."
            ]);
        }

        return response(['status' => $task->update(['deleted'=>1])]);
    }

    protected function access()
    {
        $validator = Validator::make($this->credentials, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->validation = response(['error' => $validator->errors()], 401);
        }

        $user = User::where('email', $this->credentials['email'])->first();

        if (Hash::check($this->credentials['password'], $user->password)) {
            $this->user_id = $user->id;

            return true;
        }

        return false;
    }

    protected function hasId($id)
    {
        if (null === $id) {
            $this->noId = response(['error' => 'ID can not be empty'], 401);
        }
    }

    protected function getTaskById($id)
    {
        return ($this->model)::where([
                    ['user_id', $this->user_id],
                    ['id',  $id]
                ])->first();
    }
}
