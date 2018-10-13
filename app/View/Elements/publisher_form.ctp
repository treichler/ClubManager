<!-- File: /app/View/Elements/publisher_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('name', array('label' => 'Name'));
  echo $this->Form->input('details', array('rows' => '3', 'label' => 'Details'));
?>

