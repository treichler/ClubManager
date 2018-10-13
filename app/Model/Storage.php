<?php
// app/Model/Storage.php
class Storage extends AppModel {

  public $hasOne = array(
    'Blog',
    'Group',
    'Upload',
/*
    'Original' => array(
      'className' => 'Photo',
      'foreignKey' => 'original_id'
    ),
    'Marked' => array(
      'className' => 'Photo',
      'foreignKey' => 'marked_id'
    ),
    'Thumbnail' => array(
      'className' => 'Photo',
      'foreignKey' => 'thumbnail_id'
    ),
*/
    'Profile'
  );


  public function beforeSave($options = array()) {
    $file = $this->data[$this->name]['file'];
    if (isset($file['temporary_file']) || $file['error'] === UPLOAD_ERR_OK) {
      $this->data[$this->name]['name'] = substr($file['name'], 0, strrpos($file['name'], '.'));
      $this->data[$this->name]['extension'] = substr(strrchr($file['name'], '.'), 1);
      $this->data[$this->name]['size'] = $file['size'];
      $this->data[$this->name]['type'] = $file['type'];

// TODO if possible get the name of the calling model to generate 'folder'
//      $this->data[$this->name]['folder'] = ...

      // use unique id for the filename to avoid collissions
      $uuid = String::uuid();
      $storage = [];
      if (isset($this->data[$this->name]['id']) && $this->data[$this->name]['id'])
        $storage = $this->findById($this->data[$this->name]['id']);
      if (isset($storage['Storage']['uuid']) && $storage['Storage']['uuid'])
        $uuid = $storage[$this->name]['uuid'];
      else
        $this->data[$this->name]['uuid'] = $uuid;

      // save file to filesystem
      // destination: storage/[folder]/[uuid]
      if(isset($file['temporary_file'])) {
        rename($file['tmp_name'], Configure::read('CMSystem.upload_dir') . DS .
                           $this->data[$this->name]['folder'] . DS . $uuid);
      } else {
        move_uploaded_file($file['tmp_name'], Configure::read('CMSystem.upload_dir') . DS .
                           $this->data[$this->name]['folder'] . DS . $uuid);
      }
    } else {
      return false;
    }
    return true;
  }


  public function afterFind($results, $primary = false) {
    foreach ($results as $key => $val) {
      if (isset($results[$key][$this->alias]['uuid'])) {
        $results[$key][$this->alias]['file'] = array(
          'id' => $results[$key][$this->alias]['uuid'],
          'name' => $results[$key][$this->alias]['name'],
//          'name' => $results[$key][$this->alias]['name'] . '.' . $results[$key][$this->alias]['extension'],
          'extension' => $results[$key][$this->alias]['extension'],
          'path' => Configure::read('CMSystem.upload_dir') . DS . $results[$key][$this->alias]['folder'] . DS,
          'download' => true,
        );
      }
    }
    return $results;
  }


//-----------------------------------------------------------------------------
//                             DELETE FILE
//-----------------------------------------------------------------------------
  public function beforeDelete($cascade = true) {
    $storage = $this->Read();
    $this->filepath = Configure::read('CMSystem.upload_dir') . DS . $storage['Storage']['folder'] . DS . $storage['Storage']['uuid'];
  }

  public function afterDelete() {
    App::uses('File', 'Utility');
    // new File(string $path, boolean $create = false, integer $mode = 493) 
    $file = new File($this->filepath, false, 0777);
    $file->delete();
  }
}

