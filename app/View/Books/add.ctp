<!-- File: /app/View/Books/add.ctp -->

<?php // This file contains PHP ?>

<h1>Musikbuch/Mappe erstellen</h1>

<?php
  echo $this->Form->create('Book', array('action' => 'add'));
  echo $this->element('book_form');
  echo $this->Form->end('Speichern');
?>

