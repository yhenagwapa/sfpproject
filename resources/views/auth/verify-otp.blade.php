<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="base-url" content="{{ url('https://172.31.176.49/sfpproject/public') }}">

    <title>SFP ONSE</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                                    <a class="d-flex align-items-center mt-2">
                                        <img src="{{ asset('img/DSWD_Logo.png') }}" alt=""
                                            style="width: 75%;" class="ml-auto mr-0 mb-auto mt-auto">
                                        <img src="{{ asset('img/SFP-LOGO-2024.png') }}" alt=""
                                            style="width: 25%;" class="ml-0 mr-auto mb-auto mt-auto">
                                    </a>
                                </div>

                                @if($errors->any())
                                    <div class="alert alert-danger alert-primary alert-dismissible fade show"
                                        id="danger-alert" role="alert">{{ $errors->first() }}</div>
                                @endif

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var alert = document.getElementById('success-alert');
                                        var alert2 = document.getElementById('danger-alert');
                                        if (alert) {
                                            setTimeout(function() {
                                                    var bsAlert = new bootstrap.Alert(alert);
                                                    bsAlert.close();
                                                },
                                                3000);
                                        }
                                        if (alert2) {
                                            setTimeout(function() {
                                                    var bsAlert = new bootstrap.Alert(alert);
                                                    bsAlert.close();
                                                },
                                                3000);
                                        }
                                    });
                                </script>
                                <form method="POST" action="{{ route('verify.otp') }}">
                                    @csrf
                                    <div class="col-12 mt-4">
                                        <label for="otp">Enter OTP sent to your email.</label>
                                        <input type="text" name="otp" class="form-control mt-1 no-uppercase" required><br>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 mt-2 mb-3" type="submit">Verify OTP</button>
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
    </main><!-- End #main -->
    @vite(['resources/js/app.js'])
</body>

</html>
