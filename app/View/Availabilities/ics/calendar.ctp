<?php
// app/View/Availabilities/ics/calendar.ctp

  // calendar's content
  $this->set('events', $availabilities);

  // calendar's settings
  $calendar_settings = array(
    'force_class_public'  => ($client === 'gCal'),
    'show_description'    => true,
    'show_status'         => true,
    'name'                => $profile['first_name'] . ' ' . $profile['last_name'],
    'description'         => $profile['first_name'] . ' ' . $profile['last_name'] . ' @ ' . Configure::read('club.name'),
    'file_name'           => 'calendar',
  );
  $this->set(compact('calendar_settings'));
?>
