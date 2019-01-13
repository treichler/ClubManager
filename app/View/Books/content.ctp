<!-- File: /app/View/Books/content.ctp -->

<?php // This file contains PHP ?>

<h1><?php echo $book['Book']['title'] ?></h1>

<p><?php echo $book['Book']['description'] ?></p>


<?php echo $this->Form->create('Book', array('url' => 'content')); ?>
<?php echo $this->Form->input('Book.id', array('type' => 'hidden', 'value' => $book['Book']['id'])); ?>

<table id="bookSheetsTable">
  <tr>
    <th>Musikst&uuml;ck</th>
    <th>Seite</th>
  </tr>

  <?php $i = 0; ?>
  <?php foreach ($sheets as $sheet): ?>
  <tr id="bookSheetsTableRow<?php echo $i;?>">
    <td>
      <?php
        echo $this->Form->input('Sheet.' . $i . '.id', array('type' => 'hidden', 'value' => $sheet['Sheet']['id']));
        echo $this->Form->input('Sheet.' . $i . '.book_id', array('type' => 'hidden', 'value' => $book['Book']['id']));
        echo $this->Form->input('Sheet.' . $i . '.musicsheet_id', array('label' => false, 'value' => $sheet['Sheet']['musicsheet_id']));
      ?>
    </td>
    <td>
      <?php
        echo $this->Form->input('Sheet.' . $i . '.page', array('label' => false, 'value' => $sheet['Sheet']['page'], 'class' => 'sheet-page'));
      ?>
    </td>
    <td>
      <?php
        echo $this->Html->link('löschen', 'javascript:void(0)',
        array('id' => 'SheetDelete' . $i, 'onclick' => 'deleteSheet(' . $i . ');'));
      ?>
    </td>
  </tr>
  <?php $i += 1; ?>
  <?php endforeach; ?>
  <?php unset($sheet); ?>

<?php if (isset($new_sheets)): ?>
  <?php foreach ($new_sheets as $new_sheet): ?>
  <tr>
    <td>
      <?php
        echo $this->Form->input('Sheet.' . $i . '.book_id', array('type' => 'hidden', 'value' => $book['Book']['id']));
        echo $this->Form->input('Sheet.' . $i . '.musicsheet_id', array('label' => false, 'value' => $new_sheet['Sheet']['musicsheet_id']));
      ?>
    </td>
    <td>
      <?php
        echo $this->Form->input('Sheet.' . $i . '.page', array('label' => false, 'value' => $new_sheet['Sheet']['page']));
      ?>
    </td>
    <td>
      <?php
        echo $this->Html->link('löschen', 'javascript:void(0)',
        array('id' => 'SheetDelete' . $i, 'onclick' => 'deleteSheet(' . $i . ');'));
      ?>
    </td>
  </tr>
  <?php $i += 1; ?>
  <?php endforeach; ?>
  <?php unset($new_sheet); ?>
<?php endif; ?>
</table>

<p><b><a href="javascript:void(0)" onclick="addSheet();">Neues Musikst&uuml;ck hinzufügen</a></b></p>

<?php  echo $this->Form->end('Speichern'); ?>

<?php
  // prepare musicsheets array for javascript option list
  $musicsheets_js = [];
  foreach($musicsheets as $key => $val) {
    $musicsheets_js[] = array('id' => $key, 'title' => $val);
  }
?>

<script type="text/javascript">
var _book_id = <?php echo $book['Book']['id'] ?>;
var _musicsheets = <?php echo json_encode($musicsheets_js) ?>;
var _musicsheets_options = '';
var _sheet_index = <?php echo $i; ?>;

$(document).ready(function(){
  for (i = 0; i < _musicsheets.length; i++){
    _musicsheets_options += ("<option value=\"" + _musicsheets[i]['id'] + "\">" +
                            _musicsheets[i]['title'] + "</option>");
  }
});

function addSheet() {
  i = _sheet_index;
  a = "<tr id=\"bookSheetsTableRow" + i + "\">" +
        "<td>" +
          "<input type=\"hidden\" name=\"data[Sheet][" + i + "][book_id]\" value=\"" + _book_id + "\" id=\"Sheet" + i + "BookId\" / >" +
          "<div class=\"input select\">" +
            "<select name=\"data[Sheet][" + i + "][musicsheet_id]\" id=\"Sheet" + i + "MusicsheetId\"></select>" +
          "</div>" +
        "</td>" +
        "<td>" +
          "<div class=\"input number required\">" + 
            "<input name=\"data[Sheet][" + i + "][page]\" type=\"number\" id=\"Sheet" + i + "Page\" class=\"sheet-page\" required=\"required\" / >" +
          "</div>" +
        "</td>" +
        "<td><a id=\"SheetDelete" + i + "\" href=\"javascript:void(0)\" onclick=\"deleteSheet(" + i + ");\">l&ouml;schen</a></td>" +
      "</tr>";
  $('#bookSheetsTable').append(a);
  $("#Sheet" + i + "MusicsheetId").append(_musicsheets_options);
  _sheet_index++;
}

function replaceIndex(old_i, new_i) {
  $("#bookSheetsTableRow" + old_i).attr("id", "bookSheetsTableRow" + new_i);

  $("#SheetDelete" + old_i).attr("onclick", "deleteSheet(" + new_i + ")");
  $("#SheetDelete" + old_i).attr("id", "SheetDelete" + new_i);

  if ($("#Sheet" + old_i + "Id").attr("id")) {
    $("#Sheet" + old_i + "Id").attr("name", "data[Sheet][" + new_i + "][id]");
    $("#Sheet" + old_i + "Id").attr("id", "Sheet" + new_i + "Id");
  }

  $("#Sheet" + old_i + "BookId").attr("name", "data[Sheet][" + new_i + "][book_id]");
  $("#Sheet" + old_i + "BookId").attr("id", "Sheet" + new_i + "BookId");

  $("#Sheet" + old_i + "MusicsheetId").attr("name", "data[Sheet][" + new_i + "][musicsheet_id]");
  $("#Sheet" + old_i + "MusicsheetId").attr("id", "Sheet" + new_i + "MusicsheetId");

  $("#Sheet" + old_i + "Page").attr("name", "data[Sheet][" + new_i + "][page]");
  $("#Sheet" + old_i + "Page").attr("id", "Sheet" + new_i + "Page");
}

function deleteSheet(index) {
  if (confirm("Soll die Seite tatsächlich gelöscht werden?")) {
    if ($("#Sheet" + index + "Id").attr("id")) {
      deleteRequest(index);
    } else {
      removeSheet(index);
    }
  }
  return false;
}

function removeSheet(index) {
  target = $("#bookSheetsTableRow" + index);
  target.hide('slow', function(){ target.remove(); });
  for (i = index + 1; i <= _sheet_index; i++) {
    replaceIndex(i, i - 1)
  }
  _sheet_index--;
}

function deleteRequest(index) {
  id = $("#Sheet" + index + "Id").val()
  $.ajax({
    url: "<?php echo Router::url(array('controller' => 'sheets', 'action' => 'delete'), true) . "/"; ?>" + id,
    type: 'POST',
    dataType: 'html',
    data: 'whatever',
    success: function(data, textStatus, jqXHR){
      if (data == 'true') {
        removeSheet(index);
      } else {
        if (data == 'false')
          alert('Seite konnte nicht gelöscht werden');
        else
          alert('Oops, something weired happened.');
      }
    },
    error: function(){
      alert("Übertragungsfehler");
    }
  });
}

</script>

