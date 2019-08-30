<?php
// app/Controller/CustomersController.php
class CustomersController extends AppController
{

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    if ($this->action === 'index' &&
        (array_has_key_val($privilegs['Privileg'], 'name', 'Customer create') ||
         array_has_key_val($privilegs['Privileg'], 'name', 'Customer modify') ||
         array_has_key_val($privilegs['Privileg'], 'name', 'Customer delete')) ) {
      return true;
    }

    // users with privileg 'Customer create' are allowed to call 'add'
    if ($this->action === 'add' && array_has_key_val($privilegs['Privileg'], 'name', 'Customer create'))
      return true;

    // users with privileg 'Customer modify' are allowed to call 'edit'
    if ($this->action === 'edit' && array_has_key_val($privilegs['Privileg'], 'name', 'Customer modify'))
      return true;

    // users with privileg 'Customer delete' are allowed to call 'edit'
    if ($this->action === 'delete' && array_has_key_val($privilegs['Privileg'], 'name', 'Customer delete'))
      return true;
  }


  public function index() {
//    $this->Customer->contain();
    $customers = $this->Customer->find('all');
    $this->set(compact('customers'));
  }


  public function add() {
    if ($this->request->is('post')) {
      $this->Customer->create();
      if ($this->Customer->save($this->request->data)) {
        $this->Session->setFlash('Veranstalter wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Veranstalter konnte nicht gespeichert werden.');
      }
    }
  }


  public function edit($id = null) {
    if ($this->request->is('get')) {
      $this->Customer->id = $id;
      $this->request->data = $this->Customer->read();
    } else {
      $this->Customer->create();
      if ($this->Customer->save($this->request->data)) {
        $this->Session->setFlash('Veranstalter wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Veranstalter konnte nicht gespeichert werden.');
      }
    }
  }


  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Customer->delete($id)) {
      $this->Session->setFlash('Veranstalter wurde gelöscht.', 'default', array('class' => 'info'));
    } else {
      $this->Session->setFlash('Veranstalter konnte nicht gelöscht. werden');
    }
    $this->redirect(array('action' => 'index'));
  }

}

