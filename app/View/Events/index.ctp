<!-- File: /app/View/Events/index.ctp -->

<?php // This file contains PHP ?>

<h1>Alle Termine</h1>

<p>
<?php
  echo $this->Html->link('Terminplan herunterladen', array('controller' => 'events', 'action' => 'index', 'ext' => 'pdf'));
  if ($this->Html->isGroupAdmin($this_user))
    echo ' | ' . $this->Html->link('Neuer Termin', array('controller' => 'events', 'action' => 'add'));
?>
</p>

<div class="table event">
  <div class="tr">
    <div class="th">Datum</div>
    <div class="th">Gruppe</div>
    <div class="th">Bezeichnung</div>
    <div class="th">Info</div>
    <div class="th">Resourcen</div>
    <div class="th">Ersteller</div>
  </div>
  <!-- Here is where we loop through our $events array, printing out event info -->
  <?php foreach ($events as $event):
    $class = '';
    if ($event['Event']['expired']) $class = ' expired';
    if ($event['Event']['high_priority']) $class = ' high_priority';
    if ($event['Event']['expiry'] == 0) $class = ' information';
    if ($event['Event']['finished']) $class = ' finished';
    $class = ' class="tr' . $class . '"';
    // prepare info field
    $info = h($event['Event']['info']);
    if ($event['Event']['info'] && $event['Event']['location']) $info .= '<br />';
    if ($event['Event']['location']) $info .= 'Ort: ' . h($event['Event']['location']);
  ?>
  <div<?php echo $class; ?>>
    <div class="td"><?php
        echo $this->Html->getDateTime($event['Event']['start']);
        if (!$this->Html->isSameDay($event)) echo (' ' . $this->Html->getDateTime($event['Event']['stop']));
    ?></div>
    <div class="td"><?php
        $groups = [];
        foreach($event['Group'] as $group) {
          $groups[] = h($group['name']);
        }
        unset($group);
        echo implode(', ', $groups);
    ?></div>
    <div class="td"><?php
        echo $this->Html->link($event['Event']['name'],
        array('controller' => 'events', 'action' => 'view', $event['Event']['id']));
    ?></div>
    <div class="td info-field"><?php echo $info; ?></div>
    <div class="td"><?php
        $resources = [];
        foreach($event['Resource'] as $resource) {
          $resources[] = $resource['name'];
        }
        unset($resource);
        echo implode(', ', $resources);
    ?></div>
    <div class="td"><?php echo $user_names[$event['Event']['user_id']]; ?></div>
    <div class="td icon-pdf"><?php
        echo $this->Html->link('Anwesenheitsliste',
          array('controller' => 'events', 'action' => 'view', $event['Event']['id'], 'ext' => 'pdf'),
          array('title' => 'Anwesenheitsliste herunterladen'));
    ?></div>
<?php
  // the event's creator and users with the privileg 'Administrator' are allowed to access 'edit' and 'delete'
  if( $this_user['User']['id'] == $event['Event']['user_id'] ||
      $this->Html->hasPrivileg($this_user, array('Administrator')) ):
?>
    <div class="td icon-edit"><?php
        echo $this->Html->link('bearbeiten',
        array('controller' => 'events', 'action' => 'edit', $event['Event']['id']),
        array('title' => 'Termin bearbeiten'));
    ?></div>
    <div class="td icon-delete"><?php
        echo $this->Form->postLink('löschen',
        array('action' => 'delete', $event['Event']['id']),
        array('title' => 'Termin löschen',
              'confirm' => 'Soll der Termin "' .
              $event['Event']['name'] . '" tatsächlich gelöscht werden?'));
    ?></div>
<?php endif; ?>
  </div>
  <?php endforeach; ?>
  <?php unset($event); ?>
</div>

