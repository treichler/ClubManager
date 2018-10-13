<!-- File: /app/View/Groups/add.ctp -->

<?php // This file contains PHP ?>

<h1>Neue Gruppe anlegen</h1>
<?php
  echo $this->Form->create('Group', array('action' => 'add', 'type' => 'file'));
  echo $this->element('group_form');
  echo $this->Form->end('Speichern');
?>

