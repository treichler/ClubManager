<!-- File: /app/View/Locations/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Ort bearbeiten</h1>
<?php
  echo $this->Form->create('Location', array());
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->element('location_form');
?>

