<?php
// app/Model/Customer.php
class Customer extends AppModel {

  public $actsAs = array('Containable');

  public $hasMany = array('Event');

/*
  public $validate = array(
    'name' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Name eintragen.'
    ),
    'street' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Straße eintragen.'
    ),
    'postal_code' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Postleitzahl eintragen.'
    ),
    'town' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Straße eintragen.'
    )
  );
*/


  public function afterFind($results, $primary = false) {
    foreach ($results as $key => $value) {
      if (isset($value['Customer']['id'])) {
        $val = &$value['Customer'];
        $res = &$results[$key]['Customer'];
      }
      if (isset($value['id'])) {
        $val = &$value;
        $res = &$results[$key];
      }
      $res['address'] = '';
      if (isset($val['street'])) {
        $res['address'] .= $val['street'];
      }
      if ($res['address']) {
        $res['address'] .= ', ';
      }
      if (isset($val['postal_code'])) {
        $res['address'] .= $val['postal_code'] . ' ';
      }
      if (isset($val['town'])) {
        $res['address'] .= $val['town'];
      }
    }
    return $results;
  }


  // before delete verify that there is no related event
  public function beforeDelete($cascade = true) {
    $count = $this->Event->find('count', array(
      'conditions' => array('customer_id' => $this->id)
    ));
    if ($count == 0) {
      return true;
    }
    return false;
  }

}

