<?php
// app/Model/Vote.php
class Vote extends AppModel {

  public $actsAs = array('Containable');

  public $belongsTo = array('User');

//  public $hasMany = array('BlogsVote', 'CommentsVote', 'GalleriesVote', 'PhotosVote');

}

