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
      $show_map = true;
      $map_link = '<a class="map-location" href="javascript:void(0)" ' .
                  'title="Veranstaltungsort auf Karte zeigen"' .
                  '" info="' . $info .
                  '" lat="' . $event['Location']['latitude'] .
                  '" lng="' . $event['Location']['longitude'] .
                  '" rad="' . $event['Location']['radius'] . '">Karte</a>';
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
    <td><?php echo h($event['Group']['name']); ?></td>
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

<?php if ($show_map): ?>
<div id="map-canvas"></div>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>

<script type="text/javascript">
$(".map-location").click(function(event) {
  lat = parseFloat(event.target.attributes['lat'].value);
  lng = parseFloat(event.target.attributes['lng'].value);
  if (isNaN(lat) || isNaN(lng))
    return false;

  // scroll to center of map
  obj = $("#map-canvas");
  $('html,body').animate({ scrollTop: obj.offset().top - ( $(window).height() - obj.outerHeight(true) ) / 2  }, 200);

  // set marker and center map to coordinate
  pos = new google.maps.LatLng(lat, lng);
  marker.setPosition(pos);
//  marker.setTitle(event.target.attributes['info'].value);
  marker.setVisible(true);
  infoWindow.setContent(event.target.attributes['info'].value);
  map.setCenter(pos);
//  map.setZoom(14);
});


var map;
var marker;
var infoWindow;
var circle;

function initialize() {
  // default coordinate and title
  latitude  = <?php echo Configure::read('club.latitude'); ?>;
  longitude = <?php echo Configure::read('club.longitude'); ?>;
  building  = "<?php echo Configure::read('club.building'); ?>";
  name      = "<?php echo Configure::read('club.name'); ?>";
  var myLatlng = new google.maps.LatLng(latitude, longitude);
  var title = name + ', ' + building;

  // create map centered to default coordinate
  var mapOptions = {
    zoom: 15,
    center: myLatlng
  };
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

  // create marker
  marker = new google.maps.Marker({
    position: myLatlng,
//    title: title,
    map: map,
    draggable: false,
  });

  // create info window
  infoWindow =  new google.maps.InfoWindow({
    content: title,
    map: map
  });

  // link info window to marker
  infoWindow.open(map, marker);

  // add "open info window" as click function to marker
  marker.addListener('click', function() {
    infoWindow.open(map, marker);
  });
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php endif; ?>

