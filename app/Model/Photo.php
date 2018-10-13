<?php
// app/Model/Photo.php
class Photo extends AppModel {

  public function isOwnedBy($photo, $user) {
    return ($this->field('id', array('id' => $photo, 'user_id' => $user)) === $photo);
  }

  public $belongsTo = array(
    'Gallery' => array('counterCache' => true),
    'User' => array('counterCache' => true),
    'Storage' => array(
      'className' => 'Storage',
      'foreignKey' => 'original_id'
    ),
/*
    'OriginalStorage' => array(
      'className' => 'Storage',
      'foreignKey' => 'orig_storage_id'
    ),
*/
    'Marked' => array(
      'className' => 'Storage',
      'foreignKey' => 'marked_id'
    ),
    'Thumbnail' => array(
      'className' => 'Storage',
      'foreignKey' => 'thumbnail_id'
    )
  );


  public function beforeSave($options = array()) {
    // Save attached picture
    if(isset($this->data[$this->name]['file']) && $this->data[$this->name]['file']['name']) {
      $file = $this->data[$this->name]['file'];
      $name = substr($file['name'], 0, strrpos($file['name'], '.'));

      $Im = new ImageProcess($file['tmp_name']);

      // Configure::read('photo_geometry.width')
      // Configure::read('photo_geometry.height')


      /*************************/
      /*   Process Watermark   */
      /*************************/

      $marked_type = 'image/jpeg';
      $marked_extension = 'jpg';

      // create watermarked image
      $Im->watermark();
      $marked_file = tempnam(Configure::read('CMSystem.tmp_dir'), 'marked');
      $Im->saveProcessedImage($marked_file, $marked_type);
      chmod($marked_file, 0644);

      $marked_file_data = array(
        'name'  => $name . '-marked.' . $marked_extension,
        'type'  => $marked_type,
        'size'  => filesize($marked_file),
        'tmp_name' => $marked_file,
        'temporary_file' => true
      );


      /*************************/
      /*   Process Thumbnail   */
      /*************************/

      $thumb_type = 'image/jpeg';
      $thumb_extension = 'jpg';

      // create thumbnail image
      $Im->thumbnail(Configure::read('photo_geometry.thumbnail_width'),Configure::read('photo_geometry.thumbnail_height'));
      $thumb_file = tempnam(Configure::read('CMSystem.tmp_dir'), 'thumb');
      $Im->saveProcessedImage($thumb_file, $thumb_type);
      chmod($thumb_file, 0644);

      $thumb_file_data = array(
        'name'  => $name . '-thumb.' . $thumb_extension,
        'type'  => $thumb_type,
        'size'  => filesize($thumb_file),
        'tmp_name' => $thumb_file,
        'temporary_file' => true
      );

      unset($Im);


      /*************************/
      /*  save original image  */
      /*************************/

      $storage = [];
      if (isset($this->data[$this->name]['original_id']))
        $storage = $this->Storage->findById($this->data[$this->name]['original_id']);
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
      $this->data[$this->name]['original_id'] = $storage['Storage']['id'];


      /*************************/
      /*   save marked image   */
      /*************************/

      $storage = [];
      if (isset($this->data[$this->name]['marked_id']))
        $storage = $this->Storage->findById($this->data[$this->name]['marked_id']);
      if ($storage == []) {
        // create new storage
        $this->Storage->create();
        $this->Storage->save(array('file' => $marked_file_data,
                                   'folder' => $this->name));
        $storage['Storage']['id'] = $this->Storage->id;
      } else {
        $this->Storage->save(array('id' => $storage['Storage']['id'],
                                   'folder' => $this->name,
                                   'file' => $marked_file_data));
      }
      $this->data[$this->name]['marked_id'] = $storage['Storage']['id'];


      /**************************/
      /*  save thumbnail image  */
      /**************************/

      $storage = [];
      if (isset($this->data[$this->name]['thumbnail_id']))
        $storage = $this->Storage->findById($this->data[$this->name]['thumbnail_id']);
      if ($storage == []) {
        // create new storage
        $this->Storage->create();
        $this->Storage->save(array('file' => $thumb_file_data,
                                   'folder' => $this->name));
        $storage['Storage']['id'] = $this->Storage->id;
      } else {
        $this->Storage->save(array('id' => $storage['Storage']['id'],
                                   'folder' => $this->name,
                                   'file' => $thumb_file_data));
      }
      $this->data[$this->name]['thumbnail_id'] = $storage['Storage']['id'];
    }
    parent::beforeSave();
    return true;
  }

  public function beforeDelete($cascade = true) {
    $photo = $this->read();

    // delete associated storages
    if ($photo[$this->name]['original_id'] &&
        !$this->Storage->delete($photo[$this->name]['original_id']))
      return false;
    if ($photo[$this->name]['marked_id'] &&
        !$this->Storage->delete($photo[$this->name]['marked_id']))
      return false;
    if ($photo[$this->name]['thumbnail_id'] &&
        !$this->Storage->delete($photo[$this->name]['thumbnail_id']))
      return false;
    return true;
  }

}

