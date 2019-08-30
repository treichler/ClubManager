<!-- File: /app/View/Customers/index.ctp -->

<?php // This file contains PHP ?>

<h1>Veranstalter / Kunden</h1>

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

<p><?php echo $this->Html->link('Neuer Veranstalter', array('action' => 'add')); ?></p>

<table class="tablesorter">
<thead>
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Straße</th>
    <th>Postleitzahl</th>
    <th>Ort</th>
    <th>Veranstaltungen</th>
  </tr>
</thead>
<tbody>
<?php
  foreach ($customers as $customer):
    $event_count = count($customer['Event']);
?>
  <tr>
    <td><?php echo $customer['Customer']['id']; ?></td>
    <td><?php echo h($customer['Customer']['name']); ?></td>
    <td><?php echo h($customer['Customer']['street']); ?></td>
    <td><?php echo $customer['Customer']['postal_code']; ?></td>
    <td><?php echo h($customer['Customer']['town']); ?></td>
    <td><?php echo $event_count; ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('action' => 'edit', $customer['Customer']['id']),
            array('title' => 'Veranstalter bearbeiten'));
    ?><?php if ($this->Html->hasPrivileg($this_user, array('Customer delete'))): ?></td>
    <td class="icon-delete"><?php
        if ($event_count == 0) {
          echo $this->Form->postLink('löschen',
              array('action' => 'delete', $customer['Customer']['id']),
              array('confirm' => 'Soll Veranstalter "' . $customer['Customer']['name'] . '" tatsächlich gelöscht werden?',
                    'title' => 'Veranstalter löschen'));
          }
    ?></td><? endif; ?>
  </tr>
<?php endforeach; ?>
</tbody>
<?php unset($kind); ?>
</table>

