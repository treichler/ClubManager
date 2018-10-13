<?php
// app/Controller/CommentsController.php
class CommentsController extends AppController {

  public function beforeFilter() {
    // everybody is allowed to call 'index', 'view' and 'attachment'
    $this->Auth->allow('index', 'view', 'attachment');
  }

  public function isAuthorized($user) {
    $users = $this->getUser();

    // all logged in users are allowed to call 'add'
    if ($this->action === 'add' && isset($users['User']['id']))
      return true;

    // TODO the creator is allowed to call 'edit' and 'delete'
    if ($this->action === 'edit' || $this->action === 'delete') {
      $commentId = $this->request->params['pass'][0];
      if ($this->Comment->isOwnedBy($commentId, $user['id'])) {
        return true;
      }
    }
  }


  public $helpers = array('Html', 'Form');


  public function add() {
    if ($this->request->is('post')) {
      $this->request->data['Comment']['user_id'] = $this->Auth->user('id');
      $this->Comment->create();
      if ($this->Comment->save($this->request->data)) {
        $this->Session->setFlash('Kommentar wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('controller' => 'blogs', 'action' => 'view', $this->request->data['Comment']['blog_id']));
      } else {
        $this->Session->setFlash('Kommentar konnte nicht hinzugefügt werden.');
        $this->redirect(array('controller' => 'blogs', 'action' => 'view', $this->request->data['Comment']['blog_id']));
      }
    }
//    $this->redirect($this->referer());
//    $this->redirect(array('controller' => 'blogs', 'action' => 'view', $this-request->data['Comment']['blog_id']));
  }

  public function edit($id = null) {
    $this->Comment->id = $id;
    $comment = $this->Comment->read();
    if ($this->request->is('get')) {
      $this->request->data = $comment;
    } else {
      if ($this->Comment->save($this->request->data)) {
        $this->Session->setFlash('Kommentar wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('controller' => 'blogs', 'action' => 'view', $this->request->data['Comment']['blog_id']));
      } else {
        $this->Session->setFlash('Kommentar konnte nicht aktualisiert werden.');
      }
    }
  }

}

