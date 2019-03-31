<!-- File: /app/View/Events/add.ctp -->

<?php // This file contains PHP ?>

<h1>Termin anlegen</h1>
<?php
  echo $this->Form->create('Event', array());
  echo $this->Form->input('group_id', array('empty' => true, 'label' => 'Gruppe'));
  echo $this->Form->input('mode_id', array('empty' => true, 'label' => 'Art des Termins'));
  echo $this->element('event_form');
  echo $this->Form->end('Speichern');
?>

