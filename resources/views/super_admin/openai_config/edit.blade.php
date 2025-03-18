@extends('layouts.super_admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Edit OpenAI Configuration</h1>
    <form action="{{ route('super_admin.openai_config.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="api_key" class="form-label">OpenAI API Key</label>
            <input type="text" name="api_key" id="api_key" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="assistant_id" class="form-label">Assistant ID</label>
            <input type="text" name="assistant_id" id="assistant_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="model_name" class="form-label">Model Name</label>
            <input type="text" name="model_name" id="model_name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Configuration</button>
    </form>
</div>
@endsection