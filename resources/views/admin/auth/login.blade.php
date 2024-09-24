<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{{ $site_settings['site_name'] or '' }} {{translation('admin_login')}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <!--base css styles-->
    <link rel="stylesheet" href="{{ url('/') }}/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/font-awesome/css/font-awesome.min.css">

    
    <!--page specific css styles-->
    <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/chosen-bootstrap/chosen.min.css">

    <!--flaty css styles-->
    <link rel="stylesheet" href="{{ url('/') }}/css/admin/flaty.css">
    <link rel="stylesheet" href="{{ url('/') }}/css/admin/flaty-responsive.css">
    <link rel="stylesheet" href="{{url('/')}}/css/project-custome-css.css">
    <link rel="stylesheet" href="{{url('/')}}/css/schooladmin/animate.css">

    <link rel="icon" type="image/png" sizes="16x16" href="{{url('/')}}/images/favicon.ico">


    <style type="text/css">
        .error {color: red;}        
        .form-group .controls .help-block {position: relative;bottom: 0;font-size: 10px;left: 0;}        
        .login-container {position: absolute;max-width: 680px;width: 94%;margin: auto;height: 320px;top: 0;bottom: 0;left: 0;right: 0;}
    </style>

</head>


<body class="login-page">


    <!-- BEGIN Main Content -->

    <div class="flex-main">
        <div class="student-panel-main">
            <div class="login-container">
                <div class="login-wrapper">
                    <div class="main-loginpage">
                        <div class="logo-content d-flex align-items-center justify-content-center">
                            <div class="masterset-uplogo">
                                <div class="logo-img-loign">
                                    <img src="images/admin/school-logo-color.png" alt="">
                                </div>
                                {{ config('app.project.name')}}
                            </div>
                        </div>
                        <!-- BEGIN Main Content -->
                        <div class="login-content">
                            <!-- BEGIN Login Form -->
                            <form id="form-login" action="{{url('/admin/process_login')}}" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <h3>{{translation('login_to_your_account')}}</h3>
                                <hr> @if (Session::has('flash_notification.message'))
                                <div class="alert alert-{{ Session::get('flash_notification.level') }}">
                                    <button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button>

                                    {{ Session::get('flash_notification.message') }}
                                </div>
                                @endif
                                
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="text" placeholder="{{translation('email')}}" class="form-control" id="email" name="email">
                                    <span class="error">{{ $errors->first('email') }} </span>
                                    <span id="err_email" style="color: red;font-size: 10px"> </span>
                                    <div class="icn-login-stn">
                                        <i class="fa fa-envelope"></i>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="controls">
                                        <label for="password">Password</label>
                                        <input type="password" placeholder="{{translation('password')}}" class="form-control" id="password" name="password" data-rule-required="true">
                                        <span class="error">{{ $errors->first('password') }} </span>
                                        <div class="icn-login-stn">
                                            <i class="fa fa-key"></i>
                                        </div>
                                    </div>
                                </div>                               
                                
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <div class="controls">
                                                <button style="width: 100%;" type="submit" class="btn btn-primary">{{translation('sign_in')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-12">
                                        <p class="forgot-pass-txts">
                                            <a href="#" class="goto-forgot" id="forgotPassword" style="text-align: center;margin-top: 0;" >{{translation('forget_password')}}?</a>
                                        </p>
                                    </div>
                                </div>

                            </form>


                            <!-- END Login Form -->

                            <!-- BEGIN Forgot Password Form -->


                            <form id="form-forgot" style="display:none" action="{{ url($admin_panel_slug.'/process_forgot_password') }}" method="post">
                                {{ csrf_field() }}
                                <h3>{{translation('get_back_your_password')}}</h3>
                                <hr>
                                <div class="" id="message"></div>
                                <div class="form-group">
                                    <div class="controls">
                                        <input type="text" placeholder="{{translation('email')}}" class="form-control" id="forget_email" name="email" />
                                        <span for="forget_email" class="help-block"> {{ $errors->first('email') }}</span>
                                        <span id="err_forget_email" style="color: red;font-size: 10px;"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls">
                                        <button style="width: 100%;" type="submit" class="btn btn-primary">{{translation('recover')}}</button>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="registration-txt">
                                    <a href="#" class="goto-login pull-left" id="backToLogin" style="text-align: center;">{{translation('back_to_login')}}</a>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                            <!-- END Forgot Password Form -->
                        </div>
                        <!-- END Main Content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--basic scripts-->
        <script src="{{ url('/')}}/assets/jquery/2.1.4/jquery.min.js"></script>
    <script>
        var images = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg'];

        var randomNumber = Math.floor((Math.random() * images.length) + 1);

        var randomImage = "{{url('/')}}/images/admin/" + randomNumber + ".jpg";

        $('#background').css({
            'background-image': 'url(' + randomImage + ')'
        }).addClass('loaded');
    </script>

    <script src="{{ url('/') }}/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="{{ url('/') }}/assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="{{ url('/') }}/assets/jquery-cookie/jquery.cookie.js"></script>


    <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/jquery.validate.min.js"></script>
    <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/additional-methods.min.js"></script>
    <script type="text/javascript" src="{{ url('/') }}/assets/chosen-bootstrap/chosen.jquery.min.js"></script>


    <!--flaty scripts-->
    <script src="{{ url('/') }}/js/admin/flaty.js"></script>
    <script src="{{ url('/') }}/js/admin/flaty-demo-codes.js"></script>
    <script src="{{ url('/') }}/js/admin/validation.js"></script>

    <script >
        $("#forget_email").keyup(function () {
            $("#forget_email").next('span').html("");
        });
        $("#email").keyup(function () {
            $("#email").next('span').html("");
        });

        $('#form-login').submit(function () {
            pattern = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
            var email = $("#email").val();
            if (email == "") {
                $("#err_email").text("This field is required");
                return false;
            } else if (!pattern.test(email)) {
                $("#err_email").text("Please enter valid email");
                return false;
            } else {
                $("#err_email").text("");
                return true;
            }
        });

        $('#form-forgot').submit(function () {
            pattern = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
            var email = $("#forget_email").val();
            if (email == "") {
                $("#err_forget_email").text("This field is required");
                return false;
            } else if (!pattern.test(email)) {
                $("#err_forget_email").text("Please enter valid email");
                return false;
            } else {
                $("#err_forget_email").text("");
                return true;
            }
        });

        function goToForm(form) {
            $('.login-content > form:visible').fadeOut(500, function () {
                $('#form-' + form).fadeIn(500);
            });
        }
        $(function () {
            $('.goto-login').click(function () {
                goToForm('login');
            });
            $('.goto-forgot').click(function () {
                goToForm('forgot');
            });
            $('.goto-register').click(function () {
                goToForm('register');
            });

            applyValidationToFrom($("#form-login"))
            applyValidationToFrom($("#form-forgot"))
        });
    </script>
</body>

</html>