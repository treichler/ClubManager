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
        $expiry_message = 'bis morgen';
      } else {
        $expiry_message = 'noch ' . $remaining_days . ' Tage';
      }
    }
  } else {
    $expiry_message = 'bereits abgelaufen';
  }
?>

<h1><?php echo h($event['Event']['name']); ?></h1>

<p><?php
  echo $this->Html->link('Anwesenheitsliste herunterladen', array(
    'controller' => 'events', 'action' => 'view', $event['Event']['id'], 'ext' => 'pdf'
  ));
  // the event's creator and users with the privileg 'Administrator' are allowed to access 'edit'
  if( $this_user['User']['id'] == $event['Event']['user_id'] ||
      $this->Html->hasPrivileg($this_user, array('Administrator')) ) {
    echo ', ' . $this->Html->link('Termin bearbeiten', array(
      'controller' => 'events', 'action' => 'edit', $event['Event']['id']
    ));
  }
?></p>

<ul>
  <li><b>Beginn:</b> <?php echo ($this->Html->getDateTime($event['Event']['start'], array('year' => true))); ?></li>
  <li><b>Ende:</b> <?php echo ($this->Html->getDateTime($event['Event']['stop'], array('year' => true))); ?></li>
  <li><b>Abmeldefrist:</b> <?php echo $expiry_message; ?></li>
  <li><b>Ort:</b> <?php echo $event['Event']['location']; ?></li>
  <li><b>Info:</b> <?php echo h($event['Event']['info']); ?></li>
  <li><b>Ersteller:</b> <?php echo h($admin); ?></li>
  <li><b>Gruppe(n):</b> <?php
      $groups = [];
      foreach($event['Group'] as $group) {
        $groups[] = h($group['name']);
      }
      unset($group);
      echo implode(', ', $groups);
  ?></li>
  <li><b>Art des Termins:</b> <?php echo h($event['Mode']['name']); ?></li>
<?php if ($event['Event']['customer_id']): ?>
  <li><b>Kunde:</b> <?php
  echo $event['Customer']['name'];
  if ($event['Customer']['address']) {
    echo ', ' . $event['Customer']['address'];
  }
?></li>
<?php endif; ?>
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
<b>Anwesende Mitglieder Benachrichtigen:</b>
<?php
  $links = [];
  if ($this->Html->hasPrivileg($this_user, array('Contact sms')))
    $links[] = $this->Html->link('SMS', array('controller' => 'contacts', 'action' => 'sms', '?' => array('event_id' => $event['Event']['id'])));
  if ($this->Html->hasPrivileg($this_user, array('Contact email')))
    $links[] =  $this->Html->link('E-Mail', array('controller' => 'contacts', 'action' => 'email', '?' => array('event_id' => $event['Event']['id'])));
  echo implode(', ', $links);
?>
</p>
<?php endif; ?>


<?php
  // prepare data
  $membership_states = array();
  foreach( $event['Availability'] as $availability ) {

    if( !isset($membership_states[ $availability['Membership']['State']['id'] ]) ) {
      $membership_states[$availability['Membership']['State']['id']] = array(
        'State'           => $availability['Membership']['State']['name'],
        'Availability'    => array(),
        'names'           => array(),
        'groups'          => array(),
        'first_group_ids' => array(),
      );
    }

    $id = $availability['id'];
    $group_names = [];
    foreach ($availability['Membership']['Group'] as $group)
      $group_names[] = $group['name'];

    $membership_states[$availability['Membership']['State']['id']]['Availability'][]    = $availability;
    $membership_states[$availability['Membership']['State']['id']]['names'][]           = $availability['Membership']['Profile']['first_name'] . ' ' . $availability['Membership']['Profile']['last_name'];
    $membership_states[$availability['Membership']['State']['id']]['groups'][]          = implode(', ', $group_names);
    $membership_states[$availability['Membership']['State']['id']]['first_group_ids'][] = isset($availability['Membership']['Group'][0]) ? $availability['Membership']['Group'][0]['id'] : Null;
  }
  // sort "$groups" and "infos" by "first_group_ids" and "names"
  foreach( $membership_states as $i => $f ) {
    array_multisort($membership_states[$i]['first_group_ids'], SORT_ASC, $membership_states[$i]['names'], SORT_ASC, $membership_states[$i]['groups'], $membership_states[$i]['Availability']);
  }
  // sort keys (state ids) ascending
  ksort($membership_states);
?>

<?php foreach( $membership_states as $state ): ?>
<h3>Besetzung (<?php echo $state['State'] ?>):</h3>
<div class="table availability">
  <div class="tr">
    <div class="th name">Name</div>
    <div class="th tool">Instrument(e)</div>
<?php if ($this->Html->hasPrivileg($this_user, array('Availability'))): ?>
    <div class="th">wird anwesend sein</div>
    <div class="th">war anwesend</div>
<?php else: ?>
    <div class="th">Anwesend</div>
<?php endif; ?>
    <div class="th">Persönliche Information</div>
  </div>
<?php
  for( $i = 0; $i < count($state['Availability']); $i++ ):
    $id = $state['Availability'][$i]['id'];
?>
  <div class="tr" id="<?php echo 'row_' . $id; ?>">
    <div class="td"><?php echo $state['names'][$i]; ?></div>
    <div class="td"><?php echo $state['groups'][$i]; ?></div>
    <div class="td"><?php
        $val = $state['Availability'][$i]['is_available'] == false ? '0' : '1';
        $checked = $state['Availability'][$i]['is_available'] == false ? '' : ' checked="checked"';
        echo '<input id="Availability' . $id . 'is_available" type="checkbox" name="' . $id .
                '" value="'.$val.'"'.$checked.' field-name="is_available"/>';
        echo '<label for="Availability' . $id . 'is_available">wird anwesend sein</label>';
    ?></div>
<?php if ($this->Html->hasPrivileg($this_user, array('Availability'))): ?>
    <div class="td"><?php
        $val = $state['Availability'][$i]['was_available'] == false ? '0' : '1';
        $checked = $state['Availability'][$i]['was_available'] == false ? '' : ' checked="checked"';
        echo '<input id="Availability' . $id . 'was_available" type="checkbox" name="' . $id .
                '" value="'.$val.'"'.$checked.' field-name="was_available"/>';
        echo '<label for="Availability' . $id . 'was_available">war anwesend</label>';
    ?></div>
<?php endif; ?>
    <div class="td"><?php
        $info = $state['Availability'][$i]['info'];
        echo '<input id="Availability' . $id . 'info" type="text" maxlength="50" name="' . $id .
                '" value="' . $info . '" field-name="info" onkeydown="evalInfo(event,' . $id . ')"/>';
    ?></div>
    <div class="td icon-save"><a href="javascript:void(0)" onclick="saveInfo(<?php echo $id ?>)" title="Info Speichern">Info Speichern</a></div>
  </div>
<?php endfor; ?>
</div>
<?php endforeach; ?>


<h3>Anwesenheitsliste</h3>
<div class="input checkbox">
  <?php
    $checked = $event['Event']['availabilities_checked'] == false ? '' : ' checked="checked"';
    echo '<input id="availabilitiesCheckedId" type="checkbox" name="' . $event['Event']['id'] . '" value="' .
          $event['Event']['availabilities_checked'] . '"' . $checked . ' field-name="availabilities_checked"/>';
  ?>
  <label for="availabilitiesCheckedId">Anwesenheitsliste bestätigen</label>
</div>

<h3>Musikst&uuml;cke</h3>
<?php
  echo $this->Html->link('Gespielte Musikstücke',
  array('controller' => 'tracks', 'action' => 'index', "?" => array('event_id' => $event['Event']['id'])));
?>
<div class="input checkbox">
  <?php
    $checked = $event['Event']['tracks_checked'] == false ? '' : ' checked="checked"';
    echo '<input id="tracksCheckedId" type="checkbox" name="' . $event['Event']['id'] . '" value="' .
          $event['Event']['tracks_checked'] . '"' . $checked . ' field-name="tracks_checked"/>';
  ?>
  <label for="tracksCheckedId">Liste der gespielten Musikst&uuml;cke bestätigen</label>
</div>

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

