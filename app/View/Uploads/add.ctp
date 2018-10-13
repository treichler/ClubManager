<!-- File: /app/View/Uploads/add.ctp -->

<?php // This file contains PHP ?>

<h1>Datei Hochladen</h1>

<p><?php echo $this->Html->link('zurück', array('controller' => 'uploads', 'action' => 'index')); ?></p>

<?php
  echo $this->Form->create('Upload', array('action' => 'add', 'type' => 'file'));
  echo $this->element('upload_form');
  echo $this->Form->end('Speichern');
?>


