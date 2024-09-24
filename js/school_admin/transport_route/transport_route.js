    var source_location   = 'Ashoka Business School';
    var source_lat        = 19.9799446;
    var source_lng        = 73.7610331;
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
    var transport_type    = $('#transport_type').val();
    
    var ARR_MAPS_STYLE  = [];
    var STYLE_JSON_FILE = BASE_URL+'/assets/maps_style_2.json';

    window.onload = function () {
          if(transport_type=='pickup')
          {
            var location_type = "Destination";
          }
          if(transport_type=='drop')
          {
            var location_type = "Source";
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
              SetDestinationMarker(destination_location,destination_lat,destination_lng,location_type);
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

    function initGoogleAutoComponent(elem,options,autocomplete_ref)
    {
      autocomplete_ref = new google.maps.places.Autocomplete(elem,options);
      autocomplete_ref = createPlaceChangeListener(autocomplete_ref,fillInAddress);

      return autocomplete_ref;
    }

    function createPlaceChangeListener(autocomplete_ref,fillInAddress)
    {
      autocomplete_ref.addListener('place_changed', fillInAddress);
      return autocomplete_ref;
    }

    function fillInAddress()
    {

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
              marker.setMap(null);
          });
          glob_arr_marker = [];
        }

        if(markers.length > 0)
        {
          for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
          }
          markers = [];
        }
        //Draw Route Map
        var destination_location   = $('#address').val();
        var destination_lat        = place.geometry.location.lat();
        var destination_lng        = place.geometry.location.lng();
        var transport_type         = $('#transport_type').val();
        $('#json_pickup_drop_point').val('');

        if(transport_type=='pickup')
        {
          var location_type = "Destination";
        }
        if(transport_type=='drop')
        {
          var location_type = "Source";
        }
        //Set Destination Marker
        SetDestinationMarker(destination_location,destination_lat,destination_lng,location_type);
        
        //Get Student list according to Pickup / Drop
        setTimeout(function(){
          if(transport_type!='')
          {
            getStudentList(transport_type);
          }
        }, 2000);
    }
    /* Google AutoComplete Address Script Ends here*/    


      var map;
      var bounds;
      var marker;
      
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
          
          google.maps.event.addListener(map, 'click', function(event) 
          {
              if(markers.length > 4)
              {
                swal("Error!", "Maximum 5 stops are allowed!", "error");
                return false;
              }
              else
              {
                if(destination_lat!='' && destination_lng!='')
                {
                  if(glob_arr_marker[glob_arr_marker.length]==undefined && markers.length == glob_arr_marker.length + 1 && markers.length > 0)
                  {
                    swal("Error!", "Please fill all information for current pickup/drop location!", "error");
                    return false;
                  } 
                  else
                  {
                    var current_marker = createPickupDropLocationMarker(map,schlatlng);
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
                  var location_type = "Source";
                }
                if($('#transport_type').val()=='pickup')
                {
                  var location_type = "Destination";
                }
              
              var html = '<div class="modal-content">'+
                      '<div class="modal-body">'+
                      '<div class="review-detais">'+
                              '<div class="boldtxts">'+location_type+' Location : </div>'+
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
          LoadMap(destination_lat,destination_lng,$('#transport_type').val(),location_type);
          
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
                    var location_type = "Destination";
                  }
                  if($('#transport_type').val()=='pickup')
                  {
                    var location_type = "Source";
                  }

                  var html = '<div class="modal-content">'+
                          '<div class="modal-body">'+
                          '<div class="review-detais">'+
                                  '<div class="boldtxts">'+location_type+' Location : </div>'+
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

    function createPickupDropLocationMarker(map,position)
    {
        var marker = new google.maps.Marker({
              position  : position,
              draggable : true,
              map: map
          });

        marker.addListener('click', function() 
        {
          current_marker = this;
        });

        marker.addListener('click', function() 
        {
          current_marker = this;
          if(markerExists(current_marker.position)!=false)
          {
            data = getMarkerData(current_marker.position);

            html = "<div>"+
                      "<input type='text' name='stop_no'  value='"+data.stop_no+"' placeholder='Stop No' class='form-control'/> <br>"+
                      "<input type='text' name='stop_name'  value='"+data.stop_name+"' placeholder='Stop Name' class='form-control'/> <br>"+
                      "<input type='text' name='landmark'  value='"+data.landmark+"' placeholder='Landmark' class='form-control'/> <br>"+
                      "<input type='text' name='stop_fees'  value='"+data.stop_fees+"' placeholder='Stop Fees' class='form-control'/> <br>"+
                      "<button type='button' onclick='removeMarker(1)' style='margin-left:10px' class='btn btn-primary btn-sm'>Remove</button>&nbsp;&nbsp;"+
                    "<button type='button' onclick='updateMarkerData("+current_marker.position.lat()+","+current_marker.position.lng()+",this)' class='btn btn-primary btn-sm'>Update Info</button></div>";

            glob_info_window.setContent(html);
            glob_info_window.open(map,this);  
          }
          else
          {
            html = "<div>"+
                      "<input type='text' name='stop_no' placeholder='Stop No' class='form-control'/> <br>"+
                      "<input type='text' name='stop_name' placeholder='Stop Name' class='form-control'/> <br>"+
                      "<input type='text' name='landmark' placeholder='Landmark' class='form-control'/> <br>"+
                      "<input type='text' name='stop_fees' placeholder='Stop Fees' class='form-control'/> <br>"+
                    "<button type='button' onclick='removeMarker(0)' style='margin-left:10px' class='btn btn-primary btn-sm'>Remove</button>&nbsp;&nbsp;"+
                    "<button type='button' onclick='stackCurrentLocation(true,this)' class='btn btn-primary btn-sm'>Add</button></div>";

            glob_info_window.setContent(html);
            glob_info_window.open(map,this);   
          }
          
        });

        return marker;
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

    /* Add Pickup/Drop Location marker with Validation */
    function stackCurrentLocation(close_infowindow,ref)
    {
      close_infowindow = close_infowindow | false;

      if(close_infowindow)
      {
        glob_info_window.close();
      }

      var parent_div = $(ref).parent("div");
      
      current_marker.stop_no   = $(parent_div).find("input[name='stop_no']").val();
      current_marker.stop_name = $(parent_div).find("input[name='stop_name']").val();
      current_marker.landmark  = $(parent_div).find("input[name='landmark']").val();
      current_marker.stop_fees = $(parent_div).find("input[name='stop_fees']").val();

      var price_filter = /^(?!0+$)\d{1,8}(\.\d{1,2})?$/;
      
      if(current_marker.stop_no.length<=0)
      {
        swal("Error!", "The Stop No Field Cannot be Empty!", "error");
        return false;
      }

      if(current_marker.stop_name.length<=0)
      {
        swal("Error!", "The Stop Name Field Cannot be Empty!", "error");
        return false;
      }
      if(current_marker.landmark.length<=0)
      {
        swal("Error!", "The Landmark Field Cannot be Empty!", "error");
        return false;
      }
      if(current_marker.stop_fees.length<=0)
      {
        swal("Error!", "The Stop Fees Field Cannot be Empty!", "error");
        return false;
      }

      if(!price_filter.test(current_marker.stop_fees))
      {
        swal("Error!", "Please enter valid Stop Fees!", "error");
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
        swal("Error!", "Stop No Field Cannot be Duplicate!", "error");
        return false;
      }

      glob_arr_marker.push(current_marker);
      
      serializePickupDropPoints();
    }

    /* Serialize the pickup drop data */
    function serializePickupDropPoints()
    {
      $('#json_pickup_drop_point').val('');
      var arr_tmp = [];
      if(glob_arr_marker.length>0)
      {
        $.each(glob_arr_marker,function(index)
        {
            arr_tmp.push({lat:this.position.lat(),lng:this.position.lng(),stop_no:this.stop_no,stop_name:this.stop_name,landmark:this.landmark,stop_fees:this.stop_fees});
        });
        
        $('#json_pickup_drop_point').val(JSON.stringify(arr_tmp));
      }
    }

    /* Remove Pickup/Drop Marker */
    function removeMarker(type)
    {
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
              if(markers.length > 0 && marker.position.lat() == current_marker.position.lat() && marker.position.lng() == current_marker.position.lng())
              {
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
    
    function updateMarkerData(lat,lng,ref)
    {
      var parent_div = $(ref).parent("div");
     
      var stop_no    = $(parent_div).find("input[name='stop_no']").val();
      var stop_name  = $(parent_div).find("input[name='stop_name']").val();
      var landmark   = $(parent_div).find("input[name='landmark']").val();
      var stop_fees  = $(parent_div).find("input[name='stop_fees']").val();

      var price_filter = /^(?!0+$)\d{1,8}(\.\d{1,2})?$/;

      if(stop_no.length<=0)
      {
        swal("Error!", "The Stop No Field Cannot be Empty!", "error");
        return false;
      }

      if(stop_name.length<=0)
      {
        swal("Error!", "The Stop Name Field Cannot be Empty!", "error");
        return false;
      }

      if(landmark.length<=0)
      {
        swal("Error!", "The Landmark Field Cannot be Empty!", "error");
        return false;
      }

      if(stop_fees.length<=0)
      {
        swal("Error!", "The Stop Fees Field Cannot be Empty!", "error");
        return false;
      }

      if(!price_filter.test(stop_fees))
      {
        swal("Error!", "Please enter valid Stop Fees!", "error");
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
        swal("Error!", "Stop No Field Cannot be Duplicate!", "error");
        return false;
      }

      if(marker_index!==false)
      { 
        glob_arr_marker[marker_index].stop_no    = stop_no;
        glob_arr_marker[marker_index].stop_name  = stop_name;
        glob_arr_marker[marker_index].landmark   = landmark;
        glob_arr_marker[marker_index].stop_fees  = stop_fees;
        glob_info_window.close();
      }
      serializePickupDropPoints();
    }    

    /* Add Pickup Drop Location Script Ends here*/

    /* On Change of Transport Route show student List according to pickup/drop*/
    $('#transport_type').on('change',function(){

      var transport_type         = $('#transport_type').val();
      if(transport_type!='')
      {
        getStudentList(transport_type);
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
    
    function getStudentList(transport_type)
    {
        $.ajax({
            url: MODULE_URL_PATH+'/get_student_list?transport_type='+transport_type,
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
                  if(value.pickup_location!='' || value.drop_location!='')
                  {
                    SetStudentMarker(value,transport_type);
                  }
                });
              }
              else
              {
                swal("Error!", "No Student found!", "error");
                return false;
              }
            }
        });
    }

    /* Set Student pickup/Drop marker on Google Map*/
    
    function SetStudentMarker(value,transport_type) {
        if(transport_type=='pickup')
        {
          type = 'Pickup';
          var get_location = $.parseJSON(value.pickup_location);
          var location_title = value.pickup_address;
        }
        else
        {
          type = 'Drop';
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
                                '<div class="boldtxts">'+type+' Location : </div>'+
                                '<div class="rightview-txt">'+location_title+'</div>'+
                            '</div>'+
                        '</div>'; 
                
                studentinfowindow.setContent(html);

                studentinfowindow.open(map, this);
        });
        stud_arr_marker.push(student_marker);
    };
