<?php

Configure::write('debug', 0);

$this->set('channel', array(
  'title' => __(Configure::read('club.name') . " - Veranstaltungen"),
//  'link' => $this->Html->url('/', true),
  'link' => $this->Html->url(array('controller' => 'events', 'action' => 'news.rss'), true),
  'description' => __("Bevorstehende Termine"),
  'language' => 'de-at',
  'atom:link' => array(
    'attrib' => array(
      'href' => $this->Html->url(array('controller' => 'events', 'action' => 'news.rss'), true),
//      'href' => array('controller' => 'events', 'action' => 'news.rss'),
      'rel'  => 'self',
      'type' => 'application/rss+xml'
  ))
));


foreach ($events as $event) {
  $bodyText =  $this->Html->getDate($event['Event']['start'], array('year' => true));
  if (!$this->Html->isSameDay($event))
    $bodyText .= ' - ' . $this->Html->getDate($event['Event']['stop'], array('year' => true));
  $bodyText .= ': ' . h($event['Event']['name']);
  $groups = [];
  foreach($event['Group'] as $group) {
    $groups[] = h($group['name']);
  }
  unset($group);
  echo $this->Rss->item(array(), array(
    'title' => implode(', ', $groups),
//    'link'  => array('controller' => 'events', 'action' => 'news'),
//    'link' => $post_link,
//    'guid' => array('url' => $post_link, 'isPermaLink' => 'true'),
    'description' => $bodyText,
//    'pubDate' => $event['Event']['start']
  ));
}

?>
