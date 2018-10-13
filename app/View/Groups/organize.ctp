<!-- File: /app/View/Groups/organize.ctp -->

<?php // This file contains PHP ?>

<h1>Gruppen</h1>

<p>
<?php
  echo $this->Html->link('Gruppe hinzufügen',
  array('controller' => 'groups', 'action' => 'add'));
?>
</p>

<table>
  <tr>
    <th>id</th>
    <th>privileg_id</th>
    <th>Type</th>
    <th>Name</th>
    <th>Mitlieder sind öffentlich</th>
  </tr>

  <?php foreach($groups as $group): ?>
  <tr>
    <td><?php echo $group['Group']['id'] ?></td>
    <td><?php echo $group['Group']['privileg_id'] ?></td>
    <td><?php echo $group['Kind']['name'] ?></td>
    <td><?php
        echo $this->Html->link($group['Group']['name'],
        array('controller' => 'groups', 'action' => 'details', $group['Group']['id']));
    ?></td>
    <td><?php echo $this->Html->showBoolean($group['Group']['show_members'], array('bold' => true)); ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('controller' => 'groups', 'action' => 'edit', $group['Group']['id']),
            array('title' => 'Gruppe bearbeiten'));
    ?></td><?php if ($this->Html->hasPrivileg($this_user, array('Administrator'))): ?>
    <td class="icon-delete"><?php
        echo $this->Form->postLink('löschen',
            array('action' => 'delete', $group['Group']['id']),
            array('confirm' => 'Soll die Gruppe "' . $group['Group']['name'] . '" tatsächlich gelöscht werden?',
                  'title' => 'Gruppe löschen'));
    ?></td><?php endif; ?>
  </tr>
  <?php endforeach; ?>
  <?php unset($group); ?>

</table>

