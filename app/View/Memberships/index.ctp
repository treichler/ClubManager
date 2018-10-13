<!-- File: /app/View/Memberships/index.ctp -->

<?php // This file contains PHP ?>

<h1>Mitgliedschaften</h1>

<?php if ($this->Html->hasPrivileg($this_user, array('Administrator'))): ?>
<p><?php echo $this->Html->link('Mitglied hinzufügen', array('controller' => 'memberships', 'action' => 'add')); ?></p>
<?php endif; ?>

<?php
  $states = [];
  foreach ($memberships as $membership) {
    $states[$membership['State']['id']][] = $membership;
  }
  foreach ($states as $state):
?>
<h4><?php echo $state[0]['State']['name'] ?> (<?php echo count($state); ?> <?php echo (count($state) > 1 ? 'Personen' : 'Person') ?>)</h4>
<p><?php echo $state[0]['State']['description'] ?></p>

<?php
unset($memberships);
$memberships = $state;
?>

<div class="gallery">
  <?php foreach ($memberships as $membership): ?>
  <div class="galleryItem">
    <h6><?php echo h($membership['Profile']['first_name'] . ' ' . $membership['Profile']['last_name']) ?></h6>
    <?php
        $photo = Router::url(array('controller' => 'profiles', 'action' => 'attachment', $membership['Profile']['id']), true);
        echo '<img class="thumbPortrait" src="' . $photo . '" />';
      ?>
    <div><?php
      if ($this->Html->hasPrivileg($this_user, array('Administrator'))) {
        echo $this->Html->link('bearbeiten',
               array('controller' => 'memberships', 'action' => 'edit', $membership['Membership']['id']));
        echo ' ' . $this->Form->postLink('löschen',
               array('action' => 'delete', $membership['Membership']['id']),
               array('confirm' => 'Soll die Mitgliedschaft von "' . $membership['Profile']['first_name'] .
                       ' ' . $membership['Profile']['last_name'] . '" tatsächlich gelöscht werden?'));
      }
    ?></div>
    <div class="galleryExtra"><?php
      $groups = [];
      foreach($membership['Group'] as $group) {
        $groups[] = $group['name'];
      }
      if (count($groups)) echo ($groups > 1) ? 'Gruppen: ' : 'Gruppe';
      echo implode(', ', $groups);
      unset($group);
    ?></div>
  </div>
  <?php endforeach; ?>
  <?php unset($membership); ?>
</div>

<?php endforeach; ?>

