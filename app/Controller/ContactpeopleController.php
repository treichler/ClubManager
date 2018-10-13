<?php
// app/Controller/ContactpeopleController.php
class ContactpeopleController extends AppController
{

  public function beforeFilter() {
    // everybody is allowed to call 'index'
    $this->Auth->allow('index');
  }

  var $user;

  public function isAuthorized($user) {
//    $privilegs = $this->getUser();
    $this->user = $this->getUser();

    // users with privileg 'Administrator' are allowed to call 'protocol' and 'people'
    if (($this->action === 'organize' || $this->action === 'add' ||
         $this->action === 'edit' || $this->action === 'delete') &&
        array_has_key_val($this->user['Privileg'], 'name', 'Administrator')) {
      return true;
    }
  }

//  public $components = array('RequestHandler');

//  public $helpers = array('Html', 'Form');

  public function index() {
    $contactpeople = $this->Contactperson->find('all', array(
      'contain' => array(
        'Profile' => array(
          'Membership' => array('Group' => array('Kind' => 'is_official')),
          'User' => 'email',
          'Title'
        )
      ),
      'conditions' => array( 'OR' => array(
          'Contactperson.contactlist_email' => true,
          'Contactperson.contactlist_phone' => true
      ))
    ));
    $this->set(compact('contactpeople'));
  }

  public function organize() {
    $contactpeople = $this->Contactperson->find('all', array(
      'contain' => array('Profile')
    ));
    $this->set(compact('contactpeople'));
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->Contactperson->create();
      if ($this->Contactperson->save($this->request->data)) {
        $this->Session->setFlash('Kontaktperson wurde gespeichert', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'organize'));
      } else {
        $this->Session->setFlash('Kontakperson konnte nicht gespeichert werden.');
      }
    }
    $this->fetchDropDownList();
  }

  public function edit($id = null) {
    $this->Contactperson->id = $id;
    if ($this->request->is('get')) {
      $this->request->data = $this->Contactperson->read();
    } else {
      if ($this->Contactperson->save($this->request->data)) {
        $this->Session->setFlash('Kontakperson wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'organize'));
      } else {
        $this->Session->setFlash('Kontakperson konnte nicht aktualisiert werden.');
      }
    }
    $this->fetchDropDownList();
  }

  public function delete($id = null) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Contactperson->delete($id)) {
      $this->Session->setFlash('Kontakperson wurde gelöscht.', 'default', array('class' => 'info'));
      $this->redirect(array('action' => 'organize'));
    }
  }

  private function fetchDropDownList() {
    $tmp = $this->Contactperson->Profile->find('all', array(
      'contain'    => array('Membership' => array('State')),
      'order'      => array('Profile.last_name' => 'asc'),
      'conditions' => array('Membership.id >' => 0)
//      'conditions' => array('AND' => array('Membership.State.is_member' => true, 'Membership.State.is_available' => true))
    ));
    $profiles = [];
    foreach ($tmp as $profile) {
    if ($profile['Membership']['State']['is_member'] && $profile['Membership']['State']['is_available'])
      $profiles[$profile['Profile']['id']] = $profile['Profile']['first_name'] . ' ' . $profile['Profile']['last_name'];
    }
    $this->set(compact('profiles'));
  }

}

