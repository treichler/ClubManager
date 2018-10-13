<?php
// app/Controller/AvailabilitiesController.php
class AvailabilitiesController extends AppController {

  public function beforeFilter() {
    // everybody is allowed to call 'calendar', but you have to know calendar's uuid ;-)
    $this->Auth->allow('calendar', 'is_available', 'was_available', 'info');
  }

  public function isAuthorized($user) {
    $profile = $this->getProfile();

    // club-members are allowed to access 'index' and 'publicise'
    if ($this->action === 'index' || $this->action === 'publicise') {
      if (!empty($profile) && !empty($profile['Membership']['id'])) {
        return true;
      }
    }

    // TODO user 'terminal' is allowed to access 'terminal'
//    if (($this->action === 'terminal') && (getUser()['User']['username'] === 'terminal'))
    if ($this->action === 'terminal')
      return false;
  }

  public $components = array('RequestHandler');
  public $helpers = array('Html', 'Form', 'Js' => array('Jquery'));
//public $helpers = array('Js', 'Html');

  public function index() {
    $user = $this->getUser();
    $this->Availability->Membership->contain();
    $membership = $this->Availability->Membership->findByProfileId($user['Profile']['id']);
    $availabilities = $this->getAvailabilities($membership);
    $this->set('availabilities', $availabilities);
    $this->set('membership', $membership);
  }

  public function calendar($uuid = null) {
    $client = null;
    if(isset($this->params['url']['client'])) {
      $client = $this->params['url']['client'];
    }
    $this->set(compact('client'));

    $this->Availability->Membership->contain('Profile');
    $membership = $this->Availability->Membership->findByCalendarLink($uuid);
    if (empty($membership)) {
      $this->response->type('txt');
      $this->response->body('Der Link existiert nicht.');
      return $this->response;
    } else {
      $this->set('profile', $membership['Profile']);
      // get availabilities from the last 120 days
      $days_before = 120;

      $date = new DateTime();
      $interval = date_interval_create_from_date_string($days_before . ' days');
      $interval->invert = true;
      date_add($date, $interval);

      $availabilities = $this->Availability->find('all', array(
        'contain' => array(
          'Event' => array(
            'Group.name',
            'Mode' => array('fields' => array('name', 'is_public')),
            'User'
          )
        ),
        'conditions' => array(
          'Event.stop >' => $date->format('Y-m-d'),
          'Availability.membership_id' => $membership['Membership']['id']
        ),
      ));

      $this->set(compact('availabilities'));
    }
  }

  public function publicise() {
    $user = $this->getUser();
    $membership = $this->Availability->Membership->findByProfileId($user['Profile']['id']);

//    $data = $this->request->input('json_decode');
//      $response = print_r($this->request->data);

    if ($this->request->data['command'] === 'create') {
      if (empty($membership['Membership']['calendar_link'])) {
        $uuid = $this->Availability->Membership->createCalendar($membership['Membership']['id']);
      } else {
        $uuid = $membership['Membership']['calendar_link'];
      }
      $response = $uuid;
    }
    if ($this->request->data['command'] === 'delete') {
      $this->Availability->Membership->deleteCalendar($membership['Membership']['id']);
      $response = 'deleted';
    }
    $this->response->type('txt');
    $this->response->body($response);
    return $this->response;
  }

  public function is_available($id = null) {
    if ($this->request->is('ajax')) {
      if ($this->request->is('post')) {
        $response = $this->Availability->isAvailable($id, 
                    $this->request->data['is_available'], $this->getUser());
      } else {
        $response = $this->Availability->isAvailable($id);
      }
    } else {
      $response = array('response'=>'', 'state' => 'error', 'message' => 'nothing to do...');
    }
    $this->response->type('json');
    $this->response->body(json_encode($response));
    return $this->response;
  }

  public function was_available($id = null) {
    if ($this->request->is('ajax')) {
      if ($this->request->is('post')) {
        $response = $this->Availability->wasAvailable($id, 
                    $this->request->data['was_available'], $this->getUser());
      } else {
        $response = $this->Availability->wasAvailable($id);
      }
    } else {
      $response = array('response'=>'', 'state' => 'error', 'message' => 'nothing to do...');
    }
    $this->response->type('json');
    $this->response->body(json_encode($response));
    return $this->response;
  }

  public function info($id = null) {
    if ($this->request->is('ajax')) {
      if ($this->request->is('post')) {
        $response = $this->Availability->info($id, 
                    $this->request->data, $this->getUser());
      } else {
        $response = $this->Availability->info($id);
      }
    } else {
      $response = array('response'=>'', 'state' => 'error', 'message' => 'nothing to do...');
    }
    $this->response->type('json');
    $this->response->body(json_encode($response));
    return $this->response;
  }

/*
  public function terminal() {
    $events = $this->Availability->Event->find('all', array(
      'conditions' => array('start >' => date('Y-m-d 00:00:00')),
      'order' => array('start' => 'asc')
    ));
    $event_ids = [];
    foreach ($events as $event) {
      $event_ids[] = $event['Event']['id'];
    }
    $availabilities = $this->Availability->find('all', array(
      'conditions' => array('event_id' => $event_ids)
    ));
    $this->set('availabilities', $availabilities);

    $states = $this->Availability->Membership->State->find('all', array(
      'conditions' => array('set_availability' => true),
      'order' => array('is_member' => 'asc')
    ));
    $state_ids = [];
    foreach ($states as $state) {
      $state_ids[] = $state['State']['id'];
    }
    $memberships = $this->Availability->Membership->find('all', array(
      'conditions' => array('state_id' => $state_ids),
      'order' => array('is_member' => 'asc')
    ));
    $this->set('memberships', $memberships);
  }
*/

  private function getAvailabilities($membership, $days_before = null) {
    $date = new DateTime();
    $interval = date_interval_create_from_date_string($days_before . ' days');
    $interval->invert = true;
    date_add($date, $interval);

    $this->Availability->Event->contain();
    $events = $this->Availability->Event->find('all', array(
      'conditions' => array('Event.stop >' => $date->format('Y-m-d')),
    ));
    $event_ids = [];
    foreach ($events as $event) {
      $event_ids[] = $event['Event']['id'];
    }
    $this->Availability->contain('Event');
    $availabilities = $this->Availability->find('all', array(
      'conditions' => array(
        'Availability.membership_id' => $membership['Membership']['id'],
        'Availability.event_id' => $event_ids
      ),
      'order' => array('Event.start' => 'asc')
    ));

    // fetch personal events
    $event_ids = [];
    foreach ($availabilities as $availability) {
      $event_ids[] = $availability['Event']['id'];
    }
    $this->Availability->Event->contain('Group', 'Resource');
    $events = $this->Availability->Event->find('all', array(
      'conditions' => array('Event.id' => $event_ids),
      'order' => array('Event.start' => 'asc')
    ));
    $this->set(compact('events'));

    return $availabilities;
  }

}

