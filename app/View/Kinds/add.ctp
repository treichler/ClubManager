<!-- File: /app/View/Kinds/add.ctp -->

<?php // This file contains PHP ?>

<h1>Neue Art der Gruppe erstellen</h1>

<?php
  echo $this->Form->create('Kind', array());
  echo $this->element('kind_form');
  echo $this->Form->end('Speichern');
?>

