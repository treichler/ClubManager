<?php
// app/Model/Resource.php
class Resource extends AppModel {

  public $actsAs = array('Containable');

  public $hasAndBelongsToMany = array('Event');

  public $belongsTo = array('Category', 'Membership', 'Repository');

}

