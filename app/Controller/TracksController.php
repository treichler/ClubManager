<?php
// app/Controller/TracksController.php
class TracksController extends AppController {

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    // club-members are allowed to access 'index'
    if ($this->action === 'index') {
      $profile = $this->getProfile();
      if (!empty($profile) && !empty($profile['Membership']['id'])) {
        return true;
      }
    }

    // users with privileg 'Track' are allowed to call 'add' and 'delete'
    if (($this->action === 'add' || $this->action === 'delete') &&
        array_has_key_val($privilegs['Privileg'], 'name', 'Track'))
      return true;

    // Auth fails
    if($this->request->is('ajax')) {
      $this->response->type('json');
      $this->response->statusCode(401);
      $this->response->body(json_encode(array('state' => False, 'message' => 'Unauthorized')));
      $this->response->send();
      $this->_stop();
    }
  }


  public $components = array('RequestHandler');
  public $helpers = array('Html', 'Form', 'Js' => array('Jquery'));

  public function index () {
    $event_id = null;
    if(isset($this->params['url']['event_id']))
      $event_id = $this->params['url']['event_id'];

    // add event
    $this->Track->Event->contain();
    $this->Track->Event->id = $event_id;
    $this->set('event', $this->Track->Event->read());

    // add tracks wich belong to the event
    $this->Track->contain('Musicsheet.title');
    $tracks = $this->Track->find('all', array(
      'conditions' => array('Track.event_id' => $event_id)
    ));
    $this->set(compact('tracks'));

    // add all books to the instant variable
    $this->Track->Musicsheet->Sheet->Book->contain();
    $books = $this->Track->Musicsheet->Sheet->Book->find('list',array('fields'=>array('id','title')));
    $this->set(compact('books'));

    // add all sheets to the instant variable
    $this->Track->Musicsheet->Sheet->contain('Book.title', 'Musicsheet.title');
    $sheets = $this->Track->Musicsheet->Sheet->find('all', array(
      'order' => array('Sheet.page' => 'asc')
    ));
    $this->set(compact('sheets'));

/*
    // add all musicsheets to the instant variable
    $this->Track->Musicsheet->contain();
    $musicsheets = $this->Track->Musicsheet->find('list',array('fields'=>array('id','title')));
    $this->set(compact('musicsheets'));
*/
  }

  public function add() {
    $response = '';
    if ($this->request->is('post') && $this->request->is('ajax')) {
//      $response = print_r($this->request->data);
//      $response = 'true';

      $this->Track->Event->contain();
      $event = $this->Track->Event->findById($this->request->data['Track']['event_id']);
      if ($event['Event']['tracks_checked']) {
        $response = json_encode(array(
          'state' => False,
          'message' => 'Hinzufügen nicht möglich, da die Liste der gespielten Musikstück bereits bestätigt wurde.'
        ));
      } else {
        // check if track already exists
        $this->Track->contain();
        $track = $this->Track->find('first', array(
          'conditions' => array(
            'Track.event_id' => $this->data['Track']['event_id'],
            'Track.musicsheet_id' => $this->data['Track']['musicsheet_id']
          )
        ));
        if( isset($track['Track']['id']) ) {
          // track already exists --> only update 'modified' timestamp
          $this->Track->id = $track['Track']['id'];
          $now = new DateTime();
          $track_is_saved = $this->Track->saveField( 'modified', $now->format('Y-m-d H:i:s') );
        } else {
          // create new track
          $this->Track->create();
          $track_is_saved = $this->Track->save($this->request->data);
        }
        // check track's saving status
        if( $track_is_saved ) {
          $this->Track->contain('Musicsheet.title');
          $response = json_encode(array('state' => True, 'data' => $this->Track->read()));
        } else {
          $response = json_encode(array('state' => False, 'message' => 'Musikstück konnte nicht gespeichert werden.'));
        }
      }
    } else {
      $response = json_encode(array('state' => False, 'message' => 'Nothing to do.'));
    }
    $this->response->type('json');
    $this->response->body($response);
    return $this->response;
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    $response = '';

    $this->Track->contain('Event');
    $track = $this->Track->findById($id);
    if ($track['Event']['tracks_checked']) {
      $response = json_encode(array(
        'state' => False,
        'message' => 'Löschen nicht möglich, da die Liste der gespielten Musikstück bereits bestätigt wurde.'
      ));
    } else {
      if ($this->request->is('ajax')) {
        if ($this->Track->delete($id)) {
          $response = json_encode(array('state' => True));
        } else {
          $response = json_encode(array('state' => False, 'message' => 'Seite konnte nicht gelöscht werden'));
        }
      }
    }
    $this->response->type('json');
    $this->response->body($response);
    return $this->response;
  }

}

