<?php
// app/Model/Smsprotocol.php
class Smsprotocol extends AppModel {

  public $actsAs = array('Containable');

  public $belongsTo = array('Contactprotocol', 'Profile');

}

