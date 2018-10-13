<!-- File: /app/View/Availabilities/terminal.ctp -->

<?php // This file contains PHP ?>

<h1>Anwesenheitsliste</h1>

<table>
  <tr>
    <th>Id</th>
    <th>Mitglied Id</th>
    <th>Datum</th>
    <th>Event</th>
    <th>Anwesend</th>
    <th>Tatsächlich Anwesend</th>
  </tr>

  <?php foreach ($availabilities as $availability): ?>
  <tr>
    <td><?php echo $availability['Availability']['id']; ?></td>
    <td><?php echo $availability['Availability']['membership_id']; ?></td>
    <td><?php echo $availability['Event']['start']; ?></td>
    <td><?php echo $availability['Event']['name']; ?></td>
    <td><?php echo $availability['Availability']['is_available']; ?></td>
    <td><?php echo $availability['Availability']['was_available']; ?></td>
    <td>
      <?php
        echo $this->Html->link('bearbeiten',
        array('controller' => 'availabilities', 'action' => 'edit', $availability['Availability']['id']));
      ?>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php unset($event); ?>
</table>


<table>
  <tr>
    <th>Name</th>
  </tr>

  <?php foreach ($memberships as $membership): ?>
  <tr>
    <td><?php echo $membership['Profile']['first_name']; ?></td>
  </tr>
  <?php endforeach; ?>
  <?php unset($event); ?>
</table>

