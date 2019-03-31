<?php
// app/Model/Upload.php
class Upload extends AppModel {

  public $actsAs = array('Containable');

  public function isOwnedBy($upload, $user) {
    return ($this->field('id', array('id' => $upload, 'user_id' => $user)) === $upload);
  }

  public $belongsTo = array(
    'Storage',
    'Type',
    'User' => array('counterCache' => true)
  );

// TODO evaluate MIME type according to preselected file type
/*
  public $validate = array(
    'file' => array(

      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Keine Datei vorhanden.'
      ),


      'mimeType' => array(
        'rule'    => array('mimeType', array('image/gif')),
        'message' => 'Ungültiger Dateityp.',
//        'allowEmpty' => true
        'required' => 'create'
      ),

      'checkFile' => array(
      )
    ),
  );
*/


  public function beforeSave($options = array()) {
    // Save attached file
    if(isset($this->data[$this->name]['file']['name']) && $this->data[$this->name]['file']['name']) {
      $storage = [];
      if (isset($this->data[$this->name]['storage_id']))
        $storage = $this->Storage->findById($this->data[$this->name]['storage_id']);
      if ($storage == []) {
        // create new storage
        $this->Storage->create();
        $this->Storage->save(array('file' => $this->data[$this->name]['file'],
                                   'folder' => $this->name));
        $storage['Storage']['id'] = $this->Storage->id;
      } else {
        $this->Storage->save(array('id' => $storage['Storage']['id'],
                                   'folder' => $this->name,
                                   'file' => $this->data[$this->name]['file']));
      }
      $this->data[$this->name]['storage_id'] = $storage['Storage']['id'];
    }

    parent::beforeSave();
    return true;
  }

  public function beforeDelete($cascade = true) {
    $upload = $this->read();

    // delete associated storage
    if ($upload[$this->name]['storage_id'] &&
        !$this->Storage->delete($upload[$this->name]['storage_id']))
      return false;

    return true;
  }

}

