<!-- File: /app/View/Categories/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Kategorie bearbeiten</h1>

<?php
  echo $this->Form->create('Category', array());
  echo $this->element('category_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

