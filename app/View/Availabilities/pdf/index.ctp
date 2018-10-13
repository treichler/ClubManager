<!-- File: /app/View/Availabilities/pdf/index.ctp -->

<?php // This file contains PHP
  // set the filename for the document
  $this->set('file_name', 'Terminplan_privat');
  // set the document's title
  $this->set('title', 'Persönlicher Terminplan');
  // set additional information for the document
  $this->set('information', 'Für: ' . $this_user['User']['name'] . '
    Erstellt am: ' . $this->Html->getDateTime(date("Y-m-d H:i:s"), array('year' => true)));
?>


<?php
//$this->set('events', $availabilities);
echo $this->element('event_content');
?>

