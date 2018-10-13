<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {

  public function formatName($profile, $param = null) {
    $ret = '';
    if (isset($param['salutation']) && $param['salutation'] && isset($profile['Salutation']['name'])) {
      $ret .= $profile['Salutation']['name'] . ' ';
    }
    $t = array('before' => '', 'after' => '');
    if (isset($param['title']) && $param['title'] && isset($profile['Title'])) {
      $t = $this -> splitTitles($profile['Title']);
    }
    if ($t['before']) {
      $ret .= $t['before'] . ' ';
    }
    $ret .= $profile['first_name'] . ' ' . $profile['last_name'];
    if ($t['after']) {
      $ret .= ', ' . $t['after'];
    }
    return $ret;
  }

  public function splitTitles($titles) {
    // sort titles by 'placement'
    usort($titles, function($a, $b) {
      return $a['placement'] - $b['placement'];
    });

    // separate titles to those which have to be before respectively after the name
    $before = array();
    $after = array();
    foreach ($titles as $title) {
      if ($title['placement'] < 0)
        $before[] = $title['acronym'];
      else
        $after[] = $title['acronym'];
    }

    return array('before' => implode(' ', $before), 'after' => implode(' ', $after));
  }

  public function showBoolean($val, $par = null) {
    $cross = false;
    if (isset($par['cross']) && $par['cross'])
      $cross = true;
    $bold = false;
    if (isset($par['bold']) && $par['bold'])
      $bold = true;
    if ($bold) {
      if ($val) {
        return '&#10004;';
      } else {
        if ($cross)
          return '&#10008;';
      }
    } else {
      if ($val) {
        return '&#10003;';
      } else {
        if ($cross)
          return '&#10007;';
      }
    }
    return '';
  }

  public function hasPrivileg($user, $privileg) {
    if (isset($user['Privileg'])) {
      for ($i = count($privileg); $i--; $i) {
        if (array_has_key_val($user['Privileg'], 'name', $privileg[$i]))
          return true;
      }
    }
    return false;
  }

  public function isGroupAdmin($user) {
    if (isset($user['Privileg'])) {
      foreach ($user['Privileg'] as $privileg) {
        if ($privileg['id'] > 100) {
          return true;
        }
      }
    }
    return false;
  }

  public function isMember($user) {
    if (isset($user['State']) && $user['State']['is_member'])
      return true;
    return false;
  }

  public function hasAvailability($user) {
    if (isset($user['State']) && $user['State']['set_availability'])
      return true;
    return false;
  }

  public function getDateTime($str, $par = null) {
    $date_time = new DateTime($str);
    if (isset($par)) {
      if ($par['year'])
        return $this->days[$date_time->format('w')] . ' ' . $date_time->format('d.m.Y H:i');
    }
    return $this->days[$date_time->format('w')] . ' ' . $date_time->format('d.m. H:i');
  }

  public function getDate($str, $par = null) {
    $date_time = new DateTime($str);
    if (isset($par)) {
      $ret = $this->days[$date_time->format('w')] . ' ';
      if (isset($par['day']) && $par['day'] == false)
        $ret = '';
      if (isset($par['year']) && $par['year'])
        $ret .= $date_time->format('d.m.Y');
      else
        $ret .= $date_time->format('d.m.');
      return $ret;
    }
    return $this->days[$date_time->format('w')] . ' ' . $date_time->format('d.m.');
  }

  public function getTime($str, $par = null) {
    $date_time = new DateTime($str);
    return $date_time->format('H:i');
  }

  public function getTimeStamp($str) {
    $date_time = new DateTime($str);
    return $date_time->format('Y-m-d') . 'T' . $date_time->format('H:i:s');
  }

  public function isSameDay($event) {
    $start = new DateTime($event['Event']['start']);
    $stop = new DateTime($event['Event']['stop']);
    return ($start->format('Ymd') === $stop->format('Ymd'));
  }

  public function getSize($size = null) {
    $units = array('B', 'kB', 'MB', 'GB', 'TB');
    if (!$size)
      $size = 0;
    for ($i = 0; $i < count($units); $i++) {
      if (mb_strlen($size) <= 3)
        return $size . ' ' . $units[$i];
      $size = (round($size / 1000));
    }
    return 'too big';
  }

  public $months = array(
    '01' => "J&auml;nner",
    '02' => "Februar",
    '03' => "M&auml;rz",
    '04' => "April",
    '05' => "Mai",
    '06' => "Juni",
    '07' => "Juli",
    '08' => "August",
    '09' => "September",
    '10' => "Oktober",
    '11' => "November",
    '12' => "Dezember"
  );

  private $days = array(
    '0' => "So",
    '1' => "Mo",
    '2' => "Di",
    '3' => "Mi",
    '4' => "Do",
    '5' => "Fr",
    '6' => "Sa"
  );

}
