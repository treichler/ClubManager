<?php
// app/Model/Book.php
class Book extends AppModel {

  public $actsAs = array('Containable');

  public $hasMany = array('Sheet');

  public function beforeDelete($cascade = true) {
    // delete assotiated sheets
    $this->Sheet->deleteAll(array('Sheet.book_id' => $this->id), false);
    return true;
  }

}

