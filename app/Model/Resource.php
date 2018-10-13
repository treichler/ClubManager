<?php
// app/Model/Resource.php
class Resource extends AppModel {

  public $hasAndBelongsToMany = array('Event');

}

