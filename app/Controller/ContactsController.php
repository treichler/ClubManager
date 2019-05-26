<?php
// app/Controller/ContactsController.php
class ContactsController extends AppController
{

  public function beforeFilter() {
    // everybody is allowed to call 'index' and 'contact'
    $this->Auth->allow('index', 'contact', 'sms_callback');
  }

  public function isAuthorized($user) {
    // users with privileg 'Administrator' are allowed to call 'protocol' and 'smsprotocol'
    if (($this->action === 'protocol' || $this->action === 'smsprotocol') &&
        $this->UserHasPrivileg(array('Administrator', 'Contact email', 'Contact sms'))) {
      return true;
    }

    // users with privileg 'Contact export' are allowed to call 'export'
    if ($this->action === 'export' && $this->UserHasPrivileg(array('Contact export'))) {
      return true;
    }

    // users with privileg 'Contact email' are allowed to call 'email'
    if ($this->action === 'email' && $this->UserHasPrivileg(array('Contact email'))) {
      return true;
    }

    // users with privileg 'Contact sms' are allowed to call 'sms'
    if ($this->action === 'sms' && $this->UserHasPrivileg(array('Contact sms'))) {
      return true;
    }
  }

  public $components = array('RequestHandler', 'SmsGateway');

  public $helpers = array('Html', 'Form');

  public function index() {
  }

  public function protocol() {
    $conditions = array();
    if (!$this->userHasPrivileg(array('Administrator'))) {
      $conditions = array('Contactprotocol.user_id' => $this->getUser()['User']['id']);
    }
    $this->loadModel('Contactprotocol');
    $this->Contactprotocol->contain('User');
    $contactprotocols = $this->Contactprotocol->find('all', array(
      'conditions' => $conditions,
      'order' => array('Contactprotocol.id' => 'desc')
    ));
    $this->set(compact('contactprotocols'));
  }

  public function smsprotocol($contactprotocol_id = null) {
    if (!$contactprotocol_id)
      $this->redirect(array('action' => 'protocol'));

    $this->loadModel('Smsprotocol');
    $this->Smsprotocol->contain('Profile');
    $smsprotocols = $this->Smsprotocol->find('all', array(
      'conditions' => array('Smsprotocol.contactprotocol_id' => $contactprotocol_id)
    ));
    $this->set(compact('smsprotocols'));
  }

  public function contact() {
    $this->loadModel('Contactperson');
    $contactpeople = $this->Contactperson->find('all', array(
      'contain' => array('Profile' => array('User.email')),
      'conditions' => array(
        'Contactperson.contact_recipient' => true,
        'Profile.user_id >' => 0
      )
    ));
    // put e-mail addresses of recipients together 
    $to = array();
    foreach ($contactpeople as $contactperson) {
      $to[] = $contactperson['Profile']['User']['email'];
    }
    // check if there are recipients available
    if (count($to)) {
      // check if request is post
      if ($this->request->is('post')) {
        // check if contact form's fields are not empty
        if (!empty($this->request->data['Contact']['email']) &&
            !empty($this->request->data['Contact']['subject']) &&
            !empty($this->request->data['Contact']['text'])) {
          // check ReCaptcha
          $recapcha = false;
          if( Configure::read("recaptcha_settings.public_key") && isset($this->request->data['recaptcha_challenge_field']) ) {
            App::import('Vendor', 'recaptchalib', array('file' => 'recaptchalib/recaptchalib.php'));
            $resp = recaptcha_check_answer (Configure::read("recaptcha_settings.private_key"),
                              $_SERVER["REMOTE_ADDR"],
                              $this->request->data['recaptcha_challenge_field'],
                              $this->request->data['recaptcha_response_field']);
            $recapcha = $resp->is_valid;
          }
          if ($recapcha) {
            // send e-mail
            $subject = '[' . Configure::read('club.subject_id') . '] ' . $this->request->data['Contact']['subject'];
            // mail to club's recipients
            $email = new CakeEmail('default');
            $email->from($this->request->data['Contact']['email'])
                  ->to($to)
                  ->subject($subject)
                  ->send($this->request->data['Contact']['text']);
            // confirmation mail to the sender
            $text = "Ihre Anfrage wurde an uns weiter geleitet.\r\n\r\nIhre Nachricht:\r\n" . $this->request->data['Contact']['text'];
            $email = new CakeEmail('default');
            $email->to($this->request->data['Contact']['email'])
                  ->subject($subject)
                  ->send($text);
            $this->Session->setFlash('Ihre Anfrage wurde an uns weiter geleitet.', 'default', array('class' => 'success'));
            // save protocol
            $this->saveProtocol(array(
              'user_id'             => 0,
              'name'                => 'contact',
              'report'              => 'Sender: ' . $this->request->data['Contact']['email'],
              'profiles_selected'   => 0,
              'profiles_delivered'  => count($to)
            ));
            $this->redirect(array('controller' => 'pages', 'action' => 'index'));
          } else {
            if (isset($resp)) {
              $this->Session->setFlash('Das reCAPTCHA wurde falsch eingegeben.');
            } else {
              $this->Session->setFlash('reCAPTCHA ist derzeit nicht verfügbar, bitte versuchen Sie es später wieder.',
                                       'default', array('class' => 'error'));
            }
          }
        } else {
          // fields have to be filled
          $msg = 'Fehler: ';
          if (empty($this->request->data['Contact']['email'])) $msg .= 'Bitte E-Mail eingeben. ';
          if (empty($this->request->data['Contact']['subject'])) $msg .= 'Bitte Betreff eingeben. ';
          if (empty($this->request->data['Contact']['text'])) $msg .= 'Bitte Text eingeben.';
          $this->Session->setFlash($msg);
        }
      }
    } else {
      // no recipients available
      $this->Session->setFlash('Das Kontaktformular ist vorübergehend nicht verfügbar.');
    }
    $this->set('contactpeople_available', (count($to) ? true : false));
  }

  public function email() {
    if ($this->request->is('post')) {
      // fetch selected profiles
      $this->Contact->Profile->contain('User.email');
      $profiles = $this->Contact->Profile->find('all', array(
        'conditions' => array('Profile.id' => $this->request->data['Profile']['Profile'])
      ));

      if (!empty($this->request->data['Contact']['subject']) && !empty($this->request->data['Contact']['text'])) {
        // split into receivers and those who do not have an email address
        $to = [];
        $no_mail = [];
        foreach ($profiles as $profile) {
          if( empty($profile['User']['email']) && empty($profile['Profile']['email_opt']) ) {
            $no_mail[] = $profile['Profile']['first_name'] . ' ' . $profile['Profile']['last_name'];
          } else {
            if( !empty($profile['User']['email']) )
              $to[] = $profile['User']['email'];
            if( !empty($profile['Profile']['email_opt']) )
              $to[] = $profile['Profile']['email_opt'];
          }
        }

        // send email if there are receivers
        if (!empty($to)) {
          $email = new CakeEmail('default');
          $email->from(array($this->getUser()['User']['email'] => $this->getUser()['User']['name']))
                ->to($to)
                ->subject($this->request->data['Contact']['subject'])
                ->send($this->request->data['Contact']['text']);
          $message = 'E-Mail wurde gesendet.';
          if (!empty($no_mail))
            $message .= ' Folgende Personen erhalten kein E-Mail: ' . implode(', ', $no_mail);
          $this->Session->setFlash($message, 'default', array('class' => 'success'));
          // save protocol
          $this->saveProtocol(array(
            'user_id'             => $this->getUser()['User']['id'],
            'name'                => 'email',
            'report'              => 'E-Mail wurde gesendet',
            'profiles_selected'   => count($profiles),
            'profiles_delivered'  => count($to)
          ));
        } else {
          $this->Session->setFlash('Es wurde kein E-Mail gesendet, da keine Empfänger angegeben wurden.');
        }
      } else {
        $msg = [];
        if (empty($this->request->data['Contact']['subject']))
          $msg[] = 'Bitte Betreff eingeben.';
        if (empty($this->request->data['Contact']['text']))
          $msg[] = 'Bitte Text eingeben.';
        $this->Session->setFlash(implode(' ', $msg));
      }
    }
    $this->fetchDropDownLists();
  }

  public function export() {
    if ($this->request->is('post')) {
      // fetch selected profiles
      $this->Contact->Profile->contain('User.email');
      $profiles = $this->Contact->Profile->find('all', array(
        'conditions' => array('Profile.id' => $this->request->data['Profile']['Profile']),
        'order'      => array('Profile.last_name' => 'asc')
      ));
      $this->set('contacts', $profiles);

      // render the pdf layout
      $this->response->type('pdf');
      $this->layout = 'pdf/default';
      $this->render('pdf/export');
    } else {
      $this->Contact->Profile->Membership->contain('State.is_member');
      $memberships = $this->Contact->Profile->Membership->find('all', array(
        'conditions' => array('State.is_member' => true)
      ));
      $profile_ids = [];
      foreach ($memberships as $membership) {
        $profile_ids[] = $membership['Membership']['profile_id'];
      }
      $profiles = $this->Contact->Profile->find('all', array(
        'conditions' => array('Profile.id' => $profile_ids),
        'order'      => array('Profile.last_name' => 'asc')
      ));
      $this->set('contacts', $profiles);
    }
    $this->fetchDropDownLists();
  }

  public function sms() {
    if ($this->request->is('post')) {
      // fetch selected profiles
      $this->Contact->Profile->contain('User.email');
      $profiles = $this->Contact->Profile->find('all', array(
        'conditions' => array('Profile.id' => $this->request->data['Profile']['Profile'])
      ));

      if (!empty($this->request->data['Contact']['text'])) {
        // get receivers and those who do not have a cell phone
        $to = [];
        $no_cell_phone = [];
        foreach ($profiles as $profile) {
          if( empty($profile['Profile']['phone_mobile']) && empty($profile['Profile']['phone_mobile_opt']) ) {
            $no_cell_phone[] = $profile['Profile']['first_name'] . ' ' . $profile['Profile']['last_name'];
          } else {
            if (!empty($profile['Profile']['phone_mobile']))
              $to[] = array('profile_id' => $profile['Profile']['id'],
                            'phone_nr'   => $profile['Profile']['phone_mobile']);
            if (!empty($profile['Profile']['phone_mobile_opt']))
              $to[] = array('profile_id' => $profile['Profile']['id'],
                            'phone_nr'   => $profile['Profile']['phone_mobile_opt']);
          }
        }

        // send sms if there are receivers
        if (!empty($to)) {
          $from = '';
          if (isset($this->getUser()['Profile']['phone_mobile'])) {
            $from = $this->getUser()['Profile']['phone_mobile'];
          }
          // TODO create empty protocol to get its id
          $protocol_id = $this->saveProtocol();
          $response = $this->SmsGateway->smsDeliver($from, $to, $this->request->data['Contact']['text'], $protocol_id);

          // create flash message report
          $message = $response['text'];
          if (!empty($no_cell_phone))
            $message .= '<br />Folgende Personen erhalten kein SMS: ' . implode(', ', $no_cell_phone);
          if ($response['error']) {
            $this->Session->setFlash($message);
          } else {
            $this->Session->setFlash($message, 'default', array('class' => 'success'));
          }

          // save protocol
          $this->saveProtocol(array(
            'id'                  => $protocol_id,
            'user_id'             => $this->getUser()['User']['id'],
            'name'                => 'sms',
            'report'              => $response['text'],
            'profiles_selected'   => count($profiles),
            'profiles_delivered'  => count($to)
          ));
        } else {
          $this->Session->setFlash('Es wurde keine SMS gesendet, da keine Empfänger angegeben wurden.');
        }
      } else {
        $this->Session->setFlash('Bitte Text eingeben.');
      }
    }
    $this->fetchDropDownLists();
    // get sms-gateway credit
//    $this->set('credit', $this->smsGetCredit());
    $this->set('credit', $this->SmsGateway->smsGetCredit());
  }

  public function sms_callback() {
    $message = $this->SmsGateway->smsCallback($this->request->query);
    $this->autoRender = false;
    $this->response->type('txt');
    $this->response->body($message);
  }


  private function saveProtocol($data = null, $id = null) {
    $this->loadModel('Contactprotocol');
    $this->Contactprotocol->create();
    $this->Contactprotocol->save(array('Contactprotocol' => $data));
    return $this->Contactprotocol->id;
  }

  private function fetchDropDownLists() {
    // add memberships to the instant variable
    $this->Contact->Profile->Membership->contain('Group.id', 'Profile.id', 'State');
    $memberships = $this->Contact->Profile->Membership->find('all');
    $this->set(compact('memberships'));

    // add states to the instant variable
    $states = $this->Contact->Profile->Membership->State->find('all');
    $this->set(compact('states'));

    // add groups to the instant variable
    $this->Contact->Profile->Membership->Group->contain('Kind');
    $tmp = $this->Contact->Profile->Membership->Group->find('all', array(
      'order'      => array('Group.kind_id' => 'asc')
    ));
    $groups = [];
    foreach ($tmp as $group) {
      $groups[$group['Kind']['name']][$group['Group']['id']] = $group['Group']['name'];
    }
    $this->set(compact('groups'));

    // add profiles with membership to the instant variable
    $profiles = [];
    $this->Contact->Profile->contain('Membership.id', 'User.email');
    $temp = $this->Contact->Profile->find('all', array(
      'conditions' => array('Membership.id >' => 0),
      'order' => array('first_name' => 'asc')
//      'order' => array('last_name' => 'asc')
    ));
    foreach($temp as $profile) {
//      if (isset($profile['Membership']['id'])) {
        $profiles[$profile['Profile']['id']] = $profile['Profile']['first_name'] . ' ' . $profile['Profile']['last_name'];
//        if (isset($profile['Profile']['birthday'])) {
//          $profiles[$profile['Profile']['id']] .= ' (' . $profile['Profile']['birthday'] . ')';
//        }
        $info = [];
        if (!isset($profile['User']['email']))
          $info[] = 'kein E-Mail';
        if (empty($profile['Profile']['phone_mobile']))
          $info[] = 'kein Mobiltelefon';
        if (!empty($info))
          $profiles[$profile['Profile']['id']] .= ' (' . implode(', ', $info) . ')';
//      }
    }
    $this->set(compact('profiles'));

    // preselect the meberships
    if(isset($this->params['url']['membership_ids'])) {
      $selected = [];
      $this->Contact->Profile->Membership->contain();
      $selected_members = $this->Contact->Profile->Membership->find('all', array(
        'conditions' => array('Membership.id' => $this->params['url']['membership_ids'])
      ));
      foreach ($selected_members as $selected_member) {
        $selected['Profile'][] = array('id' => $selected_member['Membership']['profile_id']);
      }
      $this->request->data = $selected;
    }

    return true;
  }

}

