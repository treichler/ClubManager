<!-- File: /app/View/Galleries/add.ctp -->

<?php // This file contains PHP ?>

<h1>Neue Galerie anlegen</h1>

<?php
  echo $this->Form->create('Gallery', array('action' => 'add'));
  echo $this->element('gallery_form');
  echo $this->Form->end('Speichern');
?>


