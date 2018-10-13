<!-- File: /app/View/Availabilities/index.ctp -->

<?php // This file contains PHP 
  echo $this->Html->script('availabilityAjax');
  echo $this->Html->script('jquery-ui-1.8.18.min');
?>

<h1>Eigene Termine</h1>

<p>
<?php echo $this->Html->link('persönlicher Terminplan', array('controller' => 'availabilities', 'action' => 'index', 'ext' => 'pdf')); ?>
</p>

<div id="iCal">
  <button id="iCalShowHide">iCal Details</button>
  <div id="calendar" style="display: none">
    <p><h5>Information zur Ver&ouml;ffentlichung des Kalenders:</h5></p>
    <p>
    Alle Termine, zu denen auch eine Anwesenheitsliste existiert, k&ouml;nnen als sogenannter
    <i>iCalendar (ICS)</i> exportiert und somit in s&auml;mtliche Kalender-Programme
    integriert werden.
    Durch Ver&ouml;ffentlichen mittels individueller Adresse wird ein Link zu deinem
    pers&ouml;nlichen Kalender generiert.
    In deiner Kalenderanwendung erstellst du nun einen neuen Kalender, und legst bei dessen
    Einstellungen fest, dass dieser sich im Netzwerk befindet.
    Anschlie&szlig;end kopierst du in das Adressfeld deinen pers&ouml;nlichen Link.
    Sofern alle Einstellungen korrekt sind, und dein Kalenderprogramm eine Verbindung
    ins Internet hat, solltest du nun alle deine Vereinstermine in deiner Kalenderanwendung
    sehen k&ouml;nnen.
    </p>
    <p>
    Achtung: Dein Link ist ohne Authentifizierung zugänglich, halte ihn daher geheim.
    Solltest du den Verdacht haben, dass andere Personen deinen Kalender lesen, dann lösche
    die bestehende Adresse und generiere eine neue.
    Anschließend brauchst du nur noch in deinen Kalenderanwendungen den Link anpassen.
    </p>
  </div>
</div>

<div class="table event">
  <div class="tr">
    <div class="th">Datum</div>
    <div class="th">Event</div>
    <div class="th">Info</div>
    <div class="th">Anwesend</div>
    <div class="th">Persönliche Information</div>
  </div>
  <?php foreach ($availabilities as $availability):
    $class = '';
    if ($availability['Event']['expired']) $class = ' expired';
    if ($availability['Event']['high_priority']) $class = ' high_priority';
    if ($availability['Event']['expiry'] == 0) $class = ' information';
    if ($availability['Event']['finished']) $class = ' finished';
    $class = ' class="tr' . $class . '"';
//    if ($class) $class = ' class="' . $class . '"';
    // prepare info field
    $event_info = h($availability['Event']['info']);
    if ($availability['Event']['info'] && $availability['Event']['location']) $event_info .= '<br />';
    if ($availability['Event']['location']) $event_info .= 'Ort: ' . h($availability['Event']['location']);
  ?>
  <div id="<?php echo 'row_' . $availability['Availability']['id']; ?>"<?php echo $class; ?>>
    <div class="td"><?php
        echo $this->Html->getDateTime($availability['Event']['start']);
        if (!$this->Html->isSameDay($availability)) echo (' ' . $this->Html->getDateTime($availability['Event']['stop']));
    ?></div>
    <div class="td"><?php
        echo $this->Html->link($availability['Event']['name'], array('controller' => 'events', 'action' => 'view',
                                $availability['Event']['id']));
    ?></div>
    <div class="td info-field"><?php echo $event_info; ?></div>
    <div class="td"><?php
        $id = $availability['Availability']['id'];
        $val = $availability['Availability']['is_available'] == false ? '0' : '1';
        $checked = $availability['Availability']['is_available'] == false ? '' : ' checked="checked"';
        echo '<input id="Availability' . $id . 'is_available" type="checkbox" name="' . $id .
                '" value="' . $val . '"' . $checked . ' field-name="is_available"/>';
        echo '<label for="Availability' . $id . 'is_available">Anwesenheit</label>';
    ?></div>
    <div class="td"><?php
        $info = $availability['Availability']['info'];
        echo '<input id="Availability' . $id . 'info" type="text" maxlength="50" name="' . $id .
                '" value="' . $info . '" field-name="info" onkeydown="evalInfo(event,' . $id . ')"/>';
    ?></div>
    <div class="td icon-save"><a href="javascript:void(0)" onclick="saveInfo(<?php echo $id ?>)" title="Info Speichern">Info Speichern</a></div>
  </div>
  <?php endforeach; ?>
  <?php unset($availability); ?>
</div>

<?php
  // write the ajax paths to a hidden <div>
  $availabilities_path = Router::url(array('controller' => 'availabilities'), true) . "/";
  echo "<div id='is_available' style='display: none'>" . $availabilities_path . "</div>";
  echo "<div id='was_available' style='display: none'>" . $availabilities_path . "</div>";
  echo "<div id='info' style='display: none'>" . $availabilities_path . "</div>";
?>


<script type="text/javascript">
var _uuid = '<?php echo $membership['Membership']['calendar_link']; ?>';
var _path = '<?php echo Router::url(array('action' => 'calendar'), true) . "/"; ?>';
var _availabilities_path = '<?php echo Router::url(array('controller' => 'availabilities'), true) . "/"; ?>';

$('#iCalShowHide').click(function(){
  button = $(this);
  $('#calendar').toggle('slow', function() {
    if ($(this).css('display') == 'block')
      button.html("iCal verbergen");
    else
      button.html("iCal Details");
  });
});


function calendarPublicate(val) {
  if(confirm(val ? 'Soll der Kalender veröffentlicht werden?' : 
                   'Soll der Link zum Kalender gelöscht werden?')) {
    $.ajax({
      url: 'availabilities/publicise',
      type: 'POST',
      dataType: 'html',
      data: 'command=' + (val ? 'create' : 'delete'),
//      dataType: 'json',
//      async: false,
//      data: '{"Calendar" : {"command" : "create"}}',
      success: function(data, textStatus, jqXHR){
        if (data == 'deleted') {
          alert('Der Link zum Kalender wurde gelöscht');
          showCalendar(_path, false);
        } else {
          alert('Kalender wurde unter folgendem Link veröffentlicht:\n' + _path + data + '.ics');
          showCalendar(_path, data);
        }
      },
//      error: function(){alert("Übertragungsfehler")}
    })
  }
}

function showCalendar(path, uuid) {
  if (uuid != false) {
    $('.calendar').detach();
    $('#calendar').append(
//      "<button class='link' onclick='$(\"p#personal_link\").toggle(\"slow\")'>Adresse zeigen</button> " +
      "<button class='calendar'onclick='calendarPublicate(false)'>Adresse l&ouml;schen</button> " +
//      "<p class='link' style=\"display: none\">" + path + uuid + "</p>"
//      "<p id='personal_link'><tt>" + path + uuid  + '.ics' + "</tt></p>"
      "<table id='personal_link'><tr><td>Standard:</td><td><tt>" + path + uuid  + '.ics' + "</tt></td></tr>" +
      "<tr><td>Google:</td><td><tt>" + path + uuid  + ".ics?client=gCal" + "</tt></td></tr></table>"
    );
  } else {
    $('#personal_link').detach();
    $('.calendar').detach();
    $('#calendar').append(
      "<button class='calendar'onclick='calendarPublicate(true)'>Adresse ver&ouml;ffentlichen</button>"
    );
  }
}

function saveInfo(id) {
  var input = $('#Availability' + id + 'info')
//  alert(input.attr('value'));
  isAvailable(id, 'info', _availabilities_path, input.attr('value'));
}

function evalInfo(e,id) {
  if (e.keyCode == 13 || e.which == 13) {
    saveInfo(id);
  }
}

$(document).ready(function(){
  // update the calendar until document is loaded
  showCalendar(_path, (_uuid ? _uuid : false));

  // each time any availability checkbox is clicked trigger the ajax request
  $('input[type=checkbox]').click(function(event) {
    var id = $(this).attr('name');
    isAvailable(id, $(this).attr('field-name'), _availabilities_path, $(this).prop('checked'));
  });
});

</script>

