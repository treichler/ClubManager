<!-- File: /app/View/Events/pdf/index.ctp -->

<?php // This file contains PHP
  // set the filename for the document
  $this->set('file_name', 'Terminplan');
  // set the document's title
  $this->set('title', 'Terminplan');
  // set additional information for the document
  $this->set('information', 'Erstellt am: ' . $this->Html->getDateTime(date("Y-m-d H:i:s"), array('year' => true)));
?>

<?php echo $this->element('event_content'); ?>


