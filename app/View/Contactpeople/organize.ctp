<!-- File: /app/View/Contactpeople/organize.ctp -->

<?php // This file contains PHP ?>

<h1>Kontaktpersonen Verwalten</h1>

<p><?php echo $this->Html->link('Kontaktperson hinzufügen', array('controller' => 'contactpeople', 'action' => 'add')); ?></p>

<table>
  <tr>
    <th>Name</th>
    <th>Fu&szlig;zeile Telefon</th>
    <th>Kontaktliste E-Mail</th>
    <th>Kontaktliste Telefon</th>
    <th>Kontaktformular Empfänger</th>
  </tr>

<?php foreach ($contactpeople as $contactperson): ?>
  <tr>
    <td><?php echo $contactperson['Profile']['first_name'] . ' ' . $contactperson['Profile']['last_name']; ?></td>
    <td><?php echo $contactperson['Contactperson']['footer_phone']; ?></td>
    <td><?php echo $contactperson['Contactperson']['contactlist_email']; ?></td>
    <td><?php echo $contactperson['Contactperson']['contactlist_phone']; ?></td>
    <td><?php echo $contactperson['Contactperson']['contact_recipient']; ?></td>
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
</table>

