<?php
// app/Controller/KindsController.php
class KindsController extends AppController {

  public function isAuthorized($user) {
    // only admins are allowed to access any action
    if (array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator')) {
      return true;
    }
  }

  public function index() {
    $this->Kind->contain();
    $kinds = $this->Kind->find('all');
    $this->set(compact('kinds'));
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->Kind->create;
      if ($this->Kind->save($this->request->data)) {
        $this->Session->setFlash('Art der Gruppe wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Art der Gruppe konnte nicht gespeichert werden.');
      }
    }
  }

  public function edit($id = null) {
    $this->Kind->id = $id;
    $this->Kind->contain();
    $kind = $this->Kind->read();
    if ($this->request->is('get')) {
      $this->request->data = $kind;
    } else {
      if ($this->Kind->save($this->request->data)) {
        $this->Session->setFlash('Art der Gruppe wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Art der Gruppe konnte nicht aktualisiert werden.');
      }
    }
  }

}

