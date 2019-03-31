<!-- File: /app/View/Memberships/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Mitgliedschaft bearbeiten</h1>
Mitglied:
<?php echo $profile['Profile']['first_name'] ?>
<?php echo $profile['Profile']['last_name'] ?>,
<?php echo $profile['Profile']['birthday'] ?>

<?php
  echo $this->Form->create('Membership', array());
  echo $this->element('membership_form');
  echo $this->Form->input('id', array('type' => 'hidden'));
  echo $this->Form->end('Speichern');
?>

