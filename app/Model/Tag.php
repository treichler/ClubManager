<?php
// app/Model/Tag.php
class Tag extends AppModel {

  public $hasAndBelongsToMany = array('Blog');

/*
  public $hasAndBelongsToMany = array(
    'Blog' => array(
      'counterCache' => true
//      'counterScope' => array('Tag.active' => 1)
    )
  );
*/

}

