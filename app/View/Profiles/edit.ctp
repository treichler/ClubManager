<!-- File: /app/View/Profiles/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Profil bearbeiten</h1>
<?php
  echo $this->Form->create('Profile', array('url' => 'edit', 'type' => 'file'));
  echo $this->element('profile_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

