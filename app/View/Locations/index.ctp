<!-- File: /app/View/Locations/index.ctp -->

<?php // This file contains PHP ?>

<?php echo $this->Html->script('jquery.tablesorter.min'); ?>

<script type="text/javascript">
$(document).ready(function() {
  // call the tablesorter plugin
  $("table").tablesorter({
    // sort on the second column, order asc
    sortList: [[1,0]]
  });
});
</script>

<h1>Orte</h1>

<p><?php echo $this->Html->link('Neuer Ort', array('action' => 'add')); ?></p>

<table class="tablesorter">
<thead>
  <tr>
    <th>ID</th>
    <th>Ort</th>
    <th>L&auml;ngengrad</th>
    <th>Breitengrad</th>
    <th>Radius [m]</th>
    <th>Karte</th>
    <th>Veranstaltungen</th>
  </tr>
</thead>
<tbody>
<?php
  foreach ($locations as $location):
    $event_count = count($location['Event']);
?>
  <tr>
    <td><?php echo $location['Location']['id']; ?></td>
    <td><?php echo $location['Location']['name']; ?></td>
    <td><?php echo $location['Location']['longitude']; ?></td>
    <td><?php echo $location['Location']['latitude']; ?></td>
    <td><?php echo $location['Location']['radius']; ?></td>
    <td><?php echo $this->Html->showBoolean($location['Location']['show_on_map'], array('bold' => true)); ?></td>
    <td><?php echo $event_count; ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('action' => 'edit', $location['Location']['id']),
            array('title' => 'Ort bearbeiten'));
    ?><?php if ($this->Html->hasPrivileg($this_user, array('Location delete'))): ?></td>
    <td class="icon-delete"><?php
        if ($event_count == 0) {
          echo $this->Form->postLink('löschen',
              array('action' => 'delete', $location['Location']['id']),
              array('confirm' => 'Soll der Ort "' . $location['Location']['name'] . '" tatsächlich gelöscht werden?',
                    'title' => 'Ort löschen'));
        }
    ?></td><? endif; ?>
  </tr>
<?php endforeach; ?>
</tbody>
<?php unset($kind); ?>
</table>

