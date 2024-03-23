<!-- File: /app/View/Repositories/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Aufbewahrungsplatz bearbeiten</h1>

<?php
  echo $this->Form->create('Repository', array());
  echo $this->element('repository_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

