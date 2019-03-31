<!-- File: /app/View/Locations/add.ctp -->

<?php // This file contains PHP ?>

<h1>Neuen Ort hinzuf&uuml;gen</h1>
<?php
  echo $this->Form->create('Location', array());
  echo $this->element('location_form');
?>

