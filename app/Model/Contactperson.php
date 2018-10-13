<?php
// app/Model/Contactperson.php
class Contactperson extends AppModel {

  public $actsAs = array('Containable');

  public $belongsTo = array('Profile');

  public $validate = array(
    'profile_id' => array(
      'unique' => array(
        'rule' => 'isUnique',
        'message' => 'Ist bereits Kontaktperson.'
      )
    )
  );

}

