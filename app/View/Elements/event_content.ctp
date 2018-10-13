<!-- File: /app/View/Elements/event_content.ctp -->

<?php // This file contains PHP ?>

<style>

.resource {
text-align: right;
}

.information {
display: block;
text-align: left;
}

.high_priority {
background-color: #ffafaf;
}

div.spacing {
line-height: 1.5;
}

div.month_spacing {
line-height: 0.5;
}

div.month {
text-align: center;
font-size: 14;
font-weight: bold;
line-height: 1.5;
}

</style>

<?php
$month = 0;
$now = new dateTime();
$intervall = 60*60*24*7; // one week
foreach ($events as $event):
  $start = new DateTime($event['Event']['start']);
  $stop = new DateTime($event['Event']['stop']);

  // do not print events that are older than '$intervall'
  if ($now->getTimestamp() - $stop->getTimestamp() > $intervall)
    continue;

  // get ressources (which are locations)
  $resources = [];
  if (isset($event['Resource'])) {
    foreach ($event['Resource'] as $resource) {
//      if ($resource['is_location'])
        $resources[] = h($resource['name']);
    }
  }

  // FIELD include the name of month at first appearance
  if ($month != $start->format('m')):
    if($month):
      // do not insert month_spacing at first appereance
    ?>
<div class="month_spacing"> </div>
    <?php
    endif;
    $month = $start->format('m');
    ?>
<div class="month"><?php echo $this->Html->months[$month] . ' ' . $start->format('Y') ?></div>
  <?php
  else:
  ?>
<div class="spacing"> </div>
  <?php
  endif;

  // prepare date
  if ($start->format('Ymd') === $stop->format('Ymd')) {
    $date = $this->Html->getDate($event['Event']['start']) . '<br/>' .
      $this->Html->getTime($event['Event']['start']) . ' - ' . $this->Html->getTime($event['Event']['stop']);
  } else {
    $date = $this->Html->getDateTime($event['Event']['start']) . '<div align="center">bis</div>' .
      $this->Html->getDateTime($event['Event']['stop']);
  }

  // prepare title line
  if ($event['Event']['expiry'] != 0) {
    $title_line = '';
    if (isset($event['Group']['name']))
      $title_line .= '<i>' . h($event['Group']['name']) . ':</i> ';
    $title_line .= '<b>' . h($event['Event']['name']) . '</b>';
  } else {
    $title_line = '<i>' . h($event['Event']['name']) . '</i>';
  }
  ?>
<table cellspacing="0" cellpadding="3" nobr="true">
  <tr>
    <td width="18%" rowspan="2"><?php echo $date ?></td>
    <td width="82%" <?php if ($event['Event']['high_priority']) echo 'class="high_priority"'?>><?php echo $title_line ?></td>
<!--
    <td width="30%" class="resource<?php if ($event['Event']['high_priority']) echo ' high_priority'?>"><?php echo implode(', ', $resources) ?></td>
-->
  </tr>
  <tr>
<!--
    <td colspan="2" class="information"><?php echo h($event['Event']['info']) ?></td>
-->
    <td class="information"><?php
      echo h($event['Event']['info']);
      if (!empty($event['Event']['location']))
        echo ('<br/><i>Ort:</i> ' . h($event['Event']['location']));
      if (!empty($resources))
        echo ('<br/><i>Ressourcen:</i> ' . implode(', ', $resources));
    ?></td>
  </tr>
</table>
<?php endforeach; ?>


<!-- this is a prototype table for events, introduced by according month -->

<!--

<div class="month">September 2013</div>

<table cellspacing="0" cellpadding="3" nobr="true">
  <tr>
    <td width="17%" rowspan="2">Datum Zeit, von bis</td>
    <td width="43%"><i>Gruppe:</i> <b>Titel der Veranstaltung</b></td>
    <td width="40%" class="resource">Ressource / Location</td>
  </tr>
  <tr>
    <td colspan="2" class="information">Genaue Beschreibung der Veranstaltung. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea.</td>
  </tr>
</table>

<div class="spacing"> </div>

<table cellspacing="0" cellpadding="3" nobr="true">
  <tr>
    <td width="17%" rowspan="2">Datum Zeit, von bis</td>
    <td width="43%"><i>Gruppe:</i> <b>Titel der Veranstaltung</b></td>
    <td width="40%" class="resource">Ressource / Location</td>
  </tr>
  <tr>
    <td colspan="2" class="information">Genaue Beschreibung der Veranstaltung. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea.</td>
  </tr>
</table>

<div class="month_spacing"> </div>

<div class="month">Oktober 2013</div>

<table cellspacing="0" cellpadding="3" nobr="true">
  <tr>
    <td width="17%" rowspan="2">Datum Zeit, von bis</td>
    <td width="43%"><i>Gruppe:</i> <b>Titel der Veranstaltung</b></td>
    <td width="40%" class="resource">Ressource / Location</td>
  </tr>
  <tr>
    <td colspan="2" class="information">Genaue Beschreibung der Veranstaltung. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea.</td>
  </tr>
</table>
<div class="spacing"> </div>

<table cellspacing="0" cellpadding="3" nobr="true">
  <tr>
    <td width="17%" rowspan="2">Mo 07.10. 15:00<div align="center">bis</div>Di 08.10. 19:00</td>
    <td width="43%" class="high_priority"><i>Stadtkapelle:</i> <b>Probentag</b></td>
    <td width="40%" class="high_priority resource">Proberaum, Archiv</td>
  </tr>
  <tr>
    <td colspan="2" class="information">Genaue Beschreibung der Veranstaltung. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea.</td>
  </tr>
</table>
<div class="spacing"> </div>

<table cellspacing="0" cellpadding="3" nobr="true">
  <tr>
    <td width="17%" rowspan="2">Datum Zeit, von bis</td>
    <td width="43%"><i>Gruppe:</i> <b>Titel der Veranstaltung</b></td>
    <td width="40%" class="resource">Ressource / Location</td>
  </tr>
  <tr>
    <td colspan="2" class="information"></td>
  </tr>
</table>
<div class="spacing"> </div>

<table cellspacing="0" cellpadding="3" nobr="true">
  <tr>
    <td width="17%" rowspan="2">Datum Zeit, von bis</td>
    <td width="43%"><i>Gruppe:</i> <b>Titel der Veranstaltung</b></td>
    <td width="40%" class="resource">Ressource / Location</td>
  </tr>
  <tr>
    <td colspan="2" class="information">Genaue Beschreibung der Veranstaltung. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea.</td>
  </tr>
</table>
<div class="spacing"> </div>

-->

