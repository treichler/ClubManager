<?php
// app/Model/Group.php
class Group extends AppModel {

  public $actsAs = array('Containable');

  public $belongsTo = array('Kind', 'Privileg', 'Storage');

  public $hasAndBelongsToMany = array(
    'Event',
    'Membership'
  );


  public $validate = array(
    'kind_id' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Bitte Art der Gruppe auswählen',
      )
    )
  );


  public function beforeSave($options = array()) {
    // keep privilegs up to date
    $privileg = [];
    if (isset($this->data[$this->name]['privileg_id']))
      $privileg = $this->Privileg->findById($this->data[$this->name]['privileg_id']);
    if ($privileg == []) {
      $this->Privileg->create();
      $this->Privileg->save(array('name' => $this->data[$this->name]['name']));
      $privileg['Privileg']['id'] = $this->Privileg->id;
    } else {
      $this->Privileg->save(array('id' => $privileg['Privileg']['id'],
                                  'name' => $this->data[$this->name]['name']));
    }
    $this->data[$this->name]['privileg_id'] = $privileg['Privileg']['id'];

    // keep memberships
    if (!isset($this->data['Membership']['Membership']) && 
        isset($this->data['Membership'])) {
      $memberships = array('Membership' => []);
      foreach ($this->data['Membership'] as $membership) {
        $memberships['Membership'][] = $membership['id'];
      }
      $this->data['Membership'] = $memberships;
    } elseif (isset($this->data['Membership']['Membership'])) {
      // clean up array
      $this->data['Membership'] = array('Membership' => $this->data['Membership']['Membership']);
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
    return true;
  }


  public function beforeDelete($cascade = true) {
    $group = $this->read();
    $ret = true;

    // delete associated storage
    if ($group[$this->name]['storage_id'] &&
        !$this->Storage->delete($group[$this->name]['storage_id']))
      $ret = false;

    // delete associated privileg
    if (!$this->Privileg->delete($group[$this->name]['privileg_id']))
      $ret = false;

    // delete assotiated events
    $this->Event->deleteAll(array('Event.group_id' => $this->id), false);

    // delete membership's join-table entries
    $this->GroupsMembership->deleteAll(array('GroupsMembership.group_id' => $this->id), false);

    return $ret;
  }

}

