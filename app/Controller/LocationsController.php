<?php
// app/Controller/LocationsController.php
class LocationsController extends AppController
{

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    if ($this->action === 'index' &&
        (array_has_key_val($privilegs['Privileg'], 'name', 'Location create') ||
         array_has_key_val($privilegs['Privileg'], 'name', 'Location modify') ||
         array_has_key_val($privilegs['Privileg'], 'name', 'Location delete')) ) {
      return true;
    }

    // users with privileg 'Location create' are allowed to call 'add'
    if ($this->action === 'add' && array_has_key_val($privilegs['Privileg'], 'name', 'Location create'))
      return true;

    // users with privileg 'Location modify' are allowed to call 'edit'
    if ($this->action === 'edit' && array_has_key_val($privilegs['Privileg'], 'name', 'Location modify'))
      return true;

    // users with privileg 'Location delete' are allowed to call 'edit'
    if ($this->action === 'delete' && array_has_key_val($privilegs['Privileg'], 'name', 'Location delete'))
      return true;
  }


  public function index() {
//    $this->Location->contain();
    $locations = $this->Location->find('all', array(
      'order' => array('Location.name' => 'asc')
    ));
    $this->set(compact('locations'));
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->Location->create();
      if ($this->Location->save($this->request->data)) {
        $this->Session->setFlash('Ort wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Ort konnte nicht gespeichert werden.');
      }
    }
  }

  public function edit($id = null) {
    if ($this->request->is('get')) {
      $this->Location->id = $id;
      $this->request->data = $this->Location->read();
    } else {
      $this->Location->create();
      if ($this->Location->save($this->request->data)) {
        $this->Session->setFlash('Ort wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Ort konnte nicht gespeichert werden.');
      }
    }
  }


  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Location->delete($id)) {
      $this->Session->setFlash('Ort wurde gelöscht.', 'default', array('class' => 'info'));
    } else {
      $this->Session->setFlash('Ort konnte nicht gelöscht. werden');
    }
    $this->redirect(array('action' => 'index'));
  }

}

