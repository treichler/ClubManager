<?php
// app/Controller/EventsController.php
class EventsController extends AppController {

  public function beforeFilter() {
    // everybody is allowed to call 'news'
    $this->Auth->allow('news', 'availabilities_checked', 'tracks_checked');
  }

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    // club-members are allowed to access 'index' and 'view'
    if ($this->action === 'index' || $this->action === 'view' || $this->action === 'pdf') {
      $profile = $this->getProfile();
      if (!empty($profile) && !empty($profile['Membership']['id'])) {
        $membership = $this->Event->Availability->Membership->contain('State');
        $membership = $this->Event->Availability->Membership->findById($profile['Membership']['id']);
        if ($membership['State']['is_member'])
          return true;
      }
    }

    // only group-admins are allowed to access 'add'
    if ($this->action === 'add') {
      foreach ($privilegs['Privileg'] as $privileg) {
        if ($privileg['id'] > 100) {
          return true;
        }
      }
    }

    // the event's creator and users with the privileg 'Administrator' are allowed to access 'edit' and 'delete'
    if ($this->action === 'edit' || $this->action === 'delete') {
      $event_id = $this->request->params['pass'][0];
      if ($this->Event->isOwnedBy($event_id, $user['id']) ||
          array_has_key_val($privilegs['Privileg'], 'name', 'Administrator')) {
        return true;
      } else {
        $this->Session->setFlash('Termin kann nur von jener Person bearbeitet werden, die diesen erstellt hat');
      }
    }
  }

  public $components = array('RequestHandler');

  public $helpers = array('Html', 'Form', 'Js');

  public function availabilities_checked($id = null) {
    if ($this->request->is('ajax')) {
      if ($this->request->is('post')) {
        // only users with privileg 'Availability' are allowed to set 'availability_checked'
        if (array_has_key_val($this->getUser()['Privileg'], 'name', 'Availability')) {
          $response = $this->Event->availabilitiesChecked($id,
                      $this->request->data['availabilities_checked']);
        } else {
          $response = $this->Event->availabilitiesChecked($id);
          $response['state'] = 'alert';
          $response['message'] = 'Nur für Benutzer, die die Anwesenheitsliste verwalten.';
        }
      } else {
        $response = $this->Event->availabilitiesChecked($id);
      }
    } else {
      $response = array('response'=>'', 'state' => 'error', 'message' => 'nothing to do...');
    }
    $this->response->type('json');
    $this->response->body(json_encode($response));
    return $this->response;
  }

  public function tracks_checked($id = null) {
    if ($this->request->is('ajax')) {
      if ($this->request->is('post')) {
        // only users with privileg 'Track' are allowed to set 'track_checked'
        if (array_has_key_val($this->getUser()['Privileg'], 'name', 'Track')) {
          $response = $this->Event->tracksChecked($id,
                      $this->request->data['tracks_checked']);
        } else {
          $response = $this->Event->tracksChecked($id);
          $response['state'] = 'alert';
          $response['message'] = 'Nur für Benutzer, die die Liste der gespielten Musikstücke verwalten.';
        }
      } else {
        $response = $this->Event->tracksChecked($id);
      }
    } else {
      $response = array('response'=>'', 'state' => 'error', 'message' => 'nothing to do...');
    }
    $this->response->type('json');
    $this->response->body(json_encode($response));
    return $this->response;
  }

  public function news($id = null) {
    $this->Event->contain('Group', 'Location');
    $modes = $this->Event->Mode->findAllByIsPublic(true);
    $mode_ids = [];
    foreach ($modes as $mode) {
      $mode_ids[] = $mode['Mode']['id'];
    }

    if ($this->RequestHandler->isRss()) {
      $events = $this->Event->find('all', array(
        'conditions' => array(
          'Event.stop >' => date('Y-m-d', strtotime('-8 days')),
          'Event.start <=' => date('Y-m-d', strtotime('+8 days')),
          'Event.mode_id' => $mode_ids
        ),
        'order' => array('Event.start' => 'asc')
      ));
      return $this->set(compact('events'));
    }

    $days_offset = -2;
//    if (isset($this->request->params['ext']) && strtolower($this->request->params['ext']) == 'ics') {
//      $days_offset = -120;
//    }
    $events = $this->Event->find('all', array(
      'conditions' => array(
        'Event.stop >' => date('Y-m-d', strtotime($days_offset . ' days')),
        'Event.mode_id' => $mode_ids
      ),
      'order' => array('Event.start' => 'asc'),
      'limit' => Configure::read('paginate.event_count'),
    ));
    $this->set(compact('events'));
  }

  public function index() {
    $this->Event->contain('Availability', 'Group', 'Mode', 'Resource');
    $events = $this->Event->find('all', array(
      'conditions' => array('OR' => array(
        'stop >' => date('Y-m-d', strtotime('-2 weeks')),
        'availabilities_checked' => false,
        'tracks_checked' => false
      )),
      'order' => array('Event.start' => 'asc')
    ));
    $this->set(compact('events'));

    // get names of users who created at least one event
    $this->set('user_names', $this->getUserNames($this->name));
  }

  public function view($id = null) {
    // find all kind of groups that have to be shown in the availability list
    $kinds = $this->Event->Group->Kind->find('all', array(
      'fields' => array('Kind.id'),
      'contain' => array(),
      'conditions' => array('Kind.show_in_availability_list' => True),
    ));
    $kind_ids = [];
    foreach( $kinds as $kind )
      $kind_ids[] = $kind['Kind']['id'];
    $this->set(compact('kind_ids'));

    // load the event and all availabilities with related data
    $event = $this->Event->find('first', array(
      'conditions' => array('Event.id' => $id),
      'contain' => array(
        'Availability' => array(
          'Membership' => array(
            'Profile' => array(
              'fields' => array('Profile.first_name', 'Profile.last_name'),
            ),
            'State' => array(
              'fields' => array('State.id', 'State.name', 'State.is_member'),
            ),
            'Group' => array(
              'fields' => array('Group.id', 'Group.name', 'Group.sorting'),
              'conditions' => array('Group.kind_id' => $kind_ids),
              'order' => array('Group.sorting' => 'asc')
            ),
          ),
        ),
        'Group',
        'Resource',
        'Mode',
        'Customer'
      ),
    ));
    // Only event's owner and users with privilege 'Administrator' or 'Availabilities'
    // are expected to see all related members
    if( !($this->Event->isOwnedBy($id, $this->getUser()['User']['id']) ||
          array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator') ||
          array_has_key_val($this->getUser()['Privileg'], 'name', 'Availability')) ) {
      // remove non-members (State.is_member == false)
      foreach ($event['Availability'] as $key => $val) {
        if ( !$val['is_available'] && isset($val['Membership']['State']['is_member']) && !$val['Membership']['State']['is_member'] ) {
          unset($event['Availability'][$key]);
        }
      }
    }
    $this->set(compact('event'));

    // load the administrator of this event
    $this->Event->User->contain('Profile');
    $user = $this->Event->User->find('first', array(
      'conditions' => array('User.id' => $event['Event']['user_id']),
    ));
    $admin = isset($user['Profile']['id']) ? $user['Profile']['first_name'] . ' ' . $user['Profile']['last_name'] : $user['User']['username'];
    $this->set(compact('admin'));
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->request->data['Event']['user_id'] = $this->Auth->user('id');

      // check if user has the privileg to create event for the selected group
      $groups = $this->Event->Group->find('all', array(
        'conditions' => array('Group.id' => $this->request->data['Group']['Group'])
      ));
      $nr_of_hits = 0;
      foreach ($groups as $group) {
        if (array_has_key_val($this->getUser()['Privileg'], 'id', $group['Privileg']['id']))
          $nr_of_hits += 1;
      }
      $user_has_groups_privilegs = false;
      if ($nr_of_hits == sizeof($groups))
        $user_has_groups_privilegs = true;

//$this->Session->setFlash( 'nr_of_hits: ' . $nr_of_hits . ', user_has_groups_privilegs: ' . $user_has_groups_privilegs );

      if ($user_has_groups_privilegs) {
        $this->Event->create;
        if ($this->Event->save($this->request->data)) {
          $this->Session->setFlash('Termin wurde gespeichert.', 'default', array('class' => 'success'));
          $this->redirect(array('action' => 'index'));
        } else {
          $this->Session->setFlash('Termin konnte nicht gespeichert werden.');
        }
      } else {
        if (isset($group['Group']['id'])) {
          $this->Session->setFlash('Termin für die Gruppe "' . $group['Group']['name'] .
            '" kann nicht angelegt werden. Keine Berechtigung.');
        } else {
          $this->Session->setFlash('Bitte Gruppe auswählen');
        }
      }
    }

    // add drop down lists to the instant variable
    $this->fetchDropDownLists();
  }

  public function edit($id = null) {
    $event = $this->Event->find('first', array(
      'conditions' => array('Event.id' => $id),
      'contain' => array(
        'Group' => array('Privileg' => array('User')),
        'Customer', 'Location', 'Resource', 'Mode'
      ),
    ));

    // add all users who are admins of the related groups
    $group_user_ids_counts = [];
    $group_user_names      = [];
    foreach ($event['Group'] as $group) {
      foreach ($group['Privileg']['User'] as $user) {
        if (isset($group_user_ids_counts[$user['id']])) {
          $group_user_ids_counts[$user['id']] += 1;
        } else {
          $group_user_names[$user['id']] = $user['name'];
          $group_user_ids_counts[$user['id']] = 1;
        }
      }
    }
    $users = [];
    foreach ($group_user_ids_counts as $user_id => $count) {
      if ($count == sizeof($event['Group']))
        $users[$user_id] = $group_user_names[$user_id];
    }
    $this->set(compact('users'));

    // add drop down lists to the instant variable
    $this->fetchDropDownLists( $event['Event']['mode_id'] );

    if ($this->request->is('get')) {
      $this->request->data = $event;
    } else {
//      $this->request->data['Event']['group_id'] = $event['Event']['group_id'];
//      $this->request->data['Event']['mode_id'] = $event['Event']['mode_id'];
//      $this->request->data['Event']['user_id'] = $event['Event']['user_id'];
      if ($this->Event->save($this->request->data)) {
        $this->Session->setFlash('Termin wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Termin konnte nicht aktualisiert werden.');
      }
    }
  }

  public function delete($id = null) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Event->delete($id)) {
      $this->Session->setFlash('Termin wurde gelöscht.', 'default', array('class' => 'info'));
      $this->redirect(array('action' => 'index'));
    }
  }

  private function fetchDropDownLists( $mode_id = null ) {
    // fetch groups where the user is allowed to add events
    $user = $this->getUser();
    $privileg_ids = [];
    foreach ($user['Privileg'] as $privileg) {
      if ($privileg['id'] > 100) {
        $privileg_ids[] = $privileg['id'];
      }
    }

// FIXME include only groups, where kind.show_in_availability_list is true
    // add groups to the instant variable
    $this->Event->Group->contain('Kind');
    $tmp = $this->Event->Group->find('all', array(
      'conditions' => array('Group.privileg_id' => $privileg_ids),
      'order'      => array('Group.kind_id' => 'asc')
    ));
    $groups = [];
    foreach ($tmp as $group) {
      $groups[$group['Kind']['name']][$group['Group']['id']] = $group['Group']['name'];
    }
    $this->set(compact('groups'));

    // add all customers to the instant variable
    $customers = $this->Event->Customer->find('list',array('fields'=>array('id','name')));
    $this->set(compact('customers'));

    // add all locations to the instant variable
    $locations = $this->Event->Location->find('list',array('fields'=>array('id','name')));
    $this->set(compact('locations'));

    // add all event modes to the instant variable
    if( $mode_id ) {
      // if 'mode_id' is provided only fetch modes with similar availability-setting
      $this->Event->Mode->id = $mode_id;
      $mode = $this->Event->Mode->read();
      $modes = $this->Event->Mode->find('list', array(
        'fields' => array('id', 'name'),
        'conditions' => array('Mode.set_availability' => $mode['Mode']['set_availability'])
      ));
    }
    else
      $modes = $this->Event->Mode->find('list',array('fields'=>array('id','name')));
    $this->set(compact('modes'));

    // add all resources to the instant variable
    $resources = $this->Event->Resource->find('list',array('fields'=>array('id','name')));
    $this->set(compact('resources'));
    return true;
  }
}

