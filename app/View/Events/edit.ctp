<!-- File: /app/View/Events/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Termin bearbeiten</h1>
<?php
  echo $this->Form->create('Event', array());
  echo $this->Form->input('expiry', array('label' => 'Abmeldefrist'));
  echo $this->element('event_form');
  echo $this->Form->input('user_id', array('label' => 'Termin anderer Person zuweisen'));
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

