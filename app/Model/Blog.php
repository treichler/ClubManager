<?php
// app/Model/Blog.php
class Blog extends AppModel {

  public $actsAs = array('Containable', 'Vote');

  public function isOwnedBy($blog, $user) {
    return ($this->field('id', array('id' => $blog, 'user_id' => $user)) === $blog);
  }


  public $belongsTo = array(
    'Storage',
    'User' => array('counterCache' => true)
  );

  public $hasMany = array('Comment' => array('dependent' => true));

  public $hasAndBelongsToMany = array('Tag', 'Vote');


  public $validate = array(
    'title' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Titel eingeben.'
    ),
/*
    'body' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Text eingeben.'
    ),
*/
/*
    // FIXME needs cakephp 2.3
    'file' => array(
      'size' => array(
        'rule' => array('fileSize', '<=', '1MB'),
        'message' => 'Die Datei muss kleiner als 1MB sein.'
      ),
      'mime' => array(
        'rule' => array('mimeType', array('image/gif', 'image/jpeg', 'image/png')),
        'message' => 'Nur Bilder vom Typ *.gif, *.jpeg und *.png sind erlaubt.'
      )
    )
*/
  );

/*
  public function vote($data) {
    $id = $data[$this->alias]['id'];
    $vote_id = $data['Vote']['id'];
    $val = $data[$this->alias]['vote'];
    $join_table_name = Inflector::pluralize($this->alias) . 'Vote';
    $join_table_this_id = strtolower($this->alias) . '_id';

// check if already voted -> return
    $join_table = $this->{$join_table_name}->find('first', array(
      'conditions' => array($join_table_this_id => $id, 'vote_id' => $vote_id)
    ));
    if (!empty($join_table))
      return array('state' => 'false', 'message' => 'Bereits abgestimmt.');

// check if val == 0 -> return
    if ($val == 0)
      return array('state' => 'false', 'message' => 'Es wurde keine Stimme abgegeben.');

// TODO lock the table
    $this->id = $id;
    $table = $this->read();

// count up sum
    $table[$this->alias]['sum']++;

// if val > 0 count up good
    if ($val > 0)
      $table[$this->alias]['good']++;

// if val < 0 count up bad
    if ($val < 0)
      $table[$this->alias]['bad']++;

// calculate median
    $table[$this->alias]['median'] = ($table[$this->alias]['good'] - $table[$this->alias]['bad']) / $table[$this->alias]['sum'];

    $this->create();
    if ($this->save($table)) {
      $this->{$join_table_name}->create();
      if ($this->{$join_table_name}->save(array($join_table_this_id => $id, 'vote_id' => $vote_id))) {
        $this->Vote->id = $vote_id;
        $vote = $this->Vote->read();
        $vote['Vote'][Inflector::tableize($this->alias) . '_votes']++;
        $this->Vote->create();
        if ($this->Vote->save($vote)) {
// TODO unlock the table
          return array('state' => 'true', 'data' => $table, 'message' => 'Stimme wurde gewertet.');
        }
      }
    }

// TODO unlock the table

    return array('state' => 'false', 'message' => 'Stimme konnte nicht gespeichert werden.');
  }
*/

  // allow html tags in body according to whitelist
  public function afterFind($results, $primary = false) {
//    $config = HTMLPurifier_Config::createDefault();
//    $purifier = new HTMLPurifier($config);
    foreach ($results as $key => $val) {
      if (isset($val['Blog']['body'])) {
//        $result[$key]['Blog']['body'] = $purifier->purify($val['Blog']['body']);
        $results[$key]['Blog']['body'] = strip_tags($val['Blog']['body'], '<h3><h4><h5><h6><p><strong><em><a><ul><ol><li><blockquote><iframe>');
      }
    }
    return $results;
  }

  public function beforeSave($options = array()) {
    // Save the comma separated list as tags
    if(isset($this->data[$this->name]['temp_tags'])) {
//      $tags = explode(",", $this->data[$this->name]['temp_tags']);
      $tags = CakeText::tokenize($this->data[$this->name]['temp_tags']);
      unset($this->data[$this->name]['temp_tags']);
      if (!empty($tags)) {
        foreach($tags as $tag) {
          $tag = trim($tag);
          if (!empty($tag)) {
            // Check if id allready exists
            $id = $this->Tag->findByName($tag);
            if(empty($id['Tag']['id'])) {
              // save new tag
              $this->Tag->create();
              $this->Tag->save(array('name' => $tag));
              $id['Tag']['id'] = $this->Tag->id;
            }
            $this->data['Tag']['Tag'][] = $id['Tag']['id'];
          }
        }
      }
    }

    // Prepare attached picture
    if(isset($this->data[$this->name]['file']['name']) && $this->data[$this->name]['file']['name']) {
      $file = $this->data[$this->name]['file'];

      // resize the uploaded image
      $Im = new ImageProcess($file['tmp_name']);
      $Im->landscape(Configure::read('image_landscape_geometry.width'), Configure::read('image_landscape_geometry.height'));
      $Im->saveProcessedImage($file['tmp_name']);
      unset($Im);
    } elseif (isset($this->data[$this->name]['file_resized'])) {
      // Save client side resized image:
      // create temporary image file from base64 data
      $temp_file = tempnam(Configure::read('CMSystem.tmp_dir'), 'temp_file');
      $fh = fopen($temp_file, 'wb');
      stream_filter_append($fh, 'convert.base64-decode');
      fwrite($fh, $this->data[$this->name]['file_resized']['data']);
      fclose($fh);
      chmod($temp_file, 0644);

      $file = array(
        'tmp_name'       => $temp_file,
        'name'           => $this->data[$this->name]['file_resized']['name'],
        'error'          => UPLOAD_ERR_OK,
        'size'           => $this->data[$this->name]['file_resized']['size'],
        'type'           => $this->data[$this->name]['file_resized']['type'],
        'temporary_file' => true
      );
    }
    // Save attached picture
    if(isset($file)) {
      $storage = [];
      if (isset($this->data[$this->name]['storage_id']))
        $storage = $this->Storage->findById($this->data[$this->name]['storage_id']);
      if ($storage == []) {
        // create new storage
        $this->Storage->create();
        $this->Storage->save(array('file' => $file,
                                   'folder' => $this->name));
        $storage['Storage']['id'] = $this->Storage->id;
      } else {
        $this->Storage->save(array('id' => $storage['Storage']['id'],
                                   'folder' => $this->name,
                                   'file' => $file));
      }
      $this->data[$this->name]['storage_id'] = $storage['Storage']['id'];
    }

    parent::beforeSave();
    return true;
  }

  public function beforeDelete($cascade = true) {
    $blog = $this->read();

    // delete associated storage
    if ($blog[$this->name]['storage_id'] &&
        !$this->Storage->delete($blog[$this->name]['storage_id']))
      return false;

    // delete assotiated comments
    if (!$this->Comment->deleteAll(array('Comment.blog_id' => $this->id), false))
      return false;

    // delete assotiations to tags but keep tags
    if (!$this->BlogsTag->deleteAll(array('BlogsTag.blog_id' => $this->id), false))
      return false;

    return true;
  }
}

