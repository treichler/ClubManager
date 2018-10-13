<!-- File: /app/View/Elements/membership_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp

  echo $this->Form->input('state_id', array('label' => 'Art der Mitgliedschaft', 'empty'=>true));

//  echo $this->Form->input('Group.Group', array('empty'=>true));

  echo $this->Form->input('Group',array(
    'label' => __('Gruppen',true),
    'type' => 'select',
    'multiple' => 'checkbox',
    'selected' => $this->Html->value('Group.Group'),
  ));

?>

