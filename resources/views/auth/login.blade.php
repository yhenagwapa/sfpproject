<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>SFP Onse</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('../img/SFP-LOGO-2024.png') }}" rel="icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->

    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


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
                                        <img src="{{ asset('../img/DSWD_Logo.png') }}" alt=""
                                            style="width: 250px;" class="ml-auto mr-0 mb-auto mt-auto">
                                        <img src="{{ asset('../img/SFP-LOGO-2024.png') }}" alt=""
                                            style="width: 100px;" class="ml-0 mr-auto mb-auto mt-auto">
                                    </a>
                                    <h5 class="card-title text-center pt-0 pb-0 fs-1 mt-4">SFP ONSE</h5>
                                </div>

                                @if (session('success'))
                                    <div class="alert alert-success alert-primary alert-dismissible fade show"
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
                                                }
                                                5000);
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
                                    <div class="col-12">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror" id="password"
                                            required>
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

                        <div class="credits mt-5 small">
                            <!-- All the links in the footer should remain intact. -->
                            <!-- You can delete the links only if you purchased the pro version. -->
                            <!-- Licensing information: https://bootstrapmade.com/license/ -->
                            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                        </div>

                    </div>
                </div>
            </div>

        </section>
    </main><!-- End #main -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>

</html>
