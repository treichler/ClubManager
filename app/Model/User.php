<?php
// app/Model/User.php
class User extends AppModel {

  public $actsAs = array('Containable');

  public $hasOne = array('Profile', 'Vote');

  public $hasMany = array('Blog', 'Comment', 'Event', 'Gallery', 'Photo', 'Upload');

  public $hasAndBelongsToMany = array(
    'Privileg' => array(
      'className'              => 'Privileg',
      'joinTable'              => 'privilegs_users',
      'foreignKey'             => 'user_id',
      'associationForeignKey'  => 'privileg_id',
      'unique'                 => true,
      'conditions'             => '',
      'fields'                 => '',
      'order'                  => '',
      'limit'                  => '',
      'offset'                 => '',
      'finderQuery'            => '',
      'deleteQuery'            => '',
      'insertQuery'            => ''
    )
  );


  public $validate = array(
    'username' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Ein Benutzername ist notwendig.'
      ),
      'length' => array(
        'rule' => array('between', 4, 20),
        'message' => 'Benutzername muss zwischen 4 und 20 Zeichen lang sein.'
      ),
/*
      'prohibited' => array(
        'rule' => array('inList', array(
// TODO Following names should be prohibited:
          'admin', 'administrator', 'client', 'root', 'terminal'
        )),
        'message' => 'Dieser Benutzername ist verboten'
      ),
*/
      'unique' => array(
        'rule' => 'isUnique',
        'message' => 'Benutzername ist bereits vergeben.'
      )
    ),

    'password' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Bitte Passwort eingeben.'
      ),
/*
      'length' => array(
        'rule' => array('minLength', 8),
        'message' => 'Passwort muss mindestens 8 Zeichen lang sein.'
      )
*/
    ),

    'email' => array(
/*
      'required' => array(
        'rule'    => array('email', true),
        'message' => 'Bitte eine gültige E-Mail Adresse eingeben'
      ),
*/
      'unique' => array(
        'rule' => 'isUnique',
        'message' => 'E-Mail Adresse wird bereits verwendet.'
      )
    ),

    'current_password' => array(
      'rule' => 'checkCurrentPassword',
      'message' => 'Aktuelles Passwort ist falsch.'
    ),
    'password1' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Ein Passwort ist notwendig.'
      ),
      'rule' => 'checkPasswordStrength',
      'message' => 'Passwort ist zu schwach.',
    ),
    'password2' => array(
      'rule' => 'passwordsMatch',
      'message' => 'Passwörter stimmen nicht überein.',
    )
  );


  public function checkCurrentPassword($data) {
    $this->id = AuthComponent::user('id');
    $password = $this->field('password');
    return(AuthComponent::password($data['current_password']) == $password);
  }

  // TODO implement
  public function checkPasswordStrength() {
    return true;
  }

  public function passwordsMatch($data) {
//    return ($data['password2'] == $data['password1']);
    return ($data['password2'] == $this->data[$this->alias]['password1']);
  }

  public function afterFind($results, $primary = false) {
    if (isset($results[0])) {
      foreach ($results as $key => $value) {
        if (isset($value['User']['id'])) {
          $this->insertName($results[$key]['User']);
        }
        if (isset($value['id'])) {
          $this->insertName($results[$key]);
        }
      }
    } else {
      if (isset($results['id'])) {
        $this->insertName($results);
      }
    }
    return $results;
  }

  // set 'User.name' to 'Profile.first_name Profile.last_name' if Profile exists
  // otherwise 'User.name' will be set to 'User.username'.
  private function insertName(&$user) {
    if (isset($user['id'])) {
      $this->Profile->contain();
      $profile = $this->Profile->findByUserId($user['id']);
      if (isset($profile['Profile']['id'])) {
        $user['name'] = $profile['Profile']['first_name'] . ' ' . $profile['Profile']['last_name'];
      } else {
        $user['name'] = $user['username'];
      }
    }
  }

  public function beforeSave($options = array()) {
    // hash password1
    if (isset($this->data[$this->alias]['password1'])) {
      $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password1']);
    }       

    // prevent creating/editing privilegs
    unset($this->data['Privileg']);

    return true;
  }

  public function afterSave( $created, $options = array() ) {
    if ($created) {
      // create vote table
      $this->Vote->create();
      $this->Vote->save(array('Vote' => array('user_id' => $this->id)));
    }
    return true;
  }

  public function beforeDelete($cascade = true) {
    // delete assotiated vote
    $this->Vote->delete(array('Vote.user_id' => $this->id), false);
    return true;
  }

  public function createTicket($data) {
    $user = $this->findByEmail($data['User']['email']);
    if (!empty($user['User']['id'])) {
      $id = $user['User']['id'];
      $date = date('Y-m-d H:i:s');
      $ticket = hash('sha256', rand() . $date);
      if ($this->save(array('User' => array('id' => $id, 'ticket' => $ticket, 'ticket_created' => $date)))) {
        return true;
      }
    }
    return false;
  }

}

