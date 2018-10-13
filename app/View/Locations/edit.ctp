<!-- File: /app/View/Locations/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Ort bearbeiten</h1>
<?php
  echo $this->Form->create('Location', array('action' => 'edit'));
  echo $this->element('location_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

<input id="pac-input" class="controls" type="text" placeholder="Search Box">
<div id="map-canvas"></div>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>

<script>

var map;
var marker;
var geocoder;

function setLatLng (latLng) {
  document.getElementById('LocationLatitude').value = latLng.lat();
  document.getElementById('LocationLongitude').value = latLng.lng();
}

function getLatLng () {
  lat = parseFloat(document.getElementById('LocationLatitude').value);
  lng = parseFloat(document.getElementById('LocationLongitude').value);
  if (isNaN(lat) || isNaN(lng))
    return false;
  return new google.maps.LatLng(lat, lng);
}

function initialize() {
  geocoder = new google.maps.Geocoder();

  var myLatlng = new google.maps.LatLng(46.683617, 15.99616081);
  var title = 'Probenraum Thermenarena';
  if (latLng = getLatLng()) {
    myLatlng = latLng;
    title = document.getElementById('LocationName').value;
  }

  var mapOptions = {
    zoom: 12,
    center: myLatlng
  };
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

  // create draggable marker and bind dragend functionality
  marker = new google.maps.Marker({
    position: myLatlng,
    map: map,
    title: title,
    draggable: true
  });
  google.maps.event.addListener(marker, 'dragend', function(e) {
    setLatLng(e.latLng);
  });

  // Create the search box and link it to the UI element.
  var input = /** @type {HTMLInputElement} */(
      document.getElementById('pac-input'));
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

  var searchBox = new google.maps.places.SearchBox(
    /** @type {HTMLInputElement} */(input));

  // Listen for the event fired when the user selects an item from the
  // pick list. Retrieve the matching places for that item.
  google.maps.event.addListener(searchBox, 'places_changed', function() {
    var places = searchBox.getPlaces();

    if (places.length == 0) {
      return;
    }

    // Take the first place and set the marker
    marker.setPosition(places[0].geometry.location);
    marker.setTitle('');
    map.setCenter(places[0].geometry.location);
    setLatLng(places[0].geometry.location);
    document.getElementById('LocationName').value = places[0].name;
  });

  document.getElementById('pac-input').value = document.getElementById('LocationName').value;

}

google.maps.event.addDomListener(window, 'load', initialize);

</script>

<style>
  #LocationEditForm {
    float:left;
  }

  #map-canvas {
    height: 300px;
    width: 600px;
    float: right;
    margin: 0px;
    padding: 0px;
  }

  .controls {
    margin-top: 16px;
    border: 1px solid transparent;
    border-radius: 2px 0 0 2px;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    height: 32px;
    outline: none;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
  }

  #pac-input {
    background-color: #fff;
    font-family: Roboto;
    font-size: 15px;
    font-weight: 300;
    margin-left: 12px;
    padding: 0 11px 0 13px;
    text-overflow: ellipsis;
    width: 400px;
  }

  #pac-input:focus {
    border-color: #4d90fe;
  }

  .pac-container {
    font-family: Roboto;
  }

/*
      #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
      }

      #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }
*/

</style>



