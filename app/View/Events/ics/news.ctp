<?php
// app/View/Events/ics/calendar.ctp

  // prepare start and end
  foreach ($events as $key => $val) {
    $event = &$events[$key]['Event'];
    if ($event['show_official_start']) {
      // set "start" to "official_start"
      $event['start'] = $event['official_start'];
    } else {
      // set event as a whole day event
      $event['whole_day'] = true;
    }
  }

  // calendar's content
  $this->set('events', $events);

  // calendar's settings
  $calendar_settings = array(
    'force_class_public'  => true,
    'organizer'           => Configure::read('club.name'),
    'organizer_mail'      => Configure::read('club.email'),
    'file_name'           => 'Termine',
  );
  $this->set(compact('calendar_settings'));
?>
