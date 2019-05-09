<?php
// app/Model/Membership.php
class Membership extends AppModel {

  public $actsAs = array('Containable');

  public $hasMany = array('Availability');

  public $belongsTo = array('Profile', 'State');

//  public $hasAndBelongsToMany = array('Group' => array('unique' => 'keepExisting'));
//  public $hasAndBelongsToMany = array('Group' => array('unique' => 'false'));
  public $hasAndBelongsToMany = array('Group');


  public $validate = array(
    'profile_id' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Bitte Benutzerprofil auswählen',
      )
    ),
    'state_id' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Bitte Zustand der Mitgliedschaft auswählen',
      )
    )
  );

  public function createCalendar($id) {
    $this->id = $id;
    $membership = $this->read();
    if (empty($membership)) {
      return false;
    }
    if (empty($membership['Membership']['calendar_link'])) {
      $membership['Membership']['calendar_link'] = CakeText::uuid();
      $groups = array('Group' => []);
      foreach ($membership['Group'] as $group) {
        $groups['Group'][] = $group['id'];
      }
      $membership['Group'] = $groups;
      $this->save($membership);
    }
    return $membership['Membership']['calendar_link'];
  }

  public function deleteCalendar($id) {
    $this->id = $id;
    $membership = $this->read();
    if (empty($membership)) {
      return false;
    }
    if (!empty($membership['Membership']['calendar_link'])) {
      $membership['Membership']['calendar_link'] = '';
      $groups = array('Group' => []);
      foreach ($membership['Group'] as $group) {
        $groups['Group'][] = $group['id'];
      }
      $membership['Group'] = $groups;
      $this->save($membership);
    }
    return true;
  }

  private $state_has_changed = false;

  public function beforeSave($options = array()) {
    if (isset($this->data['Membership']['id'])) {
      $this->contain();
      $membership = $this->findById($this->data['Membership']['id']);
      if ($membership['Membership']['state_id'] != $this->data['Membership']['state_id']) {
        $this->state_has_changed = true;
      }
    }
  }

  public function afterSave( $created, $options = array() ) {
    // if groups_memberships or state changed, create/destroy availabilities.
    $membership = $this->findById($this->id);

    // get event's modes, where availability is necessary
    $modes = $this->Group->Event->Mode->findAllBySetAvailability(true);
    $mode_ids = [];
    foreach ($modes as $mode) {
      $mode_ids[] = $mode['Mode']['id'];
    }
    // get all events, where availability is necessary
    // and 'availability_checked' is not yet checked
    $events = $this->Group->Event->find('all', array('conditions' => array(
      'availabilities_checked' => false,
      'mode_id' => $mode_ids
    )));

    // loop through events
    foreach ($events as $event) {
      // find availability
      $availability = $this->Availability->find('first', array('conditions' => array(
        'event_id'      => $event['Event']['id'],
        'membership_id' => $membership['Membership']['id']
      )));
      // check if membership needs availabilities
      if ($membership['State']['set_availability']) {
        // membership needs availabilities...
        $group_ids = array();
        foreach( $event['Group'] as $group ) {
          $group_ids[] = $group['id'];
        }
        // check if this membership belongs to event's group
        $tmp = $this->GroupsMembership->find('first', array(
            'conditions' => array(
              'group_id'      => $group_ids,
              'membership_id' => $membership['Membership']['id']
            )
        ));
        if (!empty($tmp)) {
          // check if availability has to be created
          if (empty($availability)) {
            $available = false;
            if (!$this->Group->Event->started($event['Event'])) {
              $available = $membership['State']['is_available'];
            }
            // create availability
            $this->Availability->create();
            $this->Availability->save(array(
              'membership_id' => $membership['Membership']['id'],
              'event_id'      => $event['Event']['id'],
              'is_available'  => $available,
              'was_available' => $available
            ));
          } else {
            // modify availability if membership's state has changed
            if ($this->state_has_changed) {
              $availability['Availability']['is_available'] = $membership['State']['is_available'];
              $availability['Availability']['was_available'] = $membership['State']['is_available'];
              $this->Availability->save($availability);
            }
          }
        } else {
          // check if availability has to be deleted
          if (!empty($availability)) {
            // delete availability
            $this->Availability->delete($availability['Availability']['id']);
          }
        }
      } else {
        // membership does not need availabilities...
        // delete all availabilities for upcoming events
        if (!empty($availability)) {
          // delete availability
          $this->Availability->delete($availability['Availability']['id']);
        }
      }
    }
  }

  public function beforeDelete($cascade = true) {
    $profile = $this->read();

    // delete assotiated availabilities
    $this->Availability->deleteAll(array('Availability.membership_id' => $this->id), false);

    // delete assotiations to groups but keep groups
    $this->GroupsMembership->deleteAll(array('GroupsMembership.membership_id' => $this->id), false);

    return true;
  }

}

