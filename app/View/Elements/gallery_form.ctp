<!-- File: /app/View/Elements/gallery_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('title', array('label' => 'Titel'));
//  echo $this->Form->input('body', array('rows' => '3'));
  echo $this->Form->input('date_stamp', array('label' => 'Datum', 'class' => 'date-time',
    'dateFormat' => 'DMY',
    'minYear' => date('Y') - 15,
    'maxYear' => date('Y'),
  ));
?>

