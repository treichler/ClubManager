<!-- File: /app/View/Elements/official_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Form->input('name', array('label' => 'Funktion'));
//  echo $this->Form->input('name_female', array('label' => 'Weibliche Bezeichnung der Funktion'));
  echo $this->Form->input('membership_id', array('empty' => true));

  echo $this->Form->input('phone_private_is_public', array('label' => 'Private Telefonnummer zeigen'));
  echo $this->Form->input('phone_mobile_is_public', array('label' => 'Handynummer zeigen'));
  echo $this->Form->input('phone_office_is_public', array('label' => 'Dienst Telefonnummer zeigen'));
  echo $this->Form->input('contact', array('label' => 'Kontakt'));

?>

