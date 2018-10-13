<?php
// app/Controller/StoragesController.php
class StoragesController extends AppController
{

  public function isAuthorized($user) {
    // only admins are allowed to access 'index' and 'download'
    if (($this->action === 'index' || $this->action === 'download') &&
        array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator')) {
      return true;
    }
  }

  public $helpers = array('Html', 'Form');

  public function index()
  {
//    $this->set('storages', $this->Storage->find('all'));
    $this->set('storages', $this->Storage->find('threaded'));
  }

/*
  public function view($id = null)
  {
    $this->Storage->id = $id;
    $this->set('storage', $this->Storage->read());
  }
*/

// FIXME This method is just for testing purpose.
//       Remove as soon as the project is in a mature development state.
  public function download($id = null)
  {
    $this->Storage->id = $id;
    $storage = $this->Storage->read();

/*
    // FIXME needs cakephp v2.3
    $path = 'storage/' . $this->data[$this->name]['folder'] . '/' .
            $storage['Storage']['uuid'].'.'.$storage['Storage']['extension'];
    $this->response->file($file['path']);
*/

    // is deprecated since cakephp v2.3
		$this->viewClass = 'Media';
		$this->set($storage['Storage']['file']);
/*
		$this->set(array(
			'id' => $storage['Storage']['uuid'] . '.' . $storage['Storage']['extension'],
			'name' => $storage['Storage']['name'],
			'extension' => $storage['Storage']['extension'],
			'path' => 'storage/' . $storage['Storage']['folder'] . '/',
			'download' => true,
		));
*/
  }

}

