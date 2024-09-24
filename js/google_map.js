function initMap(){
      console.log("initMap");
      console.log(latitude);
      console.log(longitude);
      
      latitude = latitude !=''?latitude:'32.1972505';
      longitude = longitude !=''?longitude:'-7.633287';

       map = new google.maps.Map(document.getElementById('dvMap'), {
         zoom: 12,
         center: new google.maps.LatLng(latitude,longitude),
         mapTypeId: 'roadmap'
      });
      marker = new SlidingMarker({
                                        position   : new google.maps.LatLng(latitude, longitude),
                                        map        : map,
                                        title      : address ,
                                        draggable:true,
                                        icon       : BASE_URL+'/images/school_admin/pointer.png'
                                    });

      google.maps.event.addListener(marker, 'dragend', function (event) {

           $('#latitude').val(event.latLng.lat());
           $('#longitude').val(event.latLng.lng());

        });
   }

   function placeMarker(){
      console.log("placeMarker");
      marker.setMap(null);
      marker = new SlidingMarker({
                                        position   : new google.maps.LatLng(latitude, longitude),
                                        map        : map,
                                        title      : address ,
                                        draggable:true,
                                        icon       : BASE_URL+'/images/school_admin/pointer.png'
                                    });

      google.maps.event.addListener(marker, 'dragend', function (event) {

           $('#latitude').val(event.latLng.lat());
           $('#longitude').val(event.latLng.lng());

        });

      map.setCenter(new google.maps.LatLng(latitude, longitude));
   }