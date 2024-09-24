  @extends('admin.layout.master') @section('main_content')

  <!-- Scroll Start Here -->
  <link href="{{ url('/') }}/css/admin/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
  <script src="{{ url('/') }}/js/admin/jquery.mCustomScrollbar.concat.min.js"></script>
  <script type="text/javascript">
  /*scrollbar start*/
  (function($){
  $(window).on("load",function(){
      $.mCustomScrollbar.defaults.scrollButtons.enable=true; //enable scrolling buttons by default
      $.mCustomScrollbar.defaults.axis="yx"; //enable 2 axis scrollbars by default
     $(".content-d").mCustomScrollbar({theme:"dark"});
  });
  })(jQuery);
  </script>
  <!-- Scroll End Here -->

  <script src="{{ url('/') }}/js/admin/jquery.rotapie.js"></script>

    <!-- BEGIN Breadcrumb -->
  <div id="breadcrumbs">
      <ul class="breadcrumb">
          <li class="active"><i class="fa fa-home"></i> {{translation('home')}}</li>

      </ul>
  </div>
  <!-- END Breadcrumb -->

  <!-- BEGIN Page Title -->
  <div class="page-title new-agetitle">
      <div>
          <h1><i class="fa fa-dashboard"></i>  {{translation('dashboard')}}</h1>

      </div>
  </div>
  <!-- END Page Title -->

  <!-- BEGIN Tiles -->
  <div class="">
      <div class="col-md-12">
  <!--        @include('schooladmin.layout._operation_status')-->
          <div class="clearfix"></div>

          <div class="row">
              
              <div class="col-sm-12 col-md-12 col-lg-12">
                 <div class="dashboard-statistics dash-color-bg">
                    <div class="panel-heading-dash">{{translation('site_statistics')}}</div>
                     <div class="panel-bodys dashbords">
                         <div class="row">
                           @if(isset($arr_final_tile) && !empty($arr_final_tile))
                                <div class="row">
                                    @foreach($arr_final_tile as  $key => $value)

                                    <div class="col-sm-12 col-md-6 col-lg-3 manual-width-section">
                                        <a class="ttl-express {{$value['tile_color'] or ''}}" href="{{$value['module_url']}}">
                                            <span class="left-icns-tp dash-circle1">
                                                <?php echo $value['fa_icons']; ?>
                                            </span>
                                            <span id="normal_rider_count" class="info-box-contentts">                                            
                                                <span class="name-tilt">{{$value['module_title'] or ''}}</span>
                                                <span class="site-statistics-semi-content">
                                                    {{$value['module_sub_title'] or ''}}
                                                </span>
                                            </span>
                                            <span class="left-icns-tp site-statistic-count">
                                                <span class="">{{$value['total_count'] or ''}}</span>
                                            </span>
                                        </a>
                                    </div>  
                                    @endforeach
                                </div>
                                @endif
                           

                                          
                         </div>
                     </div>
                 </div>
              </div>
              
            
              
                 </div>
              </div>
              
             <div class="clearfix"></div>
          <!--Dash End -->   
             
              <div class="col-sm-12 col-md-6 col-lg-6">
                  <div class="dashboard-statistics">
                      <div class="panel-heading-dash line-lft">Basic Chart</div>
                      <div class="panel-bodys">
<!--                                 <div id="pie"></div>  -->
                                 
                                 <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                                 
                                 
                                 
                      </div>
                  </div>
              </div>
              <div class="col-sm-12 col-md-6 col-lg-6">
                  <div class="dashboard-statistics">
                      <div class="panel-heading-dash line-lft">Chart</div>
                      <div class="panel-bodys">
                                  
                                   <div class="progressDiv">
                                        <div class="statChartHolder">
                                            <div class="progress-pie-chart" data-percent="67"><!--Pie Chart -->
                                                <div class="ppc-progress">
                                                    <div class="ppc-progress-fill"></div>
                                                </div>
                                                <div class="ppc-percents">
                                                <div class="pcc-percents-wrapper">
                                                    <span>%</span>
                                                </div>
                                                </div>
                                            </div><!--End Chart -->
                                        </div>
                                        <div class="statRightHolder">
                                            <ul>
                                            <li> <h3 class="blue">39.4k</h3> <span>Interactions</span></li>
                                            <li> <h3 class="purple">1.8k</h3> <span>Posts</span></li>
                                            </ul>

                                                <ul class="statsLeft">
                                                <li><h3 class="yellow">22%</h3> <span>Comments</span></li>
                                                <li><h3 class="red">37%</h3> <span>Cheers</span></li>
                                                </ul>
                                                    <ul class="statsRight">
                                                        <li><h3>18%</h3> <span>Tasks</span></li>
                                                        <li><h3>23%</h3> <span>Goals</span></li>
                                                    </ul>
                                        </div>

                                    </div>
                                   
                                   
                              
                      </div>
                  </div>
              </div>

              <div class="col-sm-12 col-md-6 col-lg-6">
                  <div class="chat-panel panel panel-success panel-mst-rd">
                      <div class="panel-heading ">
                          <div class="panel-title-box">
                              <i class="icon-comments"></i> Latest Rides <a class="btn btn-info btn-sm ride-view-all001 btn-btn pull-right" href="JavaScript:Void(0);">View All</a>
                              <!-- <div class="clearfix"></div>-->
                          </div>
                      </div>
                      <div class="space-inx-bld content-txt1 content-d" style="background:none;">
                          <ul class="chat">
                                  <li class="left clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="../images\admin\client-staff.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font "> Paul M. Stevens </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  
                                  <li class="left right-sections-cht clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="../images\admin\client-img-1.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font ">Tracy S. Shadwick </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  <li class="left clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="../images\admin\client-staff.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font "> Paul M. Stevens </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  
                                  <li class="left right-sections-cht clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="../images\admin\client-img-1.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font ">Tracy S. Shadwick </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  <li class="left clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="../images\admin\client-staff.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font "> Paul M. Stevens </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  
                                  <li class="left right-sections-cht clearfix">
                                      <div class="chat-body clearfix">
                                         <div class="left-img-chat"><img src="../images\admin\client-img-1.jpg" alt="" /></div>
                                          <div class="right-avtr">
                                              <div class="header header-right">
                                                  <strong class="primary-font ">Tracy S. Shadwick </strong>
                                              </div>
                                              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias quas repellat labore culpa, excepturi natus quis necessitatibus, voluptates. Debitis magnam veritatis qui, ipsum aperiam saepe consequatur facilis reprehenderit possimus accusamus harum. Numquam, impedit, tempore!</p>
                                                  <small class="text-muted label label-danger"><i class="icon-time"></i>56 Minutes ago  </small>
                                                  <a class="replay-link" href="#"><i class="fa fa-reply"></i></a>
                                          </div>
                                      </div>
                                  </li>
                                  
                                 
                          </ul>
                      </div>

                  </div>
              </div>

              <div class="col-sm-12 col-md-6 col-lg-6">
                  <div class="chat-panel panel panel-danger panel-mst-rd">
                      <div class="panel-heading">
                          <div class="panel-title-box">
                              Notifications Alerts Panel
                              <div class="clearfix"></div>
                          </div>
                      </div>
                      
                      <div class="notification-sectn">
                          <ul>
                              <li>
                                  <div class="align-itma">
                                      <div class="st-alphabet clr-lamtr"><i class="fa fa-envelope-o"></i></div>
                                      <div class="media-body-mstr">
                                          <p class="mb-0">
                                              <a href="#" class="text-purple-light">John Doe</a>
                                          </p>
                                          <span class="text-muted-mst">4 min ago</span>
                                      </div>
                                  </div>
                              </li>
                              <li>
                                  <div class="align-itma">
                                      <div class="st-alphabet clr-lamtr2"><i class="fa fa-bell"></i></div>
                                      <div class="media-body-mstr">
                                          <p class="mb-0">
                                              <a href="#" class="text-purple-light2">John Doe</a>
                                          </p>
                                          <span class="text-muted-mst">2 min ago</span>
                                      </div>
                                  </div>
                              </li>
                              <li>
                                  <div class="align-itma">
                                      <div class="st-alphabet clr-lamtr3"><i class="fa fa-comments-o"></i></div>
                                      <div class="media-body-mstr">
                                          <p class="mb-0">
                                              <a href="#" class="text-purple-light3">John Doe</a>
                                          </p>
                                          <span class="text-muted-mst">1 min ago</span>
                                      </div>
                                  </div>
                              </li>
                              <li>
                                  <div class="align-itma">
                                      <div class="st-alphabet clr-lamtr4"><i class="fa fa-envelope-o"></i></div>
                                      <div class="media-body-mstr">
                                          <p class="mb-0">
                                              <a href="#" class="text-purple-light4">John Doe</a>
                                          </p>
                                          <span class="text-muted-mst">12 min ago</span>
                                      </div>
                                  </div>
                              </li>
                              <li>
                                  <div class="align-itma">
                                      <div class="st-alphabet clr-lamtr5"><i class="fa fa-warning"></i></div>
                                      <div class="media-body-mstr">
                                          <p class="mb-0">
                                              <a href="#" class="text-purple-light5">John Doe</a>
                                          </p>
                                          <span class="text-muted-mst">12 min ago</span>
                                      </div>
                                  </div>
                              </li>
                              
                               <li>
                                  <div class="align-itma">
                                      <div class="st-alphabet clr-lamtr6"><i class="fa fa-envelope-o"></i></div>
                                      <div class="media-body-mstr">
                                          <p class="mb-0">
                                              <a href="#" class="text-purple-light6">John Doe</a>
                                          </p>
                                          <span class="text-muted-mst">4 min ago</span>
                                      </div>
                                  </div>
                              </li>
                              <li>
                                  <div class="align-itma">
                                      <div class="st-alphabet clr-lamtr7"><i class="fa fa-bell"></i></div>
                                      <div class="media-body-mstr">
                                          <p class="mb-0">
                                              <a href="#" class="text-purple-light7">John Doe</a>
                                          </p>
                                          <span class="text-muted-mst">2 min ago</span>
                                      </div>
                                  </div>
                              </li>
                              
                          </ul>
                      </div>
                      

<!--
                      <div class="panel-heading padding-15 content-txt1 content-d" style="background:none;">
                        @if(isset($arr_notifications) && sizeof($arr_notifications)>0)
                              @foreach($arr_notifications as $notification)
                          <div class="list-group">
                          <?php $json_notification = isset($notification) ? json_encode($notification) :''; ?>
                              <a href="JavaScript:Void(0);" onclick="change_notification_status({{$json_notification}});" class="list-group-item">
                                  <span class="chat-icnsin"><i class="fa fa-comment-o"></i>
                                  </span>  
                                  {{ isset($notification['notification_type']) ? $notification['notification_type'] :'-' }} : {{ isset($notification['title']) ? $notification['title'] :'-' }} 
                                  <span class="pull-right text-muted small"><em>18 Hours ago</em></span>
                              </a>
                          </div>
                          @endforeach
                          @endif
                      </div>
-->
                  </div>
              </div>
          </div>
          
          <div class="modal fade view-modals" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Transaction Preview</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
         
                <div class="modal-body">
                    <div class="review-detais">
                        <div class="boldtxts">ID</div> 
                        <div class="rightview-txt">7</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="review-detais">
                        <div class="boldtxts">Order Id</div> 
                        <div class="rightview-txt">Event- ABCXDF596DJF3</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="review-detais">
                        <div class="boldtxts">Date</div> 
                        <div class="rightview-txt">11 Dec 2018</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="review-detais">
                        <div class="boldtxts">Time</div> 
                        <div class="rightview-txt">11:56 AM</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="review-detais">
                        <div class="boldtxts">Event Type</div> 
                        <div class="rightview-txt">Paid</div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Print</button>
                </div>
              </div>
            </div>
          </div>
  
      
      <script type="text/javascript">
        
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-36251023-1']);
    _gaq.push(['_setDomainName', 'jqueryscript.net']);
    _gaq.push(['_trackPageview']);

    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();

  </script>
     
    <!-- canvas Chart Start -->
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"> </script>
        <script type="text/javascript">
            window.onload = function () {

            var chart = new CanvasJS.Chart("chartContainer", {
                theme: "light1", // "light2", "dark1", "dark2"
                animationEnabled: false, // change to true		
                title:{
                    text: "Basic Column Chart"
                },
                data: [
                {
                    // Change type to "bar", "area", "spline", "pie",etc.
                    type: "column",
                    dataPoints: [
                        { label: "apple",  y: 10  },
                        { label: "orange", y: 15  },
                        { label: "banana", y: 25  },
                        { label: "mango",  y: 30  },
                        { label: "grape",  y: 28  }
                    ]
                }
                ]
            });
            chart.render();

            }
          </script>
          
          
          <script>
          $(function(){
              var $ppc = $('.progress-pie-chart'),
                percent = parseInt($ppc.data('percent')),
                deg = 360*percent/100;
              if (percent > 50) {
                $ppc.addClass('gt-50');
              }
              $('.ppc-progress-fill').css('transform','rotate('+ deg +'deg)');
              $('.ppc-percents span').html(percent+'%');
            });

          </script>
           
     <!-- canvas Chart End -->     
     
      <script src="{{ url('/') }}/js/admin/ajax_loader.js"></script>            


  <!-- <script type="text/javascript">
    setInterval(function()
    { 
      var url ="{{ $admin_url_path.'/dashboard/get_dashboard_count' }}"; 
          $.ajax({
            url:url,
            type:"GET",
            success:function(response)
            {
              var super_rider_html          = '<span class="info-box-textt">Super Rider</span> <span class="info-box-numberr">'+response.arr_data['super_rider_count']+'</span>';
              $('#super_rider_count').html(super_rider_html);

              var normal_rider_html         = '<span class="info-box-textt">Normal Rider</span> <span class="info-box-numberr">'+response.arr_data['normal_rider_count']+'</span>';
              $('#normal_rider_count').html(normal_rider_html);

              var subadmin_html             = '<span class="info-box-textt">Subadmin</span> <span class="info-box-numberr">'+response.arr_data['subadmin_count']+'</span>';
              $('#subadmin_count').html(subadmin_html);

              var driver_html               = '<span class="info-box-textt">Driver</span> <span class="info-box-numberr">'+response.arr_data['driver_count']+'</span>';
              $('#driver_count').html(driver_html);

              var vehicle_html              = '<span class="info-box-textt">Vehicle</span> <span class="info-box-numberr">'+response.arr_data['vehicle_count']+'</span>';
              $('#vehicle_count').html(vehicle_html);

              var assign_vehicle_html       = '<span class="info-box-textt">Assign Vehicle</span> <span class="info-box-numberr">'+response.arr_data['assign_vehicle_count']+'</span>';
              $('#assign_vehicle_count').html(assign_vehicle_html);

              var promo_offer_html          = '<span class="info-box-textt">Promo Offer</span> <span class="info-box-numberr">'+response.arr_data['promo_offer_count']+'</span>';
              $('#promo_offer_count').html(promo_offer_html);

              var advertisement_html        = '<span class="info-box-textt">Advertisement</span> <span class="info-box-numberr">'+response.arr_data['advertisement_count']+'</span>';
              $('#advertisement_count').html(advertisement_html);

              var normal_ride_html          = '<div class="title-dashboard-box">Normal Ride : '+response.arr_data['normal_ride_count']+'</div>';
              $('#normal_ride_count').html(normal_ride_html);

              var emergency_ride_html       = '<div class="title-dashboard-box">Emergency Ride : '+response.arr_data['emergency_ride_count']+'</div>';
              $('#emergency_ride_count').html(emergency_ride_html);

              var driver                    = '<div class="title-dashboard-box">Emergency Ride : '+response.arr_data['driver_count']+'</div>';
              $('#driver').html(driver);
                                        
              var today_ride_html           = '<li>Today :<span>'+response.arr_data['today_ride_count']+'</span></li>';
              $('#today_ride_count').html(today_ride_html);
              
              var current_month_ride_html   = '<li>This Month :<span>'+response.arr_data['current_month_ride_count']+'</span></li>';
              $('#current_month_ride_count').html(current_month_ride_html);
              
              var current_year_ride_html    = '<li>This Year :<span>'+response.arr_data['current_year_ride_count']+'</span></li>';
              $('#current_year_ride_count').html(current_year_ride_html);
              
              var today_emergency_ride_html = '<li>Today :<span>'+response.arr_data['today_emergency_ride_count']+'</span></li>';
              $('#today_emergency_ride_count').html(today_emergency_ride_html);
              
              var current_month_ride_html   = '<li>This Month :<span>'+response.arr_data['current_month_emergency_ride_count']+'</span></li>';
              $('#current_month_emergency_ride_count').html(current_month_ride_html);
              
              var current_year_ride_html    = '<li>This Year :<span>'+response.arr_data['current_year_emergency_ride_count']+'</span></li>';
              $('#current_year_emergency_ride_count').html(current_year_ride_html);
              
            } 
          });

    }, 3000);
        
  </script> -->

  <!-- <script type="text/javascript">

  var active_user_id  = "{{isset($user_id) ? $user_id :0}}";

    OneSignal.push(function() {
      OneSignal.sendTags({
        active_user_id: active_user_id,
      }).then(function(tagsSent) {
        // Callback called when tags have finished sending    
      });
    });

  </script> -->
  @stop