<!-- File: /app/View/Statistics/index.ctp -->

<?php // This file contains PHP ?>

<h1>Statistik</h1>

<table>
<?php foreach ($years as $year): ?>
  <tr>
    <td><?php echo $year ?></td>
    <td><?php echo $this->Html->link('Veranstaltungen', array('controller' => 'statistics', 'action' => 'events', $year)); ?></td>
    <td><?php echo $this->Html->link('Anwesenheit', array('controller' => 'statistics', 'action' => 'availabilities', $year)); ?></td>
    <td><?php echo $this->Html->link('Musikstücke', array('controller' => 'statistics', 'action' => 'musicsheets', $year)); ?></td>
<?php if (Configure::read('CMSystem.use_akm')): ?>
    <td><?php echo $this->Html->link('AKM exportieren', array('controller' => 'statistics', 'action' => 'akm', $year)); ?></td>
<?php endif; ?>
  </tr>
<?php endforeach; ?>
<?php unset($year); ?>
</table>

