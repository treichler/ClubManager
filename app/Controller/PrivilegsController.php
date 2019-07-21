<?php
// app/Controller/PrivilegsController.php
class PrivilegsController extends AppController {

  public function isAuthorized($user) {
    // only users with privileg 'Administrator' and the primary admin
    // are allowed to access 'index' and 'assign'
    if (($this->action === 'index' || $this->action === 'assign') &&
        (array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator') ||
         (Configure::read('CMSystem.primary_admin') && (Configure::read('CMSystem.primary_admin') === 'admin')) )) {
      return true;
    }
  }

  public $helpers = array('Html', 'Form');

  public function index() {
// TODO Sort by Group['kind_id']. Start with privilegs without group
    $privilegs = $this->Privileg->find('all', array(
      'order' => array('Group.kind_id' => 'ASC')
    ));
    $this->set(compact('privilegs'));
    $tmp = $this->Privileg->Group->Kind->find('all');
    $kinds = [];
    foreach ($tmp as $kind) {
      $kinds[$kind['Kind']['id']] = $kind;
    }
    unset($tmp);
    $this->set(compact('kinds'));
  }

  public function assign($id = null) {
    $this->Privileg->id = $id;
    $privileg = $this->Privileg->read();
    // add all users to the instant variable
//    $users = $this->Privileg->User->find('list',array('fields'=>array('id','username')));

    $this->Privileg->User->contain('Profile');
    $tmp = $this->Privileg->User->find('all', array(
//      'conditions' => array('Profile.id >' => 0),
      'order' => array('Profile.first_name' => 'asc', 'User.username' => 'asc')
    ));
    $users = [];
    foreach ($tmp as $user) {
      $users[$user['User']['id']] = $user['User']['name'];
    }

    $this->set(compact('users'));
    if ($this->request->is('get')) {
      $this->request->data = $privileg;
    } else {
      if ($privileg) {
        $this->request->data['Privileg']['name'] = $privileg['Privileg']['name'];
        if ($this->Privileg->save($this->request->data)) {
          $this->Session->setFlash('Benutzerrechte wurden aktualisiert.', 'default', array('class' => 'success'));
          $this->redirect(array('action' => 'index'));
        } else {
          $this->Session->setFlash('Benutzerrechte konnten nicht aktualisiert werden.');
        }
      } else {
        $this->Session->setFlash('Benutzerrecht existiert nicht.');
      }
    }
  }

}

