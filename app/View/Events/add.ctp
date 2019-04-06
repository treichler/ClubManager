<!-- File: /app/View/Events/add.ctp -->

<?php // This file contains PHP ?>

<h1>Termin anlegen</h1>
<?php
  echo $this->Form->create('Event', array());
  echo $this->Form->input('Group.Group', array('label' => 'Gruppe(n)', 'empty' => true));
  echo $this->Form->input('mode_id', array('empty' => true, 'label' => 'Art des Termins'));
  echo $this->element('event_form');
  echo $this->Form->end('Speichern');
?>

