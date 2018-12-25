<!-- File: /app/View/Users/index.ctp -->

<?php // This file contains PHP ?>

<h1>Benutzer</h1>
<table>
  <tr>
    <th>Id</th>
    <th>Username</th>
    <th>E-Mail</th>
    <th>Profile</th>
    <th>Privilegs</th>
    <th>Created</th>
    <th>Modified</th>
<!--    <th>Blog Votes</th> -->
  </tr>

  <?php foreach ($users as $user): ?>
  <tr>
    <td><?php echo $user['User']['id']; ?></td>
    <td><?php echo $user['User']['username']; ?></td>
    <td><?php echo $user['User']['email']; ?></td>
    <td><?php echo $user['Profile']['first_name'] . ' ' . $user['Profile']['last_name']; ?></td>
    <td><?php
        $privilegs = [];
        foreach($user['Privileg'] as $privileg) {
          $privilegs[] = $privileg['name'];
        }
        echo h(implode(', ', $privilegs))
    ?></td>
    <td><?php echo $user['User']['created']; ?></td>
    <td><?php echo $user['User']['modified']; ?></td>
<!--    <td><?php echo $user['Vote']['blog_votes']; ?></td> -->
    <td class="td icon-edit"><?php
        echo $this->Html->link('bearbeiten',
        array('controller' => 'users', 'action' => 'edit', $user['User']['id']),
        array('title' => 'Benutzer bearbeiten'));
    ?></td>
    <td class="icon-delete"><?php
        echo $this->Form->postLink('löschen',
        array('action' => 'delete', $user['User']['id']),
        array('confirm' => 'Soll der Benutzer "' . $user['User']['username'] . '" tatsächlich gelöscht werden?',
              'title' => 'Benutzer löschen'));
    ?></td>
  </tr>
  <?php endforeach; ?>
  <?php unset($blog); ?>
</table>

