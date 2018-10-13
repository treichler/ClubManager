<?php
// app/Model/Contact.php
class Contact extends AppModel {

  public $actsAs = array('Containable');

  var $useTable = false;

  public $hasAndBelongsToMany = array('Profile');

  public $belongsTo = array('User');

}

