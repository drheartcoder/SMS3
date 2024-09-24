<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <title>School Management</title>
    <!-- ======================================================================== -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{url('/')}}/images/favicon.ico">
    <!-- Bootstrap CSS -->
    <!--base css styles-->
    <link rel="stylesheet" href="{{url('/')}}/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{url('/')}}/assets/font-awesome/css/font-awesome.min.css">
    <!--flaty css styles-->
    <link rel="stylesheet" href="{{url('/')}}/css/project-custome-css.css">
<!--    <link rel="stylesheet" href="{{url('/')}}/css/yogesh.css">-->
    <link rel="stylesheet" href="{{url('/')}}/css/admin/flaty.css">
    <link rel="stylesheet" href="{{url('/')}}/css/admin/flaty-responsive.css">
    <link rel="stylesheet" type="text/css" href="{{url('/')}}/assets/bootstrap-datepicker/css/datepicker.css" />
    <link rel="stylesheet" href="{{url('/')}}/css/admin/select2.min.css">
    <script src="{{url('/')}}/js/admin/jquery-1.11.3.min.js"></script>
    <script src="{{url('/')}}/assets/bootstrap/js/bootstrap.min.js"></script>
    <!-- DatePicker -->
    <script type="text/javascript" src="{{url('/')}}/assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

</head>

<body class="skin-navy_blue">
    <div class="student-panel-main">
        <div class="flex-main login-pgs-stdent">
            <div class="login-container">
                <div class="login-wrapper">
                    <div class="main-loginpage">
                        <div class="logo-content d-flex align-items-center justify-content-center">
                            <div class="masterset-uplogo">
                                <div class="logo-img-loign">
                                    <img src="{{url('/')}}/images/admin/school-logo-color.png" alt="" />
                                </div>
                                    {{config('app.project.name')}}
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <!-- BEGIN Main Content -->
                        <div class="login-content">
                            <!-- BEGIN Login Form -->
                           <form id="form-login" action="{{ url('/') }}/{{ config('app.project.role_slug.professor_role_slug') }}/process_login" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                                <h3>Login to your account</h3>
                                <hr>
                                @if (Session::has('flash_notification.message'))
                                    <div class="alert alert-{{ Session::get('flash_notification.level') }}">
                                        <button type="button" class="close" style="margin-top: 0px !important;padding: 0px !important;" data-dismiss="alert" aria-hidden="true">&times;</button>

                                        {{ Session::get('flash_notification.message') }}
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="text" placeholder="Email" class="form-control" id="email" name="email">
                                    <div class="error">{{ $errors->first('email') }}</div>
                                    <span id="err_email" style="color: #DE1212;font-size:10px;font-family: 'Open Sans';"> </span>
                                    <div class="icn-login-stn">
                                        <i class="fa fa-envelope"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" placeholder="Password" class="form-control" id="password" name="password" data-rule-required="true">
                                    <span class="error">{{ $errors->first('password') }}</span>
                                    <div class="icn-login-stn">
                                        <i class="fa fa-key"></i>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="controls btn-loginpg">
                                            <button type="submit" class="btn btn-primary">Sign In</button>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-12">
                                        <p class="forgot-pass-txts">
                                            <a href="#" class="goto-forgot" id="forgotPassword" style="text-align: center;">Forgot Password?</a>
                                        </p>
                                    </div>
                                </div>
                            </form>
                            <!-- END Login Form -->

                            <!-- BEGIN Forgot Password Form -->

                           <form id="form-forgot" style="display:none" action="{{ url($professor_panel_slug.'/process_forgot_password') }}" method="post" >
                            {{ csrf_field() }}
                                <h3>Get back your password</h3>
                                <hr>
                                <div class="" id="message"></div>
                                <div class="form-group">
                                    <div class="controls">
                                        <input type="text" placeholder="Email" class="form-control" id="forget_email" name="email">
                                        <span class="error">{{ $errors->first('email') }}</span>
                                        <span id="err_forget_email" style="color: red;font-size: 11px;font-family:'Open Sans'"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="controls btn-loginpg">
                                        <button type="submit" class="btn btn-primary">Recover</button>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <p class="forgot-pass-txts">
                                    <a href="#" class="goto-login" id="backToLogin" style="text-align: center;">Back to Login</a>
                                </p>
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

        <script>window.jQuery || document.write('<script src="{{ url('/') }}/assets/jquery/2.1.4/jquery.min.js"><\/script>')</script>
       <script type="text/javascript">
        var images       = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg'];
        
        var randomNumber = Math.floor((Math.random() * images.length)+1);
        
        var randomImage  = "{{url('/')}}/images/admin/" + randomNumber + ".jpg";
            
           $('#background').css({'background-image': 'url(' + randomImage + ')' }).addClass('loaded');

        </script>

        <script>window.jQuery || document.write('<script src="{{ url('/') }}/assets/jquery/jquery-2.1.4.min.js"><\/script>')</script>
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

      <script type="text/javascript">


        $("#forget_email").keyup(function()
        {
            $("#forget_email").next('span').html(""); 
        });    
        $("#email").keyup(function()
        {
            $("#email").next('span').html(""); 
        });

        $('#form-login').submit(function () {
            pattern = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
            var email = $("#email").val();
            if (email == "") {
                $("#err_email").text("This field is required.");
                return false;
            } else if (!pattern.test(email)) {
                $("#err_email").text("Please enter valid email.");
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

            function goToForm(form)
            {
                $('.login-content > form:visible').fadeOut(500, function(){
                    $('#form-' + form).fadeIn(500);
                });
            }
            $(function() 
            {
                $('.goto-login').click(function(){
                    goToForm('login');
                });
                $('.goto-forgot').click(function(){
                    goToForm('forgot');
                });
                $('.goto-register').click(function(){
                    goToForm('register');
                });

                applyValidationToFrom($("#form-login"))
                applyValidationToFrom($("#form-forgot"))
            });
        
        </script>

</body>

</html>