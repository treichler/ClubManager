<!-- File: /app/View/Uploads/index.ctp -->

<?php // This file contains PHP ?>

<h1>Dateien</h1>

<?php if ($this->Html->hasPrivileg($this_user, array('File upload'))): ?>
<p>
  <?php echo $this->Html->link('Datei hochladen', array('controller' => 'uploads', 'action' => 'add')); ?>
</p>
<?php endif; ?>


<table>
  <tr>
    <th>Datum</th>
    <th>Titel</th>
    <th>Größe</th>
    <th>Benutzer</th>
  </tr>

<?php
  $type_id = 0;
  foreach ($uploads as $upload):
    if ($type_id != $upload['Upload']['type_id']):
      $type_id = $upload['Upload']['type_id'];
?>
  <tr>
    <th colspan=6><?php echo $upload['Type']['name'] ?></th>
  </tr>
  <?php endif; ?>
  <tr>
    <td><?php echo $this->Html->getDate($upload['Upload']['date_stamp'], array('year' => true)) ?></td>
    <td>
    <?php
      if (isset($upload['Storage']['id']))
        echo $this->Html->link(h($upload['Upload']['title']), array('controller' => 'uploads', 'action' => 'attachment', $upload['Upload']['id']));
      else
        echo h($upload['Upload']['title']);
    ?>
    </td>
    <td><?php echo $this->Html->getSize($upload['Storage']['size']) ?></td>
    <td><?php echo h($user_names[$upload['Upload']['user_id']]) ?></td>
  <?php if ($this->Html->hasPrivileg($this_user, array('File modify')) || $this_user['User']['id'] == $upload['Upload']['user_id']): ?>
    <td class="icon-edit"><?php
      echo $this->Html->link('bearbeiten',
          array('controller' => 'uploads', 'action' => 'edit', $upload['Upload']['id']),
          array('title' => 'Eintrag bearbeiten')); ?></td>
  <?php endif; ?>
  <?php if ($this->Html->hasPrivileg($this_user, array('File delete')) || $this_user['User']['id'] == $upload['Upload']['user_id']): ?>
    <td class="icon-delete"><?php
      echo $this->Form->postLink('löschen',
          array('action' => 'delete', $upload['Upload']['id']),
          array('confirm' => 'Soll die Datei tatsächlich gelöscht werden?',
                'title' => 'Datei löschen'));
    ?></td>
  <?php endif; ?>
  </tr>
<?php endforeach; ?>
<? unset($upload) ?>
</table>

