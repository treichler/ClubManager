<?php
// app/Model/Privileg.php
class Privileg extends AppModel {

  public $actsAs = array('Containable');

  public $hasAndBelongsToMany = array('User');

  public $hasOne = array('Group');


// TODO Prevent from creating/editing privilegs.
//      Only assigning of the priviles to the users should be allowed
/*
  public function beforeSave($options = array()) {
    // keep name if id is set
    if(isset($this->data[$this->name]['id'])) {

          $this->data['Tag']['Tag'][] = $id['Tag']['id'];

    $this->data[$this->name]['name'] = $privileg['Privileg']['id'];

    }
    parent::beforeSave();
    return true;
  }
*/

  public function beforeDelete($cascade = true) {
    // delete assotiations to users but keep users
    $this->PrivilegsUser->deleteAll(array('PrivilegsUser.privileg_id' => $this->id), false);
    return true;
  }
}

