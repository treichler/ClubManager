<?php
// app/Controller/FilesController.php
class ResourcesController extends AppController {

  public function isAuthorized($user) {
    // club-members are allowed to access 'index' and 'view'
    if ($this->action === 'index' || $this->action === 'view') {
      $profile = $this->getProfile();
      if (!empty($profile) && !empty($profile['Membership']['id'])) {
        $membership = $this->Resource->Membership->findById($profile['Membership']['id']);
        if ($membership['State']['is_member'])
          return true;
      }
    }

    $privilegs = $this->getUser();
    // users with privileg 'Resource create' are allowed to call 'add'
    if ($this->action === 'add' && array_has_key_val($privilegs['Privileg'], 'name', 'Resource create'))
      return true;

    // users with privileg 'Resource modify' are allowed to call 'edit'
    if ($this->action === 'edit' && array_has_key_val($privilegs['Privileg'], 'name', 'Resource modify'))
      return true;

    // users with privileg 'Resource delete' are allowed to call 'delete'
    if ($this->action === 'delete' && array_has_key_val($privilegs['Privileg'], 'name', 'Resource delete'))
      return true;
  }


  public $helpers = array('Html', 'Form');

  public function index() {
    $this->set('resources', $this->Resource->find('all', array(
      'contain' => array(
        'Category' => array(
          'fields' => array('Category.name'),
        ),
        'Membership' => array(
          'Profile' => array(
            'fields' => array('Profile.first_name', 'Profile.last_name'),
          ),
        ),
        'Repository' => array(
          'fields' => array('Repository.name'),
        ),
      ),
    )));
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->Resource->create();
      if ($this->Resource->save($this->request->data)) {
        $this->Session->setFlash('Ressource wurde gespeichert', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Ressource konnte nicht gespeichert werden.');
      }
    }
    // add drop down lists to the instant variable
    $this->fetchDropDownLists();
  }

  public function edit($id = null) {
    $this->Resource->id = $id;
    if ($this->request->is('get')) {
      $this->request->data = $this->Resource->read();
    } else {
      if ($this->Resource->save($this->request->data)) {
        $this->Session->setFlash('Ressource wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Ressource konnte nicht aktualisiert werden.');
      }
    }
    // add drop down lists to the instant variable
    $this->fetchDropDownLists();
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Resource->delete($id)) {
      $this->Session->setFlash('Ressource wurde gelöscht.', 'default', array('class' => 'info'));
      $this->redirect(array('action' => 'index'));
    }
  }

  private function fetchDropDownLists() {
    // add all categories to the instant variable
    $categories = $this->Resource->Category->find('list', array('fields'=>array('id','name')));
    $this->set(compact('categories'));

    // add all memberships to the instant variable
    $tmp = $this->Resource->Membership->find('all',array(
      'contains' => array('Profile'),
      'order'    => 'Profile.first_name'
    ));
    $memberships = array();
    foreach ($tmp as $membership) {
      $memberships[$membership['Membership']['id']] = $membership['Profile']['first_name'] . ' ' . $membership['Profile']['last_name'];
    }
    $this->set(compact('memberships'));

    // add all repositories to the instant variable
    $repositories = $this->Resource->Repository->find('list', array('fields'=>array('id','name')));
    $this->set(compact('repositories'));
  }
}

