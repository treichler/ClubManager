<!-- File: /app/View/Memberships/contacts.ctp -->

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

<h1>Kontaktliste</h1>

<p>
  <?php echo $this->Html->link('Kontaktliste herunterladen', array('controller' => 'memberships', 'action' => 'contacts', 'ext' => 'pdf')) ?> |
  <?php echo $this->Html->link('vCard herunterladen', array('controller' => 'memberships', 'action' => 'contacts', 'ext' => 'vcf')) ?> |
  <?php echo $this->Html->link('E-Mail Liste', array('controller' => 'memberships', 'action' => 'contacts', 'ext' => 'txt')) ?>
</p>

<table class="tablesorter">
<thead>
  <tr>
    <th>Vorname</th>
    <th>Nachname</th>
    <th>E-Mail</th>
    <th>Telefon</th>
    <th>Mobiltelefon</th>
    <th>Telefon beruflich</th>
  </tr>
</thead>
<tbody>
<?php foreach ($contacts as $profile): ?>
  <tr>
    <td><?php echo h($profile['Profile']['first_name']) ?></td>
    <td><?php echo h($profile['Profile']['last_name']) ?></td>
    <td><?php echo h($profile['User']['email']) ?></td>
    <td><?php echo h($profile['Profile']['phone_private']) ?></td>
    <td><?php echo h($profile['Profile']['phone_mobile']) ?></td>
    <td><?php echo h($profile['Profile']['phone_office']) ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
<?php unset($profile); ?>
</table>

