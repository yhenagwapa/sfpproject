<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="base-url" content="{{ url('https://172.31.176.49/sfpproject/public') }}">

    <title>SFP IS</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('img/SFP-LOGO-2024.png') }}" rel="icon">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-auth-session-status class="mb-4" :status="session('status')" />
</head>

<body>
    <main>
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center ">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                        <div class="card mb-6">
                            <div class="card-body">
                                <div>
                                    <a href="https://fo11.dswd.gov.ph" class="d-flex align-items-center mt-2">
                                        <img src="{{ asset('img/DSWD_Logo.png') }}" alt=""
                                            style="width: 75%;" class="ml-auto mr-0 mb-auto mt-auto">
                                        <img src="{{ asset('img/SFP-LOGO-2024.png') }}" alt=""
                                            style="width: 25%;" class="ml-0 mr-auto mb-auto mt-auto">
                                    </a>
                                    <h5 class="text-center pt-0 pb-0 fs-2 fw-semibold mt-4">SFP Information System</h5>
                                </div>

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show"
                                        id="success-alert" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var alert = document.getElementById('success-alert');
                                        if (alert) {
                                            setTimeout(function() {
                                                    var bsAlert = new bootstrap.Alert(alert);
                                                    bsAlert.close();
                                                },
                                                3000);
                                        }
                                    });
                                </script>

                                <form class="row g-3" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email</label>
                                        <div class="input-group has-validation">
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror" id="email"
                                                :value="__('Email')" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Password Field -->
                                    <div class="col-12 relative">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" name="password"
                                            class="password form-control @error('password') is-invalid @enderror" id="password"
                                            required>
                                            <button
                                                    type="button"
                                                    class="absolute top-10 right-5 text-gray-500"
                                                    onclick="togglePassword('password', this)">
                                                    <span class="icon">
                                                        <!-- Default: Closed Eye Icon -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                        </svg>
                                                    </span>
                                                </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 mb-4" type="submit">Login</button>
                                    </div>
                                    <div class="col-12">
                                        <p class="small mb-2">Don't have account? <a
                                                href="{{ route('register') }}">Create an account</a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="credits mt-5 text-xs text-center">
                            &copy; 2025 Department of Social Welfare and Development. <br />
                        </div>

                    </div>
                </div>
            </div>

        </section>
        <script>
            function togglePassword(fieldId, button) {
                const input = document.getElementById(fieldId); // Target the input field
                const iconContainer = button.querySelector('.icon'); // Target the icon span

                if (input.type === "password") {
                    input.type = "text"; // Change input type to text
                    // Set the open eye icon
                    iconContainer.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    `;
                } else {
                    input.type = "password"; // Change input type to password
                    // Set the closed eye icon
                    iconContainer.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    `;
                }
            }
        </script>
    </main><!-- End #main -->
    @vite(['resources/js/app.js'])
</body>

</html>
