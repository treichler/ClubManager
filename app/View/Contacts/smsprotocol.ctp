<!-- File: /app/View/Contacts/smsprotocol.ctp -->

<?php // This file contains PHP ?>

<h1>SMS Protokoll</h1>

<table>
  <tr>
    <th>Message ID</th>
    <th>Name</th>
    <th>Telefonnummer</th>
    <th>Gateway</th>
    <th>Status</th>
    <th>Kosten</th>
  </tr>

<?php foreach ($smsprotocols as $smsprotocol): ?>
  <tr>
    <td><?php echo $smsprotocol['Smsprotocol']['msgid']; ?></td>
    <td><?php echo $smsprotocol['Profile']['first_name'] . ' ' . $smsprotocol['Profile']['last_name']; ?></td>
    <td><?php echo $smsprotocol['Smsprotocol']['phone']; ?></td>
    <td><?php echo $smsprotocol['Smsprotocol']['report']; ?></td>
    <td><?php echo $smsprotocol['Smsprotocol']['status']; ?></td>
    <td>&euro; <?php echo $smsprotocol['Smsprotocol']['costs']; ?></td>
  </tr>
<?php endforeach; ?>
</table>

