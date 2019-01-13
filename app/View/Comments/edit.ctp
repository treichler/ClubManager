<!-- File: /app/View/Comments/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Kommentar bearbeiten</h1>

<?php
  echo $this->Form->create('Comment', array('url' => 'add'));
  echo $this->Form->input('blog_id', array('type' => 'hidden'));
  echo $this->Form->input('body');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

