@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Widget Settings for {{ $website->name }}</h1>
        <a href="{{ route('admin.websites.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Websites
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Welcome Message</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/websites/{{ $website->id }}/widget/welcome" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="welcome_message" class="form-label">Welcome Message</label>
                            <textarea 
                                name="welcome_message" 
                                id="welcome_message" 
                                class="form-control @error('welcome_message') is-invalid @enderror" 
                                rows="5" 
                                placeholder="Enter the welcome message that will be shown to users when they open the chat widget"
                            >{{ old('welcome_message', $website->welcome_message) }}</textarea>
                            @error('welcome_message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                This message will be displayed to users when they first open the chat widget.
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Welcome Message</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Widget Buttons</h5>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addButtonModal">
                        <i class="bi bi-plus-lg"></i> Add Button
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Buttons will appear below the welcome message in the chat widget. Users can click these buttons to quickly send predefined messages or visit links.
                    </p>
                    
                    @if($buttons->isEmpty())
                        <div class="alert alert-info">
                            No buttons have been added yet. Click "Add Button" to create your first button.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover" id="buttonsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Button Text</th>
                                        <th>Action Type</th>
                                        <th>Action Value</th>
                                        <th>Status</th>
                                        <th style="width: 150px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="buttonsList">
                                    @foreach($buttons as $index => $button)
                                        <tr data-id="{{ $button->id }}">
                                            <td>
                                                <i class="bi bi-grip-vertical handle" style="cursor: move;"></i>
                                                {{ $index + 1 }}
                                            </td>
                                            <td>{{ $button->text }}</td>
                                            <td>{{ ucfirst($button->action_type) }}</td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $button->action_value }}">
                                                    {{ $button->action_value }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($button->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-primary edit-button" 
                                                    data-id="{{ $button->id }}"
                                                    data-text="{{ $button->text }}"
                                                    data-action-type="{{ $button->action_type }}"
                                                    data-action-value="{{ $button->action_value }}"
                                                    data-is-active="{{ $button->is_active }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editButtonModal"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-button" 
                                                    data-id="{{ $button->id }}"
                                                    data-text="{{ $button->text }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteButtonModal"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Button Modal -->
<div class="modal fade" id="addButtonModal" tabindex="-1" aria-labelledby="addButtonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/websites/{{ $website->id }}/widget/buttons" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addButtonModalLabel">Add New Button</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="text" class="form-label">Button Text</label>
                        <input type="text" class="form-control" id="text" name="text" required maxlength="50" placeholder="e.g., Schedule a Demo">
                        <div class="form-text">The text that will be displayed on the button (max 50 characters).</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="action_type" class="form-label">Action Type</label>
                        <select class="form-select" id="action_type" name="action_type" required>
                            <option value="message">Send Message</option>
                            <option value="link">Open Link</option>
                        </select>
                        <div class="form-text">What happens when the user clicks this button.</div>
                    </div>
                    
                    <div class="mb-3" id="messageValueField">
                        <label for="action_value_message" class="form-label">Message Text</label>
                        <textarea class="form-control" id="action_value_message" name="action_value" rows="3" placeholder="e.g., I'd like to schedule a demo"></textarea>
                        <div class="form-text">The message that will be sent when the user clicks this button.</div>
                    </div>
                    
                    <div class="mb-3" id="linkValueField" style="display: none;">
                        <label for="action_value_link" class="form-label">URL</label>
                        <input type="url" class="form-control" id="action_value_link" placeholder="e.g., https://example.com/schedule">
                        <div class="form-text">The URL that will open when the user clicks this button.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Button</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Button Modal -->
<div class="modal fade" id="editButtonModal" tabindex="-1" aria-labelledby="editButtonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editButtonForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editButtonModalLabel">Edit Button</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_text" class="form-label">Button Text</label>
                        <input type="text" class="form-control" id="edit_text" name="text" required maxlength="50">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_action_type" class="form-label">Action Type</label>
                        <select class="form-select" id="edit_action_type" name="action_type" required>
                            <option value="message">Send Message</option>
                            <option value="link">Open Link</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="edit_messageValueField">
                        <label for="edit_action_value_message" class="form-label">Message Text</label>
                        <textarea class="form-control" id="edit_action_value_message" name="action_value" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3" id="edit_linkValueField" style="display: none;">
                        <label for="edit_action_value_link" class="form-label">URL</label>
                        <input type="url" class="form-control" id="edit_action_value_link">
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                        <label class="form-check-label" for="edit_is_active">
                            Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Button Modal -->
<div class="modal fade" id="deleteButtonModal" tabindex="-1" aria-labelledby="deleteButtonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteButtonForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteButtonModalLabel">Delete Button</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the button "<span id="delete_button_text"></span>"? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle action type change in Add Button modal
        const actionTypeSelect = document.getElementById('action_type');
        const messageValueField = document.getElementById('messageValueField');
        const linkValueField = document.getElementById('linkValueField');
        const actionValueMessage = document.getElementById('action_value_message');
        const actionValueLink = document.getElementById('action_value_link');

        actionTypeSelect.addEventListener('change', function() {
            if (this.value === 'message') {
                messageValueField.style.display = 'block';
                linkValueField.style.display = 'none';
                actionValueMessage.name = 'action_value';
                actionValueLink.name = '';
            } else {
                messageValueField.style.display = 'none';
                linkValueField.style.display = 'block';
                actionValueMessage.name = '';
                actionValueLink.name = 'action_value';
            }
        });

        // Handle action type change in Edit Button modal
        const editActionTypeSelect = document.getElementById('edit_action_type');
        const editMessageValueField = document.getElementById('edit_messageValueField');
        const editLinkValueField = document.getElementById('edit_linkValueField');
        const editActionValueMessage = document.getElementById('edit_action_value_message');
        const editActionValueLink = document.getElementById('edit_action_value_link');

        editActionTypeSelect.addEventListener('change', function() {
            if (this.value === 'message') {
                editMessageValueField.style.display = 'block';
                editLinkValueField.style.display = 'none';
                editActionValueMessage.name = 'action_value';
                editActionValueLink.name = '';
            } else {
                editMessageValueField.style.display = 'none';
                editLinkValueField.style.display = 'block';
                editActionValueMessage.name = '';
                editActionValueLink.name = 'action_value';
            }
        });

        // Handle Edit Button modal
        const editButtons = document.querySelectorAll('.edit-button');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const text = this.dataset.text;
                const actionType = this.dataset.actionType;
                const actionValue = this.dataset.actionValue;
                const isActive = this.dataset.isActive === '1';

                document.getElementById('edit_text').value = text;
                document.getElementById('edit_action_type').value = actionType;
                document.getElementById('edit_is_active').checked = isActive;

                if (actionType === 'message') {
                    editMessageValueField.style.display = 'block';
                    editLinkValueField.style.display = 'none';
                    editActionValueMessage.name = 'action_value';
                    editActionValueLink.name = '';
                    editActionValueMessage.value = actionValue;
                } else {
                    editMessageValueField.style.display = 'none';
                    editLinkValueField.style.display = 'block';
                    editActionValueMessage.name = '';
                    editActionValueLink.name = 'action_value';
                    editActionValueLink.value = actionValue;
                }

                document.getElementById('editButtonForm').action = `/admin/websites/${{{ $website->id }}}/widget/buttons/${id}`;
            });
        });

        // Handle Delete Button modal
        const deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const text = this.dataset.text;

                document.getElementById('delete_button_text').textContent = text;
                document.getElementById('deleteButtonForm').action = `/admin/websites/${{{ $website->id }}}/widget/buttons/${id}`;
            });
        });

        // Initialize Sortable for button reordering
        const buttonsList = document.getElementById('buttonsList');
        if (buttonsList) {
            new Sortable(buttonsList, {
                handle: '.handle',
                animation: 150,
                onEnd: function() {
                    // Get the new order of buttons
                    const buttons = Array.from(buttonsList.querySelectorAll('tr')).map(row => row.dataset.id);
                    
                    // Send the new order to the server
                    fetch(`/admin/websites/${{{ $website->id }}}/widget/buttons/reorder`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ buttons })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the row numbers
                            buttonsList.querySelectorAll('tr').forEach((row, index) => {
                                row.querySelector('td:first-child').textContent = index + 1;
                            });
                        }
                    });
                }
            });
        }
    });
</script>
@endsection
