<?php
// app/Controller/GroupsController.php
class GroupsController extends AppController {

  public function beforeFilter() {
    // everybody is allowed to call 'index', 'view' and 'attachment'
    $this->Auth->allow('index', 'view', 'attachment');
  }

  public function isAuthorized($user) {

    // admins are allowed to access 'add' and 'delete'
    if (($this->action === 'add' || $this->action === 'delete') &&
        array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator')) {
      return true;
    }

    // admins are allowed to call edit and organize and so are group-admins for their groups
    if ($this->action === 'edit' || $this->action === 'details') {
      if (array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator')) {
        return true;
      }
      if ($this->request->params['pass'][0]) {
        $group = $this->Group->findById($this->request->params['pass'][0]);
        if (array_has_key_val($this->getUser()['Privileg'], 'name', $group['Group']['name'])) {
          return true;
        }
      }
    }

    // admins and group-admins are allowed to call organize
    if ($this->action === 'organize') {
      if (array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator')) {
        return true;
      }
      foreach ($this->getUser()['Privileg'] as $privileg) {
        // group-privileg ids start with 101
        if ($privileg['id'] >= 101) {
          return true;
        }
      }
    }
  }

  public $helpers = array('Html', 'Form');

  public function index() {
    // 'groups' are loaded as 'public_groups' before render in AppController.php
  }


  public function view($id = null) {
    $group = $this->Group->find('first', array(
      'contain' => array('Kind', 'Membership.profile_id'),
      'conditions' => array('Group.id' => $id)
    ));
    $this->set(compact('group'));

    // redirect if group's kind is not public
    if (!$group['Kind']['is_public']) {
      $this->redirect(array('action' => 'index'));
    }

    // only if members should be shown
    if ($group['Group']['show_members']) {
      // extract profile ids
      $profile_ids = [];
      foreach ($group['Membership'] as $membership) {
        $profile_ids[] = $membership['profile_id'];
      }
      $memberships = $this->Group->Membership->find('all', array(
        'contain'    => array('Profile', 'Group', 'State'),
        'conditions' => array('Membership.profile_id' => $profile_ids),
        'order'      => array('Profile.last_name' => 'asc')
      ));
      $this->set(compact('memberships'));
      $kinds = $this->Group->Kind->find('list', array(
        'conditions' => array('Kind.is_official' => true),
      ));
      $this->set(compact('kinds'));
    }
  }

  public function attachment($id = null)
  {
    $group = $this->Group->findById($id);

    if (isset($group['Storage']['file'])) {
      // needs cakephp v >= 2.3
      $this->response->type($group['Storage']['extension']);
      $this->response->file($group['Storage']['file']['path'] . $group['Storage']['uuid'],
        array(
          'download' => true,
          'name' => $group['Storage']['name'] . '.' . $group['Storage']['extension']
      ));
    } else {
      $this->response->file('img/default_group_img.png', array('download' => true));
    }
    return $this->response;
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->Group->create;
      if ($this->Group->save($this->request->data)) {
        $this->Session->setFlash('Gruppe wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'organize'));
      } else {
        $this->Session->setFlash('Gruppe konnte nicht gespeichert werden.');
      }
    }
    // add drop down lists to the instant variable
    $this->fetchDropDownLists();
  }

  public function organize() {
    if (array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator')) {
      $groups = $this->Group->find('all', array(
        'order' => array('Group.kind_id' => 'asc')
      ));
    } else {
      $names = [];
      foreach ($this->getUser()['Privileg'] as $privileg) {
        if ($privileg['id'] >= 101) {
          $names[] = $privileg['name'];
        }
      }
      $groups = $this->Group->find('all', array(
        'conditions' => array('Group.name' => $names),
        'order'      => array('Group.kind_id' => 'asc')
      ));
    }
    $this->set(compact('groups'));
  }

  public function details($id = null) {
    $group = $this->Group->find('first', array(
      'contain' => array('Kind', 'Membership.profile_id'),
      'conditions' => array('Group.id' => $id)
    ));
    $this->set(compact('group'));

    // extract profile ids
    $profile_ids = [];
    foreach ($group['Membership'] as $membership) {
      $profile_ids[] = $membership['profile_id'];
    }
    $memberships = $this->Group->Membership->find('all', array(
      'contain'    => array('Profile', 'Group', 'State'),
      'conditions' => array('Membership.profile_id' => $profile_ids),
      'order'      => array('State.id' => 'asc', 'Profile.last_name' => 'asc')
    ));
    $this->set(compact('memberships'));

    $kinds = $this->Group->Kind->find('list', array(
      'conditions' => array('Kind.is_official' => true),
    ));
    $this->set(compact('kinds'));
  }

  public function edit($id = null) {
    $this->Group->contain('Kind', 'Membership');
    $this->Group->id = $id;
    $group = $this->Group->read();

    // add drop down lists to the instant variable
    $this->fetchDropDownLists();

    if ($this->request->is('get')) {
      $this->request->data = $group;
    } else {
      if ($this->Group->save($this->request->data)) {
        $this->Session->setFlash('Gruppe wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'details', $this->request->data['Group']['id']));
      } else {
        $this->Session->setFlash('Gruppe konnte nicht aktualisiert werden.');
      }
    }
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Group->delete($id)) {
      $this->Session->setFlash('Gruppe wurde gelöscht.', 'default', array('class' => 'info'));
      $this->redirect(array('action' => 'organize'));
    }
  }

  private function fetchDropDownLists() {
    // add all group kinds to the instant variable
    $kinds = $this->Group->Kind->find('list',array('fields'=>array('id','name')));
    $this->set(compact('kinds'));

    // add all memberships to the instant variable
    $tmp = $this->Group->Membership->find('all',array(
      'contains' => array('Profile'),
      'order'    => 'Profile.first_name'
    ));
    $memberships = array();
    foreach ($tmp as $membership) {
      $memberships[$membership['Membership']['id']] = $membership['Profile']['first_name'] . ' ' . $membership['Profile']['last_name'];
    }
    $this->set(compact('memberships'));
  }

}

