<!-- File: /app/View/Users/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Benutzerdaten bearbeiten</h1>

<?php
  echo $this->Form->create('User', array('action' => 'edit'));

  echo $this->Form->input('email');
//  echo $this->Form->input('username');


  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

