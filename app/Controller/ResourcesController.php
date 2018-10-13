<?php
// app/Controller/FilesController.php
class ResourcesController extends AppController {

  public function isAuthorized($user) {
    $profile = $this->getProfile();
    // TODO only 'real' members are allowed to call 'index' and 'view'
    if (($this->action === 'index' || $this->action === 'view') &&
        !empty($profile) && !empty($profile['Membership']['id'])) {
      return true;
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
    $this->set('resources', $this->Resource->find('all'));
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

}

