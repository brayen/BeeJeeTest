@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ isset($task) ? 'Update' : 'Create'}} task</div>

                    <div class="card-body">
                        <form id="task-form" task-id="{{ isset($task) ? $task->id:'' }}" task-parent_id="{{ isset($parent_id) ? $parent_id:'' }}">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control task-data" id="title" placeholder="Enter task title" value="{{ isset($task) ? $task->title:''}}" required>
                            </div>

                            <div class="form-group mt-3">
                                <label for="description">Description</label>
                                <textarea class="form-control task-data" id="description" rows="3" required>{{ isset($task) ? $task->description : ''}}</textarea>
                            </div>

                            <div class="d-inline-flex mt-3">
                                <div class="me-1 mt-2">
                                    <label for="priority">Task Priority</label>
                                </div>
                                <div>
                                    <select id="priority" class="form-control task-data form-select" priority="{{ isset($task) ? $task->priority : 0}}">
                                        @foreach($priority::getAllItems() as $k => $v)
                                            <option value="{{ $k }}">{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr class="hr">

                            <div class="form-group" style="text-align: right">
                                <div class="btn btn-success {{ isset($task) ? 'update' : 'create'}}">{{ isset($task) ? 'Update' : 'Create'}}</div>
                                <div class="btn btn-warning cancel">Cancel</div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
