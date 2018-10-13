<!-- File: /app/View/Memberships/contacts.ctp -->

<?php // This file contains PHP ?>

<h1>Kontaktliste</h1>

<p>
  <?php echo $this->Html->link('Kontaktliste herunterladen', array('controller' => 'memberships', 'action' => 'contacts', 'ext' => 'pdf')) ?> |
  <?php echo $this->Html->link('vCard herunterladen', array('controller' => 'memberships', 'action' => 'contacts', 'ext' => 'vcf')) ?> |
  <?php echo $this->Html->link('E-Mail Liste', array('controller' => 'memberships', 'action' => 'contacts', 'ext' => 'txt')) ?>
</p>

<table>
  <tr>
    <th>Name</th>
    <th>E-Mail</th>
    <th>Telefon</th>
    <th>Mobiltelefon</th>
    <th>Telefon beruflich</th>
  </tr>

<?php foreach ($contacts as $profile): ?>
  <tr>
    <td><?php echo h($profile['Profile']['first_name'] . ' ' . $profile['Profile']['last_name']) ?></td>
    <td><?php echo h($profile['User']['email']) ?></td>
    <td><?php echo h($profile['Profile']['phone_private']) ?></td>
    <td><?php echo h($profile['Profile']['phone_mobile']) ?></td>
    <td><?php echo h($profile['Profile']['phone_office']) ?></td>
  </tr>
<?php endforeach; ?>
<?php unset($profile); ?>
</table>

