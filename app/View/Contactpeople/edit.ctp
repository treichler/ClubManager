<!-- File: /app/View/Contactpeople/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Kontaktperson bearbeiten</h1>

<?php
  echo $this->Form->create('Contactperson', array());
  echo $this->element('contactperson_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

