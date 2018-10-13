<?php
// app/Controller/ProtocolsController.php
class ProtocolsController extends AppController
{

  public function beforeFilter() {
    // everybody is allowed to call 'index' and 'contact'
    $this->Auth->allow('index', 'contact');
  }

  var $user;

  public function isAuthorized($user) {
//    $privilegs = $this->getUser();
    $this->user = $this->getUser();

    // users with privileg 'Administrator' are allowed to call 'protocol' and 'people'
    if (($this->action === 'protocol') &&
        array_has_key_val($this->user['Privileg'], 'name', 'Administrator')) {
      return true;
    }

    // users with privileg 'Contact export' are allowed to call 'export'
    if ($this->action === 'export' && array_has_key_val($this->user['Privileg'], 'name', 'Contact export')) {
      return true;
    }

    // users with privileg 'Contact email' are allowed to call 'email'
    if ($this->action === 'email' && array_has_key_val($this->user['Privileg'], 'name', 'Contact email')) {
      return true;
    }

    // users with privileg 'Contact sms' are allowed to call 'sms'
    if ($this->action === 'sms' && array_has_key_val($this->user['Privileg'], 'name', 'Contact sms')) {
      return true;
    }
  }

  public $components = array('RequestHandler', 'SmsGateway');

  public $helpers = array('Html', 'Form');

  public function index() {
    $this->loadModel('Smsprotocol');
    $smsprotocols = $this->Smsprotocol->find('all');
    $this->set(compact('smsprotocols'));
  }

  public function protocol() {
    $this->loadModel('Contactprotocol');
    $this->Contactprotocol->contain('User');
    $contactprotocols = $this->Contactprotocol->find('all', array(
      'order' => array('Contactprotocol.id' => 'desc')
    ));
    $this->set(compact('contactprotocols'));
  }

}

