<!-- File: /app/View/Elements/profile_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
/*
  echo $this->Form->input('salutation', array(
    'empty'=>true,
    'options' => array('Frau' => 'Frau', 'Herr' => 'Herr')
  ));
*/
  echo $this->Form->input('salutation_id', array('empty' => true, 'label' => 'Anrede'));

  echo $this->Form->input('Title.Title', array('empty' => true, 'label' => 'Titel (Mehrfachauswahl möglich)'));

/*
  // alternatively HABTM field: checkboxes
  echo $this->Form->input('Title',array(
    'label' => __('Titel',true),
    'type' => 'select',
    'multiple' => 'checkbox',
    'selected' => $this->Html->value('Title.Title'),
  ));
*/

  echo $this->Form->input('first_name', array('label' => 'Vorname'));
  echo $this->Form->input('last_name', array('label' => 'Familienname'));

//  echo $this->Form->input('birthday');

  echo $this->Form->input('birthday', array('class' => 'date-time',
    'label' => 'Geburtstag',
    'dateFormat' => 'DMY',
    'minYear' => date('Y') - 100,
    'maxYear' => date('Y') - 8,
    'empty' => true
  ));

  echo $this->Form->input('file', array(
    'label' => 'Foto',
    'type'  => 'file'
  ));
?>
<br /><b>Telefonnummern bitte mit standardisierter L&auml;ndervorwahl (z.B.: +43 664 1234567) eingeben.</b>
<?php
  $default = '';
  echo $this->Form->input('phone_mobile', array('label' => 'Mobiltelefon', 'default' => $default));
  echo $this->Form->input('phone_private', array('label' => 'Telefon privat (Festnetz)', 'default' => $default));
  echo $this->Form->input('phone_office', array('label' => 'Telefon beruflich', 'default' => $default));
//  echo $this->Form->input('street');
//  echo $this->Form->input('postal_code');
?>
<br /><b>Optionale Kontaktdaten (zB Erziehungsberechtigte):</b>
<?php
  echo $this->Form->input('phone_mobile_opt', array('label' => 'Mobiltelefon optional', 'default' => ''));
  echo $this->Form->input('email_opt', array('label' => 'E-Mail Adresse optional', 'default' => ''));
?>
<br /><b>Einstellungen zur Privatsph&auml;re:</b>
<?php
  echo $this->Form->input('show_name', array('label' => 'Name öffentlich zeigen', 'default' => true));
  echo $this->Form->input('show_photo', array('label' => 'Foto öffentlich zeigen', 'default' => true));
  if ($this->Html->hasPrivileg($this_user, array('Profile modify'))):
?>
<br /><b>Administrative Einstellungen:</b>
<?php
    echo $this->Form->input('is_composer', array('label' => 'Komponist'));
    echo $this->Form->input('is_arranger', array('label' => 'Arrangeur'));
    if ($this->Html->hasPrivileg($this_user, array('Administrator')))
      echo $this->Form->input('user_id', array('empty' => true, 'label' => 'Benutzer'));
  endif;
?>

