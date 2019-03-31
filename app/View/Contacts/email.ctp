<!-- File: /app/View/Contacts/email.ctp -->

<?php // This file contains PHP ?>

<h1>E-Mail</h1>

<?php
  echo $this->Form->create('Contact', array());
  echo $this->Form->input('subject', array('label' => 'Betreff'));
  echo $this->Form->input('text', array('rows' => '7', 'label' => 'Text'));
  echo $this->element('contact_form');
  echo $this->Form->end('Senden');
?>

