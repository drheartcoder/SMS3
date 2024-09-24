<?php 

     $segment2       = Request::segment(2);
     $segment3       = Request::segment(3);
     $segment4       = Request::segment(4);

?>

 <div id="left-bar">

    <!-- BEGIN Container -->
    <div class="container sidebar-navy_blue" id="main-container">
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
                    


                    <li class="<?php  if($segment2 == 'dashboard'){ echo 'active'; } ?>">
                        <a href="{{url($school_admin_panel_slug.'/dashboard')}}" title="{{translation('dashboard')}}">
                            <span class="icon-dash"><i class="fa fa-dashboard faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('dashboard')}}</span>
                        </a>
                    </li> 
                     

                    @if(array_key_exists('calendar.update', $arr_current_user_access) || array_key_exists('calendar.list', $arr_current_user_access))
                    <li class="<?php  if($segment2 == 'calendar'){ echo 'active'; } ?>">
                        <a href="{{url($school_admin_panel_slug.'/calendar')}}" title="{{translation('calendar')}}">
                            <span class="icon-dash"><i class="fa fa-calendar faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('calendar')}}</span>
                        </a>
                    </li>
                    @endif  

                    <li class="<?php  if($segment2 == 'school' || $segment2 == 'role' || $segment3 == 'payment_settings' || $segment2 == 'fees_structure' || $segment2 == 'level_class' || $segment2 == 'exam_type' || $segment2 == 'exam_period' || $segment2 == 'assessment_scale' || $segment2 == 'room' || $segment2 == 'academic_year' || $segment2 == 'notification_settings' || $segment2 == 'brotherhood' || $segment2 == 'educational_board'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('school_setup')}}">
                            <span class="icon-dash"><i class="fa fa-cog  faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('school_setup')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">
                            @if(array_key_exists('school_profile.update', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'school'){ echo 'active'; } ?>">
                                <a href="{{url($school_admin_panel_slug.'/school')}}" title="{{translation('school_profile')}}">
                                    <span class="icon-dash"><i class="fa fa-university  faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('school_profile')}}</span>
                                </a>
                            </li>
                            @endif

                            @if(array_key_exists('role.list', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'role'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('role')}}">
                                    <span class="icon-dash"><i class="fa fa-user faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('role')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                 <ul class="submenu-dropdown-toggle childmenu">
                                    <li style="display: block;" class="<?php  if($segment2 == 'role' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/role')}}">{{translation('manage')}} </a></li>
                                    @if(array_key_exists('role.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment2 == 'role' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/role/create')}}">{{translation('add')}} </a></li>   
                                    
                                    @endif                         
                                </ul>
                            </li>
                            @endif

                            <li class="<?php  if($segment3 == 'payment_settings' || $segment2 == 'fees_structure'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('payment_setup')}}">
                                    <span class="icon-dash"><i class="fa fa-money faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('payment_setup')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                    @if(array_key_exists('payment_settings.list', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment3 == 'payment_settings'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/setting/payment_settings')}}" title="{{translation('payment_settings')}}">{{translation('payment_settings')}} </a></li>
                                    @endif
                                    @if(array_key_exists('fees_structure.list', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment2 == 'fees_structure'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/fees_structure')}}" title="{{translation('payment_management')}}">{{translation('payment_management')}} </a></li>   
                                    @endif   

                                </ul>
                            </li>

                            @if(array_key_exists('level_class.list', $arr_current_user_access))

                            <li class="<?php  if($segment2 == 'level_class'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('level_class')}}">
                                    <span class="icon-dash"><i class="fa fa-server faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('level_class')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                 <ul class="submenu-dropdown-toggle childmenu">
                                        <li style="display: block;" class="<?php  if($segment2 == 'level_class' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/level_class/')}}">{{translation('manage_school_level_class')}} </a></li>
                                        @if(array_key_exists('level_class.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'level_class' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/level_class/create')}}">{{translation('add_school_level_class')}} </a></li>   
                                        @endif    
                                        <li style="display: block;" class="<?php  if($segment2 == 'level_class' && $segment3 == 'manage_new_classes'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/level_class/manage_new_classes')}}">{{translation('manage_new_classes')}} </a></li>
                                        @if(array_key_exists('level_class.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'level_class' && $segment3 == 'add_class'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/level_class/add_class')}}">{{translation('add_new_class')}} </a></li>   
                                        @endif                                              
                                    </ul>
                            </li>
                            @endif    

                            @if(array_key_exists('exam_type.list', $arr_current_user_access) || array_key_exists('exam_period.list', $arr_current_user_access))
                                <li class="<?php  if($segment2 == 'exam_type' || $segment2 == 'exam_period'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('level')}}" >
                                        <span class="icon-dash"><i class="fa fa-book faa-vertical animated-hover"></i></span>
                                        <span>{{translation('exam_setup')}}</span>
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                     <ul class="submenu-dropdown-toggle childmenu">
                                            @if(array_key_exists('exam_type.list', $arr_current_user_access))
                                            <li style="display: block;" class="<?php  if($segment2 == 'exam_type'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/exam_type')}}">{{translation('exam_type')}} </a></li>
                                            @endif
                                            @if(array_key_exists('exam_period.list', $arr_current_user_access))
                                            <li style="display: block;" class="<?php  if($segment2 == 'exam_period'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/exam_period')}}">{{translation('exam_period')}} </a></li>   
                                            @endif                         
                                        </ul>
                                </li>
                            @endif

                            @if(array_key_exists('assessment_scale.list', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'assessment_scale'){ echo 'active'; } ?>">
                                    <a href="{{url($school_admin_panel_slug.'/assessment_scale')}}" title="{{translation('assessment_scale')}}">
                                    <span class="icon-dash"><i class="fa fa-line-chart faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('assessment_scale')}}</span>
                                </a>
                            </li>
                            @endif 

                            @if(array_key_exists('room_management.list', $arr_current_user_access) || array_key_exists('room_assignment.list', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'room'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('room_management')}}">
                                    <span class="icon-dash"><i class="fa fa-home faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('room_management')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">

                                    @if(array_key_exists('room_management.list', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment2 == 'room' && $segment3 == 'management' && $segment4 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/room/management')}}">{{translation('manage')}} </a></li>
                                    @endif
                                    @if(array_key_exists('room_management.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment2 == 'room' && $segment3 == 'management' && $segment4 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/room/management/create')}}">{{translation('add')}} </a></li>
                                    @endif
                                    @if(array_key_exists('room_assignment.list', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment2 == 'room' && $segment3 == 'assignment' && $segment4 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/room/assignment')}}">{{translation('assigned_rooms')}} </a></li>   
                                    @endif
                                    @if(array_key_exists('room_assignment.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment2 == 'room' && $segment3 == 'assignment' && $segment4 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/room/assignment/create')}}">{{translation('assign_room')}} </a></li>   
                                    @endif
                                       
                                </ul>
                            </li>
                            @endif

                            @if(array_key_exists('academic_year.list', $arr_current_user_access))
                            <li class="<?php  if(Request::segment(2) == 'academic_year'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('academic_year')}}">
                                    <span class="icon-dash"><i class="fa fa-calendar faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('academic_year')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">

                                        @if(array_key_exists('academic_year.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'academic_year' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/academic_year')}}">{{translation('manage')}} </a></li>
                                        @endif
                                        @if(array_key_exists('academic_year.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'academic_year' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/academic_year/create')}}">{{translation('add')}} </a></li>   
                                        @endif
                                </ul>
                            </li>
                            @endif


                            @if(array_key_exists('notification_settings.update', $arr_current_user_access) || array_key_exists('notification_settings.list', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'notification_settings'){ echo 'active'; } ?>">
                                <a href="{{url($school_admin_panel_slug.'/notification_settings')}}" title="{{translation('notification_settings')}}">
                                    <span class="icon-dash"><i class="fa fa-bell faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('notification_settings')}}</span>
                                </a>
                            </li> 
                            @endif

                            @if(array_key_exists('brotherhood.list', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'brotherhood'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('brotherhood')}}">
                                    <span class="icon-dash"><i class="fa fa-user-o faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('brotherhood')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">

                                    @if(array_key_exists('brotherhood.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'brotherhood' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/brotherhood/')}}">{{translation('manage')}} </a>
                                        </li>
                                    @endif
                                    @if(array_key_exists('brotherhood.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'brotherhood' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/brotherhood/create')}}">{{translation('add')}} </a></li>   
                                    @endif
                                       
                                </ul>
                            </li> 
                            @endif

                            @if(array_key_exists('educational_board.list', $arr_current_user_access))
                            <li class="<?php  if(Request::segment(2) == 'educational_board'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('educational_board')}}">
                                    <span class="icon-dash"><i class="fa fa-book faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('educational_board')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>
                                <ul class="submenu-dropdown-toggle childmenu">
                                   <li style="display: block;" class="<?php  if($segment2 == 'educational_board' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/educational_board')}}">{{translation('manage')}} </a></li>
                                   @if(array_key_exists('educational_board.create', $arr_current_user_access))
                                   <li style="display: block;" class="<?php  if($segment2 == 'educational_board' && $segment3 == 'create'){ echo 'active'; } ?>" ><a href="{{ url($school_admin_panel_slug.'/educational_board/create')}}">{{translation('add')}}</a></li>
                                   @endif
                                </ul>
                            </li>  
                            @endif

                        </ul>
                    </li>

                    <li class="<?php  if($segment2 == 'canteen' || $segment2 == 'canteen_products' ||  $segment2 == 'canteen_bookings' || $segment2 == 'weekly_meals' || $segment2 == 'daily_meals' || $segment2 == 'library' || $segment2 == 'club' || $segment2 == 'transactions' ||  $segment2 == 'stock'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('school_management')}}">
                            <span class="icon-dash"><i class="fa fa-wrench  faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('school_management')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">
                            @if(array_key_exists('canteen.list', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'canteen' || $segment2 == 'canteen_products' || $segment2 == 'canteen_bookings' || $segment2 == 'weekly_meals' || $segment2 == 'daily_meals' ){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('canteen')}}">
                                    <span class="icon-dash"><i class="fa fa-cutlery faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('canteen')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                       
                                        <li style="display: block;" class="<?php  if($segment2 == 'canteen_products' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/canteen_products')}}">{{translation('manage')}} {{translation('canteen_products')}} </a></li>

                                        @if(array_key_exists('canteen.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'canteen_products' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/canteen_products/create')}}">{{translation('add')}} {{translation('canteen_product')}} </a>
                                        </li>  
                                        @endif  

                                        <li style="display: block;" class="<?php  if($segment2 == 'weekly_meals' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/weekly_meals')}}">{{translation('manage')}} {{translation('weekly_meal')}} </a></li>

                                        @if(array_key_exists('canteen.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'weekly_meals' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/weekly_meals/create')}}">{{translation('add')}} {{translation('weekly_meal')}} </a>
                                        </li>  
                                        @endif

                                        <li style="display: block;" class="<?php  if($segment2 == 'daily_meals' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/daily_meals')}}">{{translation('manage')}} {{translation('daily_meal')}} </a></li>
                                        @if(array_key_exists('canteen.create', $arr_current_user_access))
                                         <li style="display: block;" class="<?php  if($segment2 == 'daily_meals' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/daily_meals/create')}}">{{translation('add')}} {{translation('daily_meal')}} </a></li>
                                         @endif

                                        @if(array_key_exists('canteen_bookings.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'canteen_bookings'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/canteen_bookings')}}">{{translation('canteen_bookings')}} </a></li>
                                        @endif         
                                </ul>  
                            </li>
                            @endif

                            @if(array_key_exists('stocks.list', $arr_current_user_access))
                             <li class="<?php  if(Request::segment(2) == 'stock'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('stock')}}">
                                    <span class="icon-dash"><i class="fa fa-cubes faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('stock')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                  
                                    <li style="display: block;" class="<?php  if($segment2 == 'stock' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug)}}/stock">{{translation('manage')}} {{translation('stock')}} </a></li> 

                                    @if(array_key_exists('stocks.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'stock' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/stock/create')}}">{{translation('add')}} {{translation('stock')}} </a></li>
                                    @endif

                                    <li style="display: block;" class="<?php  if($segment2 == 'stock' && $segment3 == 'stock_distribution' && $segment4==''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug)}}/stock/stock_distribution">{{translation('manage')}} {{translation('stock_distribution')}} </a></li> 

                                    @if(array_key_exists('stocks.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'stock' && $segment3 == 'stock_distribution' && $segment4=='create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/stock/stock_distribution/create')}}">{{translation('add')}} {{translation('stock_distribution')}} </a></li>
                                    @endif

                                </ul>
                            </li>  
                            @endif

                            @if(array_key_exists('library.list', $arr_current_user_access) || array_key_exists('room_assignment.list', $arr_current_user_access))
                            <li class="<?php  if(Request::segment(2) == 'library'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" >
                                    <span class="icon-dash"><i class="fa fa-university faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('library')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">

                                        @if(array_key_exists('library.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'library' && Request::segment(3) == 'books_category'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/library/books_category')}}">{{translation('book_category')}} </a></li>
                                        @endif
                                        @if(array_key_exists('library.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'library' && Request::segment(3) == 'manage_library_contents'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/library/manage_library_contents')}}">{{translation('manage_library_content')}} </a></li>
                                        @if(array_key_exists('library.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'library' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/library/create')}}">{{translation('add')}} {{translation('library_content')}} </a></li>
                                        @endif
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'library' && Request::segment(3) == 'issue_book'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/library/issue_book')}}">{{translation('issue_book')}} </a></li> 
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'library' && Request::segment(3) == 'return_book'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/library/return_book')}}">{{translation('reissuereturn_books')}} </a></li>   
                                        @endif
                                       
                                </ul>
                            </li>
                            @endif

                            @if(array_key_exists('club.list', $arr_current_user_access))
                             <li class="<?php  if(Request::segment(2) == 'club'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('club')}}">
                                    <span class="icon-dash"><i class="fa fa-users faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('club')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                  
                                    <li style="display: block;" class="<?php  if(Request::segment(2) == 'club' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug)}}/club">{{translation('manage')}} </a></li> 
                                    @if(array_key_exists('club.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if(Request::segment(2) == 'club' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/club/create')}}">{{translation('add')}} </a></li>
                                    @endif
                                </ul>
                            </li>  
                            @endif

                            @if(array_key_exists('payment.list', $arr_current_user_access))
           
                            <li class="<?php  if($segment2 == 'transactions'){ echo 'active'; } ?>">
                                <a href="{{ url($school_admin_panel_slug.'/transactions')}}" class="dropdown-toggle" title="{{translation('payment')}}">
                                    <span class="icon-dash"><i class="fa fa-exchange faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('transactions')}}</span>
                                </a>
                            </li>

                            @endif
                        </ul>
                    </li>        


                    <li class="<?php  if($segment2 == 'student' || $segment2 == 'professor' || $segment2 == 'parent' || $segment2 == 'employee' || $segment2 == 'professor_replacement'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('user_management')}}">
                            <span class="icon-dash"><i class="fa fa-users  faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('user_management')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">

                            @if(array_key_exists('student.list', $arr_current_user_access))

                            <li class="<?php  if($segment2 == 'student' || $segment2 == 'payment'){ echo 'active'; } ?>">
                                <a href="{{url($school_admin_panel_slug.'/student')}}" title="{{translation('student')}}">
                                    <span class="icon-dash"><i class="fa fa-user-o faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('student')}}</span>
                                </a>
                            </li>
                            @endif

                            @if(array_key_exists('professor.list', $arr_current_user_access))

                            <li class="<?php  if($segment2 == 'professor' || $segment2 == 'professor_replacement'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('professor')}}">
                                    <span class="icon-dash"><i class="fa fa-user faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('professor')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                 <ul class="submenu-dropdown-toggle childmenu">
                                    <li style="display: block;" class="<?php  if($segment2 == 'professor' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/professor')}}">{{translation('manage')}} </a></li>
                                    @if(array_key_exists('professor.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment2 == 'professor' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/professor/create')}}">{{translation('add')}} </a></li>
                                    <li style="display: block;" class="<?php  if($segment2 == 'professor_replacement'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/professor_replacement')}}">{{translation('replacement')}} </a></li>   
                                    
                                    @endif                         
                                </ul>
                            </li>
                            @endif

                            @if(array_key_exists('parent.list', $arr_current_user_access))

                            <li class="<?php  if($segment2 == 'parent'){ echo 'active'; } ?>">
                                <a href="{{url($school_admin_panel_slug.'/parent')}}" title="{{translation('parent')}}">
                                    <span class="icon-dash"><i class="fa fa-user-secret faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('parent')}}</span>
                                </a>
                            </li>
                            @endif

                            @if(array_key_exists('employee.list', $arr_current_user_access))

                            <li class="<?php  if($segment2 == 'employee'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" >
                                    <span class="icon-dash"><i class="fa fa-user-circle-o faa-vertical animated-hover" title="{{translation('employee')}}"></i></span>
                                    <span class="mobile-nones">{{translation('employeestaff')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>
                                 <ul class="submenu-dropdown-toggle childmenu">
                                        <li style="display: block;" class="<?php  if($segment2 == 'employee' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/employee')}}">{{translation('manage')}} </a></li>
                                        @if(array_key_exists('employee.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'employee' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/employee/create')}}">{{translation('add')}} </a></li>   
                                        @endif                         
                                    </ul>
                            </li>
                            @endif

                        </ul>
                    </li>        

                    <li class="<?php   if($segment2 == 'admission_config' || $segment2 == 'timetable'||$segment2 == 'transport_bus' || $segment2 == 'course' || $segment2 == 'assign_courses' || $segment2 == 'course_material' || $segment2 == 'attendance' || $segment2 == 'exam' || $segment2 == 'student_behaviour' || $segment2 == 'task' || $segment2 == 'gradebook' || $segment2 == 'gradebook_fields'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('features')}}">
                            <span class="icon-dash"><i class="fa fa-cog  faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('features')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">
                    

                            @if(array_key_exists('admission_config.list', $arr_current_user_access))

                            <li class="<?php  if($segment2 == 'admission_config'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('admission')}}">
                                    <span class="icon-dash"><i class="fa fa-cog  faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('admission')}}</span>
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                    <li style="display: block;" class="<?php  if($segment2 == 'admission_config' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/admission_config')}}">{{translation('admission_configuration')}} </a></li>
                                    @if(array_key_exists('admission.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment2 == 'admission_config' && $segment3 == 'new_admission'){ echo 'active'; } ?>">
                                        <a href="{{ url($school_admin_panel_slug.'/admission_config/new_admission')}}">{{translation('new_admission')}} </a>
                                    </li>
                                    @endif                         
                                </ul>
                            @endif

                            @if(array_key_exists('timetable.list', $arr_current_user_access))
                             <li class="<?php  if(Request::segment(2) == 'timetable'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('timetable')}}">
                                    <span class="icon-dash"><i class="fa fa-clock-o faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('timtable')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                    <li style="display: block;" class="<?php  if($segment2 == 'timetable' && $segment3 == 'summary'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/timetable/summary')}}">{{translation('summary')}} </a></li>
                                    @if(array_key_exists('timetable.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment2 == 'timetable' && $segment3 == 'new'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug)}}/timetable/new">{{translation('new')}} {{translation('timetable')}}</a></li>
                                    @endif
                                    <li style="display: block;" class="<?php  if($segment2 == 'timetable' && $segment3 == 'edit'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug)}}/timetable/edit">{{translation('exisitng')}} {{translation('timetable')}}</a></li>
                                    @if(array_key_exists('timetable.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if($segment2 == 'timetable' && $segment3 == 'teaching_hours'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/timetable/teaching_hours')}}">{{translation('teaching_hours')}} </a></li>
                                    @endif
                                </ul>
                            </li>  
                            @endif

                            @if(array_key_exists('transport_bus.list', $arr_current_user_access))
                             <li class="<?php  if(Request::segment(2) == 'transport_bus'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('transport_bus')}}">
                                    <span class="icon-dash"><i class="fa fa-bus faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('transport_bus')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                  
                                    <li style="display: block;" class="<?php  if($segment2 == 'transport_bus' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug)}}/transport_bus">{{translation('manage')}} </a></li> 
                                    <li style="display: block;" class="<?php  if($segment2 == 'transport_bus' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/transport_bus/create')}}">{{translation('add')}} </a></li>
                                </ul>
                            </li>  
                            @endif

                            @if(array_key_exists('course.list',$arr_current_user_access ))
                            <li class="<?php  if(Request::segment(2) == 'course' ||   $segment2 == 'assign_courses' || $segment2 == 'course_material' ){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('course')}}">
                                    <span class="icon-dash"><i class="fa fa-file-code-o faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('course')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                    <li style="display: block;" class="<?php  if(Request::segment(2) == 'course' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/course')}}">{{translation('course_management')}} </a></li>     

                                    @if(array_key_exists('course_assignement.list', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if( $segment2 == 'assign_courses'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/assign_courses')}}" >{{translation('course_assignment')}} </a></li>   
                                    @endif
                                    @if(array_key_exists('course_material.list', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if( $segment2 == 'course_material'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/course_material')}}" >{{translation('material_management')}} </a></li>   
                                    @endif                         
                                </ul>
                            </li>
                            @endif

                            @if(array_key_exists('attendance.list',$arr_current_user_access ))
                            <li class="<?php  if(Request::segment(2) == 'attendance'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('attendance')}}">
                                    <span class="icon-dash"><i class="fa fa-cc-diners-club faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('attendance')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                 <ul class="submenu-dropdown-toggle childmenu">
                                        @if(array_key_exists('attendance.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'attendance' && Request::segment(3) == 'student'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/attendance/student')}}">{{translation('student')}} </a></li> 
                                        @endif
                                        @if(array_key_exists('attendance.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'attendance' && Request::segment(3) == 'professor'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/attendance/professor')}}">{{translation('professor')}} </a></li>   
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'attendance' && Request::segment(3) == 'employee'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/attendance/employee')}}">{{translation('employee')}} </a></li>   
                                        @endif

                                    </ul>
                            </li>
                            @endif

                            @if(array_key_exists('exam.list', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'exam'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('exam')}}">
                                    <span class="icon-dash"><i class="fa fa-book faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('exam')}}</span>
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">

                                        @if(array_key_exists('exam.list', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'exam' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/exam')}}">{{translation('manage')}} </a></li>
                                        @endif
                                        @if(array_key_exists('exam.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if(Request::segment(2) == 'exam' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/exam/create')}}">{{translation('add')}} </a></li>   
                                        @endif
                                       
                                </ul>
                            </li> 
                            @endif

                            @if(array_key_exists('student_behaviour.list', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'student_behaviour'){ echo 'active'; } ?>">
                                    <a href="{{url($school_admin_panel_slug.'/student_behaviour')}}" title="{{translation('student_behaviour')}}">
                                    <span class="icon-dash"><i class="fa fa-file faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('student_behaviour')}}</span>
                                </a>
                            </li>
                            @endif

                            @if(array_key_exists('task.list', $arr_current_user_access))
                            <li class="<?php  if(Request::segment(2) == 'task'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('task')}}">
                                    <span class="icon-dash"><i class="fa fa-tasks faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('task')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                  
                                    <li style="display: block;" class="<?php  if(Request::segment(2) == 'task' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug)}}/task">{{translation('manage')}} </a></li>
                                    @if(array_key_exists('task.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if(Request::segment(2) == 'task' && Request::segment(3) == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/task/create')}}">{{translation('add')}} </a></li>
                                    @endif
                                </ul>
                            </li>  
                            @endif

                            @if(array_key_exists('gradebook.list', $arr_current_user_access))
                            <li class="<?php  if(Request::segment(2) == 'gradebook' || Request::segment(2) == 'gradebook_fields'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('gradebook')}}">
                                    <span class="icon-dash"><i class="fa fa-book faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('gradebook')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                    
                                    @if(array_key_exists('gradebook_fields.list', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if(Request::segment(2) == 'gradebook_fields' && Request::segment(3) == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/gradebook_fields')}}">{{translation('fields')}} </a></li>
                                    @endif
                                    @if(array_key_exists('gradebook.create', $arr_current_user_access))
                                    <li style="display: block;" class="<?php  if(Request::segment(2) == 'gradebook'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/gradebook')}}">{{translation('manage')}} </a></li>   
                                    @endif
                                </ul>   
                            </li>
                            @endif        

                        </ul>
                    </li>

                    <li class="<?php   if($segment2 == 'email_template' || $segment2 == 'sms_template' || $segment2 == 'survey' || $segment2 == 'suggestions' || $segment2 == 'news' || $segment2 == 'claim'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('features')}}">
                            <span class="icon-dash"><i class="fa fa-comments-o  faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('communication')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">

                            @if(array_key_exists('email_template.list', $arr_current_user_access))
           
                            <li class="<?php  if($segment2 == 'email_template'){ echo 'active'; } ?>">
                                <a href="{{ url($school_admin_panel_slug.'/email_template')}}" class="dropdown-toggle" title="{{translation('email_template')}}">
                                    <span class="icon-dash"><i class="fa fa-envelope faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('email_template')}}</span>
                                </a>
                            </li>

                            @endif
                          
                            
                            @if(array_key_exists('sms_template.list', $arr_current_user_access))
                   
                            <li class="<?php  if($segment2 == 'sms_template'){ echo 'active'; } ?>">
                                <a href="{{ url($school_admin_panel_slug.'/sms_template')}}" class="dropdown-toggle" title="{{translation('sms_template')}}">
                                    <span class="icon-dash"><i class="fa fa-mobile faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('sms_template')}}</span>
                                </a>
                            </li>

                            @endif

                            @if(array_key_exists('survey.list', $arr_current_user_access))
                            <li class="<?php  if(Request::segment(2) == 'survey'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('survey')}}">
                                    <span class="icon-dash"><i class="fa fa-bar-chart faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('survey')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>
                                <ul class="submenu-dropdown-toggle childmenu">
                                   <li style="display: block;" class="<?php  if($segment2 == 'survey' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/survey')}}">{{translation('manage')}} </a></li>
                                   <li style="display: block;" class="<?php  if($segment2 == 'survey' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/survey/create')}}">{{translation('add')}}</a></li>
                                </ul>
                            </li>  
                            @endif

                            @if(array_key_exists('suggestions.list', $arr_current_user_access))
           
                            <li class="<?php  if($segment2 == 'suggestions'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('suggestions')}}">
                                    <span class="icon-dash"><i class="fa fa-dropbox faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('suggestions')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>
                                 <ul class="submenu-dropdown-toggle childmenu">
                                @if($role != config('app.project.role_slug.school_admin_role_slug'))
                                    <li style="display: block;"><a href="{{ url($school_admin_panel_slug.'/suggestions/create')}}">{{translation('create')}} {{translation('suggestion')}} </a></li>

                                    <li style="display: block;"><a href="{{ url($school_admin_panel_slug.'/suggestions/employee_suggestions/manage')}}">{{translation('manage')}} {{translation('suggestions')}} </a></li>

                                    <li style="display: block;"><a href="{{ url($school_admin_panel_slug.'/suggestions/employee_suggestions/poll_raised')}}">{{translation('manage')}} {{translation('poll_raised')}} {{translation('suggestions')}} </a></li>
                                @else  
                                   
                                   <li style="display: block;" class="<?php  if($segment2 == 'suggestions' && $segment3 == 'requested'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/suggestions/requested')}}">{{translation('suggestion_requests')}} </a></li>
                                   <li style="display: block;" class="<?php  if($segment2 == 'suggestions' && $segment3 == 'approved'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/suggestions/approved')}}">{{translation('approved_suggestions')}} </a></li>
                                   <li style="display: block;" class="<?php  if($segment2 == 'suggestions' && $segment3 == 'poll_raised'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/suggestions/poll_raised')}}">{{translation('poll_raised')}} {{translation('suggestions')}} </a></li>
                                @endif 
                                </ul>
                            </li>
                            @endif

                            @if(array_key_exists('news.list', $arr_current_user_access))
                            <li class="<?php  if(Request::segment(2) == 'news'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('news')}}">
                                    <span class="icon-dash"><i class="fa fa-newspaper-o faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('news')}}</span>    
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>
                                <ul class="submenu-dropdown-toggle childmenu">
                                   <li style="display: block;" class="<?php  if($segment2 == 'news' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/news')}}">{{translation('manage')}} </a></li>
                                   @if(array_key_exists('news.create', $arr_current_user_access))
                                   <li style="display: block;" class="<?php  if($segment2 == 'news' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/news/create')}}">{{translation('add')}} </a></li>
                                   @endif
                                </ul>
                            </li>  
                            @endif

                            @if(array_key_exists('claim.list', $arr_current_user_access))

                            <li class="<?php  if($segment2 == 'claim'){ echo 'active'; } ?>">
                                <a href="{{url($school_admin_panel_slug.'/claim')}}" title="{{translation('claim')}}">
                                    <span class="icon-dash"><i class="fa fa-file faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('claim')}}</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>            

                    
                    
                    @if(array_key_exists('contact_support.list', $arr_current_user_access))
                    <li class="<?php  if($segment2 == 'contact_support'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('contact_support')}}">
                            <span class="icon-dash"><i class="fa fa-info-circle faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('contact_support')}}</span>    
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">

                            @if(array_key_exists('contact_support.list', $arr_current_user_access))
                                <li style="display: block;" class="<?php  if($segment2 == 'contact_support' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/contact_support/')}}">{{translation('manage')}} </a>
                                </li>
                            @endif
                            @if(array_key_exists('contact_support.create', $arr_current_user_access))
                                <li style="display: block;" class="<?php  if($segment2 == 'contact_support' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($school_admin_panel_slug.'/contact_support/create')}}">{{translation('add')}} </a></li>   
                            @endif
                               
                        </ul>
                    </li>
                    @endif
                  </ul>
            </div>

            <!-- END Navlist -->            
        </div>
        <!-- END Sidebar -->
        <!-- END Content -->
    </div>
        <!-- END Container -->
        
<!-- Add Class SideBar Start-->
        
      <script>
    $( function() {
        $( "#arrowSide" ).on( "click", function() {
          $( ".navbar-collapse" ).toggleClass( "sidebarmain-admin", 1000);
        });
       $( ".right-arrow-section" ).on( "click", function() {
          $( ".navbar-collapse" ).toggleClass( "sidebarmain-admin", 1000);
        });
      });   
  </script>
    <!-- Add Class SideBar End-->   
        <!-- Scroll Start Here -->
        <link href="{{url('/')}}/css/admin/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
        <script src="{{url('/')}}/js/admin/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript">
            /*scrollbar start*/
            
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
            
        </script>
        <!-- Scroll End Here -->

        <script type="text/javascript">
            /*scrollbar start*/
            
                $(window).on("load", function() {
                    $.mCustomScrollbar.defaults.scrollButtons.enable = true; //enable scrolling buttons by default
                    $.mCustomScrollbar.defaults.axis = "yx"; //enable 2 axis scrollbars by default
                    $(".content-test").mCustomScrollbar({
                        theme: "dark"
                    });
                    
                    

                });
            
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
</div>    
