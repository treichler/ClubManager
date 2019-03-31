<?php
// app/Model/Profile.php
class Profile extends AppModel {

  public $actsAs = array('Containable');

  public $hasOne = array('Membership');

  public $belongsTo = array('Salutation', 'Storage', 'User');

  public $hasAndBelongsToMany = array(
    'Title',
    'Composer' => array(
      'className'             => 'Musicsheet',
      'joinTable'             => 'composers_musicsheets',
      'foreignKey'            => 'musicsheet_id',
      'associationForeignKey' => 'composer_id',
      'unique'                => true,
    ),
    'Arranger' => array(
      'className'             => 'Musicsheet',
      'joinTable'             => 'arrangers_musicsheets',
      'foreignKey'            => 'musicsheet_id',
      'associationForeignKey' => 'arranger_id',
      'unique'                => true,
    )
  );


  public $validate = array(
    'salutation_id' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Bitte Anrede auswählen',
      )
    ),
    'first_name' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Bitte Vorname(n) eintragen'
      )
    ),
    'last_name' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Bitte Familienname eintragen'
      )
    ),
/*
    'birthday' => array(
      'required' => array(
        'rule' => array('notBlank'),
// TODO show format of birthday in message
//      validate format of birthday
        'message' => 'Bitte Geburtstag eintragen'
      )
    ),
*/
    // validations for phone numbers
    'phone_private' => array(
      'allowEmpty' => true,
      'rule' => array('custom', Profile::PHONE_REGEX),
      'message' => Profile::PHONE_MSG
    ),
    'phone_mobile' => array(
      'allowEmpty' => true,
      'rule' => array('custom', Profile::PHONE_REGEX),
      'message' => Profile::PHONE_MSG
    ),
    'phone_office' => array(
      'allowEmpty' => true,
      'rule' => array('custom', Profile::PHONE_REGEX),
      'message' => Profile::PHONE_MSG
    ),
    'phone_mobile_opt' => array(
      'allowEmpty' => true,
      'rule' => array('custom', Profile::PHONE_REGEX),
      'message' => Profile::PHONE_MSG
    ),
    'email_opt' => array(
      'allowEmpty' => true,
      'rule' => array('custom', Profile::EMAIL_REGEX),
      'message' => Profile::EMAIL_MSG
    )
  );

//  const PHONE_REGEX = '/^\+[1-9]\d{0,2}\s[1-9]\d{0,3}\s[1-9]\d{3,8}([- ]\d{1,5})?$/';
  const PHONE_REGEX = '/^\+[1-9]\d{0,2}\s[1-9]\d{0,4}\s[1-9]\d{1,8}(\s\d{1,5})?$/';
  const PHONE_MSG = 'Bitte die Telefonnummer im gültigen Format eintragen, oder das Feld frei lassen.';
  const EMAIL_REGEX = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
  const EMAIL_MSG = 'Bitte gültige E-Mail Adresse eintragen, oder das Feld frei lassen.';


  public function beforeSave($options = array()) {
    // Save attached picture
//    if($this->data[$this->name]['file']['name']) {
    if(isset($this->data[$this->name]['file']['name']) && $this->data[$this->name]['file']['name']) {
      $file = $this->data[$this->name]['file'];
      $name = substr($file['name'], 0, strrpos($file['name'], '.'));

      // create image process
      $Im = new ImageProcess($file['tmp_name']);
/*
      $Im->portrait();
      $Im->saveProcessedImage($this->data[$this->name]['file']['tmp_name']);
      unset($Im);
      $this->data[$this->name]['file']['size'] = filesize($this->data[$this->name]['file']['tmp_name']);
*/


      // resize the uploaded image
      $portrait_type = 'image/jpeg';
      $portrait_extension = 'jpg';
      $Im->portrait();
      $portrait_file = tempnam(Configure::read('CMSystem.tmp_dir'), 'portrait');
      $Im->saveProcessedImage($portrait_file, $portrait_type);
      chmod($portrait_file, 0644);
      $portrait_file_data = array(
        'name'  => $name . '.' . $portrait_extension,
        'type'  => $portrait_type,
        'size'  => filesize($portrait_file),
        'tmp_name' => $portrait_file,
        'temporary_file' => true
      );


      $storage = [];
      if (isset($this->data[$this->name]['storage_id']))
        $storage = $this->Storage->findById($this->data[$this->name]['storage_id']);
      if ($storage == []) {
        // create new storage
        $this->Storage->create();
        $this->Storage->save(array('file' => $portrait_file_data,
                                   'folder' => $this->name));
        $storage['Storage']['id'] = $this->Storage->id;
      } else {
        $this->Storage->save(array('id' => $storage['Storage']['id'],
                                   'folder' => $this->name,
                                   'file' => $portrait_file_data));
      }
      $this->data[$this->name]['storage_id'] = $storage['Storage']['id'];
    }

    parent::beforeSave();
    return true;
  }

  public function beforeDelete($cascade = true) {
    $profile = $this->read();

    // keep if Membership or Musicsheet exists
    if ((isset($profile['Membership']['id']) && $profile['Membership']['id']) ||
        (isset($profile['Composer'][0]['id']) && $profile['Composer'][0]['id']) ||
        (isset($profile['Arranger'][0]['id']) && $profile['Arranger'][0]['id']))
      return false;

    // delete associated storage
    if ($profile[$this->name]['storage_id'] &&
        !$this->Storage->delete($profile[$this->name]['storage_id']))
      return false;

    // delete assotiations to titles but keep titles
    $this->ProfilesTitle->deleteAll(array('ProfilesTitle.profile_id' => $this->id), false);

    return true;
  }

}

