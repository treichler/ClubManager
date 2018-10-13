<?php
// app/Model/Contactprotocol.php
class Contactprotocol extends AppModel {

  public $actsAs = array('Containable');

  public $belongsTo = array('User');

  public $hasMany = array('Smsprotocol');

}

