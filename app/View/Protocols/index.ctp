<!-- File: /app/View/Contacts/index.ctp -->

<?php // This file contains PHP ?>

<h1>SMS Protokoll</h1>

<table>
  <tr>
    <th>id</th>
    <th>contactprotocol_id</th>
    <th>profile_id</th>
    <th>msgid</th>
    <th>phone</th>
    <th>report</th>
    <th>costs</th>
    <th>status</th>
  </tr>

<?php foreach ($smsprotocols as $smsprotocol): ?>
  <tr>
    <td><?php echo $smsprotocol['Smsprotocol']['id']; ?></td>
    <td><?php echo $smsprotocol['Smsprotocol']['contactprotocol_id']; ?></td>
    <td><?php echo $smsprotocol['Smsprotocol']['profile_id']; ?></td>
    <td><?php echo $smsprotocol['Smsprotocol']['msgid']; ?></td>
    <td><?php echo $smsprotocol['Smsprotocol']['phone']; ?></td>
    <td><?php echo $smsprotocol['Smsprotocol']['report']; ?></td>
    <td><?php echo $smsprotocol['Smsprotocol']['costs']; ?></td>
    <td><?php echo $smsprotocol['Smsprotocol']['status']; ?></td>
  </tr>
<?php endforeach; ?>
</table>

