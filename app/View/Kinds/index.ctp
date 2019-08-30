<!-- File: /app/View/Kinds/index.ctp -->

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

<h1>Art der Gruppen</h1>

<p><?php echo $this->Html->link('Neue Art der Gruppe', array('action' => 'add')); ?></p>

<table class="tablesorter">
<thead>
  <tr>
    <th>ID</th>
    <th>Bezeichnung</th>
    <th>Öffentlich (is_public)</th>
    <th>Funktion (is_official)</th>
    <th>Zeige zugeordnete Funktionen (show_officials)</th>
    <th>In Anwesenheitsliste zeigen (show_in_availability_list)</th>
  </tr>
</thead>
<tbody>
<?php foreach ($kinds as $kind): ?>
  <tr>
    <td><?php echo $kind['Kind']['id']; ?></td>
    <td><?php echo $kind['Kind']['name']; ?></td>
    <td><?php echo $this->Html->showBoolean($kind['Kind']['is_public'], array('bold' => true)); ?></td>
    <td><?php echo $this->Html->showBoolean($kind['Kind']['is_official'], array('bold' => true)); ?></td>
    <td><?php echo $this->Html->showBoolean($kind['Kind']['show_officials'], array('bold' => true)); ?></td>
    <td><?php echo $this->Html->showBoolean($kind['Kind']['show_in_availability_list'], array('bold' => true)); ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('action' => 'edit', $kind['Kind']['id']),
            array('title' => 'Art der Gruppe bearbeiten'));
    ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
<?php unset($kind); ?>
</table>

