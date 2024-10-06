<!-- File: /app/View/Events/pdf/view.ctp -->

<?php // This file contains PHP
  // set the filename for the document
  $this->set('file_name', 'Event-' . $event['Event']['id']);
  // set the document's title
  $this->set('title', h($event['Event']['name']));
  // set additional information for the document
  $groups = [];
  foreach($event['Group'] as $group) {
    $groups[] = h($group['name']);
  }
  unset($group);
  $this->set('information', implode(', ', $groups) . '
    Beginn: ' . $this->Html->getDateTime($event['Event']['start'], array('year' => true)) . '
    Ende: ' . $this->Html->getDateTime($event['Event']['stop'], array('year' => true)) . '
    Ort: ' . h($event['Event']['location']));
?>

<style>
th {
  text-align: center;
}

td {
  border: 1px solid black;
  font-size: 8;
}
</style>

<table cellspacing="0" cellpadding="3">
  <tr>
    <th width="35%">Instrument(e)</th>
    <th width="25%">Name</th>
    <th width="40%">Information</th>
  </tr>

<?php
  // prepare data
  $count_people = 0;
  $count_groups = 0;
  $names = array();
  $groups = array();
  $infos = array();
  $first_group_sorting = array();
  foreach( $event['Availability'] as $availability ) {
    if( $availability['is_available'] ) {
      $id = $availability['id'];
      $group_names = [];
      foreach ($availability['Membership']['Group'] as $group)
        $group_names[] = $group['name'];

      $names[]  = $availability['Membership']['Profile']['first_name'] . ' ' . $availability['Membership']['Profile']['last_name'];
      $groups[] = implode(', ', $group_names);
      $infos[]  = $availability['info'];
      $first_group_sorting[] = isset($availability['Membership']['Group'][0]) ? $availability['Membership']['Group'][0]['sorting'] : Null;

      $count_people ++;
      if( isset($availability['Membership']['Group'][0]) )
        $count_groups ++;
    }
  }

  // sort "$groups" and "infos" by "first_group_sorting" and "names"
  array_multisort($first_group_sorting, SORT_ASC, $names, SORT_ASC, $groups, $infos);

  // print table to pdf
  for( $i = 0; $i < count($names); $i++ ):
?>
  <tr>
    <td><?php echo $groups[$i] ?></td>
    <td><?php echo $names[$i]  ?></td>
    <td><?php echo $infos[$i]  ?></td>
  </tr>
<?php
  endfor;
  unset($groups);
  unset($names);
  unset($infos);
  unset($first_group_sorting);
?>
  <tr>
    <th>Summe: <?php echo $count_groups; ?></th>
    <th>Summe: <?php echo $count_people; ?></th>
  </tr>
</table>

