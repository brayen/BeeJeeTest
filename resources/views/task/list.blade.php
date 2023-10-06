@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 mb-3">
                <a href="/task/create" class="btn btn-success"><i class="bi bi-plus-circle"></i> Create task</a>
            </div>

            <div class="col-md-8">
                @if(!isset($tasks))
                    <div class="card">
                        <div class="card-header h5">Task actions:</div>

                        <div class="card-body text-center">
                            Task list is empty <a href="/task/create">Create</a> one!
                        </div>
                    </div>
                @else
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
                        @foreach($tasks as $task)
                            <tr>
                                <th scope="row">{{ $task->id }}</th>
                                <td>{{ $task->user->name }}</td>
                                <td><a href="/task/show/{{ $task->id }}" class="link-underline link-underline-opacity-0">{{ $task->title }}</a></td>
                                <td>{{ $priority::tryFrom($task->priority)->name }}</td>
                                <td class="text-{{ $status::tryFrom($task->status)->getStatusStyle() }}">{{ $status::tryFrom($task->status)->name }}</td>
                                <td>{{ count($task->subtask) }}</td>
                                <td>{{ $task->createdAt }}</td>
                                <td>{{ $task->completedAt ?? '---' }}</td>
                                <td>
                                    <span><a href="/task/show/{{ $task->id }}"><i class="bi bi-eye-fill"></i></a></span>
                                    @if (\Illuminate\Support\Facades\Auth::user()->id == $task->user->id && $status::tryFrom($task->status)->name == $status::Todo)
                                        <span><a href="/task/edit/{{ $task->id }}"><i class="bi bi-pencil-square mx-1"></i></a></span>
                                        <span><i role="button" id="complete" task-id="{{ $task->id }}" class="bi bi-calendar-check text-success mx-1"></i></span>
                                        <span><i role="button" id="delete" task-id="{{ $task->id }}" class="bi bi-trash text-danger"></i></span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
