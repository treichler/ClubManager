<?php
// app/Controller/SheetsController.php
class SheetsController extends AppController {

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    // users with privileg 'Music book' are allowed to call 'delete'
    if ($this->action === 'delete' &&
        array_has_key_val($privilegs['Privileg'], 'name', 'Music book'))
      return true;
  }


  public $components = array('RequestHandler');
  public $helpers = array('Html', 'Form', 'Js' => array('Jquery'));

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    $response = '';
    if ($this->request->is('ajax')) {
      if ($this->Sheet->delete($id)) {
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

