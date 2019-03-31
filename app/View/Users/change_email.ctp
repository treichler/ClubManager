<!-- File: /app/View/Users/change_email.ctp -->

<?php // This file contains PHP ?>

<h1>E-Mail Adresse &auml;ndern</h1>

<?php
  echo $this->Form->create('User', array());
  echo $this->Form->input('email');
//  echo $this->Form->input('password', array('type' => 'password', 'label' => 'Passwort'));
  echo $this->Form->end('Speichern');
?>

