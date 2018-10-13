<!-- File: /app/View/Contacts/protocol.ctp -->

<?php // This file contains PHP ?>

<h1>Kontakt Protokoll</h1>

<table>
  <tr>
    <th>Datum &amp; Zeit</th>
    <th>Benutzer</th>
    <th>Bezeichnung</th>
    <th>Bericht</th>
    <th>Ausgewählt</th>
    <th>Gesendet</th>
  </tr>

<?php foreach ($contactprotocols as $contactprotocol): ?>
  <tr>
    <td><?php echo $contactprotocol['Contactprotocol']['created']; ?></td>
    <td><?php if ($contactprotocol['Contactprotocol']['user_id']) echo $contactprotocol['User']['name']; ?></td>
    <td><?php
      if ($contactprotocol['Contactprotocol']['name'] === 'sms') {
        $text = $this->Html->link($contactprotocol['Contactprotocol']['name'],
                  array('controller' => 'contacts', 'action' => 'smsprotocol',
                        $contactprotocol['Contactprotocol']['id']));
      } else {
        $text = $contactprotocol['Contactprotocol']['name'];
      }
      echo $text;
    ?></td>
    <td><?php echo $contactprotocol['Contactprotocol']['report']; ?></td>
    <td><?php echo $contactprotocol['Contactprotocol']['profiles_selected']; ?></td>
    <td><?php echo $contactprotocol['Contactprotocol']['profiles_delivered']; ?></td>
  </tr>
<?php endforeach; ?>
</table>

