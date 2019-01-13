<!-- File: /app/View/Users/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Benutzerdaten bearbeiten</h1>

<?php
  echo $this->Form->create('User', array('url' => 'edit'));

  echo $this->Form->input('username', array('label' => 'Benutzername'));
  echo $this->Form->input('email', array('label' => 'E-Mail Adresse'));
  echo $this->Form->input('password1', array('type' => 'password', 'required' => false, 'label' => 'Neues Passwort'));
  echo $this->Form->input('password2', array('type' => 'password', 'required' => false, 'label' => 'Passwort wiederholen'));

  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

