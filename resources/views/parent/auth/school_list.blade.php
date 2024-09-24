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
<!--    <link rel="icon" type="image/png" sizes="16x16" href="favicon.ico">-->
    <!-- Bootstrap CSS -->
    <!--base css styles-->
    <link rel="stylesheet" href="{{url('/')}}/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <!--flaty css styles-->
    <link rel="stylesheet" href="{{url('/')}}/css/project-custome-css.css">
<!--    <link rel="stylesheet" href="{{url('/')}}/css/yogesh.css">-->
    <link rel="stylesheet" href="{{url('/')}}/css/admin/flaty.css">
    <link rel="stylesheet" href="{{url('/')}}/css/admin/flaty-responsive.css">
    <link rel="stylesheet" type="text/css" href="assets/bootstrap-datepicker/css/datepicker.css" />
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
                            <!-- BEGIN Change Email Form -->

                           <form id="validation-form1" onsubmit="return addLoader()" action="{{ url($parent_panel_slug.'/login_process') }}" method="post" >

                                 @if (Session::has('flash_notification.message'))
                                    <div class="alert alert-{{ Session::get('flash_notification.level') }}">
                                        {!! Session::get('flash_notification.message') !!}
                                    </div>
                                @endif
                                {{ csrf_field() }}
                                <h3>Select School</h3>
                                <hr>

                                <div class="" id="message"></div>
                                 <div class="form-group">
                                    <label for="school_id">School Name</label>
                                    <select name="school_id" class="form-control" data-rule-required="true">
                                        @if(!empty($arr_school))
                                        
                                            <option value="">Select School Name</option>
                                            @if(isset($arr_school)&&$arr_school!='')
                                                @foreach($arr_school as $key => $value)
                                                    <option value="{{$value['school_id']}}">
                                                        {{ $value['name'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                         @endif
                                    </select>
                                    <div class="error">{{ $errors->first('school_id') }}</div>
                                    <div class="icn-login-stn">
                                        <i class="fa fa-home"></i>
                                    </div>

                                </div> 
                                <div class="form-group">
                                    <div class="controls btn-loginpg">
                                        <button type="submit" class="btn btn-primary" id="submit_button">Go</button>
                                    </div>
                                </div>


                            </form>
                            <!-- END Change Email Form -->
                        </div>
                        <!-- END Main Content -->
                    </div>
                </div>
            </div>
        </div>
    </div>

        <script>window.jQuery || document.write('<script src="{{ url('/') }}/assets/jquery/2.1.4/jquery.min.js"><\/script>')</script>
       

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

      

</body>

</html>