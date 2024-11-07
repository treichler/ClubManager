<!-- File: /app/View/Elements/sheet_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('title', array('label' => 'Titel', 'list' => 'musicsheetList'));
  echo $this->Form->input('details', array('rows' => '3', 'label' => 'Details'));
//  echo $this->Form->input('Composer.Composer', array('label' => 'Komponist', 'empty' => true));
//  echo $this->Form->input('Arranger.Arranger', array('label' => 'Arrangeur', 'empty' => true));

//  echo $this->Form->input('publisher_id', array('label' => 'Verlag', 'empty' => true));
//  echo $this->Form->input('publisher_id', array('type' => 'text', 'empty' => true));
  echo $this->Form->input('publisher_id', array('type' => 'hidden', 'empty' => true));
?>

<!-- Composer -->
<div class="input text" id="composers">
  <label for="composer">Komponist(en), Format: <i>Familienname, Vorname, Geburtsjahr-Monat-Tag</i></label>
</div>
<div>
  <a href="javascript:void(0)" onclick="addComposer();">Komponist hinzufügen</a>
</div>

<!-- Arranger -->
<div class="input text" id="arrangers">
  <label for="arranger">Arrangeur(e), Format: <i>Familienname, Vorname, Geburtsjahr-Monat-Tag</i></label>
</div>
<div>
  <a href="javascript:void(0)" onclick="addArranger();">Arrangeur hinzufügen</a>
</div>

<!-- Publisher -->
<div class="input text">
  <label for="PublisherName">Verlag</label>
  <input name="data[new][Publisher][name]" list="publisherList" id="PublisherName">
</div>

<?php echo $this->Form->input('archives', array('label' => 'Archiv')); ?>


<script type="text/javascript">

// global variables
var _composers           = <?php echo json_encode($composers) ?>;
var _selected_composers  = <?php echo json_encode($this->Html->value('Composer.Composer')); ?>;

var _arrangers           = <?php echo json_encode($arrangers) ?>;
var _selected_arrangers  = <?php echo json_encode($this->Html->value('Arranger.Arranger')); ?>;

var _musicsheets         = <?php echo json_encode($musicsheets) ?>;
var _publishers          = <?php echo json_encode($publishers) ?>;

var _arranger_index = 0;
var _composer_index = 0;


$(document).ready(function(){
  //------------------
  // Musicsheet's init
  //------------------

  // create option list for musicsheet
  // for bigger datasets it would be better to load data via ajax requests
  datalist = document.createElement("datalist");
  datalist.id = 'musicsheetList';
  for (i in _musicsheets) {
    option = document.createElement("option");
    option.value = _musicsheets[i];
    datalist.appendChild(option);
  }
  $('#MusicsheetTitle').after(datalist);


  //------------------
  // Publisher's init
  //------------------

  // create option list for publisher
  // for bigger datasets it would be better to load data via ajax requests
  datalist = document.createElement("datalist");
  datalist.id = 'publisherList';
  for (i in _publishers) {
    option = document.createElement("option");
    option.value = _publishers[i];
    datalist.appendChild(option);
  }
  $('#PublisherName').after(datalist);

  // write previous publisher to input
  $('#PublisherName').attr( 'value', _publishers[$('#MusicsheetPublisherId').attr('value')] );


  //------------------
  // Arrangers' init
  //------------------

  // create option list for arranger
  datalist = document.createElement("datalist");
  datalist.id = 'arrangerList';
  for (i in _arrangers) {
    option = document.createElement("option");
    option.value = _arrangers[i];
    datalist.appendChild(option);
  }
  $('#arrangers').append(datalist);

  // write previous arrangers to the document
  for(j in _selected_arrangers) {
    addArranger(_arrangers[_selected_arrangers[j]], _selected_arrangers[j]);
  }

  // if there is none create one empty arranger-input
  if(!_selected_arrangers) {
    addArranger();
  }


  //------------------
  // Composers' init
  //------------------

  // create option list for composer
  datalist = document.createElement("datalist");
  datalist.id = 'composerList';
  for (i in _composers) {
    option = document.createElement("option");
    option.value = _composers[i];
    datalist.appendChild(option);
  }
  $('#composers').append(datalist);

  // write previous composers to the document
  for(j in _selected_composers) {
    addComposer(_composers[_selected_composers[j]], _selected_composers[j]);
  }

  // if there is none create one empty composer-input
  if(!_selected_composers) {
    addComposer();
  }

});


// return the index of the hash-value
function objectIndexOf(associativeArray, value) {
  for(var key in associativeArray) {
    if(associativeArray[key]==value)
      return key;
  }
  return -1;
}


//-----------------------
// Arranger's functions
//-----------------------

// add arranger to the document
function addArranger(name = 0, id = 0){
  d = document.createElement("div");
  d.id = "arranger_" + _arranger_index;

  b = document.createElement("input");
  b.setAttribute("list", "arrangerList");
  b.setAttribute("onchange", "updateArrangerId(" + _arranger_index + ");");
  b.setAttribute("id", "arrangerInput_" + _arranger_index);
  if(name)
    b.setAttribute("value", name);
  else
    b.name = "data[new][Arranger][" + _arranger_index + "][name]";
  d.innerHTML = b.outerHTML;

  b = document.createElement("input");
  b.name = "data[Arranger][Arranger][" + _arranger_index + "]";
  b.setAttribute("id", "arrangerIdInput_" + _arranger_index);
  b.setAttribute("type", "hidden");
  if(id)
    b.setAttribute("value", id);
  d.innerHTML += b.outerHTML;

  b = document.createElement("a");
  b.href = "javascript:void(0)";
  b.setAttribute("onclick", "deleteArranger(" + _arranger_index + ");");
  b.innerHTML = "l&ouml;schen";
  d.innerHTML += b.outerHTML;

  $('#arrangers').append(d);
  _arranger_index++;
}


// update arranger's id-field
function updateArrangerId(index){
  value = $("#arrangerInput_" + index).attr("value");
  id = objectIndexOf(_arrangers, this.value);
  if(id <= 0) {
    id = '';
    $("#arrangerIdInput_" + index).attr("name", "");
    $("#arrangerInput_" + index).attr("name", "data[new][Arranger][" + index + "][name]");
  }
  else {
    $("#arrangerIdInput_" + index).attr("name", "data[Arranger][Arranger][" + index + "]");
    $("#arrangerInput_" + index).attr("name", "");
  }
  $("#arrangerIdInput_" + index).attr("value", id);
}


// delete arranger from document
function deleteArranger(index){
  target = $("#arranger_" + index);
  if (confirm("Soll der Arangeur von diesem Musikstück tatsächlich entfernt werden?")) {
    target.remove();
  }
}


//-----------------------
// Composers's functions
//-----------------------

// add composer to the document
function addComposer(name = 0, id = 0){
  d = document.createElement("div");
  d.id = "composer_" + _composer_index;

  b = document.createElement("input");
  b.setAttribute("list", "composerList");
  b.setAttribute("onchange", "updateComposerId(" + _composer_index + ");");
  b.setAttribute("id", "composerInput_" + _composer_index);
  if(name)
    b.setAttribute("value", name);
  else
    b.name = "data[new][Composer][" + _composer_index + "][name]";
  d.innerHTML = b.outerHTML;

  b = document.createElement("input");
  b.name = "data[Composer][Composer][" + _composer_index + "]";
  b.setAttribute("id", "composerIdInput_" + _composer_index);
  b.setAttribute("type", "hidden");
  if(id)
    b.setAttribute("value", id);
  d.innerHTML += b.outerHTML;

  b = document.createElement("a");
  b.href = "javascript:void(0)";
  b.setAttribute("onclick", "deleteComposer(" + _composer_index + ");");
  b.innerHTML = "l&ouml;schen";
  d.innerHTML += b.outerHTML;

  $('#composers').append(d);
  _composer_index++;
}


// update composer's id-field
function updateComposerId(index){
  value = $("#composerInput_" + index).attr("value");
  id = objectIndexOf(_composers, this.value);
  if(id <= 0) {
    id = '';
    $("#composerIdInput_" + index).attr("name", "");
    $("#composerInput_" + index).attr("name", "data[new][Composer][" + index + "][name]");
  }
  else {
    $("#composerIdInput_" + index).attr("name", "data[Composer][Composer][" + index + "]");
    $("#composerInput_" + index).attr("name", "");
  }
  $("#composerIdInput_" + index).attr("value", id);
}

// delete composer from document
function deleteComposer(index){
  target = $("#composer_" + index);
  if (confirm("Soll der Arangeur von diesem Musikstück tatsächlich entfernt werden?")) {
    target.remove();
  }
}


//-----------------------
// Publisher's function
//-----------------------

// update publisher_id field
$("#PublisherName").change(function(event) {
  id = objectIndexOf(_publishers, this.value);
  if(id <= 0) {
    id = '';
    $('#MusicsheetPublisherId').attr("name", "" );
    $('#PublisherName').attr("name", "data[new][Publisher][name]" );
  }
  else {
    $('#MusicsheetPublisherId').attr("name", "data[Musicsheet][publisher_id]" );
    $('#PublisherName').attr("name", "" );
  }
  $('#MusicsheetPublisherId').attr( 'value', id );
});

</script>


