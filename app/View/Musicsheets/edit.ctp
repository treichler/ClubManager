<!-- File: /app/View/Musicsheets/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Musikst&uuml;ck bearbeiten</h1>

<?php
  echo $this->Form->create('Musicsheet', array('autocomplete' => 'off'));
  echo $this->element('musicsheet_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

