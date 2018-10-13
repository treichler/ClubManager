<!-- File: /app/View/Musicsheets/index.ctp -->

<?php // This file contains PHP ?>

<h1>Musikst&uuml;cke</h1>

<?php echo $this->Html->link('Neues Musikstück', array('controller' => 'musicsheets', 'action' => 'add')); ?>

<table>
  <tr>
    <th>Titel</th>
    <th>Details</th>
    <th>Komponist</th>
    <th>Arrangeur</th>
    <th>Verlag</th>
    <th>Archiv</th>
  </tr>

  <?php
    foreach ($musicsheets as $musicsheet):
      $composers = [];
      foreach ($musicsheet['Composer'] as $composer) {
        $composers[] = $composer['first_name'] . ' ' . $composer['last_name'];
      }
      $arrangers = [];
      foreach ($musicsheet['Arranger'] as $arranger) {
        $arrangers[] = $arranger['first_name'] . ' ' . $arranger['last_name'];
      }
  ?>
  <tr>
    <td><?php echo $musicsheet['Musicsheet']['title']; ?></td>
    <td><?php echo $musicsheet['Musicsheet']['details']; ?></td>
    <td><?php echo implode($composers, ', '); ?></td>
    <td><?php echo implode($arrangers, ', '); ?></td>
    <td><?php echo $musicsheet['Publisher']['name']; ?></td>
    <td><?php echo $musicsheet['Musicsheet']['archives']; ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('controller' => 'musicsheets', 'action' => 'edit', $musicsheet['Musicsheet']['id']),
            array('title' => 'Musikstück bearbeiten'));
    ?></td>
    <td class="icon-delete"><?php
        echo $this->Form->postLink('löschen',
            array('action' => 'delete', $musicsheet['Musicsheet']['id']),
            array('confirm' => 'Soll das Musikstück "' . $musicsheet['Musicsheet']['title'] . '" tatsächlich gelöscht werden?',
                  'title' => 'Musikstück löschen'));
    ?></td>
  </tr>
  <?php endforeach; ?>
  <?php unset($musicsheet) ?>
</table>

