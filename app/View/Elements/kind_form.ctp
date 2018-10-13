<!-- File: /app/View/Elements/kind_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('name', array('label' => 'Bezeichnung'));
  echo $this->Form->input('is_public', array('label' => 'Öffentlich zeigen (is_public)'));
  echo $this->Form->input('is_official', array('label' => 'Ist eine Funktion (is_official)'));
  echo $this->Form->input('show_officials', array('label' => 'Zeige zugeordnete Funktionen (show_officials)'));
  echo $this->Form->input('show_in_availability_list', array('label' => 'In Anwesenheitsliste zeigen (show_in_availability)'));
?>

