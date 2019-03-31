<?php
// app/Model/Group.php
class Group extends AppModel {

  public $actsAs = array('Containable');

  public $belongsTo = array('Kind', 'Privileg', 'Storage');

  public $hasMany = array('Event');

  public $hasAndBelongsToMany = array('Membership');


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

    // Save attached picture
    if($this->data[$this->name]['file']['name']) {
      // resize the uploaded image
      $Im = new ImageProcess($this->data[$this->name]['file']['tmp_name']);
      $Im->landscape(Configure::read('image_landscape_geometry.width'), Configure::read('image_landscape_geometry.height'));
      $Im->saveProcessedImage($this->data[$this->name]['file']['tmp_name']);
      unset($Im);

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

