<?php
// app/Controller/PublishersController.php
class PublishersController extends AppController
{

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    // TODO club members are allowed to call 'index' and 'view'
//    if (($this->action === 'add' || $this->action === 'edit' || $this->action === 'delete') &&
//        array_has_key_val($privilegs['Privileg'], 'name', 'Music database'))
    if ($this->action === 'index')
      return true;

    // users with privileg 'Music database' are allowed to call 'add', 'edit' and 'delete'
    if (($this->action === 'add' || $this->action === 'edit' || $this->action === 'delete') &&
        array_has_key_val($privilegs['Privileg'], 'name', 'Music database'))
      return true;
  }


  public $helpers = array('Html', 'Form');


  public function index() {
    $this->set('publishers', $this->Publisher->find('all'));
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->Publisher->create();
      if ($this->Publisher->save($this->request->data)) {
        $this->Session->setFlash('Verlag wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Verlag konnte nicht gespeichert werden.');
      }
    }
  }

  public function edit($id = null) {
    if ($this->request->is('get')) {
      $this->Publisher->id = $id;
      $this->request->data = $this->Publisher->read();
    } else {
      $this->Publisher->create();
      if ($this->Publisher->save($this->request->data)) {
        $this->Session->setFlash('Verlag wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Verlag konnte nicht gespeichert werden.');
      }
    }
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Publisher->delete($id)) {
      $this->Session->setFlash('Verlag mit ID: ' . $id . ' wurde gelöscht.', 'default', array('class' => 'info'));
      $this->redirect(array('action' => 'index'));
    }
  }

}

