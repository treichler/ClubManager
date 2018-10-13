<!-- File: /app/View/Privilegs/assign.ctp -->

<?php // This file contains PHP ?>

<h1>Benutzerrechte zuweisen für <b><?php echo $this->Html->value('Privileg')['name']; ?></b></h1>

<?php
  echo $this->Form->create('Privileg', array('action' => 'assign'));

  // HABTM field: multiple select
//  echo $this->Form->input('User.User', array('empty'=>true));

  // HABTM field: checkboxes
  echo $this->Form->input('User',array(
    'label' => __('Users',true),
    'type' => 'select',
    'multiple' => 'checkbox',
//    'options' => $users,
    'selected' => $this->Html->value('User.User'),
  ));

  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

