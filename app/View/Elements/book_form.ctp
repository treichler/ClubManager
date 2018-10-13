<!-- File: /app/View/Elements/book_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('title', array('label' => 'Bezeichnung'));
  echo $this->Form->input('description', array('rows' => '3', 'label' => 'Details'));
?>

