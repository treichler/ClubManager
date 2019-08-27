<!-- File: /app/View/Statistics/akm.ctp -->

<?php // This file contains PHP ?>

<h1>AKM <?php echo $year ?> exportieren</h1>

<p>
<?php
  echo $this->Html->link('AKM-Daten herunterladen', array('controller' => 'statistics', 'action' => 'akm', $year, 'ext' => 'xml'));
?>
</p>

<?php if (count($check_events)): ?>
<h3>
  Es sind noch nicht alle Veranstaltungen abgeschlossen
  (<?php echo count($check_events) . ' von ' . count($events); ?>)
</h3>
<?php endif; ?>

<p>
  Musikstücke werden nur dann gemeldet, wenn zur jeweiligen Veranstaltung die Liste
  der gespielten Musikstücke bestätigt wurde, weiters muss auch ein Veranstalter
  beim Termin eingetragen sein.
</p>

<table>
  <tr>
    <th>Datum</th>
    <th></th>
    <th colspan=2>Veranstaltung</th>
    <th colspan=3>Musikstücke</th>
    <th>Kopfquote</th>
    <th>Veranstalter</th>
    <th>wird gemeldet</th>
  </tr>
<?php
  foreach ($events as $event):
/*
    $available = 0;
    foreach ($event['Availability'] as $availability) {
      if ($availability['was_available'])
        $available += 1;
    }
*/
    $customer = '';
    if ($event['Event']['customer_id']) {
      $customer = $event['Customer']['name'];
      if ($event['Customer']['address']) {
        $customer .= ', ' . $event['Customer']['address'];
      }
    }
    $report = false;
    if ($event['Event']['tracks_checked'] && count($event['Track']) > 0 && $customer) {
      $report = true;
    }
?>
  <tr>
    <td><?php echo h($event['Event']['start']); ?></td>
    <td><?php echo h($event['Event']['stop']); ?></td>
    <td><?php echo $this->Html->link($event['Event']['name'],
              array('controller' => 'events', 'action' => 'view', $event['Event']['id'])); ?></td>
    <td class="td icon-edit"><?php
        echo $this->Html->link('bearbeiten',
        array('controller' => 'events', 'action' => 'edit', $event['Event']['id']),
        array('title' => 'Veranstaltung bearbeiten'));
    ?></td>
    <td><?php echo count($event['Track']); ?></td>
    <td><?php echo $this->Html->showBoolean($event['Event']['tracks_checked'], array('bold' => true)); ?></td>
    <td class="td icon-edit"><?php
        echo $this->Html->link('bearbeiten',
        array('controller' => 'tracks', 'action' => 'index', "?" => array('event_id' => $event['Event']['id'])),
        array('title' => 'Musikstücke bearbeiten'));
    ?></td>
    <td><?php echo $this->Html->showBoolean($event['Event']['quota'], array('bold' => true)); ?></td>
    <td><?php echo $customer; ?></td>
    <td><?php echo $this->Html->showBoolean($report, array('bold' => true)); ?></td>
  </tr>
<?php endforeach; ?>
<?php unset($check_event); ?>
</table>


<?php // echo count($check_events) . ' von ' . count($events); ?>
<?php // echo print_r($check_events); ?>
<?php // echo print_r($modes); ?>
<?php // echo print_r($events); ?>

<?php
  foreach ($events as $event):
    if (count($event['Track']) == 0)
      continue;
?>
<h3><?php echo h($event['Event']['name']); ?></h3>
<div><?php
      $groups = [];
      foreach($event['Group'] as $group) {
        $groups[] = h($group['name']);
      }
      unset($group);
      echo implode(', ', $groups);
  ?></div>
<div><?php echo $event['Event']['start']; ?></div>
<div><?php echo $event['Event']['stop']; ?></div>

  <?php // echo print_r($event['Track']); ?>

<table>
  <?php foreach ($event['Track'] as $track):
    $composers = array();
    foreach ($musicsheet[$track['musicsheet_id']]['Composer'] as $composer) {
      $composers[] = $composer['first_name'] . ' ' . $composer['last_name'];
    }
    $arrangers = array();
    foreach ($musicsheet[$track['musicsheet_id']]['Arranger'] as $arranger) {
      $arrangers[] = $arranger['first_name'] . ' ' . $arranger['last_name'];
    }
  ?>
  <tr>
    <td><?php echo $musicsheet[$track['musicsheet_id']]['Musicsheet']['title'] ?></td>
    <td><?php echo implode(', ', $composers) ?></td>
    <td><?php echo implode(', ', $arrangers) ?></td>
    <td><?php echo $musicsheet[$track['musicsheet_id']]['Publisher']['name'] ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<?php endforeach; ?>
<?php unset($event) ?>


