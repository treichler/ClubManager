<?php
// app/Controller/FilesController.php
class RepositoriesController extends AppController {

  public function isAuthorized($user) {
    // club-members are allowed to access 'index'
    if ($this->action === 'index') {
      $profile = $this->getProfile();
      if (!empty($profile) && !empty($profile['Membership']['id'])) {
        $membership = $this->Repository->Resource->Membership->findById($profile['Membership']['id']);
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
    $this->set('repositories', $this->Repository->find('all'));
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->Repository->create();
      if ($this->Repository->save($this->request->data)) {
        $this->Session->setFlash('Kategorie wurde gespeichert', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Kategorie konnte nicht gespeichert werden.');
      }
    }
  }

  public function edit($id = null) {
    $this->Repository->id = $id;
    if ($this->request->is('get')) {
      $this->request->data = $this->Repository->read();
    } else {
      if ($this->Repository->save($this->request->data)) {
        $this->Session->setFlash('Kategorie wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Kategorie konnte nicht aktualisiert werden.');
      }
    }
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Repository->delete($id)) {
      $this->Session->setFlash('Kategorie wurde gelöscht.', 'default', array('class' => 'info'));
      $this->redirect(array('action' => 'index'));
    }
  }

}

