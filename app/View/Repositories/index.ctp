<!-- File: /app/View/Repositories/index.ctp -->

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

<h1>Aufbewahrungsplätze</h1>

<p><?php echo $this->Html->link('Neuer Aufbewahrungsplatz', array('controller' => 'repositories', 'action' => 'add')); ?></p>

<table class="tablesorter">
<thead>
  <tr>
    <th>Bezeichnung</th>
  </tr>
</thead>
<tbody>
<?php foreach ($repositories as $repository): ?>
  <tr>
    <td><?php echo $repository['Repository']['name'] ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('controller' => 'repositories', 'action' => 'edit', $repository['Repository']['id']),
            array('title' => 'Aufbewahrungsplatz bearbeiten'));
    ?></td>
    <td class="icon-delete"><?php
        echo $this->Form->postLink('löschen',
            array('action' => 'delete', $repository['Repository']['id']),
            array('confirm' => 'Soll der Aufbewahrungsplatz "' . $repository['Repository']['name'] . '" tatsächlich gelöscht werden?',
                  'title' => 'Aufbewahrungsplatz löschen'));
    ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
<?php unset($repository) ?>
</table>

