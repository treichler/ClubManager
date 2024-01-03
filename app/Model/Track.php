<?php
// app/Model/Track.php
class Track extends AppModel {

  public $actsAs = array('Containable');

  public $belongsTo = array('Event', 'Musicsheet');


  public function afterFind($results, $primary = false) {
    foreach ($results as $key => $val) {
      $timestamp = new DateTime($results[$key]['Track']['modified']);
      $results[$key]['Track']['timestamp'] = $timestamp->format('d.m.Y H:i');
    }
    return $results;
  }

}

