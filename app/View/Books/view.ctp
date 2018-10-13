<!-- File: /app/View/Books/view.ctp -->

<?php // This file contains PHP ?>

<h1><?php echo $book['Book']['title'] ?></h1>

<p><?php echo $book['Book']['description'] ?></p>

<?php if ($this->Html->hasPrivileg($this_user, array('Music book'))): ?>
  <p><?php echo $this->Html->link('Inhalt bearbeiten', array('action' => 'content', $book['Book']['id'])); ?></p>
<?php endif; ?>

<table>
  <tr>
    <th>Seite</th>
    <th>Titel</th>
    <th>Komponist(en)</th>
    <th>Arrangeur(e)</th>
    <th>Verlag</th>
  </tr>

<?php
  foreach ($sheets as $sheet):
  $composers = array();
  foreach ($musicsheets[$sheet['Sheet']['musicsheet_id']]['Composer'] as $composer) {
    $composers[] = $composer['first_name'] . ' ' . $composer['last_name'];
  }
  $arrangers = array();
  foreach ($musicsheets[$sheet['Sheet']['musicsheet_id']]['Arranger'] as $arranger) {
    $arrangers[] = $arranger['first_name'] . ' ' . $arranger['last_name'];
  }
?>
  <tr>
    <td><?php echo $sheet['Sheet']['page'] ?></td>
    <td><?php echo h($musicsheets[$sheet['Sheet']['musicsheet_id']]['Musicsheet']['title']); ?></td>
    <td><?php echo implode(', ', $composers) ?></td>
    <td><?php echo implode(', ', $arrangers) ?></td>
    <td><?php echo h($musicsheets[$sheet['Sheet']['musicsheet_id']]['Publisher']['name']); ?></td>
    <td></td>
  </tr>
<?php endforeach; ?>
<?php unset($sheet) ?>
</table>

