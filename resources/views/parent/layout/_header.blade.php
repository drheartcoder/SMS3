<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta name="description" content="" />
            <meta name="keywords" content="" />
            <meta name="author" content="" />
            <title>{{translation('school_management_system_3')}}</title>
            <!-- ======================================================================== -->
            <link rel="icon" type="image/png" sizes="16x16" href="{{url('/')}}/images/favicon.ico">
            <!-- Bootstrap CSS -->

            <!--base css styles-->
            <link rel="stylesheet" href="{{url('/')}}/assets/bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet" href="{{url('/')}}/assets/font-awesome/css/font-awesome.min.css">

            <!--flaty css styles-->            
<!--            <link rel="stylesheet" href="{{url('/')}}/css/parent/bhavesh.css">-->
<!--            <link rel="stylesheet" href="{{url('/')}}/css/parent/yogesh.css">-->
            <link rel="stylesheet" href="{{url('/')}}/css/admin/flaty.css">
            <link rel="stylesheet" href="{{url('/')}}/css/admin/flaty-responsive.css">
             <link rel="stylesheet" href="{{url('/')}}/css/project-custome-css.css"> 
            <!-- <link rel="stylesheet" href="{{url('/')}}/css/schooladmin/animate.css"> -->
        <link rel="stylesheet" href="{{url('/')}}/css/schooladmin/yogesh.css">

            <link rel="stylesheet" type="text/css" href="{{url('/')}}/assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css" />  

            <!--page specific css styles-->
            <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-fileupload/bootstrap-fileupload.css" />

            
            <link rel="stylesheet" href="{{url('/')}}/css/admin/select2.min.css"> 
            
            
            <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />
            
            <script src="{{url('/')}}/js/admin/jquery-1.11.3.min.js"></script>

             <script src="{{url('/')}}/js/school_admin/language_custom.js"></script>

           
            <script src="{{url('/')}}/assets/bootstrap/js/bootstrap.min.js"></script>  

            <!--basic scripts-->
            <script src="{{ url('/') }}/js/admin/sweetalert.min.js"></script>
            <script src="{{ url('/') }}/assets/base64.js"></script>
        <!-- This is custom js for sweetalert messages -->
        <script type="text/javascript" src="{{ url('/js/admin') }}/sweetalert_msg.js"></script>
        <!-- Ends -->
       
       <script src="{{ url('/') }}/js/admin/image_validation.js"></script>
       <!-- DatePicker -->
        <script type="text/javascript" src="{{ url('/') }}/assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script> 
        <!-- <script type="text/javascript" src="{{url('/')}}/js/admin/select2.min.js"></script>   -->

         <!-- loader css -->
        <style>
            #loader {
                  position: fixed;
                  left: 39%;
                  top: 33%;                  
                  z-index: 99999;
                }
            .loader-active:before{background-color: rgba(0,0,0,0.7) !important;content: "";position: fixed;top: 0;right: 0;bottom: 0;left: 0;z-index: 999;}
        </style>
        <!--end loader css -->

    </head>
        <body class="skin-navy_blue">
            <div class="school-admin-main">
            <!--loader div-->
            
                <div id="loader" hidden="true">
                    <img src="{{url('/')}}/images/loader1.gif" width="300px">
                </div>
            <!--end loader div-->
            
                <div id="header">
                    <!-- BEGIN Navbar -->
                        <div id="navbar" class="navbar navbar-navy_blue">
                            <button type="button" class="navbar-toggle navbar-btn collapsed" data-toggle="collapse" data-target="#sidebar">
                                    <span class="fa fa-bars"></span>
                                </button>
                            <a class="navbar-brand logotxts" href="{{ url($parent_panel_slug.'/dashboard') }}">
<!--                               SCHOOL MANAGEMENT SYSTEM 3-->
                               <img class="logo-size" src="{{url('/')}}/images/admin/school-logo-color.png" alt="" />
                            </a>
                            <!-- BEGIN Navbar Buttons -->
                            <ul class="nav flaty-nav pull-right">
                                <!-- BEGIN Button Tasks -->

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

                                <li class="hidden-xs select-kig-main">
                                    <div class="select-kig-drop">
                                        <div class="kid-select-drop">
                                            <div class="form-group">
                                                <label class="control-label">{{translation('select')}} {{ translation('kid')}}</label>
                                                <div class="controls">
                                                @if(!empty($parent_childern_arr) && $parent_childern_arr!='')
                                                   <select name="q_action" id="" class="form-control parent_kids_arr_cls" > 
                                                    @foreach($parent_childern_arr as $key=> $value)
                                                        @if(!empty($value['get_user_details']))
                                                          <option value="{{ $value['user_id'] }}"  data-kid-level-class="{{ $value['level_class_id'] }}" 
                                                          @if(Session::get('kid_id') == $value['user_id'])
                                                           selected="selected" 
                                                        @endif
                                                            >{{$value['get_user_details']['first_name'] or ''}}
                                                          {{$value['get_user_details']['last_name'] or ''}}
                                                          </option>
                                                          @endif
                                                    @endforeach
                                                   </select>
                                                  @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>         
                                </li>
                                 <li class="hidden-xs message-notification-section onclick-remove">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                        <i class="fa fa-envelope"></i>
                                        <span class="badge badge-warning">{{$unread_message_count}}</span>
                                    </a>

                                    <!-- BEGIN Tasks Dropdown -->
                                    <ul class="dropdown-navbar dropdown-menu create-scrolls">
                                        <li class="nav-header title-none">
                                            {{translation('messages')}}
                                        </li>
                                        @foreach($unread_messages as $message)
                                        <li>
                                            <a href="{{url('/').'/'.$parent_panel_slug}}/message">
                                                <div class="message-section-block">
                                                    <div class="message-sender-proile">
                                                        <div class="profile-pic-sender">
                                                            <?php
                                                                $first_name = isset($message->get_form_user_details->first_name) ? ucfirst($message->get_form_user_details->first_name) :'';
                                                                $last_name  = isset($message->get_form_user_details->last_name) ? ucfirst($message->get_form_user_details->last_name) :'';
                                                                $name = $first_name.' '.$last_name;
                                                                $message = isset($message->text_message)?$message->text_message:'';
                                                                $date = isset($message->message_date)?$message->message_date:'';
                                                                $time = isset($message->message_time)?$message->message_time:'';
                                                                if(isset($message->get_form_user_details->profile_image) && $message->get_form_user_details->profile_image!='' )
                                                                {
                                                                    $profile_image = url('/').config('app.project.img_path.user_profile_images').$message->get_form_user_details->profile_image;
                                                                }
                                                                else{
                                                                    $profile_image = url('/').'/images/default-profile.png';
                                                                }
                                                            ?>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="message-sender-name-message">
                                                        <div class="message-seder-name">
                                                            {{$name}}
                                                        </div>
                                                        <div class="message-content">
                                                            {{$message}}
                                                        </div>
                                                    </div>
                                                    <div class="message-time">
                                                        {{$date}} {{$time}}
                                                    </div>
                                                </div>                                      
                                            </a>
                                        </li>
                                        @endforeach
                                        <li class="see-all-messages">
                                            <a href="{{url('/').'/'.$parent_panel_slug}}/message">See all Message</a>
                                        </li>
                                    </ul>    
                                    <!-- END Tasks Dropdown -->
                                </li>
                                 <li class="hidden-xs onclick-remove">

                                     <?php   
                                        $totalCount = 0;
                                        $totalCount =   $schooladminNotificationCount +  $professorNotificationCount;
                                    ?>
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                        <i class="fa fa-bell"></i>
                                        <span class="badge badge-warning">{{$totalCount}}</span>
                                    </a>

                                    <!-- BEGIN Tasks Dropdown -->
                                    <ul class="dropdown-navbar dropdown-menu">
                                        <li class="nav-header title-none">
                                            {{translation('notification')}}
                                        </li>
                                       <li class="notify">
                                        <a href="{{ url('/')}}/{{ $parent_panel_slug }}/notification?type=all">
                                            <i class="fa fa-comment blue"></i>
                                            <p>{{translation('all_notifications')}}</p>
                                            <span class="badge badge-warning badge-info notifyCountTotal">{{ $totalCount or 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="notify">
                                        <a href="{{ url('/')}}/{{ $parent_panel_slug  }}/notification?type={{config('app.project.role_slug.school_admin_role_slug') }}">
                                            <i class="fa fa-comment blue"></i>
                                            <p>{{  translation(config('app.project.role_slug.school_admin_role_slug')) }}</p>
                                            <span class="badge badge-warning badge-info notifyCountTotal">{{ $schooladminNotificationCount or 0 }}</span>
                                        </a>
                                    </li>
                                     <li class="notify">
                                        <a href="{{ url('/')}}/{{ $parent_panel_slug  }}/notification?type={{config('app.project.role_slug.professor_role_slug') }}">
                                            <i class="fa fa-comment blue"></i>
                                            <p>{{  translation(config('app.project.role_slug.professor_role_slug')) }}</p>
                                            <span class="badge badge-warning badge-info notifyCountTotal">{{ $professorNotificationCount or 0 }}</span>
                                        </a>
                                    </li>
                                    </ul>
                                    <!-- END Tasks Dropdown -->
                                </li>
                             
                                <!-- END Button Tasks -->

                                <!-- BEGIN Button Notifications -->

                                <!-- END Button Messages -->
                                <?php
                                        $obj_data  = Sentinel::check();
                                        if($obj_data)
                                        {
                                           $arr_data = $obj_data->toArray();    
                                        }

                                    ?>

                                    <?php 
                                        $profile_img = isset($arr_data['profile_image'])  ? $arr_data['profile_image'] : "";

                                        $first_name  = isset($arr_data['first_name'])  ? $arr_data['first_name'] : "";
                                        $last_name  = isset($arr_data['last_name'])  ? $arr_data['last_name'] : "";
                                    ?>  
                                <!-- BEGIN Button User -->
                                <li class="user-profile onclick-remove">
                                    <a data-toggle="dropdown" href="#" class="user-menu dropdown-toggle headr-fl">
                                        <img class="nav-user-photo" src="{{ get_resized_image($profile_img,config('app.project.img_path.user_profile_images'),119,148) }}" alt="Profile Image">
                                        <span class="hhh" id="user_info">
                                                   {{$first_name}} {{$last_name}}
                                                </span>
                                        <i class="fa fa-caret-down"></i>
                                    </a>

                                    <!-- BEGIN User Dropdown -->
                                    <ul class="dropdown-menu dropdown-navbar" id="user_menu">
                                        <li>
                                            <a href="{{ url('/') }}/{{$parent_panel_slug}}/profile">
                                                <i class="fa fa-user"></i> {{translation('my_profile')}}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('/') }}/{{$parent_panel_slug}}/change_password">
                                                <i class="fa fa-key"></i> {{translation('change_password')}}
                                            </a>
                                        </li>               

                                        <li>
                                            <a href="{{url('/')}}/{{$parent_panel_slug}}/logout">
                                                <i class="fa fa-power-off"></i> {{translation('logout')}}
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
                       
                </div> 

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
                <script>
                   

                    $(".onclick-remove").on("click", function(){
                        $(this).siblings().removeClass("active");
                    });
                </script>

                <script>
                    $(".Language-section").on("click", function(){
                        $(this).toggleClass("other-active");
                    });
                    function changeLang(event){
                        lang = event;
                        /*$.ajax({
                                      url  :"{{url('/')}}/{{config('app.project.role_slug.parent_role_slug')}}/setLanguage",
                                      type :'POST',
                                      data :{'lang':lang ,'_token':'<?php echo csrf_token();?>'},
                                      success:function(data){
                                        location.reload(true);
                                      }
                                });*/

                        var language = '{{\Session::get('locale')}}';
                        if(lang!=language)
                        {
                            var warning = "{{translation('warning')}}";
                            @if(Session::get('locale')=='en')
                               title = "Do you really want to set this language as default? \n once you set this language your acoount will be logged out";
                               ok="ok";

                            @else
                               title = "Voulez-vous vraiment définir cette langue par défaut? \n Une fois que vous avez défini cette langue, votre compte sera déconnecté"; 
                               ok = "D'accord";
                            @endif
                            swal({
                                title: "{{translation('warning')}}",
                                text: title,
                                icon: "warning",
                                confirmButtonText: ok,
                                closeOnConfirm: true,
                                dangerMode: true,
                                showCancelButton: true,
                                },
                                function(isConfirm)
                                {

                                  if(isConfirm)
                                  {
                                    
                                    $.ajax({
                                                  url  :"{{url('/')}}/{{config('app.project.role_slug.parent_role_slug')}}/setLanguage",
                                                  type :'POST',
                                                  data :{'lang':lang,'_token':'<?php echo csrf_token();?>'},
                                                  success:function(data){
                                                    window.location.href = '{{url('/')}}/login';
                                                  }
                                            });
                                  }
                                  else
                                  {
                                    location.reload(true);
                                  }
                                });
                        }
                        else
                        {
                            @if(Session::get('locale')=='en')
                               title = "You already set this language as your default language";
                               ok="ok";

                            @else
                               title = "Vous avez déjà défini cette langue comme langue par défaut"; 
                               ok = "D'accord";
                            @endif
                            swal({
                                title: "{{translation('warning')}}",
                                text: title,
                                icon: "warning",
                                confirmButtonText: ok,
                                closeOnConfirm: true,
                                dangerMode: true,
                                },
                                function(isConfirm)
                                {
                                  if(isConfirm)
                                  {
                                    location.reload(true);
                                  }
                               });
                            
                        }

                    }
                </script>



           
                
        
