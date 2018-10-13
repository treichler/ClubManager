<!-- File: /app/View/Users/view.ctp -->

<?php // This file contains PHP ?>

<h1>Benutzername: <?php echo $user['User']['username']; ?></h1>

<p>
  <?php
    if (empty($user['Profile']['id']))
      echo $this->Html->link('Profil anlegen', array('controller' => 'profiles', 'action' => 'add'));
    else
      echo $this->Html->link('Profil bearbeiten', array('controller' => 'profiles', 'action' => 'edit', $user['Profile']['id']));
  ?> |
  <?php echo $this->Html->link('Email ändern', array('controller' => 'users', 'action' => 'change_email')); ?> |
  <?php echo $this->Html->link('Passwort ändern', array('controller' => 'users', 'action' => 'change_password')); ?>
</p>

<p>E-Mail: <b><?php echo $user['User']['email']; ?></b></p>

<?php if (!empty($user['Profile']['id'])): ?>
<ul>
<!--
  <li>Anrede: <b><?php echo $user['Profile']['salutation_id']; ?></b></li>
-->
  <li>Vorname: <b><?php echo $user['Profile']['first_name']; ?></b></li>
  <li>Familienname: <b><?php echo $user['Profile']['last_name']; ?></b></li>
  <li>Geburtstag: <b><?php echo $user['Profile']['birthday']; ?></b></li>
  <li>Telefon privat: <b><?php echo $user['Profile']['phone_private']; ?></b></li>
  <li>Telefon mobil: <b><?php echo $user['Profile']['phone_mobile']; ?></b></li>
  <li>Telefon Arbeit: <b><?php echo $user['Profile']['phone_office']; ?></b></li>
</ul>
<img <?php
  echo 'src="' . Router::url(array('controller' => 'profiles', 'action' => 'attachment', $user['Profile']['id']), true) . '"';
?> />
<?php endif; ?>


<?php if (isset($membership['Group'])): ?>
<p>Mitglied bei:</p>
<ul>
  <?php foreach ($membership['Group'] as $group): ?>
  <li><?php echo h($group['name']); ?></li>
  <?php endforeach; unset($group); ?>
</ul>
<?php endif; ?>

<?php if (!empty($user['Privileg'])): ?>
<p>Benutzerrechte:</p>
<ul>
  <?php foreach ($user['Privileg'] as $privileg): ?>
  <li><?php echo $privileg['name'] ?></li>
  <?php endforeach; ?>
  <?php unset($privileg); ?>
</ul>
<?php endif; ?>

