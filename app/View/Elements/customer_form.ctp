<!-- File: /app/View/Elements/customer_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('name', array('label' => 'Name'));
  echo $this->Form->input('street', array('label' => 'Straße'));
  echo $this->Form->input('postal_code', array('label' => 'Postleitzahl'));
  echo $this->Form->input('town', array('label' => 'Ort'));
?>

