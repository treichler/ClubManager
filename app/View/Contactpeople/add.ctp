<!-- File: /app/View/Contactpeople/add.ctp -->

<?php // This file contains PHP ?>

<h1>Kontaktperson hinzufügen</h1>

<?php
  echo $this->Form->create('Contactperson', array());
  echo $this->element('contactperson_form');
  echo $this->Form->end('Speichern');
?>

