<?php
// app/Model/Sheet.php
class Sheet extends AppModel {

  public $actsAs = array('Containable');

  public $belongsTo = array('Book', 'Musicsheet');

  public $validate = array(
    'page' => array(
      'rule' => array('range', 0, 9999),
      'message' => 'Bitte eine Seite im Bereich 0..9999 eingeben'
    )
  );

}

