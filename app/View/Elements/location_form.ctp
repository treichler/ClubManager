<!-- File: /app/View/Elements/location_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('name', array('label' => 'Ort'));
  echo $this->Form->input('longitude', array('label' => 'Längengrad'));
  echo $this->Form->input('latitude', array('label' => 'Breitengrad'));
  echo $this->Form->input('radius', array('label' => 'Radius [m]'));
  echo $this->Form->input('show_on_map', array('label' => 'Auf öffentlicher Karte zeigen'));
?>

