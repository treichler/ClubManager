<?php
// app/Controller/MusicsheetsController.php
class MusicsheetsController extends AppController
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
    $this->set('musicsheets', $this->Musicsheet->find('all'));
  }

  public function add() {
    $this->fetchDropDownLists();
    if ($this->request->is('post')) {
      $this->Musicsheet->create();
      if ($this->Musicsheet->save($this->request->data)) {
        $this->Session->setFlash('Musikstück wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Musikstück konnte nicht gespeichert werden.');
      }
    }
  }

  public function edit($id = null) {
    $this->fetchDropDownLists();
    if ($this->request->is('get')) {
      $this->Musicsheet->id = $id;
      $this->request->data = $this->Musicsheet->read();
    } else {
      $this->Musicsheet->create();
      if ($this->Musicsheet->save($this->request->data)) {
        $this->Session->setFlash('Musikstück wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Musikstück konnte nicht gespeichert werden.');
      }


    }
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Musicsheet->delete($id)) {
      $this->Session->setFlash('Musikstück mit ID: ' . $id . ' wurde gelöscht.', 'default', array('class' => 'info'));
      $this->redirect(array('action' => 'index'));
    }
  }

  private function fetchDropDownLists() {
    // add musicsheets to the instant variable
    $musicsheets = $this->Musicsheet->find('list', array('fields' => array('id', 'title')));
    $this->set(compact('musicsheets'));

    // add publishers to the instant variable
    $publishers = $this->Musicsheet->Publisher->find('list', array('fields' => array('id', 'name')));
    $this->set(compact('publishers'));

    // add arrangers to the instant variable
    $this->Musicsheet->Arranger->contain();
    $temp = $this->Musicsheet->Arranger->find('all', array(
      'conditions' => array('is_arranger' => true),
      'order' => array('Arranger.last_name' => 'asc')
    ));
    $arrangers = [];
    foreach($temp as $profile) {
//      $arrangers[$profile['Arranger']['id']] = $profile['Arranger']['first_name'] . ' '
//        . $profile['Arranger']['last_name'] . ' (' . $profile['Arranger']['birthday'] . ')';
      $arrangers[$profile['Arranger']['id']] = $profile['Arranger']['last_name'] . ', '
        . $profile['Arranger']['first_name'];
      if ($profile['Arranger']['birthday'])
        $arrangers[$profile['Arranger']['id']] .= ', ' . $profile['Arranger']['birthday'];
    }
    $this->set(compact('arrangers'));

    // add composers to the instant variable
    $this->Musicsheet->Composer->contain();
    $temp = $this->Musicsheet->Composer->find('all', array(
      'conditions' => array('is_composer' => true),
      'order' => array('Composer.last_name' => 'asc')
    ));
    $composers = [];
    foreach($temp as $profile) {
      $composers[$profile['Composer']['id']] = $profile['Composer']['last_name'] . ', '
        . $profile['Composer']['first_name'];
      if ($profile['Composer']['birthday'])
        $composers[$profile['Composer']['id']] .= ', ' . $profile['Composer']['birthday'];
    }
    $this->set(compact('composers'));
  }

}

