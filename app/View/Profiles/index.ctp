<!-- File: /app/View/Profiles/index.ctp -->

<?php // This file contains PHP ?>

<h1>Profile</h1>

<?php echo $this->Html->link('Neues Profil', array('controller' => 'profiles', 'action' => 'add')); ?>

<table>
  <tr>
    <th>id</th>
    <th>user_id</th>
    <th>Vorname</th>
    <th>Familienname</th>
    <th>Geburtstag</th>
    <th>Telefon</th>
    <th>Handy</th>
    <th>Telefon Arbeit</th>
    <th>Name zeigen</th>
    <th>Foto zeigen</th>
    <th>Komponist</th>
    <th>Arrangeur</th>
  </tr>

  <?php foreach($profiles as $profile): ?>
  <tr>
    <td><?php echo $profile['Profile']['id'] ?></td>
    <td><?php echo $profile['Profile']['user_id'] ?></td>
    <td><?php echo $profile['Profile']['first_name'] ?></td>
    <td><?php echo $profile['Profile']['last_name'] ?></td>
    <td><?php echo $profile['Profile']['birthday'] ?></td>
    <td><?php echo $profile['Profile']['phone_private'] ?></td>
    <td><?php echo $profile['Profile']['phone_mobile'] ?></td>
    <td><?php echo $profile['Profile']['phone_office'] ?></td>
    <td><?php echo $profile['Profile']['show_name'] ? 'ja' : 'nein' ?></td>
    <td><?php echo $profile['Profile']['show_photo'] ? 'ja' : 'nein' ?></td>
    <td><?php echo $profile['Profile']['is_composer'] ? 'ja' : 'nein' ?></td>
    <td><?php echo $profile['Profile']['is_arranger'] ? 'ja' : 'nein' ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('controller' => 'profiles', 'action' => 'edit', $profile['Profile']['id']),
            array('title' => 'Profil bearbeiten'));
    ?></td>
    <td class="icon-delete"><?php
        echo $this->Form->postLink('löschen',
            array('action' => 'delete', $profile['Profile']['id']),
            array('confirm' => 'Soll das Profil "' . $profile['Profile']['first_name'] . ' ' .
                      $profile['Profile']['last_name'] . '" tatsächlich gelöscht werden?',
                  'title' => 'Profil löschen'));
    ?></td>
  </tr>
  <?php endforeach; ?>
  <?php unset($profile); ?>

</table>

