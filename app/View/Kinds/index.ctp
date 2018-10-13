<!-- File: /app/View/Kinds/index.ctp -->

<?php // This file contains PHP ?>

<h1>Art der Gruppen</h1>

<p><?php echo $this->Html->link('Neue Art der Gruppe', array('action' => 'add')); ?></p>

<table>
  <tr>
    <th>ID</th>
    <th>Bezeichnung</th>
    <th>Öffentlich (is_public)</th>
    <th>Funktion (is_official)</th>
    <th>Zeige zugeordnete Funktionen (show_officials)</th>
    <th>In Anwesenheitsliste zeigen (show_in_availability_list)</th>
  </tr>
<?php foreach ($kinds as $kind): ?>
  <tr>
    <td><?php echo $kind['Kind']['id']; ?></td>
    <td><?php echo $kind['Kind']['name']; ?></td>
    <td><?php echo $kind['Kind']['is_public']; ?></td>
    <td><?php echo $kind['Kind']['is_official']; ?></td>
    <td><?php echo $kind['Kind']['show_officials']; ?></td>
    <td><?php echo $kind['Kind']['show_in_availability_list']; ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('action' => 'edit', $kind['Kind']['id']),
            array('title' => 'Art der Gruppe bearbeiten'));
    ?></td>
  </tr>
<?php endforeach; ?>
<?php unset($kind); ?>
</table>

