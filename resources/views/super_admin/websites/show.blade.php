@extends('layouts.base')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Website Details: {{ $website->name }}</h1>
        <div>
            <a href="{{ route('super_admin.websites.edit', $website->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            <a href="{{ route('super_admin.websites.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Website Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Name:</div>
                        <div class="col-md-8">{{ $website->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">URL:</div>
                        <div class="col-md-8">
                            <a href="{{ $website->url }}" target="_blank">{{ $website->url }}</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">
                            @if($website->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Description:</div>
                        <div class="col-md-8">{{ $website->description ?? 'No description provided' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Website Type:</div>
                        <div class="col-md-8">{{ ucwords(str_replace('_', ' ', $website->website_type)) }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">AI Assistant Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">API Key:</div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="password" class="form-control" id="apiKey" value="{{ $website->api_key }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="toggleApiKey">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Assistant ID:</div>
                        <div class="col-md-8">{{ $website->assistant_id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Model Name:</div>
                        <div class="col-md-8">{{ $website->model_name }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Widget Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Widget Color:</label>
                        <div class="d-flex align-items-center">
                            <div class="color-preview me-2" style="width: 30px; height: 30px; border-radius: 5px; background-color: {{ $website->widget_color }}"></div>
                            <span>{{ $website->widget_color }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Widget Position:</label>
                        <span>{{ ucwords(str_replace('-', ' ', $website->widget_position)) }}</span>
                    </div>
                </div>
            </div>

            <div class="card shadow border-0">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Widget Embed Code</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Copy and paste this code snippet into your website to add the chat widget:</p>
                    <div class="position-relative">
                        <pre class="bg-light p-3 rounded" id="embedCode"><code>&lt;script src="{{ route('widget.script', ['websiteId' => $website->id]) }}" defer&gt;&lt;/script&gt;</code></pre>
                        <button class="btn btn-sm btn-primary position-absolute top-0 end-0 m-2" id="copyEmbedCode">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Widget Preview</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> This is a preview of how your chat widget will appear on your website.
                    </div>
                    <div class="border rounded p-4 position-relative" style="height: 400px; overflow: hidden;">
                        <!-- Widget Preview -->
                        <div id="chat-bubble-preview" style="position: absolute; bottom: 20px; right: 20px; z-index: 1000; display: flex; align-items: center;">
                            <span class="bubble-text" style="margin-right: 10px; font-size: 1.2rem; color: {{ $website->widget_color }};">How can I help?</span>
                            <button class="wave-btn" style="width: 65px; height: 65px; border-radius: 50%; border: none; background: {{ $website->widget_color }}; color: #fff; font-size: 1.4rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                                <span class="wave-icon">👋</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle API Key visibility
    document.getElementById('toggleApiKey').addEventListener('click', function() {
        const apiKeyInput = document.getElementById('apiKey');
        const icon = this.querySelector('i');
        
        if (apiKeyInput.type === 'password') {
            apiKeyInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            apiKeyInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    // Copy embed code
    document.getElementById('copyEmbedCode').addEventListener('click', function() {
        const embedCode = document.getElementById('embedCode').textContent;
        navigator.clipboard.writeText(embedCode).then(function() {
            const button = document.getElementById('copyEmbedCode');
            const originalHTML = button.innerHTML;
            
            button.innerHTML = '<i class="bi bi-check"></i> Copied!';
            button.classList.remove('btn-primary');
            button.classList.add('btn-success');
            
            setTimeout(function() {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-success');
                button.classList.add('btn-primary');
            }, 2000);
        });
    });
</script>
@endpush
@endsection