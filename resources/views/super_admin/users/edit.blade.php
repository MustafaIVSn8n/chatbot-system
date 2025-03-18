@extends('layouts.super_admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Edit Admin User</h1>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <form action="{{ route('super_admin.users.update', $admin->id) }}" method="POST" id="editAdminForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Admin Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Admin's Name" value="{{ $admin->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Admin Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Admin's Email" value="{{ $admin->email }}" required>
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
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="showPasswordConfirmation">
                                <label class="form-check-label" for="showPasswordConfirmation">Show Password</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Assign Websites</label>
                            <div class="row row-cols-1 row-cols-sm-2 g-2">
                                @foreach($websites as $website)
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="website_ids[]" value="{{ $website->id }}" id="website{{ $website->id }}"
                                                   @if($admin->adminWebsites->pluck('id')->contains($website->id)) checked @endif>
                                            <label class="form-check-label" for="website{{ $website->id }}">{{ $website->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Admin</button>
                            <button type="button" id="deleteButton" class="btn btn-danger">Delete Admin</button>
                        </div>
                    </form>

                    <form id="deleteForm" action="{{ route('super_admin.users.destroy', $admin->id) }}" method="POST" style="display: none;">
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
    const editAdminForm = document.getElementById('editAdminForm');

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
            text: "Are you sure you want to update this Admin?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('editAdminForm').submit();
            }
        })
    }

    editAdminForm.addEventListener('submit', confirmUpdate);
</script>
@endsection