<!-- File: /app/View/Memberships/pdf/birthdays.ctp -->

<?php // This file contains PHP
  // set the filename for the document
  $this->set('file_name', 'Geburtstagsliste');
  // set the document's title
  $this->set('title', 'Geburtstage');
  // set additional information for the document
  $this->set('information', 'Erstellt am: ' . $this->Html->getDateTime(date("Y-m-d H:i:s"), array('year' => true)));
?>

<style>

td {
  border: 1px solid black;
  font-size: 8;
}
</style>

<table cellspacing="0" cellpadding="3">
<?php
$month = 0;
foreach ($memberships as $membership):
//  $today = new DateTime();
  $birthday = new DateTime($membership['Profile']['birthday']);
//  $interval = $birthday->diff($today);
?>

  <?php if ($membership['Profile']['birthday'] && $month != $birthday->format('m')): ?>
  <tr>
    <th><?php
      $month = $birthday->format('m');
      echo $this->Html->months[$month];
    ?></th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
  </tr>
  <?php endif; ?>

  <tr>
    <td width="15%"><?php
      if ($membership['Profile']['birthday']) {
        echo $this->Html->getDate($membership['Profile']['birthday'], array('year' => true, 'day' => false));
      }
    ?></td>
    <td width="25%"><?php echo h($membership['Profile']['first_name'] . ' ' . $membership['Profile']['last_name']) ?></td>
    <td width="15%"></td>
    <td width="15%"></td>
    <td width="15%"></td>
    <td width="15%"></td>
  </tr>
<?php endforeach; ?>
<?php unset($membership) ?>
<?php unset($months) ?>
</table>

