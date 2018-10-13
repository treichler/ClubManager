<!-- File: /app/View/Profiles/view.ctp -->

<?php // This file contains PHP ?>

<h1>Profil</h1>

<p>
<ul>
  <li>Anrede: <b><?php echo $profile['Salutation']['name']; ?></b></li>
  <li>Vorname: <b><?php echo $profile['Profile']['first_name']; ?></b></li>
  <li>Familienname: <b><?php echo $profile['Profile']['last_name']; ?></b></li>
  <li>Titel:
    <b>
      <?php foreach($profile['Title'] as $title): ?>
        <?php echo $title['acronym']; ?>
      <?php  endforeach; ?>
    </b>
  </li>
  <li>Geburtstag: <b><?php echo $profile['Profile']['birthday']; ?></b></li>
  <li>Telefon privat: <b><?php echo $profile['Profile']['phone_private']; ?></b></li>
  <li>Telefon mobil: <b><?php echo $profile['Profile']['phone_mobile']; ?></b></li>
  <li>Telefon Arbeit: <b><?php echo $profile['Profile']['phone_office']; ?></b></li>
</ul>
</p>

<a name='photo'>
<?php
  echo '<img id=\'profilePhoto\' src=\'/cake_2_0/profiles/attachment/' .
       $profile['Profile']['id'] . '\' class=\'diagram\' border=\'0\'>'
?>
</a>

