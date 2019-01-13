<!-- File: /app/View/Blogs/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Blog bearbeiten</h1>

<p><?php echo $this->Html->link('zurück', array('controller' => 'blogs', 'action' => 'view', $this->Html->value('Blog.id'))); ?></p>

<?php
  echo $this->Form->create('Blog', array('url' => 'edit', 'type' => 'file'));
  echo $this->element('blog_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

