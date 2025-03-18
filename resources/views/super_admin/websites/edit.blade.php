@extends('layouts.super_admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Edit Website</h1>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <form action="{{ route('super_admin.websites.update', $website->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Website Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $website->name }}" placeholder="Enter website name" required>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label fw-bold">Website URL</label>
                            <input type="url" class="form-control" id="url" name="url" value="{{ $website->url }}" placeholder="https://example.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="api_key" class="form-label fw-bold">OpenAI API Key</label>
                            <input type="text" class="form-control" id="api_key" name="api_key" value="{{ $website->api_key }}" placeholder="Enter OpenAI API Key" required>
                        </div>
                        <div class="mb-3">
                            <label for="assistant_id" class="form-label fw-bold">Assistant ID</label>
                            <input type="text" class="form-control" id="assistant_id" name="assistant_id" value="{{ $website->assistant_id }}" placeholder="Enter Assistant ID" required>
                        </div>
                        <div class="mb-3">
                            <label for="model_name" class="form-label fw-bold">Model Name</label>
                            <input type="text" class="form-control" id="model_name" name="model_name" value="{{ $website->model_name }}" placeholder="e.g. gpt-3.5-turbo" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter a brief description">{{ $website->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="widget_color" class="form-label fw-bold">Widget Color</label>
                            <input type="color" class="form-control form-control-color" id="widget_color" name="widget_color" value="{{ $website->widget_color }}" title="Choose your color">
                        </div>
                        <div class="mb-3">
                            <label for="widget_position" class="form-label fw-bold">Widget Position</label>
                            <select class="form-select" id="widget_position" name="widget_position" required>
                                <option value="bottom-right" @if($website->widget_position == 'bottom-right') selected @endif>Bottom Right</option>
                                <option value="bottom-left" @if($website->widget_position == 'bottom-left') selected @endif>Bottom Left</option>
                                <option value="top-right" @if($website->widget_position == 'top-right') selected @endif>Top Right</option>
                                <option value="top-left" @if($website->widget_position == 'top-left') selected @endif>Top Left</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="website_type" class="form-label fw-bold">Website Type</label>
                            <select class="form-select" id="website_type" name="website_type" required>
                                <option value="iqra_virtual_school" @if($website->website_type == 'iqra_virtual_school') selected @endif>Iqra Virtual School</option>
                                <option value="quran_home_tutor" @if($website->website_type == 'quran_home_tutor') selected @endif>Quran Home Tutor</option>
                                <option value="tuition_services" @if($website->website_type == 'tuition_services') selected @endif>Tuition Services</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Website</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection