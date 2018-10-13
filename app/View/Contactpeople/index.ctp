<!-- File: /app/View/Contactpeople/index.ctp -->

<?php // This file contains PHP ?>

<h1>Kontaktpersonen</h1>


<div id="contactpeople">
<?php foreach ($contactpeople as $contactperson):
  $phone = '';
  if ($contactperson['Contactperson']['contactlist_phone']) {
    if ($contactperson['Profile']['phone_office'])
      $phone = $contactperson['Profile']['phone_office'];
    if ($contactperson['Profile']['phone_private'])
      $phone = $contactperson['Profile']['phone_private'];
    if ($contactperson['Profile']['phone_mobile'])
      $phone = $contactperson['Profile']['phone_mobile'];
  }

  $email = '';
  if ($contactperson['Contactperson']['contactlist_email'] && 
      isset($contactperson['Profile']['User']['email'])) {
    $email = $contactperson['Profile']['User']['email'];
  }

  if (!$phone && !$email)
    continue;

  $functions = [];
  foreach ($contactperson['Profile']['Membership']['Group'] as $group) {
    if ($group['Kind']['is_official'])
      $functions[] = $group['name'];
  }
  $name = $contactperson['Profile']['first_name'] . ' ' . $contactperson['Profile']['last_name'];
  $titles_pre = $titles_post = [];
  foreach ($contactperson['Profile']['Title'] as $title) {
    if ($title['placement'] < 0)
      $titles_pre[] = $title['acronym'];
    else
      $titles_post[] = $title['acronym'];
  }
  $img_src = Router::url(array('controller' => 'profiles', 'action' => 'attachment', $contactperson['Profile']['id'], true));
?>
<div class="contactpeople">
  <img src="<?php echo $img_src; ?>" />
  <ul>
    <li class="function"><?php echo implode($functions, ', '); ?></li>
    <li class="name">
      <small><?php echo implode($titles_pre, ' ') ?></small>
      <?php echo $name ?><?php if(!empty($titles_post)) echo ',' ?>
      <small><?php echo implode($titles_post, ' ') ?></small>
    </li>
    <li class="email"><a href="mailto:<?php echo $email ?>"><?php echo $email ?></a></li>
    <li class="phone"><a href="tel:<?php echo $phone ?>"><?php echo $phone ?></a></li>
  </ul>
</div>
<?php endforeach; ?>
<?php unset($contactperson); ?>
</div>

