<!-- File: /app/View/Locations/add.ctp -->

<?php // This file contains PHP ?>

<h1>Neuen Veranstalter / Kunden hinzuf&uuml;gen</h1>
<?php
  echo $this->Form->create('Customer', array('action' => 'add'));
  echo $this->element('customer_form');
  echo $this->Form->end('Speichern');
?>

