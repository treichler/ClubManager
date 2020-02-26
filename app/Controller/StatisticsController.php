<?php
// app/Controller/StatisticsController.php
class StatisticsController extends AppController {

  public function beforeFilter() {
    $this->loadModel('Event');

    // everybody is allowed to call 'index'
//    $this->Auth->allow('index');
  }

  public function isAuthorized($user) {
    // club-members are allowed to access 'index', 'events', 'availabilities', 'musicsheets'
    if ($this->action === 'index' || $this->action === 'events' || $this->action === 'akm' ||
        $this->action === 'availabilities' || $this->action === 'musicsheets') {
      $this->loadModel('Membership');
      $profile = $this->Membership->Profile->findById($this->getUser()['Profile']['id']);
      if (!empty($profile) && !empty($profile['Membership']['id'])) {
        $membership = $this->Membership->findById($profile['Membership']['id']);
        if ($membership['State']['is_member'])
          return true;
      }
    }

/*
    // only admins are allowed to access 'add', 'organize' and 'delete'
    if (($this->action === 'add' || $this->action === 'organize' || $this->action === 'delete') &&
        array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator')) {
      return true;
    }

    // admins and group-admins are allowed to access 'edit' and 'details'
    if (!($this->action === 'organize'))
      $id = $this->request->params['pass'][0];
    if (($this->action === 'edit' || $this->action === 'details') &&
        (array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator') ||
        (!($this->action === 'organize') || array_has_key_val($this->getUser()['Privileg'], 'id',
                      $this->Group->findById($id)['Privileg']['id'])))) {
      return true;
    }
*/
  }

  public $components = array('RequestHandler');

  public $helpers = array('Html', 'Form');


  public function index() {
    $years = range(Configure::read('CMSystem.start_year'), date('Y'));
    $this->set(compact('years'));
  }


  public function availabilities($year = null) {
    $this->checkYear($year);
    $this->set(compact('year'));

    // TODO show if availabilities for the current year are finished

    $data = array();
    $names = array(
      'profiles'    => array(),
      'groups'      => array(),
      'modes'       => array()
    );

    // fetch events and related data
    $events = $this->Event->find('all', array(
      'conditions' => array(
        'Event.start >=' => $year . '-1-1',
        'Event.start <=' => $year . '-12-31'
      ),
      'contain' => array(
        'Availability' => array(
          'Membership' => array(
            'Profile' => array(
              'fields' => array('first_name', 'last_name')
            ),
          'Group' => array('id'),
          )
        ),
        'Group' => array(
          'fields' => array('name', 'id')
        ),
        'Mode' => array(
          'fields' => array('name')
        )
      )
    ));

    // loop through events and availabilities
    foreach ($events as $event) {
      foreach ($event['Availability'] as $availability) {
        // add membership's profile to statistics
        if (!isset($names['memberships'][ $availability['membership_id'] ])) {
          $names['profiles'][ $availability['membership_id'] ] = $availability['Membership']['Profile'];
        }

        // do statistics relevant stuff only if availability is set
        if ($availability['was_available']) {
          // add event's mode to statistics
          if (!isset($names['modes'][ $event['Event']['mode_id'] ])) {
            $names['modes'][ $event['Event']['mode_id'] ] = $event['Mode']['name'];
          }
 
          // collect membership's groups (ids only)
          $membership_group_ids = array();
          foreach( $availability['Membership']['Group'] as $group ) {
            $membership_group_ids[] = $group['id'];
          }
          unset($group);

          // iterate event's related groups
          foreach( $event['Group'] as $group ) {
            // check if event's related group is also related to the membership
            if( in_array($group['id'], $membership_group_ids) ) {
              // add the group name to the statistics
              if (!isset($names['groups'][ $group['id'] ])) {
                $names['groups'][ $group['id'] ] = $group['name'];
              }
              // add and increase participation counter
              if (!isset($data[ $availability['membership_id'] ][ $group['id'] ][ $event['Event']['mode_id'] ])) {
                $data[ $availability['membership_id'] ][ $group['id'] ][ $event['Event']['mode_id'] ] = 0;
              }
              $data[ $availability['membership_id'] ][ $group['id'] ][ $event['Event']['mode_id'] ] += 1;
            }
          }
          unset($membership_group_ids);
        }
      }
    }

    $this->set('matrix', json_encode(array('names' => $names, 'data' => $data)));
  }


  public function akm($year = null) {
    $this->checkYear($year);
    $this->set(compact('year'));

    // TODO check if availabilities and musicsheets/playlists for the current year are finished
    $this->Event->contain('Group.name');
    $check_events = $this->Event->find('list', array(
      'conditions' => array(
        'or' => array(
          'Event.availabilities_checked' => 0,
          'Event.tracks_checked' => 0,
        ),
        'Event.start >=' => $year . '-1-1', // date('Y-m-d', strtotime('+8 days')),
        'Event.start <=' => $year . '-12-31'
      )
    ));
    $this->set(compact('check_events'));
    if (count($check_events)) {
      $this->Session->setFlash('Es wurden noch nicht alle Anwesenheitslisten bzw. Listen der gespielten Musikstücke bestätigt.');
    }

    if ($year >= date('Y')) {
      $this->Session->setFlash('Datenexport ist erst nach Ablauf des Jahres möglich.');
    }

    $modes = $this->Event->Mode->find('list', array(
      'conditions' => array('Mode.set_track' => true)
    ));
//    $this->set(compact('modes'));
    $mode_ids = [];
    foreach ($modes as $key => $val) {
      $mode_ids[] = $key;
    }

    $this->Event->contain('Track', 'Group', 'Customer');
    $events = $this->Event->find('all', array(
      'conditions' => array(
        'Event.mode_id'  => $mode_ids,
        'Event.start >=' => $year . '-1-1', // date('Y-m-d', strtotime('+8 days')),
        'Event.start <=' => $year . '-12-31'
      ),
      'order' => array('Event.start' => 'asc')
    ));
    $this->set(compact('events'));

    $this->loadModel('Musicsheet');
    $this->Musicsheet->contain('Publisher', 'Composer', 'Arranger');
    $tmp = $this->Musicsheet->find('all');
    $musicsheet = array();
    foreach ($tmp as $m) {
      $musicsheet[$m['Musicsheet']['id']] = $m;
    }
    $this->set(compact('musicsheet'));

    if ($this->RequestHandler->isXml()) {
      $this->response->download('AKM_' . $year . '.xml');
    }
  }


  public function musicsheets($year = null) {
    $this->checkYear($year);
    $this->set(compact('year'));

    // TODO show if musicsheets/playlists for the current year are finished

    $this->Event->contain('Track');
    $events = $this->Event->find('all', array(
      'conditions' => array(
        'Event.start >=' => $year . '-1-1',
        'Event.start <=' => $year . '-12-31'
      ),
//      'order' => array('Event.start' => 'asc')
    ));
    $tracks = array();
    foreach ($events as $event) {
      foreach ($event['Track'] as $track) {
        if (!isset($tracks[$track['musicsheet_id']]))
          $tracks[$track['musicsheet_id']] = 0;
        $tracks[$track['musicsheet_id']] += 1;
      }
    }
    $this->set(compact('tracks'));

    $Musicsheet_ids = array();
    foreach ($tracks as $key => $val) {
      $musicsheet_ids[] = $key;
    }

    $this->loadModel('Musicsheet');
    $this->Musicsheet->contain('Publisher', 'Composer', 'Arranger');
    if( !empty($musicsheet_ids) ) {
      $musicsheets = $this->Musicsheet->find('all', array(
        'conditions' => array('Musicsheet.id' => $musicsheet_ids),
        'order' => array('Musicsheet.title' => 'ASC')
      ));
    } else {
      $musicsheets = array();
    }
    $this->set(compact('musicsheets'));
  }

  public function events($year = null) {
    $this->checkYear($year);
    $this->set(compact('year'));

    $conditions = array(
      'Mode.set_availability' => true
    );
    $modes = $this->Event->Mode->find('list', array(
      'conditions' => $conditions
    ));
    $groups = $this->Event->Group->find('list');

    $statistic = array('titles' => array(), 'rows' => array());
    $empty_row = [];
    foreach ($modes as $key => $val) {
      $statistic['titles'][$key] = $val;
      $empty_row[$key] = array('events' => 0, 'availabilities' => 0);
    }
    foreach ($groups as $key => $val) {
      $statistic['rows'][$key] = array('name' => $val, 'data' => $empty_row);
    }

    $conditions[] = array(
      'Event.start >=' => $year . '-1-1', // date('Y-m-d', strtotime('+8 days')),
      'Event.start <=' => $year . '-12-31'
    );
    $this->Event->contain('Group', 'Mode');
    $events = $this->Event->find('all', array(
      'conditions' => $conditions,
      'order' => array('Event.start' => 'asc')
    ));
    $this->set(compact('events'));
    foreach( $events as $event ) {
      foreach( $event['Group'] as $group ) {
        $statistic['rows'][$group['id']]['data'][$event['Event']['mode_id']]['events'] += 1;
      }
    }
    $this->set(compact('statistic'));
  }

  private function checkYear($year) {
    if (!is_numeric($year) || $year < Configure::read('CMSystem.start_year') || $year > date('Y')) {
      $this->redirect(array('action' => 'index'));
    }
    return;
  }

}

