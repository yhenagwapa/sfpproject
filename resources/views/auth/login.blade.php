<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
  
    <title>SFP Onse</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
  
    <!-- Favicons -->
    <link href="{{ asset('../img/dswd.png') }}" rel="icon">
  
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('resources/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('resources/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('resources/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('resources/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('resources/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('resources/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('resources/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Template Main CSS File -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Template Main JS File -->
    <script src="{{ mix('js/app.js') }}"></script>

    <x-auth-session-status class="mb-4" :status="session('status')" />
  </head>
  
  <body>
    
    <main>
      <div class="container">
  
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center ">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
  
                <div class="d-flex justify-content-center">
                  
                </div>
                
  
                <div class="card mb-6">
  
                  <div class="card-body">
  
                    <div class="pt-1 pb-2">
                      <a href="https://fo11.dswd.gov.ph" class="d-flex align-items-center w-auto">
                        <img src="{{ asset('../img/dswd_logo.png') }}" alt="" style="width: 365px;">
                      </a>
                      <h5 class="card-title text-center pt-0 pb-0 fs-1">SFP ONSE</h5>
                    </div>

                    

                    <form class="row g-3" method="POST" action="{{ route('login') }}">
                    @csrf
                        <div class="col-12">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group has-validation">
                                <input 
                                    type="email" 
                                    name="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" 
                                    :value="__('Email')"
                                    required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>
            
                        <!-- Password Field -->
                        <div class="col-12">
                            <label for="password" class="form-label">Password</label>
                            <input 
                                type="password" 
                                name="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            
                        </div>

                       
  
                        
                      <div class="col-12">
                        <button class="btn btn-primary w-100 mb-4" type="submit">Login</button>
                      </div>
                      <div class="col-12">
                        <p class="small mb-0">Don't have account? <a href="pages-register.html">Create an account</a></p> 
                      </div>
                    </form>
  
                  </div>
                </div>
  
                <div class="credits">
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
  
      </div>
    </main><!-- End #main -->
  
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
  
    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
  
  </body>
</html>
