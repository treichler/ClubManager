<!-- File: /app/View/Elements/location_form.ctp -->

<div id="LocationInputFields">
<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('name', array('label' => 'Ort'));
  echo $this->Form->input('longitude', array('label' => 'Längengrad'));
  echo $this->Form->input('latitude', array('label' => 'Breitengrad'));
  echo $this->Form->input('radius', array('label' => 'Radius [m]'));
  echo $this->Form->input('show_on_map', array('label' => 'Auf öffentlicher Karte zeigen'));
  echo $this->Form->end('Speichern');
?>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.4/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.4/leaflet.js"></script>

<div id="LocationMapCanvas"></div>

<script>
  // write latitude/longitude to form
  function setLatLng (position) {
    document.getElementById('LocationLatitude').value = position.lat;
    document.getElementById('LocationLongitude').value = position.lng;
  }

  // get latitude/longitude from form
  function getLatLng () {
    lat = parseFloat(document.getElementById('LocationLatitude').value);
    lng = parseFloat(document.getElementById('LocationLongitude').value);
    if (isNaN(lat) || isNaN(lng))
      return false;
    return {lat : lat, lng : lng};
  }

  // get radius from form
  function getRadius () {
    r = parseFloat( document.getElementById('LocationRadius').value );
    if( isNaN(r) )
      return 0;
    return r;
  }

  // default position (club's home)
  latitude  = <?php echo Configure::read('club.latitude'); ?>;
  longitude = <?php echo Configure::read('club.longitude'); ?>;

  // overwrite default position
  if( position = getLatLng() ) {
    latitude  = position.lat;
    longitude = position.lng;
  }

  // initialize the map on the "map-canvas" div with a given center and zoom
  var map = L.map('LocationMapCanvas', {
      center: [latitude, longitude],
      zoom: 16
  });

  // load layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  // create circle
  var circle = L.circle([latitude, longitude], {
      color: 'red',
      fillColor: 'red',
      fillOpacity: 0.2,
      radius: 0
  });

  // set circle's radius
  function setRadius(radius) {
    circle.setRadius( radius );
    if( radius > 0 )
      circle.addTo(map);
    else
      circle.remove();
  }

  // initially set circle's radius
  setRadius( getRadius() );

  // bind radius input change event to set the circle's radius
  document.getElementById('LocationRadius').addEventListener('input', function (evt) {
    setRadius( getRadius() );
  });

  // set draggable marker to default destination
  var marker = L.marker( [latitude, longitude], {draggable: 'true'} ).addTo(map);

  // bind dragend event function
  marker.on('dragend', function(event) {
    var position = marker.getLatLng();
    marker.setLatLng(position, {
      draggable: 'true'
    }).bindPopup(position).update();
    setLatLng(position);
    circle.setLatLng(position);
  });
</script>


