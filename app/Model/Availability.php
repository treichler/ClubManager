<?php
// app/Model/Availability.php
class Availability extends AppModel {

  public $actsAs = array('Containable');

  public $belongsTo = array('Event', 'Membership');


  public function isAvailable($id, $val = null, $user = null) {
    $this->id = $id;
    $availability = $this->read();

    if(!empty($availability)) {
      if ($val == null) {
        // read 'is_available' from database
        $response = $availability['Availability']['is_available'] ? 'true' : 'false';
        $state = 'ok';
        $message = 'read data';
      } else {
        $tmp = $availability['Availability']['is_available'];
        $availability['Availability']['is_available'] = $availability['Availability']['was_available'] = ($val === 'true') ? true : false;
        // is only allowed before event's deadline
        // availability's event-creator is allowed until begining of event
        if (((!$this->Event->expired($availability['Event']) && $this->isOwner($user, $availability)) ||
            $this->isCreator($user, $availability)) && $this->save($availability)) {
          $response = $availability['Availability']['is_available'] ? 'true' : 'false';
          $state = 'ok';
          $message = 'Wurde gespeichert';
        } else {
          $response = $tmp ? 'true' : 'false';
          $state = 'alert';
          if ($this->Event->expired($availability['Event']) || !$this->isOwner($user, $availability)) {
            if ($this->isOwner($user, $availability)) {
              $message = 'Abmelden/Anmelden nicht möglich';
            } else {
              $message = 'Es kann nur die eigene Anwesenheit bearbeitet werden';
            }
          } else {
            $message = 'Data not saved';
          }
        }
      }
    } else {
      $response = '';
      $message = 'No database entry';
      $state = 'alert';
    }
    return array('response' => $response, 'message' => $message, 'state' => $state);
  }

  public function wasAvailable($id, $val = null, $user = null) {
    $this->id = $id;
    $availability = $this->read();
    if(!empty($availability)) {
      if ($val == null) {
        // read 'is_available' from database
        $response = $availability['Availability']['was_available'] ? 'true' : 'false';
        $state = 'ok';
        $message = 'read data';
      } else {
        $tmp = $availability['Availability']['was_available'];
        $availability['Availability']['was_available'] = ($val === 'true') ? true : false;

        // users with privileg "Availability" are allowed after begining of event until availabilities are checked
        if ($this->Event->started($availability['Event']) && !$availability['Event']['availabilities_checked'] &&
            $this->isAvailabilityAdmin($user) && $this->save($availability)) {
          $response = $availability['Availability']['was_available'] ? 'true' : 'false';
          $state = 'ok';
          $message = 'Wurde gespeichert';
        } else {
          $response = $tmp ? 'true' : 'false';
          $state = 'alert';

          if ($this->isAvailabilityAdmin($user)) {
            if ($this->Event->started($availability['Event']) && $availability['Event']['availabilities_checked']) {
              $message = 'Die Anwesenheitsliste ist bereits abgeschlossen.';
            } else {
              $message = 'Event hat noch nicht begonnen';
            }
          } else {
            $message = 'Nur für Benutzer, die die Anwesenheitsliste verwalten.';
          }
        }
      }
    } else {
      $response = '';
      $message = 'No database entry';
      $state = 'alert';
    }
    return array('response' => $response, 'message' => $message, 'state' => $state);
  }

  public function info($id, $data = null, $user = null) {
    $this->id = $id;
    $availability = $this->read();

    if(!empty($availability)) {
      if (!isset($data['info'])) {
        // read 'info' from database
        $response = $availability['Availability']['info'];
        $state = 'ok';
        $message = 'read data';
      } else {
        $tmp = $availability['Availability']['info'];
        $availability['Availability']['info'] = $data['info'];
        // owner is allowed until event's deadline
        // availability's event-creator is allowed until begining of event
        // users with privileg "Availability" are allowed after begining of event until availabilities are checked
        if (((!$this->Event->expired($availability['Event']) && $this->isOwner($user, $availability)) ||
             (!$this->Event->started($availability['Event']) && $this->isCreator($user, $availability)) ||
             ($this->Event->started($availability['Event']) && !$availability['Event']['availabilities_checked'] &&
              $this->isAvailabilityAdmin($user))) && $this->save($availability)) {
          $response = $availability['Availability']['info'];
          $state = 'ok';
          $message = 'Info wurde gespeichert';
        } else {
          $response = $tmp;
          $state = 'alert';
          if ($availability['Event']['availabilities_checked'])
            $message = 'Die Anwesenheitsliste ist bereits abgeschlossen.';
          else {
            if ($this->Event->started($availability['Event']))
              $message = 'Nur für Benutzer, die die Anwesenheitsliste verwalten.';
            else
              $message = 'Info kann nicht gespeichert werden';
          }
        }
      }
    } else {
      $response = '';
      $message = 'No database entry';
      $state = 'alert';
    }
//    return array('response' => 'foo bar', 'message' => 'Data written to DB', 'state' => 'ok');
    return array('response' => $response, 'message' => $message, 'state' => $state);
  }


/****************************************************************************************/
/*                                  Private functions                                   */
/****************************************************************************************/

/* TODO delete since implemented in Event.php
  // returns true if the availability is expired
  private function expired($availability) {
//    $mode = $this->Event->Mode->findById($availability['Event']['mode_id']);
//    $interval = new DateInterval('P' . $mode['Mode']['expiry'] . 'D');
    $interval = new DateInterval('P' . $availability['Event']['expiry'] . 'D');
    $interval->invert = true;                                   // negate the expiry interval
    $now = new DateTime();                                      // get actual time
    $deadline = new DateTime($availability['Event']['start']);  // get start dateTime
    $deadline->setTime(0, 0);                                   // set time to 00:00
    $deadline->add($interval);                                  // add (negative) expiry interval
    $calcDiff = $deadline->diff($now);                          // calculate difference to now
    return ($calcDiff->format("%r%a") > 0);
  }

  // returns true as soon as the event started
  private function started($availability) {
    $now = new DateTime();
    $start = new DateTime($availability['Event']['start']);  // get start dateTime
    return (($now->getTimestamp() - $start->getTimestamp()) > 0);
  }
*/

  // returns true if user is the owner of this availability
  private function isOwner($user = null, $availability = null) {
    if ($user == null || $availability == null) {
      return false;
    }
    $profile = $this->Membership->Profile->findById($availability['Membership']['profile_id']);
    return ($profile['Profile']['user_id'] == $user['User']['id']);
  }

  // returns true before the event starts if user is the avilability's event-creator
  private function isCreator($user = null, $availability = null) {
    if ($user == null || $availability == null) {
      return false;
    }
    $now = new DateTime();
    $start = new DateTime($availability['Event']['start']);
    return (($user['User']['id'] == $availability['Event']['user_id']) &&
            (($start->getTimestamp() - $now->getTimestamp()) > 0));
  }

  private function isAvailabilityAdmin($user) {
    return array_has_key_val($user['Privileg'], 'name', 'Availability');
  }
}

