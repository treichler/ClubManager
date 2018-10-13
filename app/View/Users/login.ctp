<?php echo $this->Session->flash('auth'); ?>

<h1>Anmelden</h1>

<p>Bitte Benutzername und Passwort eingeben</p>

<?php
  echo $this->Form->create('User');
  echo $this->Form->input('username', array('label' => 'Benutzername'));
  echo $this->Form->input('password', array('type' => 'password', 'label' => 'Passwort'));
  echo $this->Form->input('referer', array('type' => 'hidden'));
  echo $this->Form->end('Anmelden');
?>

