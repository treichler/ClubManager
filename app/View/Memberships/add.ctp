<!-- File: /app/View/Memberships/add.ctp -->

<?php // This file contains PHP ?>

<h1>Neue Mitgliedschaft anlegen</h1>
<?php
  echo $this->Form->create('Membership', array('url' => 'add'));
  echo $this->Form->input('profile_id', array('label' => 'Profil', 'empty'=>true));
  echo $this->element('membership_form');
  echo $this->Form->end('Speichern');
?>

