
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} Inicio de Session</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="16x16" href="https://sorteo.zarethpremium.com/assets/image/favicon.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Yellowtail&display=swap');
        .login,
        .image {
        min-height: 100vh;
        }

        .bg-image {
        background-image: url('https://media-exp1.licdn.com/dms/image/C4E1BAQHmoYBo8HpJmQ/company-background_10000/0/1533587175628?e=2159024400&v=beta&t=QhSm_-aOjVoa7PkWPivlY6eYMGh29RMmIryUbyOS_RA');
        background-size: cover;
        background-position: center center;
        }
        .display-4 label{
            font-family: 'Yellowtail', cursive;
            color: rgb(10, 113, 247) !important;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row no-gutter">
        <!-- The image half -->
        <div class="col-md-6 d-none d-md-flex bg-image"></div>


        <!-- The content half -->
        <div class="col-md-6 bg-light">
            <div class="login d-flex align-items-center py-5">

                <!-- Demo content-->
                <div class="container">
                    <div class="row">
                        <div class="col-lg-10 col-xl-7 mx-auto">
                            <h3 class="display-4"><label>Siver</label> Login</h3>
                            <p class="text-muted mb-4">Sistemas de verificación de referencias</p>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf        
                                <div class="form-group mb-3">

                                    <input id="inputEmail" name="email" type="email" placeholder="Email address" required class="form-control rounded-pill border-0 shadow-sm px-4 @error('email') is-invalid @enderror">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <input id="inputPassword" name="password" type="password" placeholder="Password" required class="form-control rounded-pill border-0 shadow-sm px-4 text-primary @error('password') is-invalid @enderror">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="custom-control custom-checkbox mb-3">
                                    <input id="customCheck1" type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label for="customCheck1" class="custom-control-label">Remember password</label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block text-uppercase mb-2 rounded-pill shadow-sm">Sign in</button>
                                
                            </form>
                        </div>
                    </div>
                </div><!-- End -->

            </div>
        </div><!-- End -->

    </div>
</div>
</body>
</html>
