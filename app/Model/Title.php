<?php
class Title extends AppModel {

  public $hasAndBelongsToMany = array(
    'Profile' => array(
      'className'              => 'Profile',
      'joinTable'              => 'profiles_titles',
      'foreignKey'             => 'title_id',
      'associationForeignKey'  => 'profile_id',
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
    'name' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Bitte eine Bezeichnung für den Titel eingeben'
      )
    ),
    'acronym' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Bitte eine Abkürzung für den Titel eingeben'
      )
    ),
    'placement' => array(
      'rule'    => array('range', -21, 21),
      'message' => 'Bitte eine Zahl zwischen -20 und 20 eingeben'
    )
  );

}

