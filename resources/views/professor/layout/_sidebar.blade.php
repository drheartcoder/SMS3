<div id="left-bar">
<?php 

     $segment2       = Request::segment(2);
     $segment3       = Request::segment(3);

?>
    <!-- BEGIN Container -->
    <div class="container sidebar-navy_blue professor-left-class" id="main-container">
        <!-- BEGIN Sidebar -->
        <div id="sidebar" class="navbar-collapse collapse">
            <div class="side-bar-scroll content-d">
            <!-- BEGIN Navlist -->
            <ul class="nav nav-list">

                <li class="left-bar-arrow-section">
                    <a href="javascript:void(0)" id="arrowSide" class="left-arrow-section" onclick="closeNav()">
                        <i class="fa toggle-left fa-angle-double-left"></i>                             
                    </a>
                    <a href="javascript:void(0)" class="right-arrow-section" onclick="openNav()">                            
                        <i class="fa toggle-left fa-angle-double-right"></i> 
                    </a>
                    <div class="clearfix"></div>
                </li>
                <li class="<?php  if(Request::segment(2) == 'dashboard'){ echo 'active'; } ?>">
                    <a href="{{url($professor_panel_slug.'/dashboard')}}" title="{{translation('dashboard')}}">
                        <span class="icon-dash"><i class="fa fa-dashboard faa-vertical animated-hover"></i></span>
                        <span class="mobile-nones click-name-hide">{{translation('dashboard')}}</span>
                    </a>
                </li> 

                @if(array_key_exists('calendar.update', $arr_current_user_access) || array_key_exists('calendar.list', $arr_current_user_access))
                    <li class="<?php  if($segment2 == 'calendar'){ echo 'active'; } ?>">
                        <a href="{{url($professor_panel_slug.'/calendar')}}" title="{{translation('calendar')}}">
                            <span class="icon-dash"><i class="fa fa-calendar faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('calendar')}}</span>
                        </a>
                    </li> 
                @endif

                {{-- Setup--}}

                <li class="<?php  if($segment2 == 'notification_settings'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('setup')}}">
                            <span class="icon-dash"><i class="fa fa-cog faa-vertical animated-hover"></i></span>
                            <span class="click-name-hide">{{translation('setup')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">
                             @if(array_key_exists('notification_settings.update', $arr_current_user_access) || array_key_exists('notification_settings.list', $arr_current_user_access))
                                <li class="<?php  if($segment2 == 'notification_settings'){ echo 'active'; } ?>">
                                    <a href="{{url($professor_panel_slug.'/notification_settings')}}" title="{{translation('notification_settings')}}">
                                        <span class="icon-dash"><i class="fa fa-bell faa-vertical animated-hover"></i></span>
                                        <span class="mobile-nones">{{translation('notification_settings')}}</span>
                                    </a>
                                </li> 
                            @endif  

                        </ul>
                </li>
                {{-- end setup --}}

                {{-- features --}}

                <li class="<?php  if($segment2 == 'timetable' || $segment2 == 'attendance' || $segment2 == 'homework' || $segment2 == 'course_material' || $segment2 == 'student_behaviour' || $segment2 == 'exam' || $segment2 == 'task' || $segment2 == 'canteen_bookings' || $segment2 == 'club'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('features')}}">
                            <span class="icon-dash"><i class="fa fa-gift faa-vertical animated-hover"></i></span>
                            <span class="click-name-hide">{{translation('features')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">
                             @if(array_key_exists('timetable.list',$arr_current_user_access ))
                                <li class="<?php  if(Request::segment(2) == 'timetable'){ echo 'active'; } ?>">
                                    <a href="{{url($professor_panel_slug.'/timetable')}}" title="{{translation('timetable')}}">
                                        <span class="icon-dash"><i class="fa fa-clock-o faa-vertical animated-hover"></i></span>
                                        <span class="mobile-nones">{{translation('timetable')}}</span>
                                    </a>
                                </li> 
                            @endif 

                            @if(array_key_exists('attendance.list',$arr_current_user_access ))
                                <li class="<?php  if(Request::segment(2) == 'attendance'){ echo 'active'; } ?>" >
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('attendance')}}">
                                        <span class="icon-dash"><i class="fa fa-cc-diners-club faa-vertical animated-hover"></i></span>
                                        <span>{{translation('attendance')}}</span>    
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>
                                     <ul class="submenu-dropdown-toggle childmenu">
                                        @if(array_key_exists('attendance.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'attendance' && Request::segment(3) == 'professor'){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/attendance/professor')}}">{{translation('professor')}} </a></li>   
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'attendance' && Request::segment(3) == 'student'){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/attendance/student')}}">{{translation('student')}} </a></li>   
                                        @endif                         
                                    </ul>
                                </li>
                            @endif

                            @if(array_key_exists('homework.list',$arr_current_user_access ))
                                <li class="<?php  if(Request::segment(2) == 'homework'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('homework')}}">
                                            <span class="icon-dash"><i class="fa fa-file  faa-vertical animated-hover"></i></span>
                                            <span>{{translation('homework')}}</span>    
                                            <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                    <ul class="submenu-dropdown-toggle childmenu">
                                      
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'homework' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug)}}/homework">{{translation('manage')}} </a></li> 
                                        

                                        @if(array_key_exists('homework.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'homework' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/homework/create')}}">{{translation('add')}} </a></li>
                                        @endif
                                    </ul>
                                </li> 
                            @endif

                            @if(array_key_exists('course_material.list',$arr_current_user_access ))
                                 <li class="<?php  if(Request::segment(2) == 'course_material'){ echo 'active'; } ?>">
                                    
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('course_material')}}">
                                            <span class="icon-dash"><i class="fa fa-book faa-vertical animated-hover"></i></span>
                                            <span>{{translation('course_material')}}</span>    
                                            <b class="arrow fa fa-angle-right"></b>
                                    </a>
                                     <ul class="submenu-dropdown-toggle childmenu">
                                        
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'course_material' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/course_material')}}">{{translation('manage')}} </a></li>   

                                        @if(array_key_exists('course_material.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'course_material' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/course_material/create')}}">{{translation('add')}} </a></li>   
                                        @endif                         
                                    </ul>

                                </li> 
                            @endif

                            @if(array_key_exists('student_behaviour.list', $arr_current_user_access))

                                <li class="<?php  if($segment2 == 'student_behaviour'){ echo 'active'; } ?>">
                             
                                        <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('student_behaviour')}}">
                                            <span class="icon-dash"><i class="fa fa-file  faa-vertical animated-hover"></i></span>
                                            <span>{{translation('student_behaviour')}}</span>    
                                            <b class="arrow fa fa-angle-right"></b>
                                        </a>

                                        <ul class="submenu-dropdown-toggle childmenu">
                                          
                                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'student_behaviour' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug)}}/student_behaviour">{{translation('manage')}} </a></li> 

                                            @if(array_key_exists('student_behaviour.create', $arr_current_user_access))
                                            <li style="display: block;" class="<?php  if(Request::segment(2) == 'student_behaviour' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/student_behaviour/create')}}">{{translation('add')}} </a></li>
                                            @endif
                                        </ul>
                                </li>
                            @endif

                            <li class="<?php  if(Request::segment(2) == 'exam'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('exam')}}">
                                        <span class="icon-dash"><i class="fa fa-user  faa-vertical animated-hover"></i></span>
                                        <span>{{translation('exam')}}</span>    
                                        <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                  
                                    <li style="display: block;" class="<?php  if(Request::segment(2) == 'exam' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug)}}/exam">{{translation('manage')}} </a></li> 
                                    
                                    @if(array_key_exists('exam.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if(Request::segment(2) == 'exam' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/exam/create')}}">{{translation('add')}} </a></li>
                                    @endif
                                </ul>
                            </li> 

                            @if(array_key_exists('task.list', $arr_current_user_access))
                                 <li class="<?php  if(Request::segment(2) == 'task'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('task')}}">
                                        <span class="icon-dash"><i class="fa fa-tasks faa-vertical animated-hover"></i></span>
                                        <span>{{translation('task')}}</span>    
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                    <ul class="submenu-dropdown-toggle childmenu">
                                      
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'task' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug)}}/task">{{translation('manage')}} </a></li>

                                        @if(array_key_exists('student_behaviour.create', $arr_current_user_access)) 
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'task' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/task/create')}}">{{translation('add')}} </a></li>
                                        @endif
                                    </ul>
                                </li>  
                             @endif

                             @if(array_key_exists('canteen_bookings.list', $arr_current_user_access))
                                <li class="<?php  if($segment2 == 'canteen_bookings'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('canteen_bookings')}}">
                                        <span class="icon-dash"><i class="fa fa-ticket faa-vertical animated-hover"></i></span>
                                        <span>{{translation('canteen_bookings')}}</span>    
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                    <ul class="submenu-dropdown-toggle childmenu">

                                        @if(array_key_exists('canteen_bookings.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'canteen_bookings' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/canteen_bookings/')}}">{{translation('order_history')}} </a>
                                        </li>
                                        @endif
                                        @if(array_key_exists('canteen_bookings.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'canteen_bookings' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/canteen_bookings/create')}}">{{translation('add_booking')}} </a></li>   
                                        @endif
                                           
                                    </ul>
                                </li>
                            @endif

                            @if(array_key_exists('club.list', $arr_current_user_access))
                                 <li class="<?php  if(Request::segment(2) == 'club'){ echo 'active'; } ?>">
                                    <a href="{{url($professor_panel_slug.'/club')}}" title="{{translation('club')}}">
                                        <span class="icon-dash"><i class="fa fa-users faa-vertical animated-hover"></i></span>
                                        <span class="mobile-nones">{{translation('club')}}</span>
                                    </a>
                                </li> 
                            @endif   

                        </ul>
                </li>
                {{-- end features --}}

                {{-- communication --}}
                <li class="<?php  if($segment2 == 'survey' || $segment2 == 'suggestions' || $segment2 == 'news' || $segment2 == 'message' || $segment2 == 'claim'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('communication')}}">
                            <span class="icon-dash"><i class="fa fa- fa-bullhorn faa-vertical animated-hover"></i></span>
                            <span class="click-name-hide">{{translation('communication')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">

                            @if(array_key_exists('survey.list', $arr_current_user_access))
                                 <li class="<?php  if(Request::segment(2) == 'survey'){ echo 'active'; } ?>">
                                    <a href="{{url($professor_panel_slug.'/survey')}}" title="{{translation('survey')}}">
                                        <span class="icon-dash"><i class="fa fa-bar-chart faa-vertical animated-hover"></i></span>
                                        <span class="mobile-nones">{{translation('survey')}}</span>
                                    </a>
                                </li> 
                            @endif 

                            

                            @if(array_key_exists('suggestions.list', $arr_current_user_access))
                       
                                <li class="<?php  if($segment2 == 'suggestions'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('suggestions')}}">
                                        <span class="icon-dash"><i class="fa fa-dropbox faa-vertical animated-hover"></i></span>
                                        <span>{{translation('suggestions')}}</span>    
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>
                                     <ul class="submenu-dropdown-toggle childmenu">

                                        <li class="<?php  if(Request::segment(2) == 'suggestions' && Request::segment(3) == 'manage'){ echo 'active'; } ?>" style="display: block;"><a href="{{ url($professor_panel_slug.'/suggestions/manage')}}">{{translation('manage')}} {{translation('suggestions')}} </a></li>

                                        <li  class="<?php  if(Request::segment(2) == 'suggestions' && Request::segment(3) == 'poll_raised'){ echo 'active'; } ?>" style="display: block;"><a href="{{ url($professor_panel_slug.'/suggestions/poll_raised')}}">{{translation('manage')}} {{translation('poll_raised')}} {{translation('suggestions')}} </a></li>

                                        @if(array_key_exists('suggestions.list', $arr_current_user_access))
                                          <li  class="<?php  if(Request::segment(2) == 'suggestions' && Request::segment(3) == 'create'){ echo 'active'; } ?>" style="display: block;"><a href="{{ url($professor_panel_slug.'/suggestions/create')}}">{{translation('add')}} {{translation('suggestion')}} </a></li>
                                        @endif

                                    </ul>
                                </li>

                                @endif  
                             
                             @if(array_key_exists('news.list', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'news'){ echo 'active'; } ?>">
                                <a href="{{url($professor_panel_slug.'/news')}}" title="{{translation('news')}}">
                                    <span class="icon-dash"><i class="fa fa-newspaper-o faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('news')}}</span>
                                </a>
                            </li> 
                            @endif 

                            @if(array_key_exists('message.list',$arr_current_user_access ))
                             <li class="<?php  if(Request::segment(2) == 'message'){ echo 'active'; } ?>">
                                <a href="{{url($professor_panel_slug.'/message')}}" title="{{translation('message')}}">
                                    <span class="icon-dash"><i class="fa fa-comments-o faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('message')}}</span>
                                </a>
                            </li> 
                            @endif

                            @if($claim_module_access == '1')
                                @if(array_key_exists('claim.list',$arr_current_user_access ))
                                 <li class="<?php  if(Request::segment(2) == 'claim'){ echo 'active'; } ?>">
                                   
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('claim')}}">
                                            <span class="icon-dash"><i class="fa fa-book faa-vertical animated-hover"></i></span>
                                            <span>{{translation('claim')}}</span>    
                                            <b class="arrow fa fa-angle-right"></b>
                                    </a>
                                    <ul class="submenu-dropdown-toggle childmenu">
                                        
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'claim' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/claim')}}">{{translation('manage')}} </a></li>   

                                        @if(array_key_exists('claim.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'claim' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($professor_panel_slug.'/claim/create')}}">{{translation('add')}} </a></li>   
                                        @endif
                                    </ul>
                                </li> 
                                @endif
                            @endif
                        </ul>
                </li>

                 {{-- end communication --}}

            </ul>
            </div>


                <!-- END Navlist -->

                <!-- BEGIN Sidebar Collapse Button -->
                <div id="sidebar-collapse" class="visible-lg close-leftbar">
                    <i class="fa fa-angle-double-left open-arrow" onclick="openNav()"></i>
                    <i class="fa fa-angle-double-right close-arrow" onclick="closeNav()"></i>
                </div>
                <!-- END Sidebar Collapse Button -->
        </div>
        <!-- END Sidebar -->
        <!-- END Content -->
    </div>
    <!-- END Container -->
    <!-- Add Class SideBar Start-->
    <script>
        $( function() {
            $( "#arrowSide" ).on( "click", function() {
                $( ".navbar-collapse" ).toggleClass( "sidebarmain-admin", 0);
            });
            $(".right-arrow-section" ).on( "click", function() {
                $( ".navbar-collapse" ).toggleClass( "sidebarmain-admin", 0);
            });
        });   
    </script> 
    <!-- Scroll Start Here -->
    <link href="{{url('/')}}/css/admin/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
    <script src="{{url('/')}}/js/admin/jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript">
        /*scrollbar start*/
        (function($) {
            $(window).on("load", function() {
                $.mCustomScrollbar.defaults.scrollButtons.enable = true; //enable scrolling buttons by default
                $.mCustomScrollbar.defaults.axis = "yx"; //enable 2 axis scrollbars by default
                $(".content-d").mCustomScrollbar({
                    theme: "dark"
                });
                var sections = $('.content-d');
                var link = $("li.active");
                sections.mCustomScrollbar("scrollTo", link.position().top, {
                    scrollInertia:200,
                });
            });
        })(jQuery);
    </script>
    <!-- Scroll End Here -->

        <script>
            $(".left-arrow-section").on("click", function(){
                $(this).parent().parent(".nav-list").addClass("menu-close");
            });
            $(".right-arrow-section").on("click", function(){
                $(this).parent().parent(".nav-list").removeClass("menu-close");
            });
        </script>
        
       <script>
        $(".dropdown-toggle").on("click", function(){
            $(this).siblings(".submenu").slideToggle("fast");
            $(this).parent().siblings().find(".submenu").slideUp("fast");    
            $(this).parent().toggleClass("active2");
            $(this).parent().siblings().removeClass("active2");            
            $(this).children(".arrow").toggleClass("fa-angle-right").toggleClass("fa-angle-down");
            $(this).parent().siblings().find(".arrow").removeClass("fa-angle-down").addClass("fa-angle-right");
        });
        
        $("#sidebar-collapse").on("click", function(){
            $("#sidebar").toggleClass("sidebar-collapsed");            
        });    
    </script>   
    
    <script type="text/javascript">
        /*header script end*/
        function openNav() {
            document.getElementById("sidebar").style.width = "240px";
            $("#main-content").css({
                "margin-left": "240px",            
                "transition": "margin-left .5s",            
            });        
        }

        function closeNav() {
            document.getElementById("sidebar").style.width = "41px";
            $("#main-content").css({
                "margin-left": "41px",
                "transition": "margin-left .5s",
                "position": "relative"
            });
            $("#main").removeClass("overlay");
        }
        /*header script end*/
    </script>

    <script>
        $(".last-li-section").on("click", function(){
            $(this).parent().toggleClass("active-submenu");                  
        });  
        $(".dropdown-toggle-submenu").on("click", function(){
            console.log("343430");
            if($(this).parent().hasClass("active-submenu")){                
                $(this).parent().parent().addClass("short-after-line");
            }else{                
                $(this).parent().parent(".submenu").removeClass("short-after-line");
            }; 
            $(this).siblings(".submenu-dropdown-toggle").slideToggle("slow");            
            $(this).parent().siblings().find(".submenu-dropdown-toggle").slideUp("slow");            
            $(this).parent().siblings(".active-submenu").removeClass("active-submenu");
            $(this).parent().toggleClass("active-sub");
            $(this).parent().siblings().removeClass("active-sub");
            $(this).children(".arrow").toggleClass("fa-angle-right").toggleClass("fa-angle-down");
            $(this).parent().siblings().find(".arrow").removeClass("fa-angle-down").addClass("fa-angle-right");
        });
    </script>
<script>
       $(".dropdown-toggle").on("click", function(){
           if ($(".nav-list").hasClass("menu-close")) {
               $(this).parent().siblings().find(".submenu").slideUp("fast");
           }
       });       
    </script>
    <script>
        $(document).ready(function(){
            $(".dropdown-toggle-submenu").on("click", function(){
            if($("li").hasClass("active")){
                $(this).children().children(".arrow").addClass("fa-angle-down").removeClass("fa-angle-right");
            }else{
                return false;
            }
            });
        });
        
    </script>
</div>    
