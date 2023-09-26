@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ isset($task) ? 'Update' : 'Create'}} task</div>

                    <div class="card-body">
                        <form id="task-form" task-id="{{ isset($task) ? $task->id:'' }}" task-pid="{{ isset($pid) ? $pid:'' }}">
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
                                    <select class="form-control task-data" style="width: 50px" id="priority" priority="{{ isset($task) ? $task->priority : 0}}">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
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
