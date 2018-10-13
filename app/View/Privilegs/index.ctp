<!-- File: /app/View/Privilegs/index.ctp -->

<?php // This file contains PHP ?>

<h1>Benutzerrechte</h1>

<table>
  <tr>
    <th>id</th>
    <th>Bezeichnung</th>
    <th>Benutzer</th>
  </tr>

<?php
  $kind_id = -1;
  foreach($privilegs as $privileg):
    if ($privileg['Group']['kind_id'] != $kind_id):
      $kind_id = $privileg['Group']['kind_id'];
?>
  <tr>
    <th colspan=3>
      <?php echo $privileg['Group']['kind_id'] ? $kinds[$privileg['Group']['kind_id']]['Kind']['name'] : 'Administration'; ?>
    </th>
  </tr>
  <?php endif; ?>
  <tr>
    <td><?php echo $privileg['Privileg']['id'] ?></td>
    <td><?php echo $privileg['Privileg']['name'] ?></td>
    <td><?php
        $users = [];
        foreach($privileg['User'] as $user) {
          $users[] = $user['name'];
        }
        unset($user);
        echo implode(', ', $users);
    ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('controller' => 'privilegs', 'action' => 'assign', $privileg['Privileg']['id']),
            array('title' => 'Benutzerrecht bearbeiten'));
    ?></td>
  </tr>
<?php endforeach; ?>
<?php unset($privileg); ?>
</table>

