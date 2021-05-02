<!-- File: /app/View/Elements/event_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp

  echo $this->Form->input('official_start', array( 'label' => 'Offizieller Beginn (optional)', 'class' => 'date-time',
    'dateFormat' => 'DMY',
    'minYear' => date('Y'),
    'maxYear' => date('Y') + 1,
    'timeFormat'=> '24',
    'interval' => 5
  ));
  echo $this->Form->input('show_official_start', array('label' => 'Offiziellen Beginn zeigen'));
?>

<div>
<label for="leadTime">Vorlaufzeit</label>
<select id="leadTime">
  <option value=""></option>
  <option value="0">0 Minuten</option>
  <option value="15">15 Minuten</option>
  <option value="30">30 Minuten</option>
  <option value="45">45 Minuten</option>
  <option value="60">1 Stunde</option>
  <option value="90">1 Stunde 30 Minuten</option>
  <option value="120">2 Stunden</option>
</select>
</div>

<?php
  echo $this->Form->input('start', array( 'label' => 'Beginn / Treffpunkt', 'class' => 'date-time',
/*
    'before' => '--before--',
    'after' => '--after--',
    'between' => '--between---',
    'separator' => '--separator--',
*/
    'dateFormat' => 'DMY',
    'minYear' => date('Y'),
    'maxYear' => date('Y') + 1,
    'timeFormat'=> '24',
/*
    'selected' => array(
      'day' => date('D'),
      'month' => date('M'),
      'year' => date('Y'),
      'hour' => '12',
      'minute' => '00',
    )
*/
    'interval' => 5
  ));
?>

<div>
<label for="diffHour">Stundendifferenz</label>
<input maxlength="4" type="text" value="0" id="diffHour"/>
</div>

<?php
  echo $this->Form->input('stop', array( 'label' => 'Ende', 'class' => 'date-time',
    'dateFormat' => 'DMY',
    'minYear' => date('Y'),
    'maxYear' => date('Y') + 1,
    'timeFormat'=> '24',
    'interval' => 5
  ));

  echo $this->Form->input('name', array('label' => 'Bezeichnung'));
//  echo $this->Form->input('location', array('label' => 'Veranstaltungsort'));
?>

<!-- Location -->
<div class="input text">
  <label for="LocationName">Ort</label>
  <input name="data[new][Location][name]" list="locationList" id="LocationName">
</div>

<?php echo $this->Form->input('info', array('label' => 'Information','rows' => '3')); ?>

<!-- Customer -->
<div class="input text">
  <label for="CustomerName">Kunde / Veranstalter</label>
  <input name="data[new][Customer][name]" list="customerList" id="CustomerName">
</div>

<?php
//  echo $this->Form->input('customer_id', array('type' => 'text', 'empty' => true));
  echo $this->Form->input('customer_id', array('type' => 'hidden', 'empty' => true));
//  echo $this->Form->input('location_id', array('type' => 'text', 'empty' => true));
  echo $this->Form->input('location_id', array('type' => 'hidden', 'empty' => true));
  echo $this->Form->input('Resource.Resource', array('label' => 'Ressourcen / Equipment', 'empty' => true));
?>

<script type="text/javascript">

// global variables
var _customers = <?php echo json_encode($customers) ?>;
var _locations = <?php echo json_encode($locations) ?>;

$(document).ready(function(){
  //------------------
  // Init event's duration
  //------------------
  calculateDiff();


  //------------------
  // Customer's init
  //------------------

  // create option list for customer
  // INFO: for bigger datasets it would be better to load data via ajax requests
  datalist = document.createElement("datalist");
  datalist.id = 'customerList';
  for (i in _customers) {
    option = document.createElement("option");
    option.value = _customers[i];
    datalist.appendChild(option);
  }
  $('#CustomerName').after(datalist);

  // write previous customer to input
  $('#CustomerName').attr( 'value', _customers[$('#EventCustomerId').attr('value')] );


  //------------------
  // Location's init
  //------------------

  // create option list for location
  // INFO: for bigger datasets it would be better to load data via ajax requests
  datalist = document.createElement("datalist");
  datalist.id = 'locationList';
  for (i in _locations) {
    option = document.createElement("option");
    option.value = _locations[i];
    datalist.appendChild(option);
  }
  $('#LocationName').after(datalist);

  // write previous location to input
  $('#LocationName').attr( 'value', _locations[$('#EventLocationId').attr('value')] );
});


//---------------------------------------------------------
// Datetime input field reading and manipulating
//---------------------------------------------------------

function DatetimeFromField(field) {
  return new Date(
    $('#' + field + 'Year').val(),
    $('#' + field + 'Month').val()-1,
    $('#' + field + 'Day').val(),
    $('#' + field + 'Hour').val(),
    $('#' + field + 'Min').val()
  );
}


function setDatetimeField(field, dStart) {
  $('#' + field + 'Year').val(dStart.getFullYear());
  $('#' + field + 'Month').val(dezInt((dStart.getMonth() + 1), 2, '0'));
  $('#' + field + 'Day').val(dezInt(dStart.getDate(), 2, '0'));
  $('#' + field + 'Hour').val(dezInt(dStart.getHours(), 2, '0'));
  $('#' + field + 'Min').val(dezInt(dStart.getMinutes(), 2, '0'));
}


//---------------------------------------------------------
// start-stop-date <--> duration calculation functions
//---------------------------------------------------------

function calculateDiff() {
  dStart = DatetimeFromField('EventStart');
  dStop  = DatetimeFromField('EventStop');
  var diff = (dStop.getTime() - dStart.getTime()) / (60 * 60 * 1000);
  if (diff < 0) {
    diff = 0;
    setDatetimeField('EventStop', dStop);
  }
  $('#diffHour').val(diff);
}


function calculateStop() {
  dStart = DatetimeFromField('EventStart');
  diff = ($('#diffHour').val() * 60 * 60 * 1000);
  if (diff < 0) {
    diff = 0;
    $('#diffHour').val(diff);
  }
  dStop = new Date();
  dStop.setTime(dStart.getTime() + diff);
  setDatetimeField('EventStop', dStop);
}


function dezInt(num,size,prefix) {
  prefix = (prefix) ? prefix : "0";
  var minus = (num < 0) ? "-" : "" , result = (prefix == "0") ? minus : "";
  num = Math.abs(parseInt(num, 10));
  size -= ("" + num).length;
  for (var i = 1; i <= size; i++) {
    result += "" + prefix;
  }
  result += ((prefix != "0") ? minus : "") + num;
  return result;
}


// set showOfficialStart to true
$('#EventOfficialStartDay,#EventOfficialStartMonth,#EventOfficialStartYear,#EventOfficialStartHour,#EventOfficialStartMin').change(function(event) {
  $('#EventShowOfficialStart').prop('checked', true);
});


// set start according to leadtime of official start
$("#leadTime").change(function(event) {
  dOfficialStart = DatetimeFromField('EventOfficialStart');
  minutes = parseInt(event.target.value);
//  console.log(minutes);
  if (isNaN(minutes))
    return;
  dStart = new Date();
  dStart.setTime(dOfficialStart.getTime() - (minutes * 60 * 1000));
  setDatetimeField('EventStart', dStart);
});


// set time difference
$('#EventStartDay,#EventStartMonth,#EventStartYear,#EventStartHour,#EventStartMin,#EventStopDay,#EventStopMonth,#EventStopYear,#EventStopHour,#EventStopMin').click(function(event) {
  calculateDiff();
});


// set stop time according to start and difference
$("#diffHour").keyup(function(event) {
  calculateStop();
});

</script>

