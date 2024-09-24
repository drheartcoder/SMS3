    var destination_marker= false;
    var glob_info_window  = false;
    var map               = false;
    var curr_modal_number =  '';
    var location_type     =  '';
    var glob_arr_marker   = [];
    var stud_arr_marker   = [];
    var markers           = [];
    var arr_tmp           = [];
    var current_marker    = false;
    var pickup_marker     = false;
    var student_marker    = false;
    
    var ARR_MAPS_STYLE  = [];
    var STYLE_JSON_FILE = BASE_URL+'/assets/maps_style_2.json';

    window.onload = function () {
          if(transport_type=='Pickup')
          {
            var location_type = msg_destination;
          }
          if(transport_type=='Drop')
          {
            var location_type = msg_source;
          }

          $.ajax({
              url: STYLE_JSON_FILE,
              async: false,
              dataType: 'json',
              success: function (response) {
                 ARR_MAPS_STYLE = response;
              }
          });

          setTimeout(function(){
            LoadMap(destination_lat,destination_lng,transport_type,location_type);
            // Set Destination Marker on load
            if(destination_location!='' && destination_lat!='' && destination_lng!='')
            {
              SetDestinationMarker(destination_location,destination_lat,destination_lng,location_type,transport_type);
            }
          }, 200);

          //Get Student list according to Pickup / Drop
          setTimeout(function(){
            if(transport_type!='')
            {
              getStudentList(transport_type);
            }
          }, 2000);
      };

    var glob_options = {};

      var map;
      var bounds;
      var marker;
      var circle;

      var directionsService;
      var directionsDisplay;

      function LoadMap(destination_lat,destination_lng,location_type) {

          glob_info_window = new google.maps.InfoWindow({
            content: "(1.10, 1.10)"
          });


          var mapOptions = {
              center: new google.maps.LatLng(source_lat, source_lng),
              zoom: 10,
              mapTypeId: google.maps.MapTypeId.ROADMAP,
              scrollwheel: false,
              styles: ARR_MAPS_STYLE
          };
          map    = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
          bounds = new google.maps.LatLngBounds();
          
          directionsService = new google.maps.DirectionsService;
          directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
          directionsDisplay.setMap(map);  

          SetSourceMarker(transport_type);

          schlatlng = new google.maps.LatLng(source_lat, source_lng);
          map.streetViewControl = false;
          
          $.each(arr_pickup_drop,function(index,value)
          {
            var current_marker = createPickupDropLocationMarker(map,value);
          });
      };

      function SetSourceMarker(transport_type) {

          var pickupLatlng = new google.maps.LatLng(source_lat, source_lng);
          
          var pickupinfowindow = new google.maps.InfoWindow({
                                      content: " "
                              });

          pickup_marker    = new SlidingMarker({
                                        position   : pickupLatlng,
                                        map        : map,
                                        title      : source_location,
                                        infowindow : pickupinfowindow,
                                        icon       : BASE_URL+'/images/school_admin/pointer.png'
                                    });
          
          var dropinfowindow = new google.maps.InfoWindow({
                                      content: " "
                              });

          bounds.extend(pickupLatlng);
          
          map.fitBounds(bounds);

          var listener = google.maps.event.addListener(map, "idle", function() { 
            if (map.getZoom() > 16) map.setZoom(16); 
            google.maps.event.removeListener(listener); 
          });

          google.maps.event.addListener(pickup_marker, 'click', function() {
              var location_type = "";
                if(transport_type=='Drop')
                {
                  var location_type = msg_source;
                }
                if(transport_type=='Pickup')
                {
                  var location_type = msg_destination;
                }
              
              var html = '<div class="modal-content">'+
                      '<div class="modal-body">'+
                      '<div class="review-detais">'+
                              '<div class="boldtxts">'+location_type+' '+msg_location+' </div>'+
                              '<div class="rightview-txt">'+source_location+'</div>'+
                              '<div class="clearfix"></div>'+
                          '</div>'+
              '</div>';
              
              pickupinfowindow.setContent(html);

              pickupinfowindow.open(map, this);
          });

      };

      function SetDestinationMarker(destination_location,destination_lat,destination_lng,location_type,transport_type) {
          // View Load Map and then set Location
          LoadMap(destination_lat,destination_lng,transport_type,location_type);
          
          if(destination_marker!=false)
          {
              var dropLatlng = new google.maps.LatLng(destination_lat, destination_lng);
              destination_marker.setMap(null);
          }
          
            var dropinfowindow = new google.maps.InfoWindow({
                                      content: " "
                              });

            var dropLatlng = new google.maps.LatLng(destination_lat, destination_lng);
            destination_marker = new SlidingMarker({
                                          position : dropLatlng,
                                          map      : map,
                                          title    : destination_location,
                                          infowindow : dropinfowindow,
                                          icon     : BASE_URL+'/images/school_admin/pointer.png'
                                      });
            bounds.extend(dropLatlng);
            map.fitBounds(bounds);

            var listener = google.maps.event.addListener(map, "idle", function() { 
              if (map.getZoom() > 16) map.setZoom(16); 
              google.maps.event.removeListener(listener); 
            });
            
            // Draw Source To destination Route
            calculateAndDisplayRoute(directionsService, directionsDisplay,destination_lat,destination_lng);
            
            google.maps.event.addListener(destination_marker, 'click', function() {
                  var location_type = "";
                  if(transport_type=='Drop')
                  {
                    var location_type = msg_destination;
                  }
                  if(transport_type=='Pickup')
                  {
                    var location_type = msg_source;
                  }

                  var html = '<div class="modal-content">'+
                          '<div class="modal-body">'+
                          '<div class="review-detais">'+
                                  '<div class="boldtxts">'+location_type+' '+msg_location+' </div>'+
                                  '<div class="rightview-txt">'+destination_location+'</div>'+
                                  '<div class="clearfix"></div>'+
                              '</div>'
                      '</div>';

                dropinfowindow.setContent(html);
                dropinfowindow.open(map,this);
            });
      };

      /* Draw Source To destination Route*/
      
      function calculateAndDisplayRoute(directionsService, directionsDisplay,destination_lat,destination_lng) {

        var origin_lat_lng      = source_lat+','+source_lng;
        var destination_lat_lng = destination_lat+','+destination_lng;
        
        directionsService.route({
          origin      : origin_lat_lng,
          destination : destination_lat_lng,
          travelMode  : 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            console.log('Directions request failed due to ' + status);
          }
        });
      }
    
    /* Add Pickup Drop Location marker Starts here*/

    function createPickupDropLocationMarker(map,value)
    {
        var pickupDropLatlng = new google.maps.LatLng(value.stop_lat, value.stop_lang);

        var pickupDropinfowindow = new google.maps.InfoWindow({
                                    content: " "
                            });

        marker    = new SlidingMarker({
                                      position   : pickupDropLatlng,
                                      map        : map,
                                      title      : source_location,
                                      infowindow : pickupDropinfowindow,
                                      icon       : BASE_URL+'/images/school_admin/location.png'
                                  });        

        bounds.extend(pickupDropLatlng);
        
        map.fitBounds(bounds);

        var listener = google.maps.event.addListener(map, "idle", function() { 
          if (map.getZoom() > 16) map.setZoom(16); 
          google.maps.event.removeListener(listener); 
        });

        marker.addListener('click', function() 
        {
          marker = this;
          
            html = "<div>"+
                      "<input type='text' style='background-color:#fff' name='stop_no' readonly  value='"+value.stop_no+"' class='form-control'/> <br>"+
                      "<input type='text' style='background-color:#fff' name='stop_name' readonly   value='"+value.stop_name+"' class='form-control'/> <br>"+
                      "<input type='text' style='background-color:#fff' name='landmark' readonly  value='"+value.landmark+"' class='form-control'/> <br>"+
                      "<input type='text' style='background-color:#fff' name='stop_fees' readonly  value='"+value.stop_fees+"'  class='form-control'/> <br>"+
                      "<input type='text' style='background-color:#fff' name='stop_radius' readonly  value='"+value.stop_radius+"' class='form-control'/> <br>"+
                    "</div>";

            glob_info_window.setContent(html);
            glob_info_window.open(map,this);  
          
        });
        drawRadiusOnMap(map,marker,value.stop_radius);
        return marker;
    }

    /* Draw Radius on Marker */
    function drawRadiusOnMap(map,marker,stop_radius_in_km=500)
    {
      var metres = (parseFloat(stop_radius_in_km));
            
      metres = parseInt(metres);
      
      circle = new google.maps.Circle({
        map: map,
        radius: metres,
        fillColor: '#A6CFF7'
      });
      marker.circle = circle;
      circle.bindTo('center', marker, 'position');
    }

    /* Get Marker Data if exists */
    function getMarkerData(position)
    {
      var return_data = false;

      marker_index = getGlobMarkerIndexByLatLng(position.lat(),position.lng());
      if(marker_index!==false)
      {
        return_data = glob_arr_marker[marker_index];
      }
      return return_data;
    }
    
    /* Check if marker exists */
    function markerExists(position)
    {
      marker_index = getGlobMarkerIndexByLatLng(position.lat(),position.lng());
      return (marker_index===false)?false:true;
    }

    /* Get index of marker */
    function getGlobMarkerIndexByLatLng(lat,lng)
    {
      var glob_marker_index = false;
      $.each(glob_arr_marker,function(index,marker)
      {
          tmp_lat = this.position.lat();
          tmp_lng = this.position.lng();

          if(tmp_lat == lat && tmp_lng == lng)
          {
            glob_marker_index = index
            return false;
          }
      });
      return glob_marker_index;
    }    
    /* Add Pickup Drop Location Script Ends here*/

    /* show student List according to pickup/drop*/
    
    function getStudentList(transport_type)
    {
        $.ajax({
            url: MODULE_URL_PATH+'/get_assigned_student_list?transport_type='+transport_type+'&route='+route_id,
            async: false,
            dataType: 'json',
            success: function (response) {
              if(response.status=='success')
              {
                if(stud_arr_marker.length > 0 )
                {
                  $.each(stud_arr_marker,function(index,student_marker)
                  {
                      student_marker.setMap(null);
                  });
                  stud_arr_marker = [];
                }
                $.each(response.arr_student,function(index,value){
                  if(value.student_details.pickup_location!='' || value.student_details.drop_location!='')
                  {
                    SetStudentMarker(value,transport_type);
                  }
                });
              }
            }
        });
    }

    /* Set Student pickup/Drop marker on Google Map*/
    
    function SetStudentMarker(value,transport_type) {
        if(transport_type=='Pickup')
        {
          type = msg_pickup;
          var get_location = $.parseJSON(value.student_details.pickup_location);
          var location_title = value.student_details.pickup_address;
        }
        else
        {
          type = msg_drop;
          var get_location = $.parseJSON(value.student_details.drop_location);
          var location_title = value.student_details.drop_address;
        }
        
        var Latlng = new google.maps.LatLng(get_location.latitude, get_location.longitude);
        
        var studentinfowindow = new google.maps.InfoWindow({
                                    content: " "
                            });

        student_marker    = new SlidingMarker({
                                      position   : Latlng,
                                      map        : map,
                                      title      : location_title,
                                      infowindow : studentinfowindow,
                                      icon       : BASE_URL+'/images/school_admin/student.png'
                                  });
        
        var dropinfowindow = new google.maps.InfoWindow({ content: " " });

        bounds.extend(Latlng);
        
        map.fitBounds(bounds);

        var listener = google.maps.event.addListener(map, "idle", function() { 
          if (map.getZoom() > 16) map.setZoom(16); 
          google.maps.event.removeListener(listener); 
        });

        google.maps.event.addListener(student_marker, 'click', function() {
                //var html = setSourceInfoWindow(location_type,location);
                var html = '<div class="modal-content">'+
                        '<div class="modal-body">'+
                        '<div class="review-detais">'+
                                '<div class="boldtxts">'+type+' '+msg_location+' </div>'+
                                '<div class="rightview-txt">'+location_title+'</div>'+
                            '</div>'+
                        '</div>'; 
                
                studentinfowindow.setContent(html);

                studentinfowindow.open(map, this);
        });
        stud_arr_marker.push(student_marker);
    };
