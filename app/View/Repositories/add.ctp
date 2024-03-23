<!-- File: /app/View/Repositories/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Aufbewahrungsplatz erstellen</h1>

<?php
  echo $this->Form->create('Repository', array());
  echo $this->element('repository_form');
  echo $this->Form->end('Speichern');
?>

