@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Create Agent User</h1>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.agents.store') }}" method="POST" id="createAgentForm">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Agent Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Agent's Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Agent Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Agent's Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create a strong password" required>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="showPassword">
                                <label class="form-check-label" for="showPassword">Show Password</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-bold">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="showPasswordConfirmation">
                                <label class="form-check-label" for="showPasswordConfirmation">Show Password</label>
                            </div>
                        </div>

                        <!-- Assign Websites Section -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Assign Websites</label>
                            <div class="row row-cols-1 row-cols-sm-2 g-2">
                                @foreach($websites as $website)
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="website_ids[]" value="{{ $website->id }}" id="website{{ $website->id }}">
                                            <label class="form-check-label" for="website{{ $website->id }}">{{ $website->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Submit Button Below -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Create Agent</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('createAgentForm');
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const showPasswordCheckbox = document.getElementById('showPassword');
    const showPasswordConfirmationCheckbox = document.getElementById('showPasswordConfirmation');

    showPasswordCheckbox.addEventListener('change', function() {
        passwordInput.type = this.checked ? 'text' : 'password';
    });

    showPasswordConfirmationCheckbox.addEventListener('change', function() {
        passwordConfirmationInput.type = this.checked ? 'text' : 'password';
    });

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const password = passwordInput.value;
        const passwordConfirmation = passwordConfirmationInput.value;

        if (password.length < 8) {
            Swal.fire({
                icon: 'error',
                title: 'Password too short',
                text: 'Password must be at least 8 characters long.',
            });
            return;
        }

        if (password !== passwordConfirmation) {
            Swal.fire({
                icon: 'error',
                title: 'Passwords do not match',
                text: 'Please make sure the passwords match.',
            });
            return;
        }

        form.submit();
    });
</script>
@endsection
