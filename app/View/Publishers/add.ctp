<!-- File: /app/View/Publishers/add.ctp -->

<?php // This file contains PHP ?>

<h1>Verlag erstellen</h1>

<?php
  echo $this->Form->create('Publisher', array());
  echo $this->element('publisher_form');
  echo $this->Form->end('Speichern');
?>

