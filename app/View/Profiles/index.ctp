<!-- File: /app/View/Profiles/index.ctp -->

<?php // This file contains PHP ?>

<?php echo $this->Html->script('jquery.tablesorter.min'); ?>

<script type="text/javascript">
$(document).ready(function() {
  // call the tablesorter plugin
  $("table").tablesorter({
    // sort on the fourth column, order asc
    sortList: [[3,0]]
  });
});
</script>

<h1>Profile</h1>

<?php echo $this->Html->link('Neues Profil', array('controller' => 'profiles', 'action' => 'add')); ?>

<table class="tablesorter">
<thead>
  <tr>
    <th>id</th>
    <th>user_id</th>
    <th>Vorname</th>
    <th>Nachname</th>
    <th>Geburtstag</th>
    <th>Telefon</th>
    <th>Handy</th>
    <th>Telefon Arbeit</th>
    <th>Name zeigen</th>
    <th>Foto zeigen</th>
    <th>Komponist</th>
    <th>Arrangeur</th>
  </tr>
</thead>
<tbody>
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
    <td><?php echo $this->Html->showBoolean($profile['Profile']['show_name'], array('bold' => true)); ?></td>
    <td><?php echo $this->Html->showBoolean($profile['Profile']['show_photo'], array('bold' => true)); ?></td>
    <td><?php echo $this->Html->showBoolean($profile['Profile']['is_composer'], array('bold' => true)); ?></td>
    <td><?php echo $this->Html->showBoolean($profile['Profile']['is_arranger'], array('bold' => true)); ?></td>
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
</tbody>
<?php unset($profile); ?>
</table>

