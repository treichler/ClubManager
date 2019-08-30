<!-- File: /app/View/Contactpeople/organize.ctp -->

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

<h1>Kontaktpersonen Verwalten</h1>

<p><?php echo $this->Html->link('Kontaktperson hinzufügen', array('controller' => 'contactpeople', 'action' => 'add')); ?></p>

<table class="tablesorter">
<thead>
  <tr>
    <th>Vorname</th>
    <th>Nachname</th>
    <th>Fu&szlig;zeile Telefon</th>
    <th>Kontaktliste E-Mail</th>
    <th>Kontaktliste Telefon</th>
    <th>Kontaktformular Empfänger</th>
  </tr>
</thead>
<tbody>
<?php foreach ($contactpeople as $contactperson): ?>
  <tr>
    <td><?php echo h($contactperson['Profile']['first_name']); ?></td>
    <td><?php echo h($contactperson['Profile']['last_name']); ?></td>
    <td><?php echo $this->Html->showBoolean($contactperson['Contactperson']['footer_phone'], array('bold' => true)); ?></td>
    <td><?php echo $this->Html->showBoolean($contactperson['Contactperson']['contactlist_email'], array('bold' => true)); ?></td>
    <td><?php echo $this->Html->showBoolean($contactperson['Contactperson']['contactlist_phone'], array('bold' => true)); ?></td>
    <td><?php echo $this->Html->showBoolean($contactperson['Contactperson']['contact_recipient'], array('bold' => true)); ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
            array('action' => 'edit', $contactperson['Contactperson']['id']),
            array('title' => 'Kontaktperson bearbeiten'));
    ?></td>
    <td class="icon-delete"><?php
        echo $this->Form->postLink('löschen',
            array('action' => 'delete', $contactperson['Contactperson']['id']),
            array('confirm' => 'Soll die Kontaktperson tatsächlich gelöscht werden?',
                  'title' => 'Kontaktperson löschen'));
    ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
<?php unset($contactperson); ?>
</table>

