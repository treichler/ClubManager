<?php
// app/Model/Kind.php
class Kind extends AppModel {

  public $actsAs = array('Containable');

  public $hasMany = array('Group');

}

