@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header h5">API Credentials:</div>

                    <div class="card-body text-center row p-4 inline">
                        <div class="col-md-4 border" style="border-right: 0px !important; background-color: #EFEFEF">API Key</div>
                        <div class="col-md-8 border" id="api-key">{{ $data->key }}</div>
                    </div>
                </div>
                <div class="mt-1" style="text-align: right">
                    <button class="btn btn-success" id="generate">Regenerate Key</button>
                    <button class="btn btn-primary" id="copy">Copy Key to Clipboard</button>
                </div>
            </div>
        </div>
    </div>
    <div class="clipboard-msg">
        Key copied to clipboard
    </div>
@endsection
