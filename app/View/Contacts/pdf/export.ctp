<!-- File: /app/View/Contacts/pdf/export.ctp -->

<?php // This file contains PHP
  // set the filename for the document
  $this->set('file_name', 'Kontakte');
  // set the document's title
  $this->set('title', 'Kontaktdaten');
  // set additional information for the document
  $this->set('information', 'Erstellt am: ' . $this->Html->getDateTime(date("Y-m-d H:i:s"), array('year' => true)));
?>

<style>
th {
font-size: 10;
font-weight: bold;
text-align: center;
}

td {
font-size: 9;
}

td.phone {
text-align: right;
}
</style>

<?php if (!isset($contacts[0])) $contacts = []; ?>

<table border="0.5" cellpadding="2" cellspacing="0">
  <tr>
    <th width="20%">Name</th>
    <th width="28%">E-Mail</th>
    <th width="16%">Telefon</th>
    <th width="18%">Mobiltelefon</th>
    <th width="18%">Telefon beruflich</th>
  </tr>

<?php foreach ($contacts as $profile): ?>
  <tr>
    <td><?php echo h($profile['Profile']['first_name'] . ' ' . $profile['Profile']['last_name']) ?></td>
    <td><?php echo h($profile['User']['email']) ?></td>
    <td class="phone"><?php echo h($profile['Profile']['phone_private']) ?></td>
    <td class="phone"><?php echo h($profile['Profile']['phone_mobile']) ?></td>
    <td class="phone"><?php echo h($profile['Profile']['phone_office']) ?></td>
  </tr>
<?php endforeach; ?>
<?php unset($profile); ?>
</table>

