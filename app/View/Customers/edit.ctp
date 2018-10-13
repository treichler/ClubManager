<!-- File: /app/View/Locations/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Veranstalter / Kunde bearbeiten</h1>
<?php
  echo $this->Form->create('Customer', array('action' => 'edit'));
  echo $this->element('customer_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

