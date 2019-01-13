<!-- File: /app/View/Users/apply_ticket.ctp -->

<?php // This file contains PHP ?>

<h1>Neues Passwort eingeben</h1>

<ul>
  <li>Benutzer: <b><?php echo $user['User']['username'] // echo $this->request->data['User']['username'] ?></b></li>
  <li>E-Mail: <b><?php echo $user['User']['email'] // echo $this->request->data['User']['email'] ?></b></li>
</ul>

<?php
  echo $this->Form->create('User', array('url' => 'apply_ticket'));
  echo $this->Form->input('password1', array('type' => 'password', 'label' => 'Passwort'));
  echo $this->Form->input('password2', array('type' => 'password', 'label' => 'Passwort wiederholen'));
  echo $this->Form->input('ticket', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

