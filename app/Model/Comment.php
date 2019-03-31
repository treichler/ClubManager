<?php
// app/Model/Comment.php
class Comment extends AppModel {

  public $actsAs = array('Containable');

  public function isOwnedBy($comment, $user) {
    return ($this->field('id', array('id' => $comment, 'user_id' => $user)) === $comment);
  }


  public $belongsTo = array(
    'Blog' => array('counterCache' => true),
    'User' => array('counterCache' => true)
  );

  public $validate = array(
    'body' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Kommentar eingeben.'
    )
  );

}

