<?php
// app/Controller/UploadsController.php
class UploadsController extends AppController {

  public function beforeFilter() {
    // everybody is allowed to call 'index', 'view' and 'attachment'
//    $this->Auth->allow('index', 'view', 'attachment');
  }

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    // club-members are allowed to call 'index', 'view', and 'attachment'
    if ($this->action === 'index' || $this->action === 'view' || $this->action === 'attachment') {
      $profile = $this->Upload->User->Profile->findById($this->getUser()['Profile']['id']);
      if (!empty($profile) && !empty($profile['Membership']['id'])) {
        $membership = $this->Upload->User->Profile->Membership->findById($profile['Membership']['id']);
        if ($membership['State']['is_member'])
          return true;
      }
    }

    // users with privileg 'File download' are allowed to call 'index', 'view', and 'attachment'
/*
    if (($this->action === 'index' || $this->action === 'view' || $this->action === 'attachment') &&
        array_has_key_val($privilegs['Privileg'], 'name', 'File download'))
      return true;
*/

    // users with privileg 'File create' are allowed to call 'add'
    if ($this->action === 'add' && array_has_key_val($privilegs['Privileg'], 'name', 'File upload'))
      return true;

    // the creator and users with privileg 'File modify' are allowed to call 'edit'
    if ($this->action === 'edit') {
      $upload_id = $this->request->params['pass'][0];
      if ($this->Upload->isOwnedBy($upload_id, $user['id']) ||
          array_has_key_val($privilegs['Privileg'], 'name', 'File modify')) {
        return true;
      }
    }

    // the creator and users with privileg 'File delete' are allowed to call 'delete'
    if ($this->action === 'delete') {
      $fileId = $this->request->params['pass'][0];
      if ($this->Upload->isOwnedBy($fileId, $user['id']) ||
          array_has_key_val($privilegs['Privileg'], 'name', 'File delete')) {
        return true;
      }
    }
  }


  public $helpers = array('Html', 'Form');

  public function index() {
    $this->Upload->contain('Type', 'Storage');
    $uploads = $this->Upload->find('all', array(
      'order' => array('Upload.type_id' => 'asc', 'Upload.date_stamp' => 'desc')
    ));
    $this->set(compact('uploads'));

    // get names of users who uploaded at least one file
    $this->set('user_names', $this->getUserNames($this->name));

//    $this->response->type('txt');
//    $this->response->body('Uploads');
//    return $this->response;
  }

  public function attachment($id = null) {
    $this->Upload->id = $id;
    $this->Upload->contain('Storage');
    $upload = $this->Upload->read();

    // needs cakephp v >= 2.3
    $this->response->type($upload['Storage']['extension']);
//    $this->response->header(array('Content-Type' => $photo['MarkedStorage']['type']));
    $this->response->file($upload['Storage']['file']['path'] . $upload['Storage']['uuid'],
      array(
        'download' => true,
        'name' => $upload['Storage']['name'] . '.' . $upload['Storage']['extension']
    ));
    return $this->response;
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->request->data['Upload']['user_id'] = $this->Auth->user('id');
      $this->Upload->create();
      if ($this->Upload->save($this->request->data)) {
        $this->Session->setFlash('Datei wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Datei konnte nicht gespeichert werden.');
      }
    }
    $this->fetchDropDownList();
  }

  public function edit($id = null) {
    $this->Upload->id = $id;
    $this->Upload->contain();
    $upload = $this->Upload->read();
    if ($this->request->is('get')) {
      $this->request->data = $upload;
    } else {
      // keep existing storage
      $this->request->data['Upload']['storage_id'] = $upload['Upload']['storage_id'];
      if ($this->Upload->save($this->request->data)) {
        $this->Session->setFlash('Datei wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
//        $this->redirect(array('action' => 'view', $blog['Blog']['id']));
      } else {
        $this->Session->setFlash('Datei konnte nicht aktualisiert werden.');
      }
    }
    $this->fetchDropDownList();
  }

  public function delete ($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Upload->delete($id)) {
      $this->Session->setFlash('Datei wurde gelöscht.', 'default', array('class' => 'info'));
    } else {
      $this->Session->setFlash('Datei konnte nicht gelöscht werden.');
    }
    $this->redirect(array('action' => 'index'));
  }


  private function fetchDropDownList() {
    // add all types to the instant variable
    $types = $this->Upload->Type->find('list',array('fields'=>array('id','name')));
    $this->set(compact('types'));
    return true;
  }

}

