<?php

Configure::write('debug', 0);

$this->set('channel', array(
  'title' => __(Configure::read('club.name') . " - Aktuelles"),
//  'link' => $this->Html->url('/', true),
  'link' => $this->Html->url(array('controller' => 'blogs', 'action' => 'index.rss'), true),
  'description' => __("Aktuelle Informationen"),
  'language' => 'de-at',
  'atom:link' => array(
    'attrib' => array(
      'href' => $this->Html->url(array('controller' => 'blogs', 'action' => 'index.rss'), true),
//      'href' => array('controller' => 'blogs', 'action' => 'index.rss'),
      'rel'  => 'self',
      'type' => 'application/rss+xml'
  ))
));


// You should import Sanitize
App::uses('Sanitize', 'Utility');

foreach ($blogs as $blog) {
/*
  $postTime = strtotime($blog['Blog']['created']);
  $postLink = array(
    'controller' => 'posts',
    'action' => 'view',
    'year' => date('Y', $postTime),
    'month' => date('m', $postTime),
    'day' => date('d', $postTime),
//    $blog['Blog']['slug']
  );
*/

  $post_link = array('controller' => 'blogs', 'action' => 'view', $blog['Blog']['id']);

  // This is the part where we clean the body text for output as the description
  // of the rss item, this needs to have only text to make sure the feed validates
  $bodyText = preg_replace('=\(.*?\)=is', '', $blog['Blog']['body']);
  $bodyText = $this->Text->stripLinks($bodyText);
  $bodyText = Sanitize::stripAll($bodyText);
  $bodyText = $this->Text->truncate($bodyText, 400, array(
    'ending' => '...',
    'exact' => true,
    'html' => true,
  ));
  echo $this->Rss->item(array(), array(
    'title' => $blog['Blog']['title'],
    'link' => $post_link,
    'guid' => array('url' => $post_link, 'isPermaLink' => 'true'),
    'description' => $bodyText,
    'pubDate' => $blog['Blog']['time_stamp'],
//    'enclosure' => array('url' => $this->Html->url('/img/logo_rss.png', true), 'length' => '2812', 'type' => 'image/png')
  ));
}

?>
