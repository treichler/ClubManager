<!-- File: /app/View/Events/index.ctp -->

<?php // This file contains PHP ?>

<h1>Termine</h1>

<div id="iCal">
  <button id="iCalShowHide">Kalender abonnieren</button>
  <div id="calendar" style="display: none">
    <p><h5>Kalender abonnieren</h5></p>
    <p>
    Sie k&ouml;nnen unseren Kalender abonnieren, indem Sie in Ihrer Kalenderanwendung einen
    neuen Kalender anlegen.
    Bei den Einstellungen des neuen Kalenders legen Sie fest, dass dieser sich im Netzwerk
    befindet.
    Geben Sie folgenden Link als Adresse des Netzwerks an:
    </p>
    <table>
      <tr><td>Link:</td><td><tt><?php 
        echo Router::url(array('controller' => 'events', 'action' => 'news', 'ext' => 'ics'), true); 
      ?></tt></td></tr>
    </table>
  </div>
</div>

<table>
<?php
  $show_map = false;
  foreach ($events as $event):
    // prepare information
    $info = h($event['Event']['name']);
    if ($event['Event']['location']) {
      $info .= ', ' . h($event['Event']['location']);
    }

    // prepare link only if there are coordinates and "show_on_map" is true
    if ($event['Location']['show_on_map'] &&
        $event['Location']['latitude'] != null &&
        $event['Location']['longitude'] != null) {
      $map_link = '<a class="map-location" href="javascript:void(0)" ' .
                  'title="Veranstaltungsort auf Karte zeigen" ' .
                  'event_name="' . h($event['Event']['name']) . '" ' .
                  'event_location="' . h($event['Event']['location']) . '" ' .
                  'lat="' . $event['Location']['latitude'] . '" ' .
                  'lng="' . $event['Location']['longitude'] . '" ' .
                  'rad="' . $event['Location']['radius'] . '">Karte</a>';
    } else {
      $map_link = '';
    }
?>
  <tr>
    <td><?php
        if ($event['Event']['show_official_start'] != true ||
            $event['Event']['official_start'] === '0000-00-00 00:00:00') {
          echo $this->Html->getDate($event['Event']['start'], array('year' => true));
        } else {
          echo $this->Html->getDateTime($event['Event']['official_start'], array('year' => true));
        }
        if (!$this->Html->isSameDay($event)) echo (' - ' . $this->Html->getDate($event['Event']['stop'], array('year' => true)));
    ?></td>
    <td><?php echo $info; ?></td>
    <td><?php
        $groups = [];
        foreach($event['Group'] as $group) {
          $groups[] = h($group['name']);
        }
        unset($group);
        echo implode(', ', $groups);
    ?></td>
    <td class="icon-map"><?php echo $map_link; ?></td>
  </tr>
<?php
  endforeach;
  unset($event);
?>
</table>

<script type="text/javascript">
$('#iCalShowHide').click(function(){
  button = $(this);
  $('#calendar').toggle('slow', function() {
    if ($(this).css('display') == 'block')
      button.html("Schließen");
    else
      button.html("Kalender abonnieren");
  });
});
</script>


<?php // if ($show_map): ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.4/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.4/leaflet.js"></script>

<div id="map-canvas"></div>

<script type="text/javascript">
  // format the popup content
  function formatPopupContent (name, location) {
    content = "<strong>" + name + "</strong>";
    if( location )
      content += "<br>" + location;
    return content;
  }

  // club's home data
  latitude  = <?php echo Configure::read('club.latitude'); ?>;
  longitude = <?php echo Configure::read('club.longitude'); ?>;
  building  = "<?php echo Configure::read('club.building'); ?>";
  name      = "<?php echo Configure::read('club.name'); ?>";

  // initialize the map on the "map-canvas" div with a given center and zoom
  var map = L.map('map-canvas', {
      center: [latitude, longitude],
      zoom: 16
  });

  // load layer
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  // set marker to home destination
  var marker = L.marker([latitude, longitude]).addTo(map);

  // put a popup text to the marker
  marker.bindPopup( formatPopupContent(name, building) ).openPopup();

  // create circle
  var circle = L.circle([0, 0], {
      color: 'red',
      fillColor: 'red',
      fillOpacity: 0.2,
      radius: 0
  });

  // set circle
  function setCircle(lat, lng, radius) {
    if( isNaN(radius) || radius <= 0 ) {
      circle.remove();
      return;
    }
    circle.setLatLng( {lat : lat, lng : lng} );
    circle.setRadius( radius );
    circle.addTo(map);
  }

  // click function
  $(".map-location").click(function(event) {
    lat = parseFloat(event.target.attributes['lat'].value);
    lng = parseFloat(event.target.attributes['lng'].value);
    radius = parseFloat(event.target.attributes['rad'].value);

    // set circle
    setCircle(lat, lng, radius);

    if (isNaN(lat) || isNaN(lng))
      return false;

    // scroll to center of map
    obj = $("#map-canvas");
    $('html,body').animate({ scrollTop: obj.offset().top - ( $(window).height() - obj.outerHeight(true) ) / 2  }, 200);

    // set marker and center map to coordinate
    marker.setLatLng( {lat : lat, lng : lng} );
    var info = formatPopupContent( event.target.attributes['event_name'].value, event.target.attributes['event_location'].value);
    marker.setPopupContent( info );
    map.setView( {lat : lat, lng : lng} )
  });

</script>
<?php // endif; ?>


