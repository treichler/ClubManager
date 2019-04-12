<?php
// app/View/Layouts/ics/default.ctp

  // set default values for variables (if they have not been set before)
  if (!isset($calendar_settings['force_class_public'])) {
    $calendar_settings['force_class_public'] = false;
  }
  if (!isset($calendar_settings['show_description'])) {
    $calendar_settings['show_description'] = false;
  }
  if (!isset($calendar_settings['show_status'])) {
    $calendar_settings['show_status'] = false;
  }
  if (!isset($calendar_settings['name'])) {
    $calendar_settings['name'] = Configure::read('club.name');
  }
  if (!isset($calendar_settings['description'])) {
    $calendar_settings['description'] = 'Termine @ ' . Configure::read('club.name');
  }
  if (!isset($calendar_settings['file_name'])) {
    $calendar_settings['file_name'] = 'Termine';
  }

  // prepare header
  $this->response->type(array('ics' => 'text/calendar'));
  $this->response->header(array(
    'Content-Type' => 'text/calendar',
    'Content-Disposition' => 'attachment; filename=' . $calendar_settings['file_name'] . '.ics'
  ));
  $this->response->download($calendar_settings['file_name'] . '.ics');

  $tzid = 'Europe/Vienna';

  // calendar's header
  echo  "BEGIN:VCALENDAR\r\n" .
        "VERSION:2.0\r\n" .
        "BEGIN:VTIMEZONE\r\n" .
        "TZID:" . $tzid . "\r\n" .
        "X-LIC-LOCATION:" . $tzid . "\r\n" .
        "BEGIN:DAYLIGHT\r\n" .
        "TZOFFSETFROM:+0100\r\n" .
        "TZOFFSETTO:+0200\r\n" .
        "TZNAME:CEST\r\n" .
        "DTSTART:19700329T020000\r\n" .
        "RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3\r\n" .
        "END:DAYLIGHT\r\n" .
        "BEGIN:STANDARD\r\n" .
        "TZOFFSETFROM:+0200\r\n" .
        "TZOFFSETTO:+0100\r\n" .
        "TZNAME:CET\r\n" .
        "DTSTART:19701025T030000\r\n" .
        "RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10\r\n" .
        "END:STANDARD\r\n" .
        "END:VTIMEZONE\r\n".
        "PRODID:-//" . Configure::read('club.name') . "//DE\r\n" .
        "X-WR-CALNAME:" . $calendar_settings['name'] . "\r\n" .
        "X-WR-CALDESC:" . $calendar_settings['description'] . "\r\n" .
        "X-WR-TIMEZONE:" . $tzid . "\r\n" .
        "CALSCALE:GREGORIAN\r\n" .
        "METHOD:PUBLISH\r\n";

  //loop through the events
  foreach ($events as $event) {
    $stamp = new DateTime($event['Event']['modified']);
    $start = new DateTime($event['Event']['start']);
    $end = new DateTime($event['Event']['stop']);
    $uid = md5($event['Event']['created'] . ' ' . $event['Event']['id']);

    echo  "BEGIN:VEVENT\r\n";

    // organizer
    if (isset($calendar_settings['organizer'])) {
      echo  "ORGANIZER;CN=\"" . $calendar_settings['organizer'] . "\"";
      if (isset($calendar_settings['organizer_mail'])) {
        echo  ":MAILTO:" . $calendar_settings['organizer_mail'];
      }
      echo  "\r\n";
    } elseif (isset($event['Event']['User']['name'])) {
      echo  "ORGANIZER;CN=\"" . $event['Event']['User']['name'] . "\"";
      if (isset($event['Event']['User']['email'])) {
        echo  ":MAILTO:" . $event['Event']['User']['email'];
      }
      echo  "\r\n";
    }

    // class
    if ($calendar_settings['force_class_public'] || !isset($event['Event']['Mode']['is_public'])) {
      echo  "CLASS:PUBLIC\r\n";
    } else {
      echo  "CLASS:" . ($event['Event']['Mode']['is_public'] ? 'PUBLIC' : 'PRIVATE') . "\r\n";
    }

    // category
    if (isset($event['Event']['Mode']['name'])) {
      echo  "CATEGORIES:" . $event['Event']['Mode']['name'] . "\r\n";
    } elseif (isset($event['Mode']['name'])) {
      echo  "CATEGORIES:" . $event['Mode']['name'] . "\r\n";
    }

    // timestamp
    echo  "DTSTAMP:" . $stamp->format('Ymd') . 'T' . $stamp->format('His') . "\r\n";

    // start and end
    if (isset($event['Event']['whole_day']) && $event['Event']['whole_day']) {
      echo  "DTSTART;VALUE=DATE:" . $start->format('Ymd') . "\r\n";
    } else {
      echo  "DTSTART;TZID=" . $tzid . ':' . $start->format('Ymd') . 'T' . $start->format('His') . "\r\n" .
            "DTEND;TZID=" . $tzid . ':' . $end->format('Ymd') . 'T' . $end->format('His') . "\r\n";
    }

    // location
    if ($event['Event']['location']) {
      echo  "LOCATION:" . prepareString($event['Event']['location']) . "\r\n";
    }

    // summary
    echo  "SUMMARY:" . prepareString($event['Event']['name']);
    if (isset($event['Event']['Group'])) {
      $groups = [];
      foreach($event['Event']['Group'] as $group) {
        $groups[] = h($group['name']);
      }
      unset($group);
    } elseif (isset($event['Group'])) {
      $groups = [];
      foreach($event['Group'] as $group) {
        $groups[] = h($group['name']);
      }
      unset($group);
    }
    if (!empty($groups)) {
      echo " (" . implode(', ', $groups) . ")";
    }
    echo  "\r\n";

    // description
    if ($calendar_settings['show_description'] && isset($event['Event']['info']) && $event['Event']['info']) {
      echo  "DESCRIPTION:" . prepareString($event['Event']['info']) . "\r\n";
    }

    // status
    if ($calendar_settings['show_status']) {
      echo  "STATUS:"   . ($event['Availability']['is_available'] ? 'CONFIRMED' : 'TENTATIVE') . "\r\n";
      echo  "TRANSP:"   . ($event['Availability']['is_available'] ? 'OPAQUE' : 'TRANSPARENT') . "\r\n";
      echo  "PARTSTAT:" . ($event['Availability']['is_available'] ? 'ACCEPTED' : 'DECLINED') . "\r\n";
    }

    // close event
    echo  "END:VEVENT\r\n";
  }

  // close calendar
  echo  "END:VCALENDAR\r\n";


  function prepareString($str) {
    $str = str_replace("\\", "\\\\", $str);
    $str = str_replace(",", "\,", $str);
    $str = str_replace(";", "\;", $str);
    $str = str_replace("\r", "", $str);
    $str = str_replace("\t", " ", $str);
    $str = str_replace("\n", "\\n", $str);
    return $str;
  }

?>
