@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div style="text-align: right">
                    <a href="/task/list" class="btn btn-primary">Back Home</a>
                    @if($task->parent_id > 0)
                        <a href="/task/show/{{ $task->parent_id }}" class="btn btn-primary">Back to Parent</a>
                    @endif
                </div>

                <hr class="hr">

                <div class="card">

                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">{{ $task->title }}</div>
                            <div class="col-md-6" style="text-align: right">
                                Author: <span class="text-primary">{{ $task->user->name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div>{{ $task->description }}</div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6 mt-2">
                                [ Status: <span class="text-{{ $status::tryFrom($task->status)->getStatusStyle() }}">{{ ucfirst($task->status) }}</span> |
                                Priority: {{ $task->priority }} ]
                            </div>
                            <div class="col-md-6" style="text-align: right">
                                @if (\Illuminate\Support\Facades\Auth::user()->id == $task->user->id && $status::tryFrom($task->status)->name == $status::Todo)
                                <a href="/task/edit/{{ $task->id }}" class="btn btn-primary">Edit</a>
                                <a href="/task/create/{{ $task->id }}" class="btn btn-primary">Create Subtask</a>

                                    @if ($canComplete)
                                        <span class="btn btn-success" id="complete" task-id="{{ $task->id }}">Complete</span>
                                        <span class="btn btn-danger" id="delete" task-id="{{ $task->id }}">Delete</span>
                                    @endif

                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if (count($subtasks) > 0)
                <hr class="hr">

                <h4>Subtasks</h4>

                <table class="table table-bordered mb-5">
                    <thead>
                    <tr class="table-primary">
                        <th scope="col">#</th>
                        <th scope="col">Author</th>
                        <th scope="col">Title</th>
                        <th scope="col">Priority</th>
                        <th scope="col">Status</th>
                        <th scope="col">Subtask</th>
                        <th scope="col">Created</th>
                        <th scope="col">Completed</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($subtasks as $subtask)
                        <tr>
                            <th scope="row">{{ $subtask->id }}</th>
                            <td>{{ $subtask->user->name }}</td>
                            <td><a href="/task/show/{{ $subtask->id }}" class="link-underline link-underline-opacity-0">{{ $subtask->title }}</a></td>
                            <td>{{ $priority::tryFrom($subtask->priority)->name }}</td>
                            <td class=" text-{{ $status::tryFrom($subtask->status)->getStatusStyle() }}">
                                {{ $status::tryFrom($subtask->status)->name }}
                            </td>
                            <td>{{ count($subtask->subtask) }}</td>
                            <td>{{ $subtask->createdAt }}</td>
                            <td>{{ $subtask->completedAt ?? '---' }}</td>
                            <td>
                                <span><a href="/task/show/{{ $subtask->id }}"><i class="bi bi-eye-fill"></i></a></span>
                                @if (\Illuminate\Support\Facades\Auth::user()->id == $subtask->user->id && $status::tryFrom($subtask->status)->name == $status::Todo)
                                    <span><a href="/task/edit/{{ $subtask->id }}"><i class="bi bi-pencil-square mx-1"></i></a></span>
                                    <span><i role="button" id="complete" task-id="{{ $subtask->id }}" class="bi bi-calendar-check text-success mx-1"></i></span>
                                    <span><i role="button" id="delete" task-id="{{ $subtask->id }}" class="bi bi-trash text-danger"></i></span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
@endsection
