<?php
// app/Controller/ProfilesController.php
class ProfilesController extends AppController {

  public function beforeFilter() {
    // FIXME it's not good, that everybody can access the profile photo
    // everybody is allowed to call 'attachment'
    $this->Auth->allow('attachment');
  }

  public function isAuthorized($user) {
    // logged-in users are allowed to call 'add', 'edit' and 'view'
    if (($this->action === 'add' || $this->action === 'edit' || $this->action === 'view') &&
        isset($this->getUser()['User']['id'])) {
      return true;
    }

    // users with privileg 'Profile create' or 'Profile modify' are allowed to access 'index'
    if (($this->action === 'index') &&
        (array_has_key_val($this->getUser()['Privileg'], 'name', 'Profile create') ||
         array_has_key_val($this->getUser()['Privileg'], 'name', 'Profile modify'))) {
      return true;
    }

    // users with privileg 'Profile delete' are allowed to access 'index' and 'delete'
    if (($this->action === 'index' || $this->action === 'delete') &&
        array_has_key_val($this->getUser()['Privileg'], 'name', 'Profile delete')) {
      return true;
    }
  }

  public $helpers = array('Html', 'Form');

  public function index()
  {
    $this->Profile->contain();
    $this->set('profiles', $this->Profile->find('all'));
  }

  public function attachment($id = null)
  {
    $this->Profile->id = $id;
    $this->Profile->contain('Storage');
    $profile = $this->Profile->read();
    if (($profile['Profile']['show_photo'] || $this->isClubMember()) && isset($profile['Storage']['file'])) {
      // needs cakephp v >= 2.3
      $this->response->type($profile['Storage']['extension']);
      $this->response->file($profile['Storage']['file']['path'] . $profile['Storage']['uuid'],
        array(
          'download' => true,
          'name' => $profile['Storage']['name'] . '.' . $profile['Storage']['extension']
      ));
    } else {
      $this->response->file('img/default_profile_img.png', array('download' => true));
    }
    return $this->response;
  }

  public function add()
  {
    if ($this->request->is('post'))
    {
      $this->Profile->id = $this->getUser()['Profile']['id'];
      if ($this->Profile->read()) {
        if (!array_has_key_val($this->getUser()['Privileg'], 'name', 'Profile create')) {
//          $this->Session->setFlash('Es kann nur das eigene Profil bearbeitet werden.');
          $this->redirect(array('action' => 'edit'));
        }
        // create new profile without user
        $this->Profile->create();
        if (isset($this->request->data['Profile']['user_id']) && !$this->request->data['Profile']['user_id']) {
          unset($this->request->data['Profile']['user_id']);
        }
        if ($this->Profile->save($this->request->data))
        {
          $this->Session->setFlash('Profil wurde ohne Benutzer gespeicher.', 'default', array('class' => 'success'));
          $this->redirect(array('action' => 'index'));
        }
      } else {
        // create new profile and connect to user
        $this->request->data['Profile']['user_id'] = $this->Auth->user('id');
        $this->Profile->create();
        if ($this->Profile->save($this->request->data))
        {
          $this->Session->setFlash('Profil wurde gespeichert.', 'default', array('class' => 'success'));
//          $this->redirect(array('action' => 'index'));
          $this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
        }
        else
        {
          $this->Session->setFlash('Profil konnte nicht gespeichert werden.');
        }
      }
    }

    // add drop down lists
    $this->fetchDropDownLists();
  }

  public function edit($id = null)
  {
    $is_admin = array_has_key_val($this->getUser()['Privileg'], 'name', 'Profile modify');
    $this->Profile->id = ($id && $is_admin) ? $id : $this->getUser()['Profile']['id'];
    $this->Profile->contain('Salutation', 'Title', 'User');
    $profile = $this->Profile->read();

    // redirect to add if user does not have a profile
    if (!$profile)
      $this->redirect(array('action' => 'add'));

    // add drop down lists
    $this->fetchDropDownLists($profile);

    if ($this->request->is('get')) {
      $this->request->data = $profile;
    } else {
      // keep user if not administrator
      if (!array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator')) {
        $this->request->data['Profile']['user_id'] = $profile['Profile']['user_id'];
      }
      if ($this->Profile->save($this->request->data)) {
        $this->Session->setFlash('Profil wurde aktualisiert.', 'default', array('class' => 'success'));
//        $this->redirect(array('action' => 'view/' . $id . ''));
//        $this->redirect(array('action' => 'view', $this->request->data['Profile']['id']));
        if ($is_admin)
          $this->redirect(array('action' => 'index'));
        else
          $this->redirect(array('controller' => 'users', 'action' => 'view', $this->Auth->user('id')));
      } else {
        $this->Session->setFlash('Profil konnte nicht aktualisiert werden.');
      }
    }
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    $profile = $this->Profile->findById($id);
    if ($this->Profile->delete($id)) {
      $this->Session->setFlash('Profil wurde gelöscht.', 'default', array('class' => 'info'));
    } else {
      $this->Session->setFlash('Profil konnte nicht gelöscht werden.');
    }
    $this->redirect(array('action' => 'index'));
  }

  private function fetchDropDownLists($profile = null) {
    // add all titles to the instant variable
    $titles = $this->Profile->Title->find('list',array('fields'=>array('id','name')));
    $this->set(compact('titles'));
    // add all salutations to the instant variable
    $salutations = $this->Profile->Salutation->find('list',array('fields'=>array('id','name')));
    $this->set(compact('salutations'));
    // add users without profile to the instant variable
    $this->Profile->User->contain('Profile.id');
    $temp = $this->Profile->User->find('all');
    $users = array();
    foreach ($temp as $user) {
      if (empty($user['Profile']['id']) || $user['Profile']['id'] == $profile['Profile']['id']) {
        $users[$user['User']['id']] = $user['User']['username'];
      }
    }
    $this->set(compact('users'));
  }
}

