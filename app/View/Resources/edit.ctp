<!-- File: /app/View/Resources/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Ressource bearbeiten</h1>

<?php
  echo $this->Form->create('Resource', array());
  echo $this->element('resource_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

