<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ChatFlow Admin Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional: Font Awesome or Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <!-- Custom Styles -->
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            /* Replace with a more chatbot-themed background image */
            background: url('https://assets.grok.com/users/6dbd7a74-3e0d-4a42-aa32-6a6bc5bbfbc9/15FTToFgvvsXcPwu-generated_image.jpg') no-repeat center center;
            background-size: cover;
        }
        .overlay {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
        }
        .hero-heading {
            font-size: 2.5rem;
            font-weight: 700;
        }
        .hero-subheading {
            font-size: 1.1rem;
            font-weight: 400;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex flex-column min-vh-100">
        <div class="row flex-grow-1">
            <div class="col d-flex flex-column justify-content-center align-items-center text-center p-4">
                <!-- Hero Section -->
                <h1 class="hero-heading text-white mb-3">ChatFlow Admin Portal</h1>
                <p class="hero-subheading mb-5">
                    Manage your chatbot system with ease and efficiency.
                </p>

                <!-- Login Card -->
                <div class="card shadow border-0 overlay" style="max-width: 420px; width: 100%;">
                    <div class="card-body p-4">
                        <h4 class="mb-3 fw-bold">Sign In</h4>
                        <p class="text-muted mb-4">Access your ChatFlow administration panel.</p>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <!-- Email Field -->
                            <div class="mb-3 text-start">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email"
                                       placeholder="Enter your email"
                                       value="{{ old('email') }}"
                                       required autofocus>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="mb-3 text-start">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       placeholder="Enter your password"
                                       required>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                                <!-- Show Password Checkbox -->
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="showPassword">
                                    <label class="form-check-label" for="showPassword">Show Password</label>
                                </div>
                            </div>

                            <!-- Forgot Password Link -->
                            <div class="mb-4 text-end">
                                <a href="{{ route('password.request') }}" class="small text-decoration-none">
                                    Forgot Password?
                                </a>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Log In
                            </button>
                        </form>
                    </div>
                </div>
                <!-- End Login Card -->
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Show Password Script -->
    <script>
        const showPasswordCheckbox = document.getElementById('showPassword');
        const passwordField = document.getElementById('password');

        showPasswordCheckbox.addEventListener('change', function() {
            if (this.checked) {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        });
    </script>
</body>
</html>
