<!-- File: /app/View/Uploads/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Datei bearbeiten</h1>

<p><?php echo $this->Html->link('zurück', array('controller' => 'uploads', 'action' => 'index')); ?></p>

<?php
  echo $this->Form->create('Upload', array('type' => 'file'));
  echo $this->element('upload_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

