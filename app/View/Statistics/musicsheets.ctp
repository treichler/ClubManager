<!-- File: /app/View/Statistics/musicsheets.ctp -->

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

<h1>Musikstücke <?php echo $year ?></h1>

<p>Verschiedene Musikstücke: <?php echo count($musicsheets)?></p>

<table class="tablesorter">
<thead>
  <tr>
    <th>Titel</th>
    <th>Komponist(en)</th>
    <th>Arrangeur(e)</th>
    <th>Verlag</th>
    <th>Anzahl</th>
  </tr>
</thead>
<tbody>
<?php
  $sum = 0;
  foreach ($musicsheets as $musicsheet):
    $sum += $tracks[$musicsheet['Musicsheet']['id']];
    $composers = array();
    foreach ($musicsheet['Composer'] as $composer) {
      $composers[] = $composer['first_name'] . ' ' . $composer['last_name'];
    }
    $arrangers = array();
    foreach ($musicsheet['Arranger'] as $arranger) {
      $arrangers[] = $arranger['first_name'] . ' ' . $arranger['last_name'];
    }
?>
  <tr>
    <td><?php echo h($musicsheet['Musicsheet']['title']) ?></td>
    <td><?php echo implode(', ', $composers) ?></td>
    <td><?php echo implode(', ', $arrangers) ?></td>
    <td><?php echo h($musicsheet['Publisher']['name']) ?></td>
    <td><?php echo $tracks[$musicsheet['Musicsheet']['id']] ?></td>
  </tr>
<?php endforeach; ?>
<?php unset($musicsheet); ?>
</tbody>
<tfoot>
  <tr>
    <th>Summe</th>
    <td></td>
    <td></td>
    <td></td>
    <td><?php echo $sum ?></td>
  </tr>
</tfoot>
</table>


