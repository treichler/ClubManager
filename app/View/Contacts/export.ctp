<!-- File: /app/View/Contacts/export.ctp -->

<?php // This file contains PHP ?>

<h1>Kontaktdaten exportieren</h1>

<?php
  echo $this->Form->create('Contact', array('url' => 'export'));
  echo $this->element('contact_form');
  echo $this->Form->end('Exportieren');
?>

