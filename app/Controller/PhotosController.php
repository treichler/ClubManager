<?php
// app/Controller/PhotosController.php
class PhotosController extends AppController
{

  public function beforeFilter() {
    // everybody is allowed to call 'index', 'view', 'photo' and 'thumb'
    $this->Auth->allow('photo', 'thumb');
  }

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    // the creator and users with privileg 'Photo delete' are allowed to call 'delete'
    if ($this->action === 'delete') {
      $photo_id = $this->request->params['pass'][0];
      if ($this->Photo->isOwnedBy($photo_id, $user['id']) ||
          array_has_key_val($privilegs['Privileg'], 'name', 'Gallery delete')) {
        return true;
      }
    }
  }


  public function orig($id = null) {
    $this->Photo->id = $id;
    $photo = $this->Photo->read();

    // is deprecated since cakephp v2.3
//    $this->viewClass = 'Media';
//    $this->set($photo['ThumbnailStorage']['file']);

    // needs cakephp v >= 2.3
    $this->response->type($photo['Storage']['extension']);
//    $this->response->header(array('Content-Type' => $photo['MarkedStorage']['type']));
    $this->response->file($photo['Storage']['file']['path'] . $photo['Storage']['uuid'],
      array(
        'download' => true,
        'name' => $photo['Storage']['name'] . '.' . $photo['Storage']['extension']
    ));
    return $this->response;
  }

  public function photo($id = null) {
    $this->Photo->id = $id;
    $photo = $this->Photo->read();

    // is deprecated since cakephp v2.3
//    $this->viewClass = 'Media';
//    $this->set($photo['MarkedStorage']['file']);

    // needs cakephp v >= 2.3
    $this->response->type($photo['Marked']['extension']);
//    $this->response->header(array('Content-Type' => $photo['MarkedStorage']['type']));
    $this->response->file($photo['Marked']['file']['path'] . $photo['Marked']['uuid'],
      array(
        'download' => true,
        'name' => $photo['Marked']['name'] . '.' . $photo['Marked']['extension']
    ));
    return $this->response;
  }

  public function thumb($id = null) {
    $this->Photo->id = $id;
    $photo = $this->Photo->read();

    // is deprecated since cakephp v2.3
//    $this->viewClass = 'Media';
//    $this->set($photo['ThumbnailStorage']['file']);

    // needs cakephp v >= 2.3
    $this->response->type($photo['Thumbnail']['extension']);
//    $this->response->header(array('Content-Type' => $photo['MarkedStorage']['type']));
    $this->response->file($photo['Thumbnail']['file']['path'] . $photo['Thumbnail']['uuid'],
      array(
        'download' => true,
        'name' => $photo['Thumbnail']['name'] . '.' . $photo['Thumbnail']['extension']
    ));
    return $this->response;
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    $response = '';
    if ($this->request->is('ajax')) {
      if ($this->Photo->delete($id)) {
        $response = 'true';
      } else {
        $response = 'false';
      }
    }
    $this->response->type('json');
    $this->response->body($response);
    return $this->response;
  }

}

