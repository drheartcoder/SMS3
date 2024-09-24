<?php 

     $admin_path     = config('app.project.admin_panel_slug');
     $segment2       = Request::segment(2);
     $segment3       = Request::segment(3);

?>
            <div id="left-bar">
            <div id="main-container"  class="container sidebar-navy_blue">
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
                        <a href="{{ url('/').'/'.$admin_path.'/dashboard'}}" title="{{translation('dashboard')}}">
                            <span class="icon-dash"><i class="fa fa-dashboard faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('dashboard')}}</span>
                        </a>
                    </li>

                    <li class="<?php  if($segment2 == 'site_settings' || $segment2 == 'membership_plans' || $segment3 == 'payment_settings' || $segment2 == 'static_pages' || $segment2 == 'exam_period' || $segment2 == 'exam_type' || $segment2 == 'notification_modules' || $segment2 == 'role' || $segment2 == 'school_template' || $segment2 == 'level' || $segment2 == 'fees' || $segment2 == 'keyword_translation' || $segment2 == 'enquiry_category'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('setup')}}">
                            <span class="icon-dash"><i class="fa fa-cog faa-vertical animated-hover"></i></span>
                            <span class="click-name-hide">{{translation('setup')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                                @if(array_key_exists('site_settings.update', $arr_current_user_access))
                                <li class="<?php  if($segment2 == 'site_settings'){ echo 'active'; } ?>">
                                    <a href="{{ url($admin_panel_slug.'/site_settings') }}" title="{{translation('site_settings')}}" >
                                        <span class="icon-dash"><i class="fa  fa-wrench faa-vertical animated-hover"></i></span>
                                        <span class="mobile-nones">{{translation('site_settings')}}</span>
                                    </a>
                                </li>
                                @endif
                                 
                                
                                @if(array_key_exists('static_pages.list', $arr_current_user_access))
                                <li class="<?php  if($segment2 == 'static_pages'){ echo 'active'; } ?>">
                                    <a href="javascript:void(0)" class="dropdown-toggle-submenu"  title="{{translation('cms')}}">
                                        <span class="icon-dash"> <i class="fa fa-sitemap faa-vertical animated-hover"></i></span>
                                        <span>{{translation('cms')}}</span>
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                     <ul class="submenu-dropdown-toggle childmenu">
                                        <li style="display: block;" class="<?php  if($segment2 == 'static_pages' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/static_pages')}}">{{translation('manage')}} </a></li>
                                        @if(array_key_exists('static_pages.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'static_pages' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/static_pages/create')}}">{{translation('add')}} </a></li>   
                                        @endif                         
                                    </ul>
                                </li>
                                @endif

                                @if(array_key_exists('membership_plans.list', $arr_current_user_access))
                                <li class="<?php  if($segment2 == 'membership_plans'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('membership_plans')}}" >
                                        <span class="icon-dash"><i class="fa fa-bar-chart faa-vertical animated-hover"></i></span>
                                        <span>{{translation('membership_plans')}}</span>
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                     <ul class="submenu-dropdown-toggle childmenu">
                                            <li style="display: block;" class="<?php  if($segment2 == 'membership_plans' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/membership_plans')}}">{{translation('manage')}} </a></li>
                                            @if(array_key_exists('membership_plans.create', $arr_current_user_access))
                                            <li style="display: block;" class="<?php  if($segment2 == 'membership_plans' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/membership_plans/create')}}">{{translation('add')}} </a></li>   
                                            @endif                         
                                        </ul>
                                </li> 
                                @endif

                                @if(array_key_exists('school_template.list', $arr_current_user_access))
                                <li class="<?php  if($segment2 == 'school_template'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('school_template')}}">
                                        <span class="icon-dash"><i class="fa fa-server faa-vertical animated-hover"></i></span>
                                        <span>{{translation('school_template')}}</span>
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                     <ul class="submenu-dropdown-toggle childmenu">
                                            <li style="display: block;" class="<?php  if($segment2 == 'school_template' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/school_template')}}">{{translation('manage')}} </a></li>
                                            @if(array_key_exists('school_template.create', $arr_current_user_access))
                                            <li style="display: block;" class="<?php  if($segment2 == 'school_template' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/school_template/create')}}">{{translation('add')}} </a></li>   
                                            <li style="display: block;" class="<?php  if($segment2 == 'view' && $segment3 == 'view'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/school_template/view')}}">{{translation('view')}} </a></li>  
                                            @endif                         
                                        </ul>
                                </li>
                                @endif

                                @if(array_key_exists('notification_modules.list', $arr_current_user_access))
           
                                <li class="<?php  if($segment2 == 'notification_modules'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('notification_modules')}}" >
                                        <span class="icon-dash"><i class="fa fa-bell faa-vertical animated-hover"></i></span>
                                        <span>{{translation('notification_modules')}}</span>
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                     <ul class="submenu-dropdown-toggle childmenu">
                                            <li style="display: block;" class="<?php  if($segment2 == 'notification_modules' && $segment3==config('app.project.role_slug.school_admin_role_slug')){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/notification_modules/'.config('app.project.role_slug.school_admin_role_slug'))}}">{{translation('school_admin')}}</a></li>
                                            <li style="display: block;" class="<?php  if($segment2 == 'notification_modules'  && $segment3==config('app.project.role_slug.professor_role_slug')){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/notification_modules/'.config('app.project.role_slug.professor_role_slug'))}}">{{translation('professor')}} </a></li>
                                            <li style="display: block;" class="<?php  if($segment2 == 'notification_modules' && $segment3==config('app.project.role_slug.student_role_slug')){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/notification_modules/'.config('app.project.role_slug.student_role_slug'))}}">{{translation('student')}} </a></li>
                                            <li style="display: block;" class="<?php  if($segment2 == 'notification_modules' && $segment3==config('app.project.role_slug.parent_role_slug')){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/notification_modules/'.config('app.project.role_slug.parent_role_slug'))}}">{{translation('parent')}} </a></li>                 
                                        </ul>
                                </li>
                                @endif

                                @if(array_key_exists('role.list', $arr_current_user_access))
           
                                <li class="<?php  if($segment2 == 'role'){ echo 'active'; } ?>">
                                        <a href="javascript:void(0)" class="dropdown-toggle-submenu"  title="{{translation('role')}}">
                                            <span class="icon-dash"> <i class="fa  fa-user faa-vertical animated-hover"></i></span>
                                            <span>{{translation('role')}}</span>
                                            <b class="arrow fa fa-angle-right"></b>
                                        </a>

                                         <ul class="submenu-dropdown-toggle childmenu">
                                            <li style="display: block;" class="<?php  if($segment2 == 'role' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/role')}}">{{translation('manage')}} </a></li>
                                            @if(array_key_exists('role.create', $arr_current_user_access))
                                            <li style="display: block;" class="<?php  if($segment2 == 'role' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/role/create')}}">{{translation('add')}} </a></li>   
                                            @endif                         
                                        </ul>
                                    </li>
                                @endif

                                

                                @if(array_key_exists('level.list', $arr_current_user_access))
                                <li class="<?php  if($segment2 == 'level'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('level')}}" >
                                        <span class="icon-dash"><i class="fa fa-graduation-cap faa-vertical animated-hover"></i></span>
                                        <span>{{translation('level')}}</span>
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                     <ul class="submenu-dropdown-toggle childmenu">
                                            <li style="display: block;" class="<?php  if($segment2 == 'level' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/level')}}">{{translation('manage')}} </a></li>
                                            @if(array_key_exists('level.create', $arr_current_user_access))
                                            <li style="display: block;" class="<?php  if($segment2 == 'level' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/level/create')}}">{{translation('add')}} </a></li>   
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
                                            <li style="display: block;" class="<?php  if($segment2 == 'exam_type'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/exam_type')}}">{{translation('exam_type')}} </a></li>
                                            @endif
                                            @if(array_key_exists('exam_period.list', $arr_current_user_access))
                                            <li style="display: block;" class="<?php  if($segment2 == 'exam_period'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/exam_period')}}">{{translation('exam_period')}} </a></li>   
                                            @endif                         
                                        </ul>
                                </li>
                                @endif

                                @if(array_key_exists('payment_settings.list', $arr_current_user_access))
                                 <li class="<?php  if($segment3 == 'payment_settings'){ echo 'active'; } ?>">
                                    <a href="{{ url('/').'/'.$admin_path.'/setting/payment_settings' }}" title="{{translation('payment_settings')}}" >
                                        <span class="icon-dash"><i class="fa fa-money faa-vertical animated-hover"></i></span>
                                        <span class="mobile-nones">{{translation('payment_settings')}}</span>
                                    </a>
                                </li> 
                                @endif

                                @if(array_key_exists('fees.list', $arr_current_user_access))

                                <li class="<?php  if($segment2 == 'fees'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('fees')}}" >
                                        <span class="icon-dash"><i class="fa fa-money faa-vertical animated-hover"></i></span>
                                        <span>{{translation('fees')}}</span>
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                     <ul class="submenu-dropdown-toggle childmenu">
                                            <li style="display: block;" class="<?php  if($segment2 == 'fees' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/fees')}}">{{translation('manage')}} </a></li>
                                            @if(array_key_exists('fees.create', $arr_current_user_access))
                                            <li style="display: block;" class="<?php  if($segment2 == 'fees' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/fees/create')}}">{{translation('add')}} </a></li>   
                                            @endif                         
                                    </ul>
                                </li>
                                @endif

                                @if(array_key_exists('keyword_translation.list', $arr_current_user_access))

                                <li class="<?php  if($segment2 == 'keyword_translation'){ echo 'active'; } ?>">
                                    <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('keyword_translation')}}" >
                                        <span class="icon-dash"><i class="fa fa-file-text-o faa-vertical animated-hover"></i></span>
                                        <span>{{translation('keyword_translation')}}</span>
                                        <b class="arrow fa fa-angle-right"></b>
                                    </a>

                                     <ul class="submenu-dropdown-toggle childmenu">
                                            <li style="display: block;" class="<?php  if($segment2 == 'keyword_translation' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/keyword_translation')}}">{{translation('manage')}}</a></li>
                                            @if(array_key_exists('keyword_translation.create', $arr_current_user_access))
                                            <li style="display: block;" class="<?php  if($segment2 == 'keyword_translation' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/keyword_translation/create')}}">{{translation('add')}} </a></li>   
                                            @endif                         
                                        </ul>
                                </li>
                                @endif

                                @if(array_key_exists('enquiry_category.list', $arr_current_user_access))
                       
                                <li class="<?php  if($segment2 == 'enquiry_category'){ echo 'active'; } ?>">
                                    <a href="javascript:void(0);" class="dropdown-toggle-submenu" title="{{translation('enquiry_category')}}">
                                        <span class="icon-dash"><i class="fa fa-info-circle faa-vertical animated-hover"></i></span>
                                        <span>{{translation('enquiry_category')}}</span>
                                            <b class="arrow fa fa-angle-right"></b>
                                        {{-- <span class="mobile-nones">{{translation('enquiry_category')}}</span> --}}
                                    </a>
                                    <ul class="submenu-dropdown-toggle childmenu">
                                        <li style="display: block;" class="<?php  if($segment2 == 'enquiry_category' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/enquiry_category')}}">{{translation('manage')}} </a></li>
                                        @if(array_key_exists('enquiry_category.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'enquiry_category' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/enquiry_category/create')}}">{{translation('add')}} </a></li>   
                                        @endif                         
                                    </ul>
                                </li>
                                @endif

                            </ul>
                    </li>

                    <li class="<?php  if($segment2 == 'school_admin' || $segment2 == 'users'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('setup')}}">
                            <span class="icon-dash"><i class="fa fa-users faa-vertical animated-hover"></i></span>
                            <span class="click-name-hide">{{translation('user_management')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">
                            @if(array_key_exists('school_admin.list', $arr_current_user_access))

                            <li class="<?php  if($segment2 == 'school_admin' || $segment2 == 'school'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('school_admin')}}" >
                                    <span class="icon-dash"><i class="fa fa-user faa-vertical animated-hover"></i></span>
                                    <span>{{translation('school_admin')}}</span>
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                 <ul class="submenu-dropdown-toggle childmenu">
                                        <li style="display: block;" class="<?php  if($segment2 == 'school_admin' && $segment3 == ''){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/school_admin')}}">{{translation('manage')}} </a></li>
                                        @if(array_key_exists('school.create', $arr_current_user_access))
                                        <li style="display: block;" class="<?php  if($segment2 == 'school_admin' && $segment3 == 'create'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/school_admin/create')}}">{{translation('add')}} </a></li>     
                                        @endif                         
                                    </ul>
                            </li>
                            @endif 

                            @if(array_key_exists('users.list', $arr_current_user_access))
                            <li class="<?php  if($segment2 == 'users'){ echo 'active'; } ?>">
                                <a href="JavaScript:void(0)" class="dropdown-toggle-submenu" title="{{translation('users')}}" >{{-- {{ url($admin_panel_slug.'/users')}} --}}
                                    <span class="icon-dash"><i class="fa fa-users faa-vertical animated-hover"></i></span>
                                    <span>{{translation('users')}}</span>
                                    <b class="arrow fa fa-angle-right"></b>
                                </a>

                                <ul class="submenu-dropdown-toggle childmenu">
                                        <li style="display: block;" class="<?php  if($segment2 == 'users' && $segment3 == 'parent'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/users/parent')}}">{{translation('parent')}} </a></li>
                                        <li style="display: block;" class="<?php  if($segment2 == 'users' && $segment3 == 'student'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/users/student')}}">{{translation('student')}} </a></li>
                                        <li style="display: block;" class="<?php  if($segment2 == 'users' && $segment3 == 'professor'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/users/professor')}}">{{translation('professor')}} </a></li>  
                                        <li style="display: block;" class="<?php  if($segment2 == 'users' && $segment3 == 'employee'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/users/employee')}}">{{translation('employee')}} </a></li>                      
                                </ul>
                            </li>
                            @endif                  
                    
                        </ul>
                    </li>

                    <li class="<?php  if($segment2 == 'email_template' || $segment2 == 'sms_template' || $segment2 == 'suggestions' || $segment2 == 'contact_enquiry' ){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle" title="{{translation('setup')}}">
                            <span class="icon-dash"><i class="fa fa- fa-bullhorn faa-vertical animated-hover"></i></span>
                            <span class="click-name-hide">{{translation('communication')}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                        <ul class="submenu">        
                            @if(array_key_exists('email_template.list', $arr_current_user_access))
                   
                            <li class="<?php  if($segment2 == 'email_template'){ echo 'active'; } ?>">
                                <a href="{{ url($admin_panel_slug.'/email_template')}}" class="dropdown-toggle"  title="{{translation('email_template')}}">
                                    <span class="icon-dash"><i class="fa fa-envelope faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('email_template')}}</span>
                                </a>
                            </li>
                            @endif

                            @if(array_key_exists('sms_template.list', $arr_current_user_access))
                   
                            <li class="<?php  if($segment2 == 'sms_template'){ echo 'active'; } ?>">
                                <a href="{{ url($admin_panel_slug.'/sms_template')}}" class="dropdown-toggle" title="{{translation('sms_template')}}">
                                    <span class="icon-dash"><i class="fa fa-mobile faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('sms_template')}}</span>
                                </a>
                            </li>
                            @endif

                            @if(array_key_exists('suggestions.list', $arr_current_user_access))
            
                            <li class="<?php  if($segment2 == 'suggestions'){ echo 'active'; } ?>">
                                <a href="{{ url($admin_panel_slug.'/suggestions') }}"  title="{{translation('suggestions')}}" >
                                    <span class="icon-dash"> <i class="fa fa-thumbs-up faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('suggestions')}}</span>
                                </a>
                            </li>
                            @endif

                            @if(array_key_exists('contact_support.list', $arr_current_user_access))
           
                            <li class="<?php  if($segment2 == 'contact_enquiry'){ echo 'active'; } ?>">
                                <a href="{{ url($admin_panel_slug.'/contact_enquiry')}}" class="dropdown-toggle"  title="{{translation('enquiry_management')}}">
                                    <span class="icon-dash"><i class="fa fa-info-circle faa-vertical animated-hover"></i></span>
                                    <span class="mobile-nones">{{translation('enquiry_management')}}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    
                    @if(array_key_exists('report.list', $arr_current_user_access))

                    <li class="<?php  if($segment2 == 'report'){ echo 'active'; } ?>">
                        <a href="JavaScript:void(0)" class="dropdown-toggle"  title="{{translation('report')}}">
                            <span class="icon-dash"><i class="fa fa-file faa-vertical animated-hover"></i></span>
                            <span class="click-name-hide">{{ucwords(translation('reporting'))}}</span>
                            <b class="arrow fa fa-angle-right"></b>
                        </a>

                         <ul class="submenu">
                                <li style="display: block;" class="<?php  if($segment2 == 'report'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/report?type='.config('app.project.role_slug.school_admin_role_slug'))}}">{{ ucfirst(translation('school_admin'))}}</a></li>
                                <li style="display: block;" class="<?php  if($segment2 == 'report'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/report?type='.config('app.project.role_slug.parent_role_slug'))}}">{{ ucfirst(translation('parent'))}} </a>
                                </li>   
                                <li style="display: block;" class="<?php  if($segment2 == 'report'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/report?type='.config('app.project.role_slug.student_role_slug'))}}">{{ucfirst(translation('student'))}} </a>
                                </li>   
                                <li style="display: block;" class="<?php  if($segment2 == 'report'){ echo 'active'; } ?>"><a href="{{ url($admin_panel_slug.'/report?type='.config('app.project.role_slug.professor_role_slug'))}}">{{ucfirst(translation('professor'))}} </a>
                                </li>   
                            </ul>
                    </li>
                    @endif

                    @if(array_key_exists('activity_log.update', $arr_current_user_access))
            
                    <li class="<?php  if($segment2 == 'activity_log'){ echo 'active'; } ?>">
                        <a href="{{ url($admin_panel_slug.'/activity_log') }}" title="{{translation('activity_log')}}" >
                            <span class="icon-dash"> <i class="fa fa-file-text-o faa-vertical animated-hover"></i></span>
                            <span class="mobile-nones click-name-hide">{{translation('logs')}}</span>
                        </a>
                    </li>
                    @endif
                </ul>
                </div>
                   
                    
                   
                <!-- END Navlist -->

                <!-- BEGIN Sidebar Collapse Button -->
                {{-- <div id="sidebar-collapse" class="visible-lg">
                    <i class="fa fa-angle-double-left"></i>
                </div> --}}
                <!-- END Sidebar Collapse Button -->
            </div>
                </div>
</div>
<!-- Add Class SideBar Start-->
  <script>
           $( function() {
        $( "#arrowSide" ).on( "click", function() {
          $( ".navbar-collapse" ).toggleClass( "sidebarmain-admin", 0);
        });

       $( ".right-arrow-section" ).on( "click", function() {
          $( ".navbar-collapse" ).toggleClass( "sidebarmain-admin", 0);
        });
      } );   
  </script> 
          
    <script>
        $(".dropdown-toggle").on("click", function(){
            $(this).siblings(".submenu").find(".submenu").slideToggle("fast");
            $(this).parent().siblings().find(".submenu").slideUp("fast");    
//            $(this).parent().toggleClass("active2");
//            $(this).parent().siblings().removeClass("active2");            
            $(this).children(".arrow").toggleClass("fa-angle-right").toggleClass("fa-angle-down");
            $(this).parent().siblings().find(".arrow").removeClass("fa-angle-down").addClass("fa-angle-right");
        });
        
        $("#sidebar-collapse").on("click", function(){
            $("#sidebar").toggleClass("sidebar-collapsed");            
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
        
    