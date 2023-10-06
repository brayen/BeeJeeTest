<?php

namespace App\Models;

use App\Models\Scopes\TodoScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class Todo extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'todo';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'parent_id',
        'title',
        'description',
        'status',
        'priority',
        'deleted',
        'completedAt'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TodoScope());
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subtask()
    {
        return $this->hasMany(Todo::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->subtask()->with('childs');
    }

    /**
     * @param Builder $q
     * @param int $param
     */
    public function scopeStatus(Builder $q, int $param): void
    {
        $q->where('status', $param);
    }

    /**
     * @param Builder $q
     * @param int $param
     */
    public function scopePriority(Builder $q, int $param): void
    {
        $q->where('priority', $param);
    }

    /**
     * @param Builder $q
     * @param array $params
     */
    public function scopeOrder(Builder $q, array $params): void
    {
        $q->orderBy($params['order'], $params['sort']);
    }

    /**
     * @param Builder $q
     * @param string $param
     */
    public function scopeTitle(Builder $q, string $param): void
    {
        $q->where('title', 'like', "%{$param}%");
    }

    // Functions

    /**
     * @param $tasks
     * @param int $level
     * @param array $subtaskIds
     * @return array
     */
    public function allSubtask($tasks, $level = 0, &$subtaskIds = []): array
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
    public function canComplete($allSubtasks): bool
    {
        $subtasks = Todo::whereIn('id', $allSubtasks)->get();

        return collect($subtasks)->pluck('status')->min() == 1;
    }

    /**
     * @param $allSubtasks
     * @return bool
     */
    public function canDelete($allSubtasks): bool
    {
        $subtasks = Todo::whereIn('id', $allSubtasks)->get();

        return collect($subtasks)->pluck('status')->max() == 0;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getTaskById($id)
    {
        $task = Todo::find($id);

        $this->checkAccess($task->uid);

        return $task;
    }
}
