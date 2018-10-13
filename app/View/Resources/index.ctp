<!-- File: /app/View/Resources/index.ctp -->

<?php // This file contains PHP ?>

<h1>Ressourcen</h1>

<p><?php echo $this->Html->link('Neue Ressource', array('controller' => 'resources', 'action' => 'add')); ?></p>

<table>
  <tr>
    <th>Bezeichnung</th>
    <th>Details</th>
    <th>Raum</th>
  </tr>

  <?php foreach ($resources as $resource): ?>
  <tr>
    <td><?php echo $resource['Resource']['name'] ?></td>
    <td><?php echo $resource['Resource']['info'] ?></td>
    <td><?php echo $resource['Resource']['is_location'] ?></td>
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
  <?php unset($resource) ?>
</table>

