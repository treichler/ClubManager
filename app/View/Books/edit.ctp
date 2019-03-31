<!-- File: /app/View/Books/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Musikbuch/Mappe bearbeiten</h1>

<?php
  echo $this->Form->create('Book', array());
  echo $this->element('book_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

