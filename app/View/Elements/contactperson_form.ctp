<!-- File: /app/View/Elements/contact_form.ctp -->

<?php // This file holds the common form element for contact_person
//  echo $this->Form->input('title', array('label' => 'Bezeichnung'));
//  echo $this->Form->input('description', array('rows' => '3', 'label' => 'Details'));

  echo $this->Form->input('profile_id', array('label' => 'Person'));
  echo $this->Form->input('footer_phone', array('label' => 'Fußzeile Telefon'));
  echo $this->Form->input('contactlist_email', array('label' => 'Kontaktliste E-Mail'));
  echo $this->Form->input('contactlist_phone', array('label' => 'Kontaktliste Telefon'));
  echo $this->Form->input('contact_recipient', array('label' => 'Kontaktformular Empfänger'));
?>

