<!-- File: /app/View/Publishers/index.ctp -->

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

<h1>Musik Verlage</h1>

<?php echo $this->Html->link('Neuer Verlag', array('controller' => 'publishers', 'action' => 'add')); ?>

<table class="tablesorter">
<thead>
  <tr>
    <th>Name</th>
    <th>Details</th>
  </tr>
</thead>
<tbody>
<?php foreach ($publishers as $publisher): ?>
  <tr>
    <td><?php echo $publisher['Publisher']['name']; ?></td>
    <td><?php echo $publisher['Publisher']['details']; ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('controller' => 'publishers', 'action' => 'edit', $publisher['Publisher']['id']),
            array('title' => 'Verlag bearbeiten'));
    ?></td>
    <td class="icon-delete"><?php
        echo $this->Form->postLink('löschen',
            array('action' => 'delete', $publisher['Publisher']['id']),
            array('confirm' => 'Soll der Verlag "' . $publisher['Publisher']['name'] . '" tatsächlich gelöscht werden?',
                  'title' => 'Verlag löschen'));
    ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
<?php unset($publisher) ?>
</table>

