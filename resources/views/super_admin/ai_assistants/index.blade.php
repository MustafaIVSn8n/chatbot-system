@extends('layouts.base')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>AI Assistants</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAssistantModal">
            <i class="bi bi-robot"></i> Create New Assistant
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Assistant ID</th>
                            <th>Model</th>
                            <th>Created</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assistantsTableBody">
                        <!-- Assistant data will be loaded here via AJAX -->
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading assistants...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Assistant Modal -->
<div class="modal fade" id="createAssistantModal" tabindex="-1" aria-labelledby="createAssistantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAssistantModalLabel">Create New AI Assistant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createAssistantForm">
                    <div class="mb-3">
                        <label for="apiKey" class="form-label">OpenAI API Key</label>
                        <input type="text" class="form-control" id="apiKey" name="api_key">
                        <div class="form-text">Your OpenAI API key will be used to create and manage assistants.</div>
                    </div>

                    <div class="mb-3">
                        <label for="assistantId" class="form-label">Existing Assistant ID (Optional)</label>
                        <input type="text" class="form-control" id="assistantId" name="assistant_id">
                        <div class="form-text">If you have already created an assistant in OpenAI, enter the Assistant ID here.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assistantName" class="form-label">Assistant Name</label>
                        <input type="text" class="form-control" id="assistantName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assistantModel" class="form-label">Model</label>
                        <select class="form-select" id="assistantModel" name="model" required>
                            <option value="gpt-4o">GPT-4o</option>
                            <option value="gpt-4-turbo">GPT-4 Turbo</option>
                            <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assistantInstructions" class="form-label">Instructions</label>
                        <textarea class="form-control" id="assistantInstructions" name="instructions" rows="5" required>You are a helpful customer support assistant for our website. Answer questions about our products and services in a friendly, professional manner. If you don't know the answer to a question, politely say so and offer to connect the customer with a human agent.
                        
                        When a user first connects, respond with a list of options:
                        
                        Curriculum details
                        Fees information
                        Registration process
                        Class schedules
                        
                        When responding to a user's message, always include the user's message in your response.
                        </textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assistantDescription" class="form-label">Description (Optional)</label>
                        <input type="text" class="form-control" id="assistantDescription" name="description">
                    </div>
                    <input type="hidden" name="has_assistant_id" id="hasAssistantId" value="0">
                </form>
                
                <div id="createAssistantStatus" class="alert alert-info d-none">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div>Creating assistant... This may take a moment.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createAssistantBtn">Create Assistant</button>
            </div>
        </div>
    </div>
</div>

<!-- View Assistant Modal -->
<div class="modal fade" id="viewAssistantModal" tabindex="-1" aria-labelledby="viewAssistantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAssistantModalLabel">Assistant Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Assistant ID</label>
                    <input type="text" class="form-control" id="viewAssistantId" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Name</label>
                    <input type="text" class="form-control" id="viewAssistantName" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Model</label>
                    <input type="text" class="form-control" id="viewAssistantModel" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <input type="text" class="form-control" id="viewAssistantDescription" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Instructions</label>
                    <textarea class="form-control" id="viewAssistantInstructions" rows="5" readonly></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Created At</label>
                    <input type="text" class="form-control" id="viewAssistantCreatedAt" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="copyAssistantIdBtn">Copy Assistant ID</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load assistants when page loads
        loadAssistants();
        
        // Create assistant button click handler
        document.getElementById('createAssistantBtn').addEventListener('click', createAssistant);
        
        // Copy assistant ID button click handler
        document.getElementById('copyAssistantIdBtn').addEventListener('click', function() {
            const assistantId = document.getElementById('viewAssistantId').value;
            navigator.clipboard.writeText(assistantId).then(function() {
                const button = document.getElementById('copyAssistantIdBtn');
                const originalText = button.textContent;
                
                button.textContent = 'Copied!';
                button.classList.remove('btn-primary');
                button.classList.add('btn-success');
                
                setTimeout(function() {
                    button.textContent = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                }, 2000);
            });
        });
    });
    
    // Function to load assistants
    function loadAssistants() {
        const tableBody = document.getElementById('assistantsTableBody');
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading assistants...</p>
                </td>
            </tr>
        `;
        
        // Get API key from local storage or prompt user
        const apiKey = localStorage.getItem('openai_api_key');
        if (!apiKey) {
            const newApiKey = prompt('Please enter your OpenAI API key to fetch assistants:');
            if (newApiKey) {
                localStorage.setItem('openai_api_key', newApiKey);
                fetchAssistants(newApiKey);
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <p>API key is required to fetch assistants.</p>
                        </td>
                    </tr>
                `;
            }
        } else {
            fetchAssistants(apiKey);
        }
    }
    
    // Function to fetch assistants from the API
    function fetchAssistants(apiKey) {
        fetch('{{ route('super_admin.ai_assistants.list') }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ api_key: apiKey })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderAssistants(data.assistants);
            } else {
                // If API key is invalid, clear it from local storage
                if (data.message.includes('API key')) {
                    localStorage.removeItem('openai_api_key');
                }
                
                const tableBody = document.getElementById('assistantsTableBody');
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <p class="text-danger">Error: ${data.message}</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching assistants:', error);
            const tableBody = document.getElementById('assistantsTableBody');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <p class="text-danger">Error fetching assistants. Please try again.</p>
                    </td>
                </tr>
            `;
        });
    }
    
    // Function to render assistants in the table
    function renderAssistants(assistants) {
        const tableBody = document.getElementById('assistantsTableBody');
        
        if (assistants.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <p>No assistants found. Create your first assistant to get started.</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        let html = '';
        assistants.forEach(assistant => {
            const createdDate = new Date(assistant.created_at).toLocaleDateString();
            
            html += `
                <tr>
                    <td>${assistant.name}</td>
                    <td><code>${assistant.id}</code></td>
                    <td>${assistant.model}</td>
                    <td>${createdDate}</td>
                    <td><span class="badge bg-success">${assistant.status || 'active'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary view-assistant" data-assistant='${JSON.stringify(assistant)}'>
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-assistant" data-assistant-id="${assistant.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = html;
        
        // Add event listeners to view buttons
        document.querySelectorAll('.view-assistant').forEach(button => {
            button.addEventListener('click', function() {
                const assistant = JSON.parse(this.getAttribute('data-assistant'));
                showAssistantDetails(assistant);
            });
        });
        
        // Add event listeners to delete buttons
        document.querySelectorAll('.delete-assistant').forEach(button => {
            button.addEventListener('click', function() {
                const assistantId = this.getAttribute('data-assistant-id');
                if (confirm('Are you sure you want to delete this assistant? This action cannot be undone.')) {
                    deleteAssistant(assistantId);
                }
            });
        });
    }
    
    // Function to show assistant details in modal
    function showAssistantDetails(assistant) {
        document.getElementById('viewAssistantId').value = assistant.id;
        document.getElementById('viewAssistantName').value = assistant.name;
        document.getElementById('viewAssistantModel').value = assistant.model;
        document.getElementById('viewAssistantDescription').value = assistant.description || 'No description';
        document.getElementById('viewAssistantInstructions').value = assistant.instructions;
        document.getElementById('viewAssistantCreatedAt').value = new Date(assistant.created_at).toLocaleString();
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('viewAssistantModal'));
        modal.show();
    }
    
    // Function to create a new assistant
    function createAssistant() {
        // Get form data
        const form = document.getElementById('createAssistantForm');
        const formData = new FormData(form);
        const data = {};
        let hasAssistantId = false;
        formData.forEach((value, key) => {
            data[key] = value;
            if (key === 'assistant_id' && value) {
                hasAssistantId = true;
            }
        });
        document.getElementById('hasAssistantId').value = hasAssistantId ? 1 : 0;
        data['has_assistant_id'] = hasAssistantId ? 1 : 0;
        // If API key is not provided, try to get it from local storage
        if (!data.api_key) {
            const storedApiKey = localStorage.getItem('openai_api_key');
            if (storedApiKey) {
                data.api_key = storedApiKey;
            } else {
                alert('API key is required to create an assistant.');
                return;
            }
        } else {
            // Save API key to local storage for future use
            localStorage.setItem('openai_api_key', data.api_key);
        }
        
        // Show loading status
        const statusDiv = document.getElementById('createAssistantStatus');
        statusDiv.classList.remove('d-none');
        
        // Disable create button
        const createBtn = document.getElementById('createAssistantBtn');
        createBtn.disabled = true;
        
        // Send request to create assistant
        fetch('{{ route('super_admin.ai_assistants.create') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading status
            statusDiv.classList.add('d-none');
            
            // Enable create button
            createBtn.disabled = false;
            
            if (data.success) {
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('createAssistantModal'));
                modal.hide();
                
                // Show success message
                alert('Assistant created successfully!');
                
                // Reload assistants
                loadAssistants();
                
                // Reset form
                form.reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error creating assistant:', error);
            
            // Hide loading status
            statusDiv.classList.add('d-none');
            
            // Enable create button
            createBtn.disabled = false;
            
            alert('Error creating assistant. Please try again.');
        });
    }
    
    // Function to delete an assistant
    function deleteAssistant(assistantId) {
        const apiKey = localStorage.getItem('openai_api_key');
        if (!apiKey) {
            alert('API key is required to delete an assistant.');
            return;
        }

        fetch(`{{ route('super_admin.ai_assistants.delete', ['assistantId' => 'ASSISTANT_ID_PLACEHOLDER']) }}`.replace('ASSISTANT_ID_PLACEHOLDER', assistantId), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ api_key: apiKey })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assistant deleted successfully!');
                loadAssistants();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error deleting assistant:', error);
            alert('Error deleting assistant. Please try again.');
        });
    }
</script>
@endpush
@endsection
