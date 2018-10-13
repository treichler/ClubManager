<?php
// app/Model/Location.php
class Location extends AppModel {

  public $actsAs = array('Containable');

  public $hasMany = array('Event');


  // before delete verify that there is no related event
  public function beforeDelete($cascade = true) {
    $count = $this->Event->find('count', array(
      'conditions' => array('location_id' => $this->id)
    ));
    if ($count == 0) {
      return true;
    }
    return false;
  }

}

