<!DOCTYPE html>
<html  >
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>{{ isset($page_title)?$page_title:"" }} - {{ config('app.project.name') }}</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="icon" type="image/png" sizes="16x16" href="{{url('/')}}/images/favicon.ico">
        <!--base css styles-->
        <link rel="stylesheet" href="{{ url('/') }}/assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ url('/') }}/assets/font-awesome/css/font-awesome.min.css">

        <!--page specific css styles-->
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-fileupload/bootstrap-fileupload.css" />

        <!--flaty css styles-->
        <link rel="stylesheet" href="{{ url('/') }}/css/admin/flaty.css">
        <link rel="stylesheet" href="{{ url('/') }}/css/admin/flaty-responsive.css">
        <link rel="stylesheet" type="text/css" href="{{url('/')}}/assets/bootstrap-datepicker/css/datepicker.css" />
        <link rel="stylesheet" href="{{url('/')}}/css/project-custome-css.css">
        <link rel="stylesheet" href="{{url('/')}}/css/schooladmin/animate.css">

        <link rel="stylesheet" href="{{ url('/') }}/assets/jquery-ui/jquery-ui.min.css">
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />

        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-switch/static/stylesheets/bootstrap-switch.css" />
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />

        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/select2.min.css" />
        
        <!-- Auto load email address -->
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/chosen-bootstrap/chosen.min.css" />
        <!-- date picker css -->
        <!--  <link href="css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" /> -->
         <link rel="stylesheet" type="text/css" href="{{url('/')}}/assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css" />

        <!--basic scripts-->
        <script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
        <script src="{{ url('/') }}/assets/base64.js"></script>

        <!-- This is custom js for sweetalert messages -->
        <script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>

        <!-- Ends -->
    
        <script>window.jQuery || document.write('<script src="{{ url('/') }}/assets/jquery/jquery-2.1.4.min.js"><\/script>')</script>
        <script src="{{ url('/') }}/assets/jquery-ui/jquery-ui.min.js"></script>
        <script src="{{ url('/') }}/js/admin/select2.min.js"></script>
        <!-- DatePicker -->
        <script type="text/javascript" src="{{ url('/') }}/assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script> 
       
       <script src="{{url('/')}}/js/school_admin/language_custom.js"></script>

        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/jquery-tags-input/jquery.tagsinput.css" />
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-duallistbox/duallistbox/bootstrap-duallistbox.css" />
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/dropzone/downloads/css/dropzone.css" />
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-colorpicker/css/colorpicker.css" />
        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/chosen-bootstrap/chosen.min.css" />
        <!-- <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/font-awesome/css/font-awesome-animation.min.css" /> -->

        <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/jquery.multiselect.css" />

        <link href="{{ url('/') }}/assets/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ url('/') }}/assets/font-awesome.css" rel="stylesheet" type="text/css" />
       
        <script src="{{ url('/') }}/js/admin/image_validation.js"></script>
        <script src="{{ url('/') }}/js/admin/jquery.multiselect.js"></script>
        <script src="{{ url('/') }}/js/admin/ajax_loader.js"></script>
    


    </head>
    @if(\Session::get('locale') == 'fr')
    <style type="text/css">
      .fileupload.fileupload-new .btn.btn-default.btn-file:before{content: 
        'Sélectionnez une photo'}
    </style>
    @else
    <style type="text/css">
      .fileupload.fileupload-new .btn.btn-default.btn-file:before{content: "Select Photo";}
    </style>
    @endif

    <body class="{{ theme_body_color() }}">
    <div class="school-admin-main">
    <?php
            $admin_path = config('app.project.admin_panel_slug');

    ?>
        <!-- BEGIN Theme Setting -->
        
        <!-- END Theme Setting -->

        <!-- BEGIN Navbar -->
        <div id="navbar" class="navbar {{ theme_navbar_color() }}">
            <button type="button" class="navbar-toggle navbar-btn collapsed" data-toggle="collapse" data-target="#sidebar">
                <span class="fa fa-bars"></span>
            </button>
            
            <a class="navbar-brand logotxts" href="#">
<!--
              <span class="admin-full-name-logo"> {{translation('school_management_system_3')}}</span>
              <span class="admin-short-name-logo">SMS</span>
-->
                <img class="logo-size" src="{{url('/')}}\images\admin\school-logo-color.png" alt="" />
            </a>
            

            {{-- {{dd($arr_notifications)}} --}}
            <!-- BEGIN Navbar Buttons -->
            <ul class="nav flaty-nav pull-right">


                <li class="hidden-xs select-kig-main">
                      <div class="selection-block">
                         <div class="language_bx">
                             <div class="wrapper-demo">
                                 <div tabindex="1" class="wrapper-dropdown-1" id="dd">
                                     <span id="new_word">
                                        <span class="icon-flags"> 
                                            @if(\Session::has('locale') && \Session::get('locale')=='en')
                                                <img alt="Flag Icon" src="{{url('/')}}\images\english.png" class="flag_icon">English 

                                            @elseif(\Session::has('locale') && \Session::get('locale')=='fr')
                                                <img alt="Flag Icon" src="{{url('/')}}\images\french.png" class="flag_icon">French 

                                            @endif
                                     </span>
                                     <ul tabindex="1" class="dropdown">
                                         <li onClick="changeLang('en')" ><a href="#"><img alt="Flag Icon" src="{{url('/')}}\images\english.png" class="flag_icon">English</a></li>
                                         <li onClick="changeLang('fr')"><a data-flag="french" href="#"><img alt="Flag Icon" src="{{url('/')}}\images\french.png" class="flag_icon">French</a></li>
                                     </ul>
                                 </div>
                             </div>
                         </div>
                     </div>
                </li>
            <?php
                        $totalCount = 0;
                        $totalCount = $employeeNotificationCount + $schooladminNotificationCount + $professorNotificationCount + $studentNotificationCount + $parentNotificationCount;
                        ?>
                <!-- BEGIN Button Tasks -->
                <!--<li class="hidden-xs">-->
                <li>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="fa fa-bell"></i>
                        <span class="badge badge-warning">{{ $totalCount or 0 }}</span>
                    </a>
                    
                    <!-- BEGIN Tasks Dropdown -->
                    <ul class="dropdown-navbar dropdown-menu">
                        <li class="nav-header title-none">
                            {{translation('notification')}}
                        </li>
                        <li class="notify">
                            <a href="{{ url('/')}}/{{ config('app.project.admin_panel_slug') }}/notification?type=all">
                                <i class="fa fa-comment blue"></i>
                                <p>{{translation('all_notifications')}}</p>
                                <span class="badge badge-warning badge-info notifyCountTotal">{{ $totalCount or 0 }}</span>
                            </a>
                        </li>

                        <li class="notify">
                            <a href="{{ url('/')}}/{{ config('app.project.admin_panel_slug') }}/notification?type={{config('app.project.role_slug.employee_role_slug')}}">
                                <i class="fa fa-comment blue"></i>
                                <p>{{ translation(config('app.project.role_slug.employee_role_slug')) }}</p>
                                <span class="badge badge-warning badge-info notifyCountTotal">{{ $employeeNotificationCount or 0 }}</span>
                            </a>
                        </li>

                        <li class="notify">
                            <a href="{{ url('/')}}/{{ config('app.project.admin_panel_slug') }}/notification?type={{config('app.project.role_slug.school_admin_role_slug')}}">
                                <i class="fa fa-comment blue"></i>
                                <p>{{ translation(config('app.project.role_slug.school_admin_role_slug')) }}</p>
                                <span class="badge badge-warning badge-info notifyCountTotal">{{ $schooladminNotificationCount or 0 }}</span>
                            </a>
                        </li>

                        <li class="notify">
                            <a href="{{ url('/')}}/{{ config('app.project.admin_panel_slug') }}/notification?type={{config('app.project.role_slug.professor_role_slug')}}">
                                <i class="fa fa-comment blue"></i>
                                <p>{{ translation(config('app.project.role_slug.professor_role_slug')) }}</p>
                                <span class="badge badge-warning badge-info notifyCountTotal">{{ $professorNotificationCount or 0 }}</span>
                            </a>
                        </li>

                        <li class="notify">
                            <a href="{{ url('/')}}/{{ config('app.project.admin_panel_slug') }}/notification?type={{config('app.project.role_slug.student_role_slug')}}">
                                <i class="fa fa-comment blue"></i>
                                <p>{{  translation(config('app.project.role_slug.student_role_slug')) }}</p>
                                <span class="badge badge-warning badge-info notifyCountTotal">{{ $studentNotificationCount or 0 }}</span>
                            </a>
                        </li>

                        <li class="notify">
                            <a href="{{ url('/')}}/{{ config('app.project.admin_panel_slug') }}/notification?type={{config('app.project.role_slug.parent_role_slug')}}">
                                <i class="fa fa-comment blue"></i>
                                <p>{{  translation(config('app.project.role_slug.parent_role_slug')) }}</p>
                                <span class="badge badge-warning badge-info notifyCountTotal">{{ $parentNotificationCount or 0 }}</span>
                            </a>
                        </li>



                               

                        {{-- <li class="more">
                            <a href="#">See tasks with details</a>
                        </li> --}}
                    </ul>
                    <!-- END Tasks Dropdown -->
                </li>
                <!-- END Button Tasks -->

                <!-- BEGIN Button Notifications -->
               
                <!-- END Button Messages -->

                <!-- BEGIN Button User -->
                <li class="user-profile">
                    <a data-toggle="dropdown" href="#" class="user-menu dropdown-toggle headr-fl">

                    <?php
                        $obj_data  = Sentinel::check();
                        if($obj_data)
                        {
                           $arr_data = $obj_data->toArray();    
                        }

                    ?>


                    <?php 
                        $profile_img = isset($arr_data['profile_image'])  ? $arr_data['profile_image'] : "";
                    ?>                

                    <img class="nav-user-photo" src="{{ get_resized_image($profile_img,config('app.project.img_path.user_profile_images'),119,148) }}" alt="">
                        <span class="hhh" id="user_info">
                          {{$arr_data['first_name']}} {{$arr_data['last_name']}}
                        </span>
                        <i class="fa fa-caret-down"></i>
                    </a>

                    <!-- BEGIN User Dropdown -->
                    <ul class="dropdown-menu dropdown-navbar" id="user_menu">
                        
                        <li>
                            <a href="{{ url('/').'/'.$admin_path }}/profile" >
                                <i class="fa fa-user"></i>
                                {{translation('my_profile')}}
                            </a>    
                        </li> 
                        
                        <li>
                            <a href="{{ url('/').'/'.$admin_path }}/change_password" >
                                <i class="fa fa-key"></i>
                                {{translation('change_password')}}
                            </a>    
                        </li> 

                        <li>
                             <a href="{{ url('/').'/'.$admin_path }}/logout "> 
                                <i class="fa fa-power-off"></i>
                                {{translation('logout')}}
                            </a>
                        </li>
                    </ul>
                    <!-- BEGIN User Dropdown -->
                </li>
                <!-- END Button User -->
            </ul>
            <!-- END Navbar Buttons -->
        </div>
        <!-- END Navbar -->
        <script type="text/javascript">
            
            function DropDown(el) {
                this.dd = el;
                this.placeholder = this.dd.children('span');
                this.opts = this.dd.find('ul.dropdown > li');
                this.val = '';
                this.index = -1;
                this.initEvents();
            }
            DropDown.prototype = {
                initEvents : function() {
                    var obj = this;
                    obj.dd.on('click', function(event){
                        $(this).toggleClass('active');
                        return false;
                    });



                    obj.opts.on('click',function(){
                        var opt = $(this);
                        var obj_a       = opt.find("a");
                        var language_text = obj_a.text();
                        $("#new_word").html(obj_a.html());
                        
                    });
                },
                getValue : function() {
                    return this.val;
                },
                getIndex : function() {
                    return this.index;
                }
            }

            $(function() {

                var dd = new DropDown( $('#dd') );

                $(document).click(function() {
                    // all dropdowns
                    $('.wrapper-dropdown-1').removeClass('active');
                });

            });
            
        </script>
        <!-- BEGIN Container -->
        <div class="container {{ theme_sidebar_color() }}" id="main-container">
    <script type="text/javascript">
        function change_notification_status(ref) {
            var base_url = '{{ url('/') }}'; 
            if(ref!=undefined){
                if(ref.id!=0 && ref.view_url!=''){
                    $.ajax({
                            url:locations_url_path+'/change_notification_status?notification_id='+btoa(ref.id),
                            type:'GET',
                            data:'flag=true',
                            dataType:'json',
                            success:function(response)
                            {
                            var url = base_url+'/'+ref.view_url;
                            window.location.replace(url); 
                            }     
                    });
                }
            }
        }
    </script>

    <script>
        $(".Language-section").on("click", function(){
            $(this).toggleClass("other-active");
        });
        function changeLang(event){
            lang = event;
            $.ajax({
                          url  :"{{url('/')}}/{{config('app.project.role_slug.admin_role_slug')}}/setLanguage",
                          type :'POST',
                          data :{'lang':lang ,'_token':'<?php echo csrf_token();?>'},
                          success:function(data){
                            location.reload(true);
                          }
                    });

        }
    </script>
     
    
  