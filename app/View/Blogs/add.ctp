<!-- File: /app/View/Blogs/add.ctp -->

<?php // This file contains PHP ?>

<h1>Neuer Blog</h1>

<?php
  echo $this->Form->create('Blog', array('type' => 'file'));
  echo $this->element('blog_form');
  echo $this->Form->end('Speichern');
?>

