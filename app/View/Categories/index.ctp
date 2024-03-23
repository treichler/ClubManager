<!-- File: /app/View/Categories/index.ctp -->

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

<h1>Kategorien</h1>

<p><?php echo $this->Html->link('Neue Kategorie', array('controller' => 'categories', 'action' => 'add')); ?></p>

<table class="tablesorter">
<thead>
  <tr>
    <th>Bezeichnung</th>
  </tr>
</thead>
<tbody>
<?php foreach ($categories as $category): ?>
  <tr>
    <td><?php echo $category['Category']['name'] ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('controller' => 'categories', 'action' => 'edit', $category['Category']['id']),
            array('title' => 'Kategorie bearbeiten'));
    ?></td>
    <td class="icon-delete"><?php
        echo $this->Form->postLink('löschen',
            array('action' => 'delete', $category['Category']['id']),
            array('confirm' => 'Soll die Kategorie "' . $category['Category']['name'] . '" tatsächlich gelöscht werden?',
                  'title' => 'Kategorie löschen'));
    ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
<?php unset($category) ?>
</table>

