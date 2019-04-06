<?php
// app/Model/Event.php
class Event extends AppModel {

  public $actsAs = array('Containable');

  public function isOwnedBy($event, $user) {
    return ($this->field('id', array('id' => $event, 'user_id' => $user)) === $event);
  }

  public $hasMany = array('Availability', 'Track');

  public $belongsTo = array(
    'Customer',
    'Location',
    'Mode',
    'User' => array('counterCache' => true)
  );

  public $hasAndBelongsToMany = array(
    'Group',
    'Resource'
  );


  public $validate = array(
    'mode_id' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Art des Termins auswählen.'
    )
  );
// TODO validate time: 'start' has to be before 'stop'
// TODO validate against members collissions
// TODO validate against resources collissions

/*
  public function afterFind($results, $primary = false) {
    foreach ($results as $key => $val) {
      if (!$results[$key]['Event']['name'] && isset($results[$key]['Mode'])) {
        $results[$key]['Event']['name'] = $results[$key]['Mode']['name'];
      }
    }
    return $results;
  }
*/

  public function availabilitiesChecked ($id, $val = null) {
    $this->id = $id;
    $event = $this->read();
    if (empty($event)) {
      $response = '';
      $message = 'No database entry';
      $state = 'error';
    } else {
      if ($val == null) {
        $response = $event['Event']['availabilities_checked'] ? 'true' : 'false';
        $state = 'ok';
        $message = 'read data';
      } else {
        $tmp = $event['Event']['availabilities_checked'];
        $event['Event']['availabilities_checked'] = ($val == 'true') ? true : false;
        // keep resources
        $resources = array('Resource' => []);
        foreach ($event['Resource'] as $resource) {
          $resources['Resource'][] = $resource['id'];
        }
        $event['Resource'] = $resources;
        if ($this->started($event['Event']) && $this->save($event)) {
          $response = $event['Event']['availabilities_checked'] ? 'true' : 'false';
          $state = 'alert';
          $message = 'Die Anwesenheitsliste wurde ' . (($response === 'true') ? 'bestätigt' : 'widerrufen');
        } else {
          $response = $tmp ? 'true' : 'false';
          $state = 'alert';
          if (!$this->started($event['Event'])) {
            $message = 'Event hat noch nicht begonnen';
          } else {
            $message = 'Data not saved';
          }
        }
      }
    }
    return array('response' => $response, 'message' => $message, 'state' => $state);
  }

  public function tracksChecked ($id, $val = null) {
    $this->id = $id;
    $event = $this->read();
    if (empty($event)) {
      $response = '';
      $message = 'No database entry';
      $state = 'error';
    } else {
      if ($val == null) {
        $response = $event['Event']['tracks_checked'] ? 'true' : 'false';
        $state = 'ok';
        $message = 'read data';
      } else {
        $tmp = $event['Event']['tracks_checked'];
        $event['Event']['tracks_checked'] = ($val == 'true') ? true : false;
        // keep resources
        $resources = array('Resource' => []);
        foreach ($event['Resource'] as $resource) {
          $resources['Resource'][] = $resource['id'];
        }
        $event['Resource'] = $resources;
        if ($this->started($event['Event']) && $this->save($event)) {
          $response = $event['Event']['tracks_checked'] ? 'true' : 'false';
          $state = 'alert';
          $message = 'Die Liste der gespielten Musikstücke wurde ' . (($response === 'true') ? 'bestätigt' : 'widerrufen');
        } else {
          $response = $tmp ? 'true' : 'false';
          $state = 'alert';
          if (!$this->started($event['Event'])) {
            $message = 'Event hat noch nicht begonnen';
          } else {
            $message = 'Data not saved';
          }
        }
      }
    }
    return array('response' => $response, 'message' => $message, 'state' => $state);
  }


  public function beforeSave($options = array()) {
    $mode = $this->Mode->findById($this->data['Event']['mode_id']);
    // if empty set the event's name to default value
    if(empty($this->data['Event']['name'])) {
      $this->data['Event']['name'] = $mode['Mode']['name'];
    }
    // if empty set the event's expiry to default value
    if(empty($this->data['Event']['expiry'])) {
      $this->data['Event']['expiry'] = $mode['Mode']['expiry'];
    }
    // on create (event has no id yet)
    if (empty($this->data['Event']['id'])) {
      // set availabilities_checked if mode does not need availabilities
      if (!$mode['Mode']['set_availability']) {
        $this->data['Event']['availabilities_checked'] = true;
      } else {
        $this->data['Event']['availabilities_checked'] = false;
      }
      // set tracks_checked if mode does not need tracks
      if (!$mode['Mode']['set_track']) {
        $this->data['Event']['tracks_checked'] = true;
      } else {
        $this->data['Event']['tracks_checked'] = false;
      }
      // set quota to default value
      $this->data['Event']['quota'] = $mode['Mode']['quota_default'];
    }

    // handle new entries
    if (isset($this->data['new'])) {

      // handle new customer
      if (isset($this->data['new']['Customer'])) {
        if (empty($this->data['new']['Customer']['name'])) {
          // clear link to customer
          $this->data['Event']['customer_id'] = null;
        } else {
          $customer = $this->Customer->find('first', array(
            'conditions' => array('Customer.name' => $this->data['new']['Customer']['name'])
          ));
          if (isset($customer['Customer']['id'])) {
            // use existing customer
            $this->data['Event']['customer_id'] = $customer['Customer']['id'];
          } else {
            // create new customer
            $this->Customer->create();
            if($this->Customer->save($this->data['new']))
              $this->data['Event']['customer_id'] = $this->Customer->id;
          }
        }
      } // END: handle new customer

      // handle new location
      if (isset($this->data['new']['Location'])) {
        // if empty on create set the event's location to default value
        if(empty($this->data['Event']['id']) && empty($this->data['new']['Location']['name'])) {
          $this->data['new']['Location']['name'] = $mode['Mode']['location_default'];
        }
        if (empty($this->data['new']['Location']['name'])) {
          // clear link to location
          $this->data['Event']['location_id'] = null;
        } else {
          $location = $this->Location->find('first', array(
            'conditions' => array('Location.name' => $this->data['new']['Location']['name'])
          ));
          if (isset($location['Location']['id'])) {
            // use existing location
            $this->data['Event']['location_id'] = $location['Location']['id'];
          } else {
            // create new location
            $this->Location->create();
            if($this->Location->save($this->data['new']))
              $this->data['Event']['location_id'] = $this->Location->id;
          }
        }
      } // END: handle new location

    } // END: handle new entries
  }


  public function afterSave( $created, $options = array() ) {
    // create availabilities at create and if mode and membership set_availability is true
    $this->contain('Group.id', 'Mode');
    $event = $this->read();
    if ($created && $event['Mode']['set_availability']) {
      $this->Group->contain('Membership');
      $group_ids = [];
      foreach ($event['Group'] as $group) {
        $group_ids[] = $group['id'];
      }
      $groups = $this->Group->find('all', array(
        'conditions' => array('Group.id' => $group_ids)
      ));
      $membership_ids = [];
      foreach ($groups as $group) {
        foreach ($group['Membership'] as $membership) {
          if (!in_array($membership['id'], $membership_ids))
            $membership_ids[] = $membership['id'];
        }
      }
      $this->Group->Membership->contain('State');
      $memberships = $this->Group->Membership->find('all', array(
        'conditions' => array(
          'Membership.id' => $membership_ids,
          'State.set_availability' => true,
        ),
      ));
      foreach ($memberships as $membership) {
        $this->Availability->create();
        $this->Availability->save(array(
          'membership_id' => $membership['Membership']['id'],
          'event_id'      => $this->id,
          'is_available'  => $membership['State']['is_available'],
          'was_available' => $membership['State']['is_available']
        ));
      }
    }
  }

  public function afterFind($results, $primary = false) {
    $modes = $this->Mode->find('list', array('fields' => array('id', 'is_important')));
    $now = new DateTime();
    foreach ($results as $key => $value) {
      if (isset($value['Event']['id'])) {
        $val = &$value['Event'];
        $res = &$results[$key]['Event'];
      }
      if (isset($value['id'])) {
        $val = &$value;
        $res = &$results[$key];
      }

//      $result['Event']['expired'] = $this->expired($result['Event']);
      if (isset($val['expiry'])) {
        $res['expired'] = $this->expired($val);
        $res['high_priority'] = $modes[$val['mode_id']];
        $stop = new DateTime($val['stop']);
        $res['finished'] = ($now->getTimestamp() > $stop->getTimestamp());
      }

      $res['location'] = '';
      if (isset($val['location_id'])) {
        $this->Location->contain();
        $location = $this->Location->findById($val['location_id']);
        if (isset($location['Location']['name'])) {
          $res['location'] = $location['Location']['name'];
        }
      }
    }
    return $results;
  }

  // returns true if the event is expired
  public function expired($event) {
//    $mode = $this->Event->Mode->findById($availability['Event']['mode_id']);
//    $interval = new DateInterval('P' . $mode['Mode']['expiry'] . 'D');
    $interval = new DateInterval('P' . $event['expiry'] . 'D');
    $interval->invert = true;                                   // negate the expiry interval
    $now = new DateTime();                                      // get actual time
    $deadline = new DateTime($event['start']);                  // get start dateTime
    $deadline->setTime(0, 0);                                   // set time to 00:00
    $deadline->add($interval);                                  // add (negative) expiry interval
    $calcDiff = $deadline->diff($now);                          // calculate difference to now
    return ($calcDiff->format("%r%a") > 0);
  }

  // returns true as soon as the event started
  public function started($event) {
    $now = new DateTime();
    $start = new DateTime($event['start']);  // get start dateTime
    return (($now->getTimestamp() - $start->getTimestamp()) > 0);
  }

  public function beforeDelete($cascade = true) {
    // delete availabilities
    $this->Availability->deleteAll(array('Availability.event_id' => $this->id), false);
    return true;
  }

}

