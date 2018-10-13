<!-- File: /app/View/Statistics/events.ctp -->

<?php // This file contains PHP ?>

<?php echo $this->Html->script('jquery.tablesorter.min'); ?>

<script type="text/javascript">
$(document).ready(function() {
  // call the tablesorter plugin
  $("table").tablesorter({
    // sort on the first column, order asc
    sortList: [[0,0]]
  });
});
</script>

<h1>Statistik: Veranstaltungen <?php echo $year ?></h1>

<table class="tablesorter">
<thead>
  <tr>
    <th>Gruppe</th>
<?php
  $sum = 0;
  $sum_cols = [];
  foreach ($statistic['titles'] as $key => $title):
    $sum_cols[$key] = 0;
?>
    <th><?php echo h($title); ?></th>
<?php endforeach; ?>
    <th>&sum;</th>
  </tr>
</thead>
<tbody>
<?php foreach ($statistic['rows'] as $row):
  $sum_row = 0;
  foreach ($row['data'] as $key => $data) {
    $sum_row += $data['events'];
    $sum_cols[$key] += $data['events'];
  }
  if (!$sum_row) {
    continue;
  }
?>
  <tr>
    <td><?php echo h($row['name']); ?></td>
  <?php foreach ($row['data'] as $key => $data): ?>
    <td><?php echo $data['events'] ?></td>
  <?php endforeach; ?>
    <td><?php echo $sum_row; $sum += $sum_row; ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
<tfoot>
  <tr>
    <th>Summe</th>
<?php foreach ($statistic['titles'] as $key => $title): ?>
    <td><?php echo $sum_cols[$key]; ?></td>
<?php endforeach; ?>
    <td><?php echo $sum; ?></td>
  </tr>
</tfoot>
</table>


<div class="table event">
  <div class="tr">
    <div class="th">Datum</div>
    <div class="th">Gruppe</div>
    <div class="th">Bezeichnung</div>
    <div class="th">Info</div>
  </div>
  <!-- Here is where we loop through our $events array, printing out event info -->
  <?php foreach ($events as $event):
    $class = '';
    if ($event['Event']['expired']) $class = ' expired';
    if ($event['Event']['high_priority']) $class = ' high_priority';
    if ($event['Event']['expiry'] == 0) $class = ' information';
    if ($event['Event']['finished']) $class = ' finished';
    $class = ' class="tr' . $class . '"';
    // prepare info field
    $info = h($event['Event']['info']);
    if ($event['Event']['info'] && $event['Event']['location']) $info .= '<br />';
    if ($event['Event']['location']) $info .= 'Ort: ' . h($event['Event']['location']);
  ?>
  <div<?php echo $class; ?>>
    <div class="td"><?php
        echo $this->Html->getDateTime($event['Event']['start']);
        if (!$this->Html->isSameDay($event)) echo (' ' . $this->Html->getDateTime($event['Event']['stop']));
    ?></div>
    <div class="td"><?php echo h($event['Group']['name']); ?></div>
    <div class="td"><?php
        echo $this->Html->link($event['Event']['name'],
        array('controller' => 'events', 'action' => 'view', $event['Event']['id']));
    ?></div>
    <div class="td info-field"><?php echo $info; ?></div>
  </div>
  <?php endforeach; ?>
  <?php unset($event); ?>
</div>


