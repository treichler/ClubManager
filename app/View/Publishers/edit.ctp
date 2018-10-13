<!-- File: /app/View/Publishers/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Verlag bearbeiten</h1>

<?php
  echo $this->Form->create('Publisher', array('action' => 'edit'));
  echo $this->element('publisher_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

