<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}">
    <link rel="icon" type="image/png"
        href="{{ url('https://www.ifpusa.com/wp-content/uploads/2021/11/KYB%20DRUPAL%20LOGO.png') }}">
    <title>
        Kayaba Indonesia
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Host+Grotesk:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Nucleo Icons -->
    <link href="{{ asset('css/nucleo-icons.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link href="{{ asset('css/login.css') }}" rel="stylesheet" />


</head>

<style>
    .form-floating {
        position: relative;
        margin-bottom: 1rem;
    }

    .form-floating input:focus~label,
    .form-floating input:not(:placeholder-shown)~label {
        top: -0.75rem;
        font-size: 0.75rem;
        color: #007bff;
    }

    .form-floating label {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        pointer-events: none;
        transition: all 0.2s;
    }

    .otp-input {
        width: 40px;
        height: 50px;
        font-size: 24px;
        text-align: center;
        margin: 5px;
        border: 2px solid #ced4da;
        border-radius: 8px;
    }

    .otp-input:focus {
        border-color: #007bff;
        outline: none;
    }

    footer.footer {
        position: sticky;
        bottom: 0;
        background: white;
        z-index: 1000;
    }

    @media (max-height: 500px) {

        /* Jika keyboard muncul, hide footer */
        footer.footer {
            display: none;
        }
    }
</style>

<body class="d-flex flex-column min-vh-100">
    <main class="d-flex flex-grow-1 align-items-center justify-content-center">
        <section class="flex-grow-1">
            <div class="page-header min-vh-75">
                <div class="wrapper">
                    <div class="title text-center mb-4">
                        <img src="{{ asset('img/logo.png') }}" alt="">
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger text-dark text-center">
                            @if ($errors->has('captcha'))
                                Incorrect Captcha, please try again.
                            @elseif ($errors->has('npk'))
                                Incorrect NPK or Password, please try again.
                            @endif
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger text-dark text-center">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row no-gutters d-flex flex-column align-items-center">
                        <div class="container-main shadow-lg">

                            <div class="bottom w-100">
                                <header class="mb-4">SIGN IN</header>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <!-- NPK -->
                                    <div class="form-group">
                                        <label class="form-label" for="npk">NPK</label>
                                        <br>
                                        <input class="form-control" id="npk" type="text" name="npk"
                                            required autofocus placeholder="NPK">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label" for="password">Password</label>
                                        <br>
                                        <input class="form-control" id="password" type="password" name="password"
                                            required placeholder="Password">
                                    </div>

                                    <div class="form-group text-center">
                                        {{-- <label class="form-label" for="captcha">Captcha</label> --}}
                                        <div class="d-flex justify-content-center align-items-center mb-2">
                                            <span id="captcha-img">{!! captcha_img() !!}</span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                                id="refresh-captcha">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <input class="form-control w-50 text-center" type="text" name="captcha"
                                                required placeholder="Captcha">
                                        </div>
                                    </div>

                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-login">LOGIN</button>
                                    </div>

                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </section>
    </main>

    <!-- -------- START FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
    <footer class="footer text-center mt-auto py-3">
        <div class="container">
            <div class="row">
                <div class="col-12 mx-auto text-center">
                    <p class="mb-0 text-secondary">
                        Copyright ©
                        <script>
                            document.write(new Date().getFullYear())
                        </script> PT Kayaba Indonesia
                    </p>
                </div>
            </div>
        </div>
    </footer>




    <!-- Modal OTP -->


    <!-- -------- END FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
    <!--   Core JS Files   -->
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>

    {{-- <script>
        var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
    </script> --}}

    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const refreshBtn = document.getElementById("refresh-captcha");
            const captchaImg = document.getElementById("captcha-img");

            refreshBtn.addEventListener("click", function() {
                fetch('/refresh-captcha')
                    .then(res => res.json())
                    .then(data => {
                        captchaImg.innerHTML = data.captcha;
                    });
            });
        });
    </script>
    <script>
        window.addEventListener('resize', function() {
            const footer = document.querySelector('footer.footer');
            if (window.innerHeight < 500) {
                footer.style.display = 'none';
            } else {
                footer.style.display = 'block';
            }
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>



</body>

</html>
