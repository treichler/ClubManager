<!-- File: /app/View/Musicsheets/add.ctp -->

<?php // This file contains PHP ?>

<h1>Musikst&uuml;ck anlegen</h1>

<?php
  echo $this->Form->create('Musicsheet', array('autocomplete' => 'off'));
  echo $this->element('musicsheet_form');
  echo $this->Form->end('Speichern');
?>

