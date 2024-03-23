<!-- File: /app/View/Elements/resource_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('name', array('label' => 'Bezeichnung'));
  echo $this->Form->input('info', array('rows' => '3', 'label' => 'Details'));
  echo $this->Form->input('category_id', array('label' => 'Kategorie', 'empty'=>true));
  echo $this->Form->input('repository_id', array('label' => 'Aufbewahrungsplatz', 'empty'=>true));
  echo $this->Form->input('membership_id', array('label' => 'Dauerausleihe', 'empty'=>true));
?>

