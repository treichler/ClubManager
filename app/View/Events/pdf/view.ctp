<!-- File: /app/View/Events/pdf/view.ctp -->

<?php // This file contains PHP
  // set the filename for the document
  $this->set('file_name', 'Event-' . $event['Event']['id']);
  // set the document's title
  $this->set('title', h($event['Event']['name']));
  // set additional information for the document
  $groups = [];
  foreach($event['Group'] as $group) {
    $groups[] = h($group['name']);
  }
  unset($group);
  $this->set('information', implode(', ', $groups) . '
    Beginn: ' . $this->Html->getDateTime($event['Event']['start'], array('year' => true)) . '
    Ende: ' . $this->Html->getDateTime($event['Event']['stop'], array('year' => true)) . '
    Ort: ' . h($event['Event']['location']));
?>

<style>
th {
  text-align: center;
}

td {
  border: 1px solid black;
  font-size: 8;
}
</style>

<table cellspacing="0" cellpadding="3">
  <tr>
    <th width="35%">Instrument(e)</th>
    <th width="25%">Name</th>
    <th width="40%">Information</th>
  </tr>

<?php
$count_people = 0;
$count_groups = 0;
foreach ($memberships as $membership):

  $group_names = [];
  foreach ($membership['Group'] as $group) {
    $group_names[] = $group['name'];
  }
  if ($membership['Availability']['is_available']):
    $count_people ++;
    if ($membership['FirstGroup']['name']) $count_groups ++;
?>
  <tr>
    <td><?php echo implode(', ', $group_names) ?></td>
<!--
    <td><?php echo $membership['FirstGroup']['name'] ?></td>
-->
    <td><?php echo $membership['Profile']['first_name'] . ' ' . $membership['Profile']['last_name'] ?></td>
    <td><?php echo $membership['Availability']['info'] ?></td>
  </tr>
  <?php endif; ?>
<?php endforeach; ?>
<?php unset($membership); ?>
<?php unset($group_names); ?>
  <tr>
    <th>Summe: <?php echo $count_groups ?></th>
    <th>Summe: <?php echo $count_people ?></th>
  </tr>
</table>

