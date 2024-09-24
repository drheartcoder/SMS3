<!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
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
            <link rel="stylesheet" href="{{url('/')}}/css/admin/flaty.css">
            <link rel="stylesheet" href="{{ url('/') }}/assets/chosen-bootstrap/chosen.min.css" />
            <link rel="stylesheet" href="{{url('/')}}/css/admin/flaty-responsive.css">
            <link rel="stylesheet" href="{{url('/')}}/css/project-custome-css.css">
            <link rel="stylesheet" href="{{url('/')}}/css/schooladmin/animate.css">
<!--        <link rel="stylesheet" href="{{url('/')}}/css/schooladmin/yogesh.css">-->

            <link rel="stylesheet" type="text/css" href="{{url('/')}}/assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css" /> 

            <!--page specific css styles-->
            <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-fileupload/bootstrap-fileupload.css" />

            
            <link rel="stylesheet" href="{{url('/')}}/css/admin/select2.min.css"> 
            <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/admin/sweetalert.css" />

            <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/bootstrap-timepicker/compiled/timepicker.css" />
            
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
        <script src="{{ url('/') }}/assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script> 
        <script type="text/javascript" src="{{url('/')}}/js/admin/select2.min.js"></script>  
        <style>
            #loader {
                  position: fixed;
                  left: 44%;
                  top: 42%;                  
                  z-index: 99999;
                }
            .loader-active:before{background-color: rgba(0,0,0,0.7) !important;content: "";position: fixed;top: 0;right: 0;bottom: 0;left: 0;z-index: 999;}
                

        </style>
        
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

        <body class="skin-navy_blue">

            <div class="school-admin-main">

                <!--loader div-->
                    <div id="loader" hidden="true">
                        <img src="{{url('/')}}/images/Loader1.gif" width="120px">
                    </div>
                <!--end loader div-->

                  @if(isset($terms_and_conditions) && $terms_and_conditions==1)  
                    <div class="terms-condition-block">
                        <div class="terms-condition-content">
                            <div class="content-terms content-test">
                        
                                <h1>Terms &amp; Conditions</h1>
                                <p>
                                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                                </p>
                                <p>
                                    Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.
                                </p>
                                <p>
                                    The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.
                                </p>
                                <p>
                                    It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).
                                </p>

                            </div>
                            <div class="form-group pull-right terms-btns">                
                                <input class="btn btn btn-primary" value="Decline" type="button" id="decline">
                                <input class="btn btn btn-primary" value="Agree" type="button" id="accept">                 
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <!-- END Theme Setting -->  
                  @endif
                
                <div id="header">

                    <!-- BEGIN Navbar -->
                    <div id="navbar" class="navbar navbar-navy_blue">
                        <button type="button" class="navbar-toggle navbar-btn collapsed" data-toggle="collapse" data-target="#sidebar">
                                <span class="fa fa-bars"></span>
                            </button>
                        <a class="navbar-brand logotxts" href="{{ url($school_admin_panel_slug.'/dashboard') }}">
<!--                           SCHOOL MANAGEMENT SYSTEM 3-->
                           <img class="logo-size" src="{{url('/')}}\images\admin\school-logo-color.png" alt="" />
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
                                            <label class="control-label">{{translation('academic_year')}}</label>
                                            <div class="controls">
                                            
                                               <input type="hidden" name="year_hidden" id="year_hidden">
                                               
                                               <select name="current_academic_year" id="academic_year" class="form-control">
                                                  @if(isset($getAcademicYear) && $getAcademicYear != '')
                                                    @foreach($getAcademicYear as  $year)
                                                        <option value="{{$year['id']}}" 
                                                                @if(\Session::has('academic_year') && Session::get('academic_year') == $year['id'] ) 
                                                                 selected
                                                                   
                                                                @endif>{{$year['academic_year']}}</option>
                                                    @endforeach
                                                  @endif
                                              </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>         
                            </li>        
                            <li class="hidden-xs onclick-remove">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                    <i class="fa fa-bell"></i>
                                    <?php   
                                        $totalCount = 0;
                                        $totalCount = $adminNotificationCount + $professorNotificationCount + $studentNotificationCount + $parentNotificationCount + (isset($employeeNotificationCount)?$employeeNotificationCount:0)+(isset($schoolAdminNotificationCount)?$schoolAdminNotificationCount:0);
                                    ?>
                                    <span class="badge badge-warning">{{$totalCount}}</span>
                                </a>

                                <!-- BEGIN Tasks Dropdown -->
                                <ul class="dropdown-navbar dropdown-menu ">
                                    <li class="nav-header title-none">
                                        {{translation('notification')}}
                                    </li>
                                     <li class="notify">
                                        <a href="{{ url('/')}}/{{ config('app.project.school_admin_panel_slug') }}/notification?type=all">
                                            <i class="fa fa-comment blue"></i>
                                            <p>{{translation('all_notifications')}}</p>
                                            <span class="badge badge-warning badge-info notifyCountTotal">{{ $totalCount or 0 }}</span>
                                        </a>
                                    </li>

                                    <li class="notify">
                                        <a href="{{ url('/')}}/{{ config('app.project.school_admin_panel_slug') }}/notification?type={{config('app.project.role_slug.admin_role_slug') }}">
                                            <i class="fa fa-comment blue"></i>
                                            <p>{{  translation(config('app.project.role_slug.admin_role_slug')) }}</p>
                                            <span class="badge badge-warning badge-info notifyCountTotal">{{ $adminNotificationCount or 0 }}</span>
                                        </a>
                                    </li>

                                    <li class="notify">
                                        <a href="{{ url('/')}}/{{ config('app.project.school_admin_panel_slug') }}/notification?type={{config('app.project.role_slug.professor_role_slug') }}">
                                            <i class="fa fa-comment blue"></i>
                                            <p>{{  translation(config('app.project.role_slug.professor_role_slug')) }}</p>
                                            <span class="badge badge-warning badge-info notifyCountTotal">{{ $professorNotificationCount or 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="notify">
                                        <a href="{{ url('/')}}/{{ config('app.project.school_admin_panel_slug') }}/notification?type={{config('app.project.role_slug.student_role_slug')}}">
                                            <i class="fa fa-comment blue"></i>
                                            <p>{{  translation(config('app.project.role_slug.student_role_slug')) }}</p>
                                            <span class="badge badge-warning badge-info notifyCountTotal">{{ $studentNotificationCount or 0 }}</span>
                                        </a>
                                    </li>
                                     <li class="notify">
                                        <a href="{{ url('/')}}/{{ config('app.project.school_admin_panel_slug') }}/notification?type={{config('app.project.role_slug.parent_role_slug')}}">
                                            <i class="fa fa-comment blue"></i>
                                            <p>{{  translation(config('app.project.role_slug.parent_role_slug')) }}</p>
                                            <span class="badge badge-warning badge-info notifyCountTotal">{{ $parentNotificationCount or 0 }}</span>
                                        </a>
                                    </li>

                                @if(\Session::has('role') && \Session::get('role')==config('app.project.role_slug.school_admin_role_slug'))
                                    <li class="notify">
                                        <a href="{{ url('/')}}/{{ config('app.project.school_admin_panel_slug') }}/notification?type={{config('app.project.role_slug.employee_role_slug')}}">
                                            <i class="fa fa-comment blue"></i>
                                            <p>{{  translation(config('app.project.role_slug.employee_role_slug')) }}</p>
                                            <span class="badge badge-warning badge-info notifyCountTotal">{{ $employeeNotificationCount or 0 }}</span>
                                        </a>
                                    </li>
                                @else
                                    <li class="notify">
                                        <a href="{{ url('/')}}/{{ config('app.project.school_admin_panel_slug') }}/notification?type={{config('app.project.role_slug.school_admin_role_slug')}}">
                                            <i class="fa fa-comment blue"></i>
                                            <p>{{  translation(config('app.project.role_slug.school_admin_role_slug')) }}</p>
                                            <span class="badge badge-warning badge-info notifyCountTotal">{{ $schoolAdminNotificationCount or 0 }}</span>
                                        </a>
                                    </li>
                                @endif

                                </ul>
                                <!-- END Tasks Dropdown -->
                            </li>
                            
                            <!-- END Button Tasks -->

                            <!-- BEGIN Button Notifications -->

                            <!-- END Button Messages -->

                            <!-- BEGIN Button User -->
                            <li class="user-profile onclick-remove">
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
                                            {{$obj_data->first_name}} {{$obj_data->last_name}} 
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </a>

                                <!-- BEGIN User Dropdown -->
                                
                                    <ul class="dropdown-menu dropdown-navbar" id="user_menu">
                                    <li>
                                    <?php
                                        $user = \Sentinel::check(); 
                                    ?>
                                        @if($user->inRole(config('app.project.role_slug.school_admin_role_slug')))
                                            <a href="{{url('/').'/'.$school_admin_panel_slug}}/profile">
                                                
                                        @else
                                            <a href="{{url('/').'/'.$school_admin_panel_slug.'/technical_profile'}}">
                                                
                                        @endif
                                        <i class="fa fa-user"></i> {{translation('my_profile')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{url('/').'/'.$school_admin_panel_slug}}/change_password">
                                            <i class="fa fa-key"></i> {{translation('change_password')}}
                                        </a>
                                    </li>               
                                    <li>
                                        <a href="{{url('/').'/'.$school_admin_panel_slug}}/logout">
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
         
<script>
                    $("#accept").click(function(){

                            var url = "{{url('/')}}/{{config('app.project.role_slug.school_admin_role_slug')}}/change_first_time";
                            
                           $.ajax({

                                url:url,
                                method:"post",
                                data:{"_token":"{{csrf_token()}}"},
                                success:function(data){
                                      //  location.reload(true);
                                      }
                            });

                        $(".terms-condition-block").css({
                            "display": "none",            
                        });
                        $("body").removeClass("no-scroll");   
                    }); 
                    $("#decline").on("click", function(){
                       window.location.href = "{{url('/').'/'.$school_admin_panel_slug}}"+'/login'; 
                       
                   });
                </script>  
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
						var obj_a 		= opt.find("a");
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

                    $('#academic_year').on("change",function(){
                      var year = $('#academic_year').val();

                          if(year != '')
                          {
                            $('#loader').fadeIn('slow');
                            $('body').addClass('loader-active');
                            $('#year_hidden').val(year); 
                                $.ajax({
                                      url  :"{{url('/')}}/{{config('app.project.role_slug.school_admin_role_slug')}}/setSession",
                                      type :'POST',
                                      data :{'year':year ,'_token':'<?php echo csrf_token();?>'},
                                      success:function(data){
                                        location.reload(true);
                                      }
                                });
                          }
                    });


                </script> 
                
                <script>
                    $(".Language-section").on("click", function(){
                        $(this).toggleClass("other-active");
                    });
                    
                    function changeLang(event){
                        lang = event;
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
                                title: warning,
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
                                                  url  :"{{url('/')}}/{{config('app.project.role_slug.school_admin_role_slug')}}/setLanguage",
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

                
        
