<!-- File: /app/View/Users/change_password.ctp -->

<?php // This file contains PHP ?>

<h1>Passwort &auml;ndern</h1>

<?php
  echo $this->Form->create('User', array('action' => 'change_password'));
  echo $this->Form->input('current_password', array('type' => 'password', 'label' => 'Aktuelles Passwort'));
  echo $this->Form->input('password1', array('type' => 'password', 'label' => 'Neues Passwort'));
  echo $this->Form->input('password2', array('type' => 'password', 'label' => 'Neues Passwort wiederholen'));
  echo $this->Form->end('Speichern');
?>

