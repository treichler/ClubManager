<?php
// app/Model/Gallery.php
class Gallery extends AppModel {

  public $actsAs = array('Containable');

  public function isOwnedBy($gallery, $user) {
    return ($this->field('id', array('id' => $gallery, 'user_id' => $user)) === $gallery);
  }

  public $hasMany = array('Photo');

  public $belongsTo = array('User' => array('counterCache' => true));


  public function beforeDelete($cascade = true) {
    // delete assotiated photos
    if (!$this->Photo->deleteAll(array('Photo.gallery_id' => $this->id), false))
      return false;

    return true;
  }

}

