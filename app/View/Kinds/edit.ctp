<!-- File: /app/View/Kinds/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Art der Gruppe bearbeiten</h1>

<?php
  echo $this->Form->create('Kind', array());
  echo $this->element('kind_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

