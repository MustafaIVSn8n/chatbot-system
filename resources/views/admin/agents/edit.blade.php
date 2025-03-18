@extends('layouts.admin') 

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Edit Agent User</h1>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.agents.update', $agent->id) }}" method="POST" id="editAgentForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Agent Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $agent->name }}" placeholder="Enter Agent's Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Agent Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $agent->email }}" placeholder="Enter Agent's Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="showPassword">
                                <label class="form-check-label" for="showPassword">Show Password</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-bold">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="showPasswordConfirmation">
                                <label class="form-check-label" for="showPasswordConfirmation">Show Password</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assign Websites</label>
                            <div>
                                @foreach($websites as $website)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="website_ids[]" value="{{ $website->id }}" id="website{{ $website->id }}"
                                            @if(in_array($website->id, $agent->adminWebsites->pluck('id')->toArray())) checked @endif>
                                        <label class="form-check-label" for="website{{ $website->id }}">{{ $website->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Agent</button>
                            <button type="button" id="deleteButton" class="btn btn-danger">Delete Agent</button>
                        </div>
                    </form>
                    <form id="deleteForm" action="{{ route('admin.agents.destroy', $agent->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const showPasswordCheckbox = document.getElementById('showPassword');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const showPasswordConfirmationCheckbox = document.getElementById('showPasswordConfirmation');
    const editAgentForm = document.getElementById('editAgentForm');

    showPasswordCheckbox.addEventListener('change', function() {
        passwordInput.type = this.checked ? 'text' : 'password';
    });

    showPasswordConfirmationCheckbox.addEventListener('change', function() {
        passwordConfirmationInput.type = this.checked ? 'text' : 'password';
    });

    document.getElementById('deleteButton').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        });
    });

    function confirmUpdate(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "Are you sure you want to update this Agent?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                editAgentForm.submit();
            }
        });
    }

    editAgentForm.addEventListener('submit', confirmUpdate);
</script>
@endsection
