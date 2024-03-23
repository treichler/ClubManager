<!-- File: /app/View/Resources/index.ctp -->

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

<h1>Ressourcen</h1>

<p><?php echo $this->Html->link('Neue Ressource', array('controller' => 'resources', 'action' => 'add')); ?></p>

<table class="tablesorter">
<thead>
  <tr>
    <th>Bezeichnung</th>
    <th>Details</th>
    <th>Kategorie</th>
    <th>Aufbewahrungsplatz</th>
    <th>Dauerausleihe</th>
  </tr>
</thead>
<tbody>
<?php foreach ($resources as $resource): ?>
  <tr>
    <td><?php echo $resource['Resource']['name'] ?></td>
    <td><?php echo $resource['Resource']['info'] ?></td>
    <td><?php if( $resource['Resource']['category_id'] ) echo $resource['Category']['name'] ?></td>
    <td><?php if( $resource['Resource']['repository_id'] ) echo $resource['Repository']['name'] ?></td>
    <td><?php if( $resource['Resource']['membership_id'] )
                echo $resource['Membership']['Profile']['first_name'] . ' ' . 
                     $resource['Membership']['Profile']['last_name'] ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('controller' => 'resources', 'action' => 'edit', $resource['Resource']['id']),
            array('title' => 'Ressource bearbeiten'));
    ?></td>
    <td class="icon-delete"><?php
        echo $this->Form->postLink('löschen',
            array('action' => 'delete', $resource['Resource']['id']),
            array('confirm' => 'Soll die Ressource "' . $resource['Resource']['name'] . '" tatsächlich gelöscht werden?',
                  'title' => 'Ressource löschen'));
    ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
<?php unset($resource) ?>
</table>

