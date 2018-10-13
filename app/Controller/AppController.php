<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

App::uses('CakeEmail', 'Network/Email');

App::uses('ImageProcess', 'Lib');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

  public $components = array(
    'Session',
    'Auth' => array(
//      'loginRedirect' => array('controller' => 'blogs', 'action' => 'index'),
      'loginRedirect' => array('controller' => 'pages', 'action' => 'display', 'home'),
      'logoutRedirect' => array('controller' => 'pages', 'action' => 'display', 'home'),
      'authorize' => array('Controller')
    )
  );

/*
  public function beforeFilter() {
    $this->Auth->allow('index', 'view');
  }
*/

  function beforeFilter() {
    // allow static pages
    $this->Auth->allow('display');
  }

  function beforeRender () {
    $this->loadModel('Membership');
    $this->loadModel('Contactperson');
    $footer_contactpeople = $this->Contactperson->find('all', array(
      'contain' => array(
        'Profile' => array('Membership' => array('Group' => array('Kind' => 'is_official')))
      ),
      'conditions' => array('Contactperson.footer_phone' => true)
    ));
    $this->set(compact('footer_contactpeople'));

/*
    // get members who belong to groups where kind.show_contact is set
    $this->Membership->Group->contain('Kind.show_contact', 'Membership.id');
    $show_contact_groups = $this->Membership->Group->find('all', array(
      'conditions' => array('Kind.show_contact' => true)
    ));
    // extract membership ids
    $membership_ids = [];
    foreach ($show_contact_groups as $tmp) {
      foreach ($tmp['Membership'] as $membership) {
        $membership_ids[] = $membership['id'];
      }
    }
    // get memberships
    $contact_people = $this->Membership->find('all', array(
      'contain' => array('Profile', 'Group' => array('Kind.show_contact')),
      'conditions' => array('Membership.id' => $membership_ids)
    ));
    // get names of groups which need to be shown and remove groups
    foreach ($contact_people as $key => $val) {
      $tmp = [];
      foreach ($val['Group'] as $group_key => $group) {
        if ($group['Kind']['show_contact'])
          $tmp[] = $group['name'];
      }
      $contact_people[$key]['group_names'] = implode($tmp, ', ');
      unset($contact_people[$key]['Group']);
    }
    $this->set(compact('contact_people'));
*/

    // get public groups for dropdown-menu
    $this->loadModel('Group');
    $kinds = $this->Group->Kind->findAllByIsPublic(true);
    $kind_ids = [];
    foreach ($kinds as $kind) {
      $kind_ids[] = $kind['Kind']['id'];
    }
    $this->Group->contain();
    $groups = $this->Group->find('all', array(
      'conditions' => array('Group.kind_id' => $kind_ids),
      'order'      => array('Group.kind_id' => 'asc')
    ));
    $this->set('public_groups', $groups);

    // get current user
    $user_username = $profile_name = null;
    $user = $this->getUser();
    if (!empty($user['User'])) {
      $this->Membership->contain('State');
      $membership = $this->Membership->findByProfileId($user['Profile']['id']);
      if (!empty($membership)) {
        $user['State'] = $membership['State'];
      }
    }
    $this->set('this_user', $user);
  }

  // a helper to return user and all associations
  // this function is extensivly used in the controllers to check privilegs
  public function getUser() {
    // get user's privilegs
    $this->loadModel('User');
    $this->User->contain('Privileg', 'Profile', 'Vote');
    return $this->User->findById($this->Auth->user('id'));
  }

  public function getProfile() {
    $user = $this->getUser();
    if(isset($user['Profile']['id'])) {
      $this->User->Profile->contain('User', 'Membership');
      return $this->User->Profile->findById($user['Profile']['id']);
    }
    return false;
  }

  // returns true if session user has at least one specified privileg
  public function userHasPrivileg($privilegs) {
    $user = $this->getUser();
    if (!is_array($user['Privileg'])) {
      return false;
    }
    if (!is_array($privilegs)) {
      return false;
    }
    foreach ($user['Privileg'] as $user_privileg) {
      foreach ($privilegs as $privileg) {
        if ($user_privileg['name'] == $privileg)
          return true;
      }
    }
    return false;
  }

  // returns true if session-user is club-member
  public function isClubMember() {
    // logged-in club-members are allowed to get the real attachment if available
    $profile = $this->getProfile();
    if (!empty($profile) && !empty($profile['Membership']['id'])) {
      $this->loadModel('Membership');
      $membership = $this->Membership->contain('State');
      $membership = $this->Membership->findById($profile['Membership']['id']);
      if ($membership['State']['is_member'])
        return true;
    }
    return false;
  }

  // counterCache needs to be activated
  // if profile exists it returns 'first_name last_name (birthday)' where birthday is optional
  // otherwhise it returns the username
  // name:    takes the controller's name ($this->name)
  // options: array(
  //            'username' => true/false: show username if true, otherwise show profile's name
  //            'birthday' => true/false: show birthday if true
  //          )
  public function getUserNames($name = null, $options = null) {

    // TODO needs to be adapted if function is put somewhere else
    $this->loadModel('User');
    $this->User->contain('Profile');

    if (isset($name)) {
      $column = 'User.' . strToLower(Inflector::singularize($name)) . '_count';
      $tmp = $this->User->find('all', array(
        'conditions' => array($column . ' >' => '0')
      ));
    } else {
      $tmp = $this->User->find('all');
    }
    $user_names = [];
    foreach ($tmp as $user) {
      if (!isset($user['Profile']['id']) || (isset($options['username']) && $options['name'] == true)) {
        $user_names[$user['User']['id']] = $user['User']['username'];
      } else {
        $user_names[$user['User']['id']] = $user['Profile']['first_name'] . ' ' . $user['Profile']['last_name'];
        if (isset($options['birthday']) && $options['birthday'] == true)
          $user_names[$user['User']['id']] .= ' (' . $user['Profile']['birthday'] . ')';
      }
    }
    return $user_names;
  }

  public function getUserName($id = null, $options = null) {
    $this->loadModel('User');
    $this->User->contain('Profile');
    $user = $this->User->findById($id);
    $user_name = [];
    if (!isset($user['Profile']['id']) || (isset($options['username']) && $options['name'] == true)) {
      $user_name[$user['User']['id']] = $user['User']['username'];
    } else {
      $user_name[$user['User']['id']] = $user['Profile']['first_name'] . ' ' . $user['Profile']['last_name'];
      if (isset($options['birthday']) && $options['birthday'] == true)
        $user_name[$user['User']['id']] .= ' (' . $user['Profile']['birthday'] . ')';
    }
    return $user_name;
  }


/*
// TODO implement helper for function array_has_key_val()
//      and remove this function from app/Config/bootstrap.php

  public $helper = array('array_has_key_val');

  function array_has_key_val($elements, $key, $val) {
    if (is_array($elements)) {
      foreach($elements as $element)
        if($element[$key] == $val)
          return true;
    }
    return false;
  }
*/
}
