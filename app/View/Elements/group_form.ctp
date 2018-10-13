<!-- File: /app/View/Elements/group_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Html->script('ckeditor');
  echo $this->Form->input('kind_id', array('label' => 'Art der Gruppe', 'empty'=>true));
  echo $this->Form->input('name', array('label' => 'Bezeichnung der Gruppe'));
  echo $this->Form->input('info', array('rows' => '3', 'label' => 'Text', 'class' => 'ckeditor'));
  echo $this->Form->input('file', array('type' => 'file', 'label' => 'Bild der Gruppe'));
  echo $this->Form->input('show_members', array('label' => 'Aktive Mitglieder öffentlich zeigen'));

//  echo $this->Form->input('Membership.Membership', array('empty' => true, 'label' => 'Mitglieder'));

  echo $this->Form->input('Membership',array(
    'label' => __('Mitglieder',true),
    'type' => 'select',
    'multiple' => 'checkbox',
    'selected' => $this->Html->value('Membership.Membership'),
  ));
?>

