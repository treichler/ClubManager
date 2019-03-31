<?php
// app/Model/Musicsheet.php
class Musicsheet extends AppModel {

  public $actsAs = array('Containable');

  public $hasMany = array('Sheet');

  public $belongsTo = array('Publisher');

  public $hasAndBelongsToMany = array(
    'Composer' => array(
      'className'             => 'Profile',
      'joinTable'             => 'composers_musicsheets',
      'foreignKey'            => 'musicsheet_id',
      'associationForeignKey' => 'composer_id',
      'unique'                => true,
    ),
    'Arranger' => array(
      'className'             => 'Profile',
      'joinTable'             => 'arrangers_musicsheets',
      'foreignKey'            => 'musicsheet_id',
      'associationForeignKey' => 'arranger_id',
      'unique'                => true,
    )
  );

  public $validate = array(
    'title' => array(
      'required' => array(
        'rule' => array('notBlank'),
        'message' => 'Bitte Titel des Musikstücks eingeben.'
      ),
/*
      'unique' => array(
        'rule' => 'isUnique',
        'message' => 'Musikstück ist bereits vorhanden.'
      )
*/
    ),
/* 
   'composer_id' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Komponist auswählen.'
    ),
    'arranger_id' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Arrangeur auswählen.'
    ),
    'publisher_id' => array(
      'rule' => 'notBlank',
      'message' => 'Bitte Verlag auswählen.'
    ),
*/
  );


  public function beforeSave($options = array()) {

    // handle new entries
    if (isset($this->data['new'])) {

      // handle new publisher
      if (isset($this->data['new']['Publisher'])) {
        $publisher = $this->Publisher->find('first', array(
          'conditions' => array('Publisher.name' => $this->data['new']['Publisher']['name'])
        ));
        if (isset($publisher['Publisher']['id'])) {
          // use existing publisher
          $this->data['Musicsheet']['publisher_id'] = $publisher['Publisher']['id'];
        } else {
          // create new publisher
          $this->Publisher->create();
          if($this->Publisher->save($this->data['new']))
            $this->data['Musicsheet']['publisher_id'] = $this->Publisher->id;
        }
      } // END: handle new publisher

      // handle new arrangers
      if (isset($this->data['new']['Arranger'])) {
        // XXX Workarround: Use the Profile-Model since it's not possible to save data as Arranger.
        $Profile = ClassRegistry::init('Profile');
        foreach ($this->data['new']['Arranger'] as $arranger) {
          $tmp = CakeText::tokenize($arranger['name']);
          if ((count($tmp) < 2) || (count($tmp) > 3))
            continue;
          $old_arranger = $this->Arranger->find('first', array('conditions' => array(
            'Arranger.last_name'  => $tmp[0],
            'Arranger.first_name' => $tmp[1],
          )));
          if (isset($old_arranger['Arranger']['id'])) {
            // use existing arrangers
            $this->data['Arranger']['Arranger'][] = $old_arranger['Arranger']['id'];
            if ($old_arranger['Arranger']['is_arranger'] != true) {
              $Profile->id = $old_arranger['Arranger']['id'];
              $Profile->saveField('is_arranger', true);
            }
          } else {
            // create new arrangers
            $new_arranger = array('Arranger' => array(
              'last_name'   => $tmp[0],
              'first_name'  => $tmp[1],
              'is_arranger' => true,
            ));
            if(count($tmp) >= 3)
              $new_arranger['Arranger']['birthday'] = $tmp[2];
            $params = array('validate' => false, 'callbacks' => false, 'fieldList' => array(
                            'last_name', 'first_name', 'is_arranger', 'birthday'));
            $Profile->create();
            if ($Profile->save(array('Profile' => $new_arranger['Arranger']), $params))
              $this->data['Arranger']['Arranger'][] = $Profile->id;
          }
        }
      } // END: handle new arrangers

      // handle new composers
      if (isset($this->data['new']['Composer'])) {
        // XXX Workarround: Use the Profile-Model since it's not possible to save data as Composer.
        $Profile = ClassRegistry::init('Profile');
        foreach ($this->data['new']['Composer'] as $composer) {
          $tmp = CakeText::tokenize($composer['name']);
          if ((count($tmp) < 2) || (count($tmp) > 3))
            continue;
          $old_composer = $this->Composer->find('first', array('conditions' => array(
            'Composer.last_name'  => $tmp[0],
            'Composer.first_name' => $tmp[1],
          )));
          if (isset($old_composer['Composer']['id'])) {
            // use existing composers
            $this->data['Composer']['Composer'][] = $old_composer['Composer']['id'];
            if ($old_composer['Composer']['is_composer'] != true) {
              $Profile->id = $old_composer['Composer']['id'];
              $Profile->saveField('is_composer', true);
            }
          } else {
            // create new composers
            $new_composer = array('Composer' => array(
              'last_name'   => $tmp[0],
              'first_name'  => $tmp[1],
              'is_composer' => true,
            ));
            if(count($tmp) >= 3)
              $new_composer['Composer']['birthday'] = $tmp[2];
            $params = array('validate' => false, 'callbacks' => false, 'fieldList' => array(
                            'last_name', 'first_name', 'is_composer', 'birthday'));
            $Profile->create();
            if ($Profile->save(array('Profile' => $new_composer['Composer']), $params))
              $this->data['Composer']['Composer'][] = $Profile->id;
          }
        }
      } // END: handle new composers

    } // END: handle new entries
  }

}

