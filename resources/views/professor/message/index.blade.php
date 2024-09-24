@extends('professor.layout.master')                
@section('main_content')

<link href="{{url('/')}}/css/admin/jquery.mCustomScrollbar.css" rel="stylesheet" />
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($professor_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa {{$module_icon}}"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title pr0">
         <h3>
            <i class="fa fa-list"></i>
            {{ isset($page_title)?$page_title:"" }}
         </h3>

          
         <div class="box-tool">
               <a 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
               
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')  
         
         <div class="col-md-10">
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>
        
         <br/>
         <div class="clearfix"></div>
                    <div class="message-main">
                        <div class="dash-white-main">
                            <div data-responsive-tabs class="verticalslide">
                                <nav>
                                    <div class="search-member-block">
                                        <input type="text" name="Search" id="search_key" placeholder="Search" />
                                        <button type="submit"><img src="{{url('/')}}/images/message-search-icon.png" alt="" /> </button>
                                    </div>
                                    <ul class="content-d">
                                      @if(!empty($arr_parents))
                                        @foreach($arr_parents as $parent)
                                        <li>
                                            <a onclick="getChatDiv('{{isset($parent['id'])?base64_encode($parent['id']):base64_encode(0)}}')">
                                                <span class="travles-img active">
                                                    @if(isset($parent['profile_image']) && $parent['profile_image'] !='')
                                                      <img src="{{$profile_image_public_img_path}}/{{$parent['profile_image']}}" alt="" />
                                                    @else
                                                      <img src="{{url('/')}}/images/default-profile.png" alt="" />
                                                    @endif
                                                </span>
                                                <span class="travles-name-blo">
                                                    <span class="travles-name-head">{{isset($parent['first_name']) ? ucfirst($parent['first_name']) :'' }} {{isset($parent['last_name']) ? ucfirst($parent['last_name']) :'' }} </span>
                                                    <span class="travles-name-sub"></span>
                                                   
                                                </span>
                                                <?php
                                                  $data = get_kids($parent['id']);
                                                ?>
                                                {{-- <div class="hover-info">{{get_kids($parent['id'])}}</div> --}}
                                                <div class="hover-info">
                                                  @if($data)
                                                    @foreach($data as $key => $val)
                                                     {{$val}}<br/>
                                                    @endforeach
                                                  @endif

                                                </div>
                                            </a>
                                        </li>
                                       @endforeach
                                       @else
                                       <li>
                                            <a>
                                                <span class="travles-img active">
                                                    
                                                </span>
                                                <span class="travles-name-blo">
                                                    <span class="travles-name-head">{{translation('no_data_available')}}</span>
                                                    {{-- <span class="travles-name-sub"> School Manager</span> --}}
                                                </span>
                                            </a>
                                        </li>
                                       @endif
                                       <li id="hide_row" style="display:none">
                                            <a>
                                                <span class="travles-img active">
                                                    
                                                </span>
                                                <span class="travles-name-blo">
                                                    <span class="travles-name-head">{{translation('no_data_available')}}</span>
                                                    {{-- <span class="travles-name-sub"> School Manager</span> --}}
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                                <div class="chat-travels-name" data-id="0">
                                    
                                </div>
                                <div class="content chat-div">
                                    <section id="tabone">
                                      <div class="messages-section-main">
                                        <input type="hidden" class="check_first_occurence" value="1">
                                      </div>
                                    </section>
                                </div> 
                                <div class="clear"></div>
                                <div class="write-message-block">
                                    
                                    <div class="prent-right-mess">
                                    <input type="text" name="message" placeholder="Typing..." />
                                  
                                    <button class="send-message-btn" id="submit_button" type="submit" onclick="sendMessage()"><i class="fa fa-paper-plane"></i></button>
                                    </div>
                                </div>
                                <div class="clr"></div>
                            </div>
                            <input type="hidden" id="div_height" />
                        </div>
                    </div>
      </div>
    </div>
   </div>
</div>

<script>

var ajaxRequest;
var myTime;
var last_message;
myTime = setInterval(function(){  
    var id = $(".chat-travels-name").data('id');
    if(id!='0'){
        getChatDiv(id);
    }
    
  },5000);;



$("input[name='message']").keyup(function(event) {
    if (event.keyCode === 13) {
        sendMessage();
    }
});

function getChatDiv(id){
      $(".write-message-block").show();
      last_message = $(".message-id").data("message"); 
      var previous_user = $(".chat-travels-name").data('id');
      if(previous_user != id){
        last_message =0;
      }
      if(ajaxRequest){

        ajaxRequest.abort();  
      }

      ajaxRequest = $.ajax({
              url  :"{{ $module_url_path }}/get_chat/"+id,
              type :'post',
              data:{"_token":"{{csrf_token()}}","last_message":last_message},
              success:function(data){
                if(data)
                {
                   var data = JSON.parse(data);

                    if(data.count>0)
                    {
                                        $(".message-id").data('message',data.last_id);
                                        
                                        var first_occured = $(".check_first_occurence").val();
                                        
                                        if(previous_user!=data.user_details.id){
                                           first_occured=1;
                                        }
                                        if(first_occured==1){
                                          $(".chat-div").html(data.chat_div);  
                                        }
                                        else{
                                        $("#tabone").find("div:eq(0)").find("div:eq(0)").find("div:eq(0)").find("div:eq(0)").append(data.chat_div); 
                                        }
                     
                    
                    }
                    if(data.count==0 && previous_user!=data.user_details.id){
                      
                      $(".chat-div").html(data.chat_div);
                    }

                    if(last_message==0){
                    
                                          $(".messages-section-main").mCustomScrollbar("update");
                                          $.mCustomScrollbar.defaults.scrollButtons.enable = true; //enable scrolling buttons by default
                                        
                                          $(".content-d, .messages-section-main").mCustomScrollbar({
                                            theme: "dark"
                                          });
                                          
                                        }
                    $(".messages-section-main").mCustomScrollbar("scrollTo","bottom",{
                                            scrollInertia:0
                                          });                    
                    $(".chat-travels-name").text(data.user_details.name);
                    $(".chat-travels-name").data('id',data.user_details.id);
                }
                
            }
          });  
  
}

function sendMessage(){

  var id = $(".chat-travels-name").data('id');
  var message = $("input[name='message']").val();
  
  
  if(id!='' && message!=''){
    $("input[name='message']").val('');
    $.ajax({
              url  :"{{$module_url_path}}/send_message",
              type :'post',
              data: {'_token':'{{csrf_token()}}','id':id,'message':message},
              beforeSend:function(data, statusText, xhr, wrapper) 
              {
              
                $("#submit_button").attr('disabled', true);
                $("#submit_button").html("<i class='fa fa-spinner fa-spin'></i>");
             },
              success:function(data){
                $("#submit_button").html("<i class='fa fa-paper-plane'></i>");
                $("#submit_button").removeAttr('disabled');
                if(data)
                {   
                    if(last_message_id!=data.last_id){
                      var data = JSON.parse(data);  
                    $(".message-id").data('message',data.last_id); 

                    if(data.count<=1){

                      $(".chat-div").html(data.chat_div);  
                      $(".messages-section-main").mCustomScrollbar("update");
                      $.mCustomScrollbar.defaults.scrollButtons.enable = true; //enable scrolling buttons by default
                      
                      $(".content-d, .messages-section-main").mCustomScrollbar({
                          theme: "dark"
                      });
                      
                      
                      $.mCustomScrollbar.defaults.scrollButtons.enable = true; //enable scrolling buttons by default
                    }
                    else
                    {
                     
                      $("#tabone").find("div:eq(0)").find("div:eq(0)").find("div:eq(0)").find("div:eq(0)").append(data.chat_div); 
                     
                    }
                    $(".messages-section-main").mCustomScrollbar("scrollTo","bottom",{

                        scrollInertia:0,scrollSpeed:300,
                      });
                    $(".chat-travels-name").text(data.user_details.name);
                    $(".chat-travels-name").data('id',data.user_details.id);
                    
                    //start_time();  
                    }
                    
                }
            }
          });
    }
}
$("#search_key").keyup(function(){
    var flag=0;
        $("span.travles-name-head").each(function(){
        
              var data = $(this).text().trim();
              data = data.toLowerCase();
              var search_key = $("#search_key").val();
              search_key = search_key.toLowerCase();
              search_key = new RegExp(search_key) ; 
                
                if(search_key.test(data)){
                  flag=1;
                  $(this).parent().parent().parent().show();
                  
                }
                else{
                  $(this).parent().parent().parent().hide();
                }
         })
         if(flag==0)
          {
            $("#hide_row").show();
          }
          else
          {
            $("#hide_row").hide();
          }  
      })
</script>
<script type="text/javascript" src="{{url('/')}}/js/admin/jquery.mCustomScrollbar.concat.min.js"></script>
   <script>
    /*responsive tab start here*/
    $(document).ready(function () {
        $(document).on('responsive-tabs.initialised', function (event, el) {
           
        });

        $(document).on('responsive-tabs.change', function (event, el, newPanel) {
           //console.log("hey");
        });

        $('[data-responsive-tabs]').responsivetabs({
            initialised: function () {
                
            },

            change: function (newPanel) {
            }
        });
    });
</script>
<!--responsive tab end here-->

<script type="text/javascript">
  /*scrollbar start*/
  (function ($) {
      $(window).on("load", function () {

            $.mCustomScrollbar.defaults.scrollButtons.enable = true; //enable scrolling buttons by default

            $.mCustomScrollbar.defaults.axis = "yx"; //enable 2 axis scrollbars by default

            $(".content-d, .messages-section-main").mCustomScrollbar({
              theme: "dark",
              advanced :{ updateOnContentResize:false}
            });
            $(".messages-section-main").mCustomScrollbar("scrollTo","bottom",{scrollInertia:0});
            
      });
  })(jQuery);

</script>
<script src="{{url('/')}}/js/admin/responsivetabs.js"></script>
@stop