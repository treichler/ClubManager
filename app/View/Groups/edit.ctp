<!-- File: /app/View/Groups/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Gruppe bearbeiten</h1>
<?php
  echo $this->Form->create('Group', array('url' => 'edit', 'type' => 'file'));
  echo $this->element('group_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
//  echo $this->Form->input('privileg_id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

