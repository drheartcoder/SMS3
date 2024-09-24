    var destination_marker= false;
    var glob_info_window  = false;
    var map               = false;
    var curr_modal_number =  '';
    var location_type     =  '';
    var glob_arr_marker   = [];
    var stud_arr_marker   = [];
    var markers           = [];
    var arr_tmp           = [];
    var arr_tmp_student   = [];
    var arr_circles       = [];
    var current_marker    = false;
    var pickup_marker     = false;
    var student_marker    = false;
    var bus_capacity      = 0;
    var transport_type    = $('#transport_type').val();
    var arr_radius        = [];
    var arr_radius        = [({name:'500 Meters',val:500}),({name:'1000 Meters',val:1000}),({name:'1500 Meters',val:1500}),({name:'2000 Meters',val:2000}),({name:'2500 Meters',val:2500})];
    var ARR_MAPS_STYLE  = [];
    var STYLE_JSON_FILE = BASE_URL+'/assets/maps_style_2.json';

    $("#submit_button").on('click',function(){
        
        if($("#json_pickup_drop_point").val()=="" && $("#bus_number").val()!="" && $("#transport_type").val()!="" && $("#route_name").val()!="" && $("#address").val()!="")
        {
          if($("#transport_type").val()=='pickup')
          {
            swal(msg_error,msg_pickup_location_is_required,'error');
          }
          else
          {
            swal(msg_error,msg_drop_location_is_required,'error');
          }
          return false;
        }
        else
        {
          $.each(stud_arr_marker,function(index,value)
          {
            calculate_location_using_lat_lng(this.position.lat(),this.position.lng(),this.student_id);
          });
          if($("#json_pickup_drop_point").val()!="" && $("#bus_number").val()!="" && $("#transport_type").val()!="" && $("#route_name").val()!="" && $("#address").val()!=""){
            if(arr_tmp_student.length > 0)
            {
              $("#json_arr_student").val(JSON.stringify(arr_tmp_student));
            }
          }
          $("#validation-form1").submit();
        }
    });

    window.onload = function () {
          
          $("#json_pickup_drop_point").val('');
          $("#json_arr_student").val('');
          
          if(transport_type=='pickup')
          {
            var location_type = msg_destination;
          }
          if(transport_type=='drop')
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
            initAutocomplete();

            var destination_location   = $('#address').val();
            var destination_lat        = $('#lat').val();
            var destination_lng        = $('#lng').val();      
            LoadMap(destination_lat,destination_lng,transport_type,location_type);
            // Set Destination Marker on load
            if(destination_location!='' && destination_lat!='' && destination_lng!='')
            {
              //SetDestinationMarker(destination_location,destination_lat,destination_lng,location_type);
            }
          }, 200);

          //Get Student list according to Pickup / Drop
          setTimeout(function(){
            var bus_id = $('#bus_id').val();
            if(transport_type!='')
            {
              getStudentList(transport_type);
              if(bus_id!='')
              {
                checkIfRouteExists(bus_id,transport_type);
                getBusCapacity(bus_id,transport_type);
              }
            }
          }, 2000);
      };  

    var glob_autocomplete;

    var glob_component_form =
    {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'long_name',
    postal_code: 'short_name',
    country : 'long_name',
    postal_code : 'short_name',
    };

    var glob_options = {};

    /* Google AutoComplete Address Script Starts here*/
    function initAutocomplete() {
      glob_autocomplete = false;
      $('#radius_in_km').attr("disabled", true);
      glob_autocomplete = initGoogleAutoComponent($('#address')[0],glob_options,glob_autocomplete);
    }

    function initGoogleAutoComponent(elem,options,autocomplete_ref){
      autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
      autocomplete_ref = createPlaceChangeListener(autocomplete_ref,fillInAddress);

      return autocomplete_ref;
    }

    function createPlaceChangeListener(autocomplete_ref,fillInAddress){
      autocomplete_ref.addListener('place_changed', fillInAddress);
      return autocomplete_ref;
    }

    function fillInAddress(){

      var place = glob_autocomplete.getPlace();

      $('#lat').val(place.geometry.location.lat());
      $('#lng').val(place.geometry.location.lng());

      for (var component in glob_component_form)
      {
      $("#"+component).val("");
      $("#"+component).attr('disabled',false);
      }

      if(place.address_components.length > 0 )
      {
        $.each(place.address_components,function(index,elem){

        var addressType = elem.types[0];

          if(addressType!=undefined){
            if(glob_component_form[addressType]!=undefined){
            var val = elem[glob_component_form[addressType]];
            $("#"+addressType).val(val) ;
            }
          }
        });

      }
        if(glob_arr_marker.length>0)
        {
          $.each(glob_arr_marker,function(index,marker)
          {
              glob_arr_marker.splice(index,1);
          });
          glob_arr_marker = [];
        }

        if(markers.length > 0)
        {
          for (var i = 0; i < markers.length; i++) {
            var last_circle = arr_circles[i].circle;
            last_circle.setMap(null);
            markers[i].setMap(null);
          }
          markers     = [];
          arr_circles = [];
        }

        //Draw Route Map
        var destination_location   = $('#address').val();
        var destination_lat        = place.geometry.location.lat();
        var destination_lng        = place.geometry.location.lng();
        var transport_type         = $('#transport_type').val();
        var bus_id                 = $('#bus_id').val();
        $('#json_pickup_drop_point').val('');
        $('#bus_id_error').html('');

        if(transport_type=='pickup')
        {
          var location_type = msg_destination;
        }
        if(transport_type=='drop')
        {
          var location_type = msg_source;
        }
        //Set Destination Marker
        //SetDestinationMarker(destination_location,destination_lat,destination_lng,location_type);
        
        //Get Student list according to Pickup / Drop
        setTimeout(function(){
          if(transport_type!='')
          {
            getStudentList(transport_type);
            if(bus_id!='')
            {
              checkIfRouteExists(bus_id,transport_type);
              getBusCapacity(bus_id,transport_type);
            }
          }
        }, 2000);
    }
    /* Google AutoComplete Address Script Ends here*/    


      var map;
      var bounds;
      var marker;
      var circle;

      var directionsService;
      var directionsDisplay;
      var schlatlng;
      var type;

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

          map.streetViewControl = false;

          //Create existing pickupdrop points with circle
          $.each(arr_pickup_drop,function(index,value)
          {
            schlatlng = new google.maps.LatLng(value.stop_lat, value.stop_lang);
            var current_marker = createPickupDropLocationMarker(map,schlatlng,true,value);
            markers.push(current_marker);
            glob_arr_marker.push(current_marker);
            serializePickupDropPoints();
          });
          
          schlatlng = new google.maps.LatLng(source_lat, source_lng);
          google.maps.event.addListener(map, 'click', function(event) 
          {
              if(markers.length > 16)
              {
                swal(msg_warning, msg_maximum_15_stops_are_allowed, "warning");
                return false;
              }
              else
              {
                if(destination_lat!='' && destination_lng!='')
                {
                  if(glob_arr_marker[glob_arr_marker.length]==undefined && markers.length == glob_arr_marker.length + 1 && markers.length > 0)
                  {
                    swal(msg_warning, msg_please_fill_all_information_for_current_pickupdrop_location, "warning");
                    return false;
                  } 
                  else
                  {
                    var current_marker = createPickupDropLocationMarker(map,schlatlng,false);
                    current_marker.setPosition(event.latLng);
                    markers.push(current_marker);
                    var yeri = event.latLng;
                    var latlongi = "(" + yeri.lat().toFixed(6) + ", " +yeri.lng().toFixed(6) + ")";
                    glob_info_window.setContent(latlongi);
                  }
                }
              }
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
                if($('#transport_type').val()=='drop')
                {
                  var location_type = msg_source;
                }
                if($('#transport_type').val()=='pickup')
                {
                  var location_type = msg_destination;
                }
              
              var html = '<div class="modal-content">'+
                      '<div class="modal-body">'+
                      '<div class="review-detais">'+
                              '<div class="boldtxts">'+location_type+'  '+msg_location+' : </div>'+
                              '<div class="rightview-txt">'+source_location+'</div>'+
                              '<div class="clearfix"></div>'+
                          '</div>'+
              '</div>';
              
              pickupinfowindow.setContent(html);

              pickupinfowindow.open(map, this);
          });

      };

      function SetDestinationMarker(destination_location,destination_lat,destination_lng,location_type) {
          // View Load Map and then set Location
          //LoadMap(destination_lat,destination_lng,$('#transport_type').val(),location_type);
          
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
                  if($('#transport_type').val()=='drop')
                  {
                    var location_type = msg_destination;
                  }
                  if($('#transport_type').val()=='pickup')
                  {
                    var location_type = msg_source;
                  }

                  var html = '<div class="modal-content">'+
                          '<div class="modal-body">'+
                          '<div class="review-detais">'+
                                  '<div class="boldtxts">'+location_type+'  '+msg_location+' : </div>'+
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
    function createPickupDropLocationMarker(map,position,is_existing_marker,existing_value=false){
        var marker = new google.maps.Marker({
              position  : position,
              draggable : true,
              map       : map,
              icon      : BASE_URL+'/images/school_admin/location.png'
          });

        marker.addListener('click', function() 
        {
          current_marker = this;
          if(markerExists(current_marker.position)!=false)
          {
            data = getMarkerData(current_marker.position);
            option_html = '';
            $.each(arr_radius,function(index,_value){
              if(_value.val==data.stop_radius)
              {
               option_html += "<option value='"+_value.val+"' selected>"+_value.name+"</option>";
              }
              else
              {
               option_html += "<option value='"+_value.val+"'>"+_value.name+"</option>";
              }
            });
            
            if(is_existing_marker==true)
            {
              html = "<div>"+
                        "<input type='text' name='stop_no'  value='"+existing_value.stop_no+"' placeholder='"+msg_stop_no+"' class='form-control'/> <br>"+
                        "<input type='text' name='stop_name'  value='"+existing_value.stop_name+"' placeholder='"+msg_stop_name+"' class='form-control'/> <br>"+
                        "<input type='text' name='landmark'  value='"+existing_value.landmark+"' placeholder='"+msg_landmark+"' class='form-control'/> <br>"+
                        "<input type='text' name='stop_fees'  value='"+existing_value.stop_fees+"' placeholder='"+msg_stop_fees+"' class='form-control'/> <br>"+
                        "<select name='stop_radius' placeholder='"+msg_stop_radius+"' class='form-control' onchange='drawRadiusOnMap(map,current_marker,this,true)'>"+option_html+"</select><br>"+
                        "<button type='button' onclick='removeMarker(1)' style='margin-left:10px' class='btn btn-primary btn-sm'>"+msg_remove+"</button>&nbsp;&nbsp;"+
                      "<button type='button' onclick='updateMarkerData("+current_marker.position.lat()+","+current_marker.position.lng()+",this)' class='btn btn-primary btn-sm'>"+msg_update_info+"</button></div>";
            }
            else
            {
              html = "<div>"+
                        "<input type='text' name='stop_no'  value='"+data.stop_no+"' placeholder='"+msg_stop_no+"' class='form-control'/> <br>"+
                        "<input type='text' name='stop_name'  value='"+data.stop_name+"' placeholder='"+msg_stop_name+"' class='form-control'/> <br>"+
                        "<input type='text' name='landmark'  value='"+data.landmark+"' placeholder='"+msg_landmark+"' class='form-control'/> <br>"+
                        "<input type='text' name='stop_fees'  value='"+data.stop_fees+"' placeholder='"+msg_stop_fees+"' class='form-control'/> <br>"+
                        "<select name='stop_radius' placeholder='"+msg_stop_radius+"' class='form-control' onchange='drawRadiusOnMap(map,current_marker,this,true)'>"+option_html+"</select><br>"+
                        "<button type='button' onclick='removeMarker(1)' style='margin-left:10px' class='btn btn-primary btn-sm'>"+msg_remove+"</button>&nbsp;&nbsp;"+
                      "<button type='button' onclick='updateMarkerData("+current_marker.position.lat()+","+current_marker.position.lng()+",this)' class='btn btn-primary btn-sm'>"+msg_update_info+"</button></div>";              
            }

            glob_info_window.setContent(html);
            glob_info_window.open(map,this);
          }
          else
          {
            option_html = '';
            $.each(arr_radius,function(index,_value){
               option_html += "<option value='"+_value.val+"'>"+_value.name+"</option>";
            });
            html = "<div>"+
                      "<input type='text' name='stop_no' placeholder='"+msg_stop_no+"' class='form-control'/> <br>"+
                      "<input type='text' name='stop_name' placeholder='"+msg_stop_name+"' class='form-control'/> <br>"+
                      "<input type='text' name='landmark' placeholder='"+msg_landmark+"' class='form-control'/> <br>"+
                      "<input type='text' name='stop_fees' placeholder='"+msg_stop_fees+"' class='form-control'/> <br>"+
                      "<select name='stop_radius' placeholder='"+msg_stop_radius+"' class='form-control' onchange='drawRadiusOnMap(map,current_marker,this,true)'>"+option_html+"</select><br>"+
                    "<button type='button' onclick='removeMarker(0)' style='margin-left:10px' class='btn btn-primary btn-sm'>"+msg_remove+"</button>&nbsp;&nbsp;"+
                    "<button type='button' onclick='stackCurrentLocation(true,this)' class='btn btn-primary btn-sm'>"+msg_add+"</button></div>";

            glob_info_window.setContent(html);
            glob_info_window.open(map,this);
          }

        });
        
        if(is_existing_marker==true)
        {
          marker.stop_no     = existing_value.stop_no;
          marker.stop_name   = existing_value.stop_name;
          marker.landmark    = existing_value.landmark;
          marker.stop_fees   = existing_value.stop_fees;
          marker.stop_radius = existing_value.stop_radius;

          drawRadiusOnMap(map,marker,false,false,existing_value.stop_radius);
        }
        else
        {
          if(markerExists(marker.position)==false)
          {
            drawRadiusOnMap(map,marker,false,false);
          }
        }
        return marker;
    }

    /* Draw Radius on Marker */
    function drawRadiusOnMap(map,marker,ref,info_window,stop_radius_in_km=500)
    {
      if(info_window==true)
      {
        if(circle!=undefined)
        {
          if(arr_circles.length>0)
          {
            $.each(arr_circles,function(index,c_marker)
            {
              if(c_marker!=undefined && c_marker.position.lat()==marker.position.lat() && c_marker.position.lng()==marker.position.lng())
              {
                  var last_circle = arr_circles[index].circle;
                  arr_circles.splice(index,1);              
                  last_circle.setMap(null);
              }
            });
          }
        }
        if(ref!=false)
        {
          var stop_radius_in_km = $(ref).val();
        }
      }

      var metres = (parseFloat(stop_radius_in_km));
            
      metres = parseInt(metres);
      
      circle = new google.maps.Circle({
        map: map,
        radius: metres,
        fillColor: '#A6CFF7'
      });
      marker.circle = circle;
      arr_circles.push(marker);
      circle.bindTo('center', marker, 'position');
    } 

    /* Get Marker Data if exists */
    function getMarkerData(position){
      var return_data = false;

      marker_index = getGlobMarkerIndexByLatLng(position.lat(),position.lng());
      if(marker_index!==false)
      {
        return_data = glob_arr_marker[marker_index];
      }
      return return_data;
    }
    
    /* Check if marker exists */
    function markerExists(position){
      marker_index = getGlobMarkerIndexByLatLng(position.lat(),position.lng());
      return (marker_index===false)?false:true;
    }

    /* Get index of marker */
    function getGlobMarkerIndexByLatLng(lat,lng){
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

    /* Add Pickup/Drop Location marker with Validation */
    function stackCurrentLocation(close_infowindow,ref){
      close_infowindow = close_infowindow | false;

      /*if(close_infowindow)
      {
        glob_info_window.close();
      }*/

      var parent_div = $(ref).parent("div");
      
      current_marker.stop_no     = $(parent_div).find("input[name='stop_no']").val();
      current_marker.stop_name   = $(parent_div).find("input[name='stop_name']").val();
      current_marker.landmark    = $(parent_div).find("input[name='landmark']").val();
      current_marker.stop_fees   = $(parent_div).find("input[name='stop_fees']").val();
      current_marker.stop_radius = $(parent_div).find("select[name='stop_radius']").val();
      
      var price_filter = /^(?!0+$)\d{1,8}(\.\d{1,2})?$/;
      
      if(current_marker.stop_no.length<=0)
      {
        swal(msg_warning, msg_the_stop_no_field_cannot_be_empty, "warning");
        return false;
      }

      if(current_marker.stop_name.length<=0)
      {
        swal(msg_warning, msg_the_stop_name_field_cannot_be_empty, "warning");
        return false;
      }
      if(current_marker.landmark.length<=0)
      {
        swal(msg_warning, msg_the_landmark_field_cannot_be_empty, "warning");
        return false;
      }
      if(current_marker.stop_fees.length<=0)
      {
        swal(msg_warning, msg_the_stop_fees_field_cannot_be_empty, "warning");
        return false;
      }

      if(!price_filter.test(current_marker.stop_fees))
      {
        swal(msg_warning, msg_please_enter_valid_stop_fees, "warning");
        return false;
      }

      if(current_marker.stop_radius.length<=0)
      {
        swal(msg_warning, msg_the_stop_radius_field_cannot_be_empty, "warning");
        return false;
      }      

      /*Check where duplicate stop*/
      var duplicate_flag = false;
      if(glob_arr_marker.length>0)
      {
        $.each(glob_arr_marker,function(index)
        {
            if(this.stop_no == current_marker.stop_no)
            {
              duplicate_flag = true;
              return false;
            }
        });
      }
      if(duplicate_flag)
      {
        swal(msg_warning, msg_stop_no_field_cannot_be_duplicate, "warning");
        return false;
      }

      if(close_infowindow)
      {
        glob_info_window.close();
      }

      glob_arr_marker.push(current_marker);
      
      serializePickupDropPoints();
    }

    /* Serialize the pickup drop data */
    function serializePickupDropPoints(){
      $('#json_pickup_drop_point').val('');
      var arr_tmp = [];
      if(glob_arr_marker.length>0)
      {
        $.each(glob_arr_marker,function(index)
        {
            arr_tmp.push({lat:this.position.lat(),lng:this.position.lng(),stop_no:this.stop_no,stop_name:this.stop_name,landmark:this.landmark,stop_fees:this.stop_fees,stop_radius:this.stop_radius});
        });
        
        $('#json_pickup_drop_point').val(JSON.stringify(arr_tmp));
      }
    }

    /* Remove Pickup/Drop Marker */
    function removeMarker(type){
        if(type==1)
        {
          var marker_index = false;
          marker_index = getGlobMarkerIndexByLatLng(current_marker.position.lat(),current_marker.position.lng());
          glob_arr_marker.splice(marker_index,1);
        }
        
        if(markers.length > 0)
        {
          $.each(markers,function(index,marker)
          {
              if(markers!=undefined && markers.length > 0 && marker.position.lat() == current_marker.position.lat() && marker.position.lng() == current_marker.position.lng())
              {
                if(arr_circles[index].position.lat()==marker.position.lat() && arr_circles[index].position.lng()==marker.position.lng())
                {
                  var old_circle = arr_circles[index].circle;
                  old_circle.setMap(null);
                  arr_circles.splice(index,1);
                }
                markers.splice(index,1);
                marker.setMap(null);
                current_marker.setMap(null);
                return false;
              }
          });
        }

        serializePickupDropPoints();
    }

    /* Update Pickup/Drop Marker with validations */
    
    function updateMarkerData(lat,lng,ref){
      var parent_div = $(ref).parent("div");

      var stop_no      = $(parent_div).find("input[name='stop_no']").val();
      var stop_name    = $(parent_div).find("input[name='stop_name']").val();
      var landmark     = $(parent_div).find("input[name='landmark']").val();
      var stop_fees    = $(parent_div).find("input[name='stop_fees']").val();
      var stop_radius  = $(parent_div).find("select[name='stop_radius']").val();

      var price_filter = /^(?!0+$)\d{1,8}(\.\d{1,2})?$/;

      if(stop_no.length<=0)
      {
        swal(msg_warning, msg_the_stop_no_field_cannot_be_empty, "warning");
        return false;
      }

      if(stop_name.length<=0)
      {
        swal(msg_warning, msg_the_stop_name_field_cannot_be_empty, "warning");
        return false;
      }

      if(landmark.length<=0)
      {
        swal(msg_warning, msg_the_landmark_field_cannot_be_empty, "warning");
        return false;
      }

      if(stop_fees.length<=0)
      {
        swal(msg_warning, msg_the_stop_fees_field_cannot_be_empty, "warning");
        return false;
      }

      if(!price_filter.test(stop_fees))
      {
        swal(msg_warning, msg_please_enter_valid_stop_fees, "warning");
        return false;
      }

      if(stop_radius.length<=0)
      {
        swal(msg_warning, msg_the_stop_radius_field_cannot_be_empty, "warning");
        return false;
      }
      marker_index = getGlobMarkerIndexByLatLng(lat,lng);
      
      /*Check where duplicate stop*/
      var duplicate_flag = false;
      if(glob_arr_marker.length>0)
      {
        $.each(glob_arr_marker,function(index)
        {
            if(this.stop_no == stop_no && marker_index!=index)
            {
              duplicate_flag = true;
              return false;
            }
        });
      }
      
      if(duplicate_flag)
      {
        swal(msg_warning, msg_stop_no_field_cannot_be_duplicate, "warning");
        return false;
      }
      
      if(marker_index!==false)
      { 
        
        glob_arr_marker[marker_index].stop_no      = stop_no;
        glob_arr_marker[marker_index].stop_name    = stop_name;
        glob_arr_marker[marker_index].landmark     = landmark;
        glob_arr_marker[marker_index].stop_fees    = stop_fees;
        glob_arr_marker[marker_index].stop_radius  = stop_radius;
        glob_info_window.close();
      }
      serializePickupDropPoints();
    }    

    /* Add Pickup Drop Location Script Ends here*/

    /* On Change of Transport Route show student List according to pickup/drop*/
    $('#transport_type').on('change',function(){

      var transport_type  = $('#transport_type').val();
      var bus_id          = $('#bus_id').val();
      var transport_type  = $('#transport_type').val();

      $('#bus_id_error').html('');

      if(transport_type!='')
      {
        getStudentList(transport_type);
        if(bus_id!='')
        {
          checkIfRouteExists(bus_id,transport_type);
          getBusCapacity(bus_id,transport_type);
        }
      }
      else
      {
        $.each(stud_arr_marker,function(index,student_marker)
        {
            student_marker.setMap(null);
        });
        stud_arr_marker = [];
      }
    });

    /* show student List according to pickup/drop*/
    
    function getStudentList(transport_type){
        $.ajax({
            url: MODULE_URL_PATH+'/get_non_student_list?transport_type='+transport_type+'&route='+route_id,
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
                if(response.arr_student.length > 0)
                {
                  $.each(response.arr_student,function(index,value){
                    if(value.pickup_location!='' || value.drop_location!='')
                    {
                    
                        SetStudentMarker(value,transport_type);
                      
                    }
                  });
                }
                else
                {
                  if(transport_type=='pickup')
                  {
                    //swal(msg_error,msg_no_student_available_for_pickup,'error');
                  }
                  else
                  {
                    //swal(msg_error,msg_no_student_available_for_drop,'error');
                  }
                  //return false;
                }
              }
              else
              {
                swal(msg_warning, msg_no_student_found, "warning");
                return false;
              }
            }
        });
    }

    /* Set Student pickup/Drop marker on Google Map*/
    function SetStudentMarker(value,transport_type) {
        var student_id = value.get_user_details.id;
        if(transport_type=='pickup')
        {
          type = msg_pickup;
          var get_location = $.parseJSON(value.pickup_location);
          var location_title = value.pickup_address;
        }
        else
        {
          type = msg_drop;
          var get_location = $.parseJSON(value.drop_location);
          var location_title = value.drop_address;
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
                                '<div class="boldtxts">'+type+'  '+msg_location+' : </div>'+
                                '<div class="rightview-txt">'+location_title+'</div>'+
                            '</div>'+
                        '</div>'; 
                
                studentinfowindow.setContent(html);

                studentinfowindow.open(map, this);
        });
        student_marker.student_id = student_id; 
        stud_arr_marker.push(student_marker);
    };

  function calculate_location_using_lat_lng(student_lat,student_lng,student_id)
  {
      var final_distance_in_meter = distance_in_meter = 0 ;
      var Stop_No = "";
      var bus_capacity = parseInt($('#bus_capacity').val());
      
      $.each(glob_arr_marker,function(index)
      {
        stop_lat = this.position.lat();
        stop_lng = this.position.lng();
        stop_no  = this.stop_no;
        
        if(stop_lat !="" && stop_lng !="" && student_lat !="" && student_lng !="")
        { 
          distance_in_meter = parseInt(getDistance(stop_lat,stop_lng,student_lat,student_lng));
          
          if(index==0)
          {
            final_distance_in_meter = distance_in_meter;
            Stop_No = stop_no;
          }
          
          if(distance_in_meter < final_distance_in_meter )
          {
            final_distance_in_meter = distance_in_meter;
            Stop_No = stop_no;
          }
        }
      });
      
      if(Stop_No!='' && bus_capacity > 0)
      {
        arr_tmp_student.push({stop_no:Stop_No, student_id: student_id, distance: final_distance_in_meter});
        bus_capacity = bus_capacity - 1;
      }
  }

  function rad(x) {
    return x * Math.PI / 180;
  };

  function  getDistance(stop_lat,stop_lng,student_lat,student_lng)
  {
    var R = 6378137; // Earth’s mean radius in meter
    
    var dLat = rad(student_lat - stop_lat);
    var dLong = rad(student_lng - stop_lng);
    
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(rad(student_lat)) * Math.cos(rad(student_lat)) *
    Math.sin(dLong / 2) * Math.sin(dLong / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var d = R * c;
    var k = d ;// returns the distance in meter
    return Math.round(k).toFixed(2); /*fix 2 digts after decimal*/ 
  }

  /* On Change of Bus Selection get bus capacity*/
  $('#bus_id').on('change',function(){

    var bus_id         = $('#bus_id').val();
    var transport_type = $('#transport_type').val();
      
    $('#bus_id_error').html('');

    if(bus_id!='' && transport_type!='')
    {
      checkIfRouteExists(bus_id,transport_type);
      getBusCapacity(bus_id,transport_type);
    }

  });

  function getBusCapacity(bus_id,transport_type){
      $.ajax({
          url: MODULE_URL_PATH+'/get_bus_capacity',
          async: false,
          method: 'post',
          data: { bus_id : bus_id, transport_type : transport_type, _token : _token },
          dataType: 'json',
          success: function (response) {
            if(response.status=='success')
            {
              if(parseInt(response.bus_capacity) > 0 )
              {
                var bus_capacity = parseInt(response.bus_capacity);
                $('#bus_capacity').val(parseInt(response.bus_capacity));
              }
              else
              {
                swal(msg_error,msg_bus_capacity_is_full,'error');
                return false;
              }
            }
          }
      });
  }

  /* On Change of Bus Selection and Transport Type Check if route for that bus is already exists*/

  function checkIfRouteExists(bus_id,transport_type){
    $('#bus_id_error').html('');
    $('#bus_id_error').hide();

      $.ajax({
          url: MODULE_URL_PATH+'/check_if_route_exists',
          async: false,
          method: 'post',
          data: { bus_id : bus_id, transport_type : transport_type, enc_route_id : route_id, _token : _token },
          dataType: 'json',
          success: function (response) {
            if(response.status=='success')
            {
              $('#bus_id_error').show();
              $('#bus_id_error').html(response.msg);
            }
          }
      });
  }  