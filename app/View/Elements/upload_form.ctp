<!-- File: /app/View/Elements/upload_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('title', array('label' => 'Titel'));
  echo $this->Form->input('type_id', array('label' => 'Datei zuordnen'));
  echo $this->Form->input('date_stamp', array('label' => 'Datum', 'class' => 'date-time',
    'dateFormat' => 'DMY',
    'minYear' => date('Y') - 15,
    'maxYear' => date('Y'),
  ));
  echo $this->Form->input('file', array('type' => 'file', 'label' => 'Datei'));
?>

