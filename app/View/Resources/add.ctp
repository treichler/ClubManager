<!-- File: /app/View/Resources/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Ressource erstellen</h1>

<?php
  echo $this->Form->create('Resource', array());
  echo $this->element('resource_form');
  echo $this->Form->end('Speichern');
?>

