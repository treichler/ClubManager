<?php
// app/Controller/MembershipsController.php
class MembershipsController extends AppController {

  public function isAuthorized($user) {
    // only admins are allowed to access 'add', 'edit' and 'delete'
    if (($this->action === 'add' || $this->action === 'edit' || $this->action === 'delete') &&
        array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator')) {
      return true;
    }

    // club-members are allowed to access 'birthdays', 'contacts' and 'index'
    if ($this->action === 'birthdays' || $this->action === 'contacts' || $this->action === 'index') {
      $profile = $this->Membership->Profile->findById($this->getUser()['Profile']['id']);
      if (!empty($profile) && !empty($profile['Membership']['id'])) {
        $membership = $this->Membership->findById($profile['Membership']['id']);
        if ($membership['State']['is_member'])
          return true;
      }
    }
  }

  public $components = array('RequestHandler');

  public $helpers = array('Html', 'Form');

  public function birthdays() {
    $this->Membership->contain('State.is_member');
/*
    $states = $this->Membership->State->find('all', array(
      'conditions' => array('State.is_member' => true)
    ));
    $state_ids = [];
    foreach ($states as $state) {
      $state_ids[] = $state['State']['id'];
    }
    $this->Membership->contain('Profile', 'Group', 'State');
    $memberships = $this->Membership->find('all', array(
      'conditions' => array('Membership.state_id' => $state_ids),
      'order' => array('MONTH(Profile.birthday)' => 'asc', 'DAY(Profile.birthday)' => 'asc')
    ));
*/
    $this->Membership->contain('Profile', 'State.is_member');
    $memberships = $this->Membership->find('all', array(
      'conditions' => array('State.is_member' => true),
      'order' => array('MONTH(Profile.birthday)' => 'asc', 'DAY(Profile.birthday)' => 'asc')
    ));
    $this->set(compact('memberships'));
  }

  public function contacts () {
    $this->Membership->contain('State.is_member');
    $memberships = $this->Membership->find('all', array(
      'conditions' => array('State.is_member' => true)
    ));
    $profile_ids = [];
    foreach ($memberships as $membership) {
      $profile_ids[] = $membership['Membership']['profile_id'];
    }
    $this->Membership->Profile->contain('User');
    $profiles = $this->Membership->Profile->find('all', array(
      'conditions' => array('Profile.id' => $profile_ids),
      'order'      => array('Profile.last_name' => 'asc')
    ));
    $this->set('contacts', $profiles);
  }

  public function index() {
    if( array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator') ) {
      $find_condition = array(
        'order' => array(
          'State.id' => 'asc',
          'Profile.first_name' => 'asc',
        )
      );
    } else {
      $find_condition = array(
        'conditions' => array('State.is_member' => true),
        'order' => array(
          'State.id' => 'asc',
          'Profile.first_name' => 'asc',
        )
      );
    }
    $this->Membership->contain('Profile', 'Group', 'State');
    $memberships = $this->Membership->find('all', $find_condition);
    $this->set(compact('memberships'));
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->Membership->create;
      if ($this->Membership->save($this->request->data)) {
        $this->Session->setFlash('Mitgliedschaft wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Mitgliedschaft konnte nicht gespeichert werden.');
      }
    }
    // add profiles without membership to the instant variable
    $profiles = [];
    $temp = $this->Membership->Profile->find('all');
    foreach($temp as $profile) {
      if (!$profile['Membership']['id']) {
        $profiles[$profile['Profile']['id']] = $profile['Profile']['first_name'] . ' '
          . $profile['Profile']['last_name'] . ' (' . $profile['Profile']['birthday'] . ')';
      }
    }
    $this->set(compact('profiles'));
    // add drop down lists to the instant variable
    $this->fetchDropDownLists();
  }

  public function edit($id = null) {
    $this->Membership->id = $id;
    $membership = $this->Membership->read();
    // add drop down lists to the instant variable
    $this->fetchDropDownLists();
    // get profile
    $this->Membership->Profile->id = $membership['Membership']['profile_id'];
    $this->set('profile', $this->Membership->Profile->read());
    if ($this->request->is('get')) {
      $this->request->data = $membership;
    } else {
      if ($this->Membership->save($this->request->data)) {
        $this->Session->setFlash('Mitgliedschaft wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Mitgliedschaft konnte nicht aktualisiert werden.');
      }
    }
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Membership->delete($id)) {
      $this->Session->setFlash('Mitdliedschaft wurde gelöscht.', 'default', array('class' => 'info'));
    } else {
      $this->Session->setFlash('Mitdliedschaft konnte nicht gelöscht werden.');
    }
    $this->redirect(array('action' => 'index'));
  }

  private function fetchDropDownLists() {
    // add states to the instant variable
    $states = $this->Membership->State->find('list',array('fields'=>array('id','name')));
    $this->set(compact('states'));
    // add all groups to the instant variable
    $groups = $this->Membership->Group->find('list',array('fields'=>array('id','name')));
    $this->set(compact('groups'));
  }

}

