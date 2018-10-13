<?php
// app/Controller/GalleriesController.php
class GalleriesController extends AppController
{

  public function beforeFilter() {
    // everybody is allowed to call 'index' and 'view'
    $this->Auth->allow('index', 'view');
  }

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    // users with privileg 'Gallery upload' are allowed to call 'add' and 'edit'
    if (($this->action === 'add' || $this->action === 'upload')
         && array_has_key_val($privilegs['Privileg'], 'name', 'Gallery upload'))
      return true;

    // users with privileg 'Gallery modify' are allowed to call 'edit'
    if ($this->action === 'edit' && array_has_key_val($privilegs['Privileg'], 'name', 'Gallery modify'))
      return true;

    // users with privileg 'Gallery delete' are allowed to call 'delete'
    if ($this->action === 'delete' && array_has_key_val($privilegs['Privileg'], 'name', 'Gallery delete'))
      return true;
  }

  public $helpers = array('Session', 'Html', 'Form');

	public $components = array('Session', 'RequestHandler');

  public function index() {
    $galleries_per_page = Configure::read('paginate.gallery_count');

    $count = $this->Gallery->find('count');
    $pages = ceil($count / $galleries_per_page);
    $this->set(compact('pages'));

    $page = isset($this->params['url']['page']) ? $this->params['url']['page'] : 0;
    $offset = $galleries_per_page * $page;
    if ($offset >= $count) $offset = $count - $galleries_per_page;
    $this->set(compact('page'));

    $this->Gallery->contain();
    $this->set('galleries', $this->Gallery->find('all', array(
      'order' => array('Gallery.date_stamp' => 'desc'),
      'limit'  => $galleries_per_page,
      'offset' => $offset
    )));
  }

  public function view($id = null) {
    $this->Gallery->id = $id;
    $this->Gallery->contain('Photo');
    $gallery = $this->Gallery->read();
    $this->set(compact('gallery'));

    // get names of users who uploaded at least one photo
    $user_names = $this->getUserNames('Photos');
    // get name of the user who created the gallery
    $user_names[] = $this->getUserName($gallery['Gallery']['user_id']);
    $this->set(compact('user_names'));
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->request->data['Gallery']['user_id'] = $this->Auth->user('id');
      $this->Gallery->create();
      if ($this->Gallery->save($this->request->data)) {
        $this->Session->setFlash('Galerie wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Galerie konnte nicht gespeichert werden.');
      }
    }
  }

  public function edit($id = null) {
    $this->Gallery->id = $id;
    $gallery = $this->Gallery->read();
    if ($this->request->is('get')) {
      $this->request->data = $gallery;
    } else {
      $id = $this->request->data['Gallery']['id'];
      if ($this->Gallery->saveAssociated($this->request->data)) {
        $this->Session->setFlash('Galerie wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'view', $id));
      } else {
        $this->Session->setFlash('Galerie konnte nicht aktualisiert werden.');
      }
    }
    $photos = $this->Gallery->Photo->find('list',array(
      'conditions' => array('gallery_id' => $id),
      'fields' => array('id')
    ));
    $this->set(compact('photos'));

    // get names of users who created at least one event
    $this->set('user_names', $this->getUserNames($this->name));
  }

  public function upload($id = null) {
    $this->Gallery->id = $id;
    $this->set('gallery', $this->Gallery->read());
    if ($this->request->is('post')) {
/*
      // Plupload
      $file = $this->params['form']['file'];
      $file['name'] = $this->request->data['name'];
      $data = array('Photo' => array(
        'file' => $file,
        'gallery_id' => $id,
        'user_id' => $this->Auth->user('id')
      ));
*/

      // FileAPI
      $data = array('Photo' => array(
        'file' => $this->params['form']['images'],
        'gallery_id' => $id,
        'user_id' => $this->Auth->user('id')
      ));

      if ($this->Gallery->Photo->save($data)) {
        $response = 'ok';
      } else {
        $response = 'Could not save photo.';
      }

      $this->response->type('txt');
      $this->response->body($response);
      return $this->response;
    }
    // get names of users who created at least one gallery
    $this->set('user_names', $this->getUserNames($this->name));
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Gallery->delete($id)) {
      $this->Session->setFlash('Galerie wurde gelöscht.', 'default', array('class' => 'info'));
    } else {
      $this->Session->setFlash('Gallerie konnte nicht gelöscht werden.');
    }
    $this->redirect(array('action' => 'index'));
  }

}

