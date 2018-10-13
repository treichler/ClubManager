<?php
// app/Model/Track.php
class Track extends AppModel {

  public $actsAs = array('Containable');

  public $belongsTo = array('Event', 'Musicsheet');


  public function afterFind($results, $primary = false) {
    foreach ($results as $key => $val) {
      $created = new DateTime($results[$key]['Track']['created']);
      $results[$key]['Track']['timestamp'] = $created->format('d.m. H:i');
    }
    return $results;
  }

}

