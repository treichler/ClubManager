<!-- File: /app/View/Events/view.ctp -->

<?php // This file contains PHP 
  echo $this->Html->script('jquery-ui-1.8.18.min');
  echo $this->Html->script('availabilityAjax');

  $now = new DateTime();
  $start = new DateTime($event['Event']['start']);
  $start->setTime(23, 59, 59);
  $remaining_days = floor(($start->getTimestamp() - $now->getTimestamp()) / (60*60*24) - $event['Event']['expiry']);
  $expiry_message = '';
  if ($remaining_days >= 0) {
    if ($remaining_days == 0) {
      $expiry_message = 'heute';
    } else {
      if ($remaining_days == 1) {
        $expiry_message = 'noch ' . $remaining_days . ' Tag';
      } else {
        $expiry_message = 'noch ' . $remaining_days . ' Tage';
      }
    }
  } else {
    $expiry_message = 'bereits abgelaufen';
  }

//  $event['Event']['expiry'];
?>

<h1><?php echo h($event['Event']['name']); ?></h1>

<p><?php echo $this->Html->link('Anwesenheitsliste herunterladen', array(
  'controller' => 'events', 'action' => 'view', $event['Event']['id'], 'ext' => 'pdf')); ?></p>

<ul>
  <li><b>Beginn:</b> <?php echo ($this->Html->getDateTime($event['Event']['start'], array('year' => true))); ?></li>
  <li><b>Ende:</b> <?php echo ($this->Html->getDateTime($event['Event']['stop'], array('year' => true))); ?></li>
  <li><b>Abmeldefrist:</b> <?php echo $expiry_message; ?></li>
  <li><b>Ort:</b> <?php echo $event['Event']['location']; ?></li>
  <li><b>Info:</b> <?php echo h($event['Event']['info']); ?></li>
  <li><b>Ersteller:</b> <?php echo h($admin); ?></li>
  <li><b>Gruppe:</b> <?php echo h($event['Group']['name']); ?></li>
  <li><b>Art des Termins:</b> <?php echo h($event['Mode']['name']); ?></li>
</ul>

<?php if (count($event['Resource'])): ?>
<p>
  <b>Resourcen:</b>
  <ul><?php foreach ($event['Resource'] as $resource): ?>
    <li><?php echo $resource['name']; ?></li>
  <?php endforeach;
        unset($resource); ?></ul>
</p>
<?php endif; ?>

<?php if ($this->Html->hasPrivileg($this_user, array('Contact email', 'Contact sms'))): ?>
<p>
<b>Mitglieder Benachrichtigen:</b>
<br/><i>Wenn die Anwesenheitsliste geändert wurde, muss die Seite neu geladen werden, damit die aktuelle Auswahl richtig an das SMS- und E-Mail-Modul übergeben wird.</i><br/>
<?php
  $membership_ids = [];
  foreach ($event['Availability'] as $availability) {
    if($availability['was_available'])
      $membership_ids[] = $availability['membership_id'];
  }
  $links = [];
  if ($this->Html->hasPrivileg($this_user, array('Contact sms')))
    $links[] = $this->Html->link('SMS', array('controller' => 'contacts', 'action' => 'sms', '?' => array('membership_ids' => $membership_ids)));
  if ($this->Html->hasPrivileg($this_user, array('Contact email')))
    $links[] =  $this->Html->link('E-Mail', array('controller' => 'contacts', 'action' => 'email', '?' => array('membership_ids' => $membership_ids)));
  echo implode(' | ', $links);
?>
</p>
<?php endif; ?>



<b>Besetzung:</b>
<div class="table availability">
  <div class="tr">
    <div class="th">Name</div>
    <div class="th">Instrument(e)</div>
<?php if ($this->Html->hasPrivileg($this_user, array('Availability'))): ?>
    <div class="th">wird anwesend sein</div>
    <div class="th">war anwesend</div>
<?php else: ?>
    <div class="th">Anwesend</div>
<?php endif; ?>
    <div class="th">Persönliche Information</div>
  </div>

<?php
foreach ($memberships as $membership):
  $group_names = [];
  foreach ($membership['Group'] as $group)
    $group_names[] = $group['name'];
    $id = $membership['Availability']['id'];
?>
  <div class="tr" id="<?php echo 'row_' . $id; ?>">
    <div class="td">
      <?php echo $membership['Profile']['first_name']; ?>
      <?php echo $membership['Profile']['last_name']; ?>
    </div>
    <div class="td"><?php echo implode(', ', $group_names); ?></div>
<!--
    <div class="td"><?php if (isset($membership['Group'][0]['id'])) echo $membership['Group'][0]['name']; ?></div>
    <div class="td"><?php echo $membership['FirstGroup']['name']; ?></div>
-->
    <div class="td"><?php
        $val = $membership['Availability']['is_available'] == false ? '0' : '1';
        $checked = $membership['Availability']['is_available'] == false ? '' : ' checked="checked"';
        echo '<input id="Availability' . $id . 'is_available" type="checkbox" name="' . $id .
                '" value="'.$val.'"'.$checked.' field-name="is_available"/>';
        echo '<label for="Availability' . $id . 'is_available">wird anwesend sein</label>';
    ?></div>
<?php if ($this->Html->hasPrivileg($this_user, array('Availability'))): ?>
    <div class="td"><?php
        $val = $membership['Availability']['was_available'] == false ? '0' : '1';
        $checked = $membership['Availability']['was_available'] == false ? '' : ' checked="checked"';
        echo '<input id="Availability' . $id . 'was_available" type="checkbox" name="' . $id .
                '" value="'.$val.'"'.$checked.' field-name="was_available"/>';
        echo '<label for="Availability' . $id . 'was_available">war anwesend</label>';
    ?></div>
<?php endif; ?>
    <div class="td"><?php
        $info = $membership['Availability']['info'];
        echo '<input id="Availability' . $id . 'info" type="text" maxlength="50" name="' . $id .
                '" value="' . $info . '" field-name="info" onkeydown="evalInfo(event,' . $id . ')"/>';
    ?></div>
    <div class="td icon-save"><a href="javascript:void(0)" onclick="saveInfo(<?php echo $id ?>)" title="Info Speichern">Info Speichern</a></div>
  </div>
<?php endforeach; ?>
<?php unset($membership); ?>
<?php unset($group_names); ?>
</div>

<?php
  // write the ajax paths to a hidden <div>
  $availabilities_path = Router::url(array('controller' => 'availabilities'), true) . "/";
  $events_path = Router::url(array('controller' => 'events', 'action' => 'index'), true) . "/";
  echo "<div id='is_available' style='display: none'>" . $availabilities_path . "</div>";
  echo "<div id='was_available' style='display: none'>" . $availabilities_path . "</div>";
  echo "<div id='availabilities_checked' style='display: none'>" . $events_path . "</div>";
  echo "<div id='tracks_checked' style='display: none'>" . $events_path . "</div>";
?>

<p>
  <div>
    <label for="availabilitiesCheckedId">Anwesenheitsliste bestätigen</label>
    <?php
      $checked = $event['Event']['availabilities_checked'] == false ? '' : ' checked="checked"';
      echo '<input id="availabilitiesCheckedId" type="checkbox" name="' . $event['Event']['id'] . '" value="' .
            $event['Event']['availabilities_checked'] . '"' . $checked . ' field-name="availabilities_checked"/>';
    ?><br />
  </div>
</p>

<p>
  <?php
    echo $this->Html->link('Gespielte Musikstücke',
    array('controller' => 'tracks', 'action' => 'index', "?" => array('event_id' => $event['Event']['id'])));
  ?>
</p>

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

<?php if ($event['Event']['customer_id']): ?>
<p>
  <b>Kunde:</b><br />
<?php
  echo $event['Customer']['name'];
  if ($event['Customer']['address']) {
    echo ', ' . $event['Customer']['address'];
  }
?>
</p>
<?php endif; ?>


<script type="text/javascript">
var _availabilities_path = '<?php echo Router::url(array('controller' => 'availabilities'), true) . "/"; ?>';
var _events_path = '<?php echo Router::url(array('controller' => 'events', 'action' => 'index'), true) . "/"; ?>';

$(document).ready(function(){
  // each time any availability checkbox is clicked trigger the ajax request
  $('input[type=checkbox]').click(function(event) {
    var id = $(this).attr('name');
    var field_name = $(this).attr('field-name');
    if (field_name === 'is_available' || field_name === 'was_available')
      isAvailable(id, field_name, _availabilities_path, $(this).prop('checked'));
    if (field_name === 'availabilities_checked' || field_name === 'tracks_checked')
      isAvailable(id, field_name, _events_path, $(this).prop('checked'));
  });
});

function saveInfo(id) {
  var input = $('#Availability' + id + 'info')
  isAvailable(id, 'info', _availabilities_path, input.attr('value'));
}

function evalInfo(e,id) {
  if (e.keyCode == 13 || e.which == 13) {
    saveInfo(id);
  }
}
</script>

