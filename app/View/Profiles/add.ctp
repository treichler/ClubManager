<!-- File: /app/View/Profiles/add.ctp -->

<?php // This file contains PHP ?>

<h1>Profil anlegen</h1>
<?php
  echo $this->Form->create('Profile', array('url' => 'add', 'type' => 'file'));
  echo $this->element('profile_form');
  echo $this->Form->end('Speichern');
?>

