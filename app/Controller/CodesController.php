<?php
// app/Controller/CodesController.php
class CodesController extends AppController {

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    // club-members are allowed to access 'index'
    if ($this->action === 'index') {
      $profile = $this->getProfile();
      if (!empty($profile) && !empty($profile['Membership']['id'])) {
        return true;
      }
    }

    // users with privileg 'Track' are allowed to call 'add' and 'delete'
//    if (($this->action === 'add' || $this->action === 'delete') &&
//        array_has_key_val($privilegs['Privileg'], 'name', 'Track'))
//      return true;
  }


//  public $components = array('RequestHandler');
//  public $helpers = array('Html', 'Form', 'Js' => array('Jquery'));

  public function index () {
    // TODO Alle Vereinsmitglieder dürfen darauf zugreifen und scannen
    //      Wenn ZVR übereinstimmt zeige Details, andernfalls Fehlermeldung
    //        Codierung bzw falscher Verein ;-)
    //      Gespieltes Musikstück loggen --> nur berechtigte
    //      Inventar zuweisen --> jeder (berechtigte)
    //      Inventar zurück --> nur berechtigte
  }

}

