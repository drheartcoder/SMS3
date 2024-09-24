var school_latitude, school_longitude;
var geocoder;

var arr_students = [];
var str_content = '';


var directionsService = new google.maps.DirectionsService();


function initialize_for_pickup() {
  transport_type = "pickup";
  var directionsDisplay = new google.maps.DirectionsRenderer({
    suppressMarkers: true
  });

  var map = new google.maps.Map(document.getElementById('dvMap'), {
    zoom: 10,
    center: new google.maps.LatLng(-33.92, 151.25),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });

  bounds = new google.maps.LatLngBounds();

  directionsDisplay.setMap(map);
  var infowindow = new google.maps.InfoWindow();

  var marker, i, k;
  var request = {
    travelMode: google.maps.TravelMode.DRIVING
  };
  for (i = 0; i < pickup_students.length; i++) {

    var pickupinfowindow = new google.maps.InfoWindow({
      content: "school_name"
    });
    marker = new SlidingMarker({
      position: new google.maps.LatLng(pickup_students[i][1], pickup_students[i][2]),
      map: map,
      title: 'Student',
      infowindow: pickupinfowindow,
      icon: i==0 ? 'http://www.google.com/mapfiles/dd-start.png' :'http://www.google.com/mapfiles/marker.png'
    });

    bounds.extend(marker.getPosition());
    map.fitBounds(bounds);

    google.maps.event.addListener(marker, 'click', (function (marker, i) {
      return function () {
        str_content = pickup_students[i][0];
        var html =  str_content ;

        pickupinfowindow.setContent(html);

        pickupinfowindow.open(map, this);
      }
    })(marker, i));

    if (i == 0) {
      request.origin = marker.getPosition();
    } else {
      if (!request.waypoints) request.waypoints = [];
      request.waypoints.push({
        location: marker.getPosition()
      });
    }
  }

  var pickupinfowindow = new google.maps.InfoWindow({
    content: "school_name"
  });
  marker = new SlidingMarker({
    position: new google.maps.LatLng(school_latitude, school_longitude),
    map: map,
    title: source_location,
    infowindow: pickupinfowindow,
    icon: 'http://www.google.com/mapfiles/dd-end.png'
  });

  bounds.extend(marker.getPosition());
  map.fitBounds(bounds);

  google.maps.event.addListener(marker, 'click', function () {

    var html =source_location;

    pickupinfowindow.setContent(html);

    pickupinfowindow.open(map, this);
  });

  request.destination = marker.getPosition();


  directionsService.route(request, function (result, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(result);
    }
  });

}

function initialize_for_drop() {
  transport_type = "drop";
  var directionsDisplay = new google.maps.DirectionsRenderer({
    suppressMarkers: true
  });
  var view = 'dvMap2';
  if(arr_count==1){
    view = 'dvMap';
  }
  var map2 = new google.maps.Map(document.getElementById(view), {
    zoom: 10,
    center: new google.maps.LatLng(-33.92, 151.25),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });

  bounds = new google.maps.LatLngBounds();

  directionsDisplay.setMap(map2);
  var infowindow = new google.maps.InfoWindow();

  var marker, i, k;
  var request = {
    travelMode: google.maps.TravelMode.DRIVING
  };

  var pickupinfowindow = new google.maps.InfoWindow({
    content: "school_name"
  });
  marker = new SlidingMarker({
    position: new google.maps.LatLng(school_latitude, school_longitude),
    map: map2,
    title: source_location,
    infowindow: pickupinfowindow,
    icon: 'http://www.google.com/mapfiles/dd-start.png'
  });

  bounds.extend(marker.getPosition());
  map2.fitBounds(bounds);

  var listener = google.maps.event.addListener(map2, "idle", function () {
    if (map2.getZoom() > 16) map2.setZoom(16);
    google.maps.event.removeListener(listener);
  });

  google.maps.event.addListener(marker, 'click', function () {

    var html = source_location;

    pickupinfowindow.setContent(html);

    pickupinfowindow.open(map2, this);
  });

  request.origin = marker.getPosition();

  for (i = 0; i < drop_students.length; i++) {
    
    var pickupinfowindow = new google.maps.InfoWindow({
      content: "school_name"
    });
    marker = new SlidingMarker({
      position: new google.maps.LatLng(drop_students[i][1], drop_students[i][2]),
      map: map2,
      title: 'Student',
      infowindow: pickupinfowindow,
      icon: i==drop_students.length-1 ? 'http://www.google.com/mapfiles/dd-end.png' :'http://www.google.com/mapfiles/marker.png'
    });

    bounds.extend(marker.getPosition());
    map2.fitBounds(bounds);

    google.maps.event.addListener(marker, 'click', (function (marker, i) {
      return function () {
        str_content = drop_students[i][0];
        var html = str_content;

        pickupinfowindow.setContent(html);

        pickupinfowindow.open(map2, this);
      }
    })(marker, i));

    if (i == drop_students.length - 1) {
      
        request.destination = marker.getPosition();
      
    } else {
      if (!request.waypoints) request.waypoints = [];
      request.waypoints.push({
        location: marker.getPosition()
      });
    }
  }

  directionsService.route(request, function (result, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(result);
    }
  });
}

window.onload = function () {
  
  if(pickup_students.length>1){
    initialize_for_pickup();  
  }
  if(drop_students.length>1){
    initialize_for_drop();
  }
  

};