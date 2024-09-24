var school_latitude,school_longitude;
var geocoder;
var map;
var directionsDisplay;
var bounds;
var arr_students=[];
var str_content='' ;


var directionsService = new google.maps.DirectionsService();
var locations=[];

function initialize() {
  directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
 

  var map = new google.maps.Map(document.getElementById('dvMap'), {
    zoom: 10,
    center: new google.maps.LatLng(-33.92, 151.25),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });

  bounds = new google.maps.LatLngBounds();

  directionsDisplay.setMap(map);
  var infowindow = new google.maps.InfoWindow();

  var marker, i,k;
  var request = {
    travelMode: google.maps.TravelMode.DRIVING
  };

  if(transport_type=="drop"){
 
    var pickupinfowindow = new google.maps.InfoWindow({
                                      content: "school_name"
                              });
    marker = new SlidingMarker({
                                        position   : new google.maps.LatLng(school_latitude, school_longitude),
                                        map        : map,
                                        title      : source_location,
                                        infowindow : pickupinfowindow,
                                        icon       : BASE_URL+'/images/school_admin/location.png'
                                    });

      bounds.extend(marker.getPosition());     
      map.fitBounds(bounds);
     
      var listener = google.maps.event.addListener(map, "idle", function() { 
            if (map.getZoom() > 16) map.setZoom(16); 
            google.maps.event.removeListener(listener); 
          });

          google.maps.event.addListener(marker, 'click', function() {
              
              var html = '<div class="modal-content">'+
                      '<div class="modal-body">'+
                      '<div class="review-detais">'+
                              '<div class="boldtxts"></div>'+
                              '<div class="rightview-txt">'+source_location+'</div>'+
                              '<div class="clearfix"></div>'+
                          '</div>'+
              '</div>';
              
              pickupinfowindow.setContent(html);

              pickupinfowindow.open(map, this);
          });

      request.origin = marker.getPosition();
  }
 
  for (i = 0; i < locations.length; i++) {

      var pickupinfowindow = new google.maps.InfoWindow({
                                      content: "school_name"
                              });
      marker = new SlidingMarker({
                                        position   : new google.maps.LatLng(locations[i][1],locations[i][2]),
                                        map        : map,
                                        title      : 'Student',
                                        infowindow : pickupinfowindow,
                                        icon       : BASE_URL+'/images/school_admin/pointer.png'
                                    });

     bounds.extend(marker.getPosition());     
     map.fitBounds(bounds);
          
          google.maps.event.addListener(marker, 'click', (function(marker,i) {
              return function(){
                str_content = locations[i][0];
                var html = '<div class="modal-content">'+
                      '<div class="modal-body">'+
                      '<div class="review-detais">'+
                              '<div class="boldtxts"></div>'+
                              '<div class="rightview-txt">'+str_content+'</div>'+
                              '<div class="clearfix"></div>'+
                          '</div>'+
                '</div>';
                
                pickupinfowindow.setContent(html);

                pickupinfowindow.open(map, this);
              }
          })(marker,i));

    if (i == 0) 
    { 
      if(transport_type=="drop"){
        if (!request.waypoints) request.waypoints = [];
        request.waypoints.push({
          location: marker.getPosition()
        });
      }
      else{
        request.origin = marker.getPosition();  
      }
      
    }  
    else if (i == locations.length - 1){
      if(transport_type=="drop"){
        request.destination = marker.getPosition();
      }
      else{
        if (!request.waypoints) request.waypoints = [];
        request.waypoints.push({
          location: marker.getPosition()
        });
      }
    }
    else {
      if (!request.waypoints) request.waypoints = [];
      request.waypoints.push({
        location: marker.getPosition()
      });
    }
  }
   
  if(transport_type=="pickup"){
  
      var pickupinfowindow = new google.maps.InfoWindow({
                                      content: "school_name"
                              });
    marker = new SlidingMarker({
                                        position   : new google.maps.LatLng(school_latitude, school_longitude),
                                        map        : map,
                                        title      : source_location,
                                        infowindow : pickupinfowindow,
                                        icon       : BASE_URL+'/images/school_admin/location.png'
                                    });

      bounds.extend(marker.getPosition());     
      map.fitBounds(bounds);

          google.maps.event.addListener(marker, 'click', function() {
              
              var html = '<div class="modal-content">'+
                      '<div class="modal-body">'+
                      '<div class="review-detais">'+
                              '<div class="boldtxts"></div>'+
                              '<div class="rightview-txt">'+source_location+'</div>'+
                              '<div class="clearfix"></div>'+
                          '</div>'+
              '</div>';
              
              pickupinfowindow.setContent(html);

              pickupinfowindow.open(map, this);
          });
      
      request.destination = marker.getPosition();
  }

  directionsService.route(request, function(result, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(result);
    }
  });
  
}
window.onload = function () {
  
      getStudentList(transport_type);
    };
    
    function getStudentList(transport_type){
        
        $.ajax({
            url: MODULE_URL_PATH+'/get_non_student_list?transport_type='+transport_type+'&bus_id='+bus_id,
            async: false,
            dataType: 'json',
            success: function (response) {
              console.log(response);
              if(response.status=="success"){
                var students = response.arr_student;
                console.log(students);
                $.each(students,function(index,value){
                    if(value.pickup_location!='' || value.drop_location!='')
                    {
                      var location=  JSON.parse(value.pickup_location);
                      var temp_arr = [];
                      if(transport_type=='pickup'){
                        temp_arr[0]=value.pickup_address;  
                      }
                      else{
                       temp_arr[0]=value.drop_address;   
                      }
                      var first_name  = value.get_user_details.first_name;
                      var last_name  = value.get_user_details.last_name;
                      temp_arr[1]=location.latitude;
                      temp_arr[2]=location.longitude;
                      temp_arr[3]=first_name+' '+last_name;
                      locations.push(temp_arr);
                    }
                  });
                 school_longitude = response.school_longitude;
                 school_latitude = response.school_latitude;
                 if(locations.length<=1){
                    $("#error").show();
                    $("#error").html(msg_no_student_assigned_to_bus);
                 }
                 else{
                  initialize(); 
                 }
                 
              }
             }
        });
    }


