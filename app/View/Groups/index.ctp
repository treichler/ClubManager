<!-- File: /app/View/Groups/index.ctp -->

<?php // This file contains PHP ?>

<h1>Verein</h1>

<table>
  <tr>
    <th>Name</th>
  </tr>

  <?php if (!isset($groups)) $groups = $public_groups; ?>
  <?php foreach($groups as $group): ?>
  <tr>
    <td>
      <?php
        echo $this->Html->link($group['Group']['name'],
        array('controller' => 'groups', 'action' => 'view', $group['Group']['id']));
      ?>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php unset($group); ?>

</table>

