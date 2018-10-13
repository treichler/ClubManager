<?php
// app/Controller/UsersController.php
class UsersController extends AppController {

  public function beforeFilter() {
    parent::beforeFilter();
    // Let users register themselves
    $this->Auth->allow('add', 'logout', 'create_ticket', 'apply_ticket');
//    $this->Auth->authError = "huhu haha...";
//    $this->Auth->flash('auth', 'default', array('class' => 'success'));
  }

  public function isAuthorized($user) {

    // Logged in user is allowed to call (own) 'view', 'change_email', 'change_password'
    if ( ($this->Auth->user('id') > 0) && 
         (($this->action === 'view') ||
          ($this->action === 'change_email') ||
          ($this->action === 'change_password')) ) {
      return true;
    }

    // only users with privileg 'Administrator' are allowed to call the other actions
    if (array_has_key_val($this->getUser()['Privileg'], 'name', 'Administrator')) {
      return true;
    }
  }

  public function login() {
    if ($this->request->is('post')) {
      if ($this->Auth->login()) {
        $user = $this->getUser();
        $message = 'Willkommen ' . $user['User']['name'];
        if (!isset($user['Profile']['id'])) {
          $message = 'Willkommen ' . $user['User']['name'] . ". Bitte <a href=\"" . Router::url(array('controller' => 'profiles', 'action'=>'add')) . "\">Profil</a> anlegen.";
        }
        $this->Session->setFlash($message, 'default', array('class' => 'success'));
        $this->redirect($this->Auth->redirect());
      } else {
        $this->Session->setFlash('Ungültiger Benutzername oder Passwort, bitte nochmals versuchen');
      }
    }
    $this->request->data['User']['referer'] = $this->referer();
  }

  public function logout() {
    $this->Session->setFlash('Erfolgreich abgemeldet', 'default', array('class' => 'success'));
    $this->redirect($this->Auth->logout());
  }

  public function index() {
//    $this->User->recursive = 0;
//    $this->set('users', $this->paginate());
    $this->User->contain('Profile', 'Privileg', 'Vote');
    $this->set('users', $this->User->find('all'));
  }

  public function view() {
    $this->User->contain('Profile', 'Privileg');
    $user = $this->User->read(null, $this->Auth->user('id'));
    $this->set(compact('user'));
    if (isset($user['Profile']['id'])) {
      $this->User->Profile->Membership->contain('Group');
      $membership = $this->User->Profile->Membership->findByProfileId($user['Profile']['id']);
      $this->set(compact('membership'));
    }
  }

  public function change_email() {
    $user = $this->User->read(null, $this->Auth->user('id'));
    unset($user['User']['password']);
    if ($this->request->is('get')) {
      $this->request->data = $user;
    } else {
      $new_email = array('User' => array(
        'id'       => $user['User']['id'],
        'email'    => $this->request->data['User']['email']
      ));
      if ($this->User->save($new_email)) {
        $this->Session->setFlash('E-Mail Adresse wurde gespeichert', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'view'));
      } else {
        $this->Session->setFlash('E-Mail Adresse konnte nicht gespeichert werden');
      }
    }
  }

  public function change_password() {
    $user = $this->User->read(null, $this->Auth->user('id'));
    unset($user['User']['password']);
    if ($this->request->is('get')) {
      $this->request->data = $user;
    } else {
      $new_password = array('User' => array(
        'id'               => $user['User']['id'],
        'current_password' => $this->request->data['User']['current_password'],
        'password1'        => $this->request->data['User']['password1'],
        'password2'        => $this->request->data['User']['password2']
      ));
      if ($this->User->save($new_password)) {
        $this->Session->setFlash('Passwort wurde geändert', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'view'));
      } else {
        $this->Session->setFlash('Passwort konnte nicht geändert werden');
      }
    }
  }

  public function create_ticket() {
    if ($this->request->is('post')) {
      App::import('Vendor', 'recaptchalib', array('file' => 'recaptchalib/recaptchalib.php'));        
      $resp = recaptcha_check_answer (Configure::read("recatpch_settings.private_key"),
                          $_SERVER["REMOTE_ADDR"],
                          $this->request->data['recaptcha_challenge_field'],
                          $this->request->data['recaptcha_response_field']);
      if (!$resp->is_valid) {
        $this->Session->setFlash('Das reCAPTCHA wurde falsch eingegeben.');
      } else {
        if ($this->User->createTicket($this->request->data)) {
          $user = $this->User->read();
          // send the email with the ticket
          $link = Router::url(array('controller' => 'users', 'action' => 'apply_ticket', $user['User']['ticket']), true);
          $email = new CakeEmail('default');
          $email->to($user['User']['email'])
                ->subject('[' . Configure::read('club.subject_id') .'] Passwort zurücksetzen')
                ->send('Um das Passwort neu zu setzen bitte folgenden Link aufrufen: ' . $link);
          $this->Session->setFlash('An die angegebene E-Mail Adresse wurde ein Ticket gesendet. Bitte den Anweisungen im Mail folgen.', 'default', array('class' => 'success'));
          $this->redirect('/');
        } else {
          $this->Session->setFlash('Ticket konnte nicht erstellt werden.');
        }
      }
    }
  }

  public function apply_ticket($ticket = null) {
    if ($this->request->is('get')) {
      $user = $this->User->findByTicket($ticket);
      unset($user['User']['password']);
      if (!empty($user['User']['id'])) {
        $now = new DateTime();
        $ticket_created = new DateTime($user['User']['ticket_created']);
        // check if ticket is older than one hour
        if ($now->getTimestamp() - $ticket_created->getTimestamp() > 1*60*60) {
          $this->Session->setFlash('Ticket ist abgelaufen.');
          $this->redirect('/');
        } else {
          $this->request->data = $user;
          $this->set(compact('user'));
        }
      } else {
        $this->Session->setFlash('Ticket existiert nicht.');
        $this->redirect('/');
      }
    }
    if ($this->request->is('post')) {
      $user = $this->User->findByTicket($this->request->data['User']['ticket']);
      unset($user['User']['password']);
      if (!empty($user['User']['id'])) {
        $now = new DateTime();
        $ticket_created = new DateTime($user['User']['ticket_created']);
        // check if ticket is older than one hour
        if ($now->getTimestamp() - $ticket_created->getTimestamp() > 1*60*60) {
          $this->Session->setFlash('Ticket ist abgelaufen.');
          $this->redirect('/');
        } else {
          $new_password = array('User' => array(
            'id'             => $user['User']['id'],
            'ticket'         => null,
            'ticket_created' => null,
            'password1'      => $this->request->data['User']['password1'],
            'password2'      => $this->request->data['User']['password2']
          ));
          if ($this->User->save($new_password)) {
            $this->Session->setFlash('Neues Passwort wurde gesetzt. Anmelden ist nun möglich', 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'login'));
          } else {
            $this->Session->setFlash('Neues Passwort konnte nicht gesetzt werden');
          }
        }
      } else {
        $this->Session->setFlash('Ticket existiert nicht.');
      }
    }
  }

  public function add() {
    if ($this->request->is('post')) {
      App::import('Vendor', 'recaptchalib', array('file' => 'recaptchalib/recaptchalib.php'));        
      $resp = recaptcha_check_answer (Configure::read("recatpch_settings.private_key"),
                          $_SERVER["REMOTE_ADDR"],
                          $this->request->data['recaptcha_challenge_field'],
                          $this->request->data['recaptcha_response_field']);
      if (!$resp->is_valid) {
        $this->Session->setFlash('Das reCAPTCHA wurde falsch eingegeben.');
      } else {
        $this->User->create();
        if ($this->User->save($this->request->data)) {
          $this->Session->setFlash('Benutzer wurde gespeichert. Anmelden ist nun möglich', 'default', array('class' => 'success'));
          $this->redirect(array('action' => 'login'));
        } else {
          $this->Session->setFlash('Benutzer konnte nicht gespeichert werden, bitte nochmals versuchen');
        }
      }
    }
  }


//---------------------------------------------------------------------------------------
//                              Admin's actions
//---------------------------------------------------------------------------------------

  public function edit($id = null) {
    $this->User->id = $id;
    if (!$this->User->exists()) {
      throw new NotFoundException('Ungültiger Benutzer');
    }
    if ($this->request->is('post') || $this->request->is('put')) {
      if ($this->User->save($this->request->data)) {
        $this->Session->setFlash('Benutzer wurde gespeichert', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Benutzer konnte nicht gespeichert werden, bitte nochmals versuchen');
      }
    } else {
      $this->request->data = $this->User->read(null, $id);
      unset($this->request->data['User']['password']);
    }
  }

  public function delete($id = null) {
    if (!$this->request->is('post')) {
      throw new MethodNotAllowedException();
    }
    $this->User->id = $id;
    if (!$this->User->exists()) {
      throw new NotFoundException('Ungültiger Benutzer');
    }
    if ($this->User->delete()) {
      $this->Session->setFlash('Benutzer wurde gelöscht', 'default', array('class' => 'info'));
      $this->redirect(array('action' => 'index'));
    }
    $this->Session->setFlash('Benutzer konnte nicht gelöscht werden');
    $this->redirect(array('action' => 'index'));
  }

}

