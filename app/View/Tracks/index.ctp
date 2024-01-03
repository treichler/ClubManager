<!-- File: /app/View/Books/index.ctp -->

<?php // This file contains PHP 
  echo $this->Html->script('availabilityAjax');
?>

<h1>Gespielte Musikst&uuml;cke</h1>

<p>
  <b>Veranstaltung:</b>
  <?php
    echo $this->Html->link(h($event['Event']['name']),
    array('controller' => 'events', 'action' => 'view', $event['Event']['id']));
  ?>
  (<?php echo $this->Html->getDateTime($event['Event']['start'], array('year' => true)); ?>)
</p>

<table id="tracksTableId">
  <tr>
    <th>Musikst&uuml;ck</th>
    <th>Zeitstempel</th>
  </tr>
<?php foreach ($tracks as $track): ?>
  <tr id="tracksTableRowId<?php echo $track['Track']['id'] ?>">
    <td><?php echo h($track['Musicsheet']['title']); ?></td>
    <td><?php echo $track['Track']['timestamp']; ?></td>
    <td>
      <?php
        echo $this->Html->link('löschen', 'javascript:void(0)',
        array('onclick' => 'deleteTrack(' . $track['Track']['id'] . ');'));
      ?>
    </td>
  </tr>
<?php endforeach; ?>
<?php unset($track); ?>
</table>

<p>
  <div>
    <label for="tracksCheckedId">Liste der gespielten Musikst&uuml;cke bestätigen</label>
    <?php
      $checked = $event['Event']['tracks_checked'] == false ? '' : ' checked="checked"';
      echo '<input id="tracksCheckedId" type="checkbox" name="' . $event['Event']['id'] . '" value="' .
            $event['Event']['tracks_checked'] . '"' . $checked . ' field-name="tracks_checked"/>';
    ?><br />
  </div>
</p>

<select id="BookId"></select>
<select id="MusicsheetId"></select>
<button id="AddTrack">Hinzuf&uuml;gen</button>

<script type="text/javascript">
var _books = <?php echo json_encode($books); ?>;
var _books_options = '<option value=""></option>';
var _sheets = <?php echo json_encode($sheets); ?>;
var _musicsheets_options = '';
var _events_path = '<?php echo Router::url(array('controller' => 'events', 'action' => 'index'), true) . "/"; ?>';

function deleteTrack(id) {
  if (confirm("Soll das Musikstück tatsächlich gelöscht werden?")) {
    $.ajax({
      url: "<?php echo Router::url(array('controller' => 'tracks', 'action' => 'delete'), true) . "/"; ?>" + id,
      type: 'POST',
      dataType: 'html',
      data: 'whatever',
      success: function(raw_data, textStatus, jqXHR){
        var data = jQuery.parseJSON(raw_data);
        if (data.state == true) {
          target = $('#tracksTableRowId' + id);
          target.hide('slow', function(){ target.remove(); });
        } else {
          if (data.state == false)
            alert(data.message);
          else
            alert('Oops, something weired happened.');
        }
      },
      error: function(){
        alert("Übertragungsfehler");
      }
    });
  }
  return false;
}

$('#AddTrack').click(function() {
  event_id = <?php echo $event['Event']['id']?>;
  musicsheet_id = $('#MusicsheetId').val();
  data = "data%5BTrack%5D%5Bevent_id%5D=" + event_id + "&data%5BTrack%5D%5Bmusicsheet_id%5D=" + musicsheet_id;
  url  = "<?php echo Router::url(array('controller' => 'tracks', 'action' => 'add'), true); ?>";

  if (musicsheet_id) {
    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'html',
      data: data,
      success: function(raw_data, textStatus, jqXHR){
        var data = jQuery.parseJSON(raw_data);
        if (data.state == true) {
          a = "<tr id=\"tracksTableRowId" + data.data.Track.id + "\">" +
                "<td>" + data.data.Musicsheet.title + "</td>" +
                "<td>" + data.data.Track.timestamp + "</td>" +
                "<td><a href=\"javascript:void(0)\" onclick=\"deleteTrack(" + data.data.Track.id + ");\">l&ouml;schen</a></td>" +
              "</tr>";
          target = $('#tracksTableId');
          target.append(a);
        } else {
          if (data.state == false)
            alert(data.message);
          else
            alert('Oops, something weired happened.');
        }
      },
      error: function(){
        alert("Übertragungsfehler");
      }
    });
  } else {
    alert('Bitte Musikstück auswählen');
  }
});

$('#BookId').change(function(){
  _musicsheets_options = '';
  var book_id = $('#BookId').val();
  i = 0;
  for (var key in _sheets) {
    if (_sheets[key]['Sheet']['book_id'] == book_id) {
      _musicsheets_options += ("<option value=\"" + _sheets[key]['Sheet']['musicsheet_id'] + "\">" +
                              _sheets[key]['Sheet']['page'] + ": " +
                              _sheets[key]['Musicsheet']['title'] + "</option>");
    }
    i++;
  }
  $('#MusicsheetId').empty().append(_musicsheets_options);
});

$(document).ready(function(){
  for (var key in _books){
    if (_books.hasOwnProperty(key))
      _books_options += ("<option value=\"" + key + "\">" + _books[key] + "</option>");
  }
  $('#BookId').append(_books_options);

  $('#tracksCheckedId').click(function(event) {
    var id = $(this).attr('name');
    var field_name = $(this).attr('field-name');
//    isAvailable($(this).attr('name'), $(this).attr('class'), path, $(this).prop('checked'));
    isAvailable(id, field_name, _events_path, $(this).prop('checked'));
  });
});
</script>

