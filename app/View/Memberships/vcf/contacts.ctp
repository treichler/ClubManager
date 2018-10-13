<?php // File: /app/View/Memberships/vcf/contacts.ctp ?>
<?php foreach ($contacts as $profile): ?>
BEGIN:VCARD
VERSION:3.0
N:<?php echo h($profile['Profile']['last_name'] . ';' . $profile['Profile']['first_name']) ?> 
FN:<?php echo h($profile['Profile']['first_name'] . ' ' . $profile['Profile']['last_name']) ?> 
<?php if ($profile['User']['email']): ?>
EMAIL;TYPE=INTERNET:<?php echo h($profile['User']['email']) ?> 
<?php endif; ?>
<?php $pref_phone = false; ?>
<?php if ($profile['Profile']['phone_mobile']): ?>
TEL;TYPE=VOICE,CELL,PREF:<?php echo h($profile['Profile']['phone_mobile']) ?> 
<?php $pref_phone = true; ?>
<?php endif; ?>
<?php if ($profile['Profile']['phone_private']): ?>
TEL;TYPE=VOICE,HOME<?php if (!$pref_phone) echo ',PREF' ?>:<?php echo h($profile['Profile']['phone_private']) ?> 
<?php $pref_phone = true; ?>
<?php endif; ?>
<?php if ($profile['Profile']['phone_office']): ?>
TEL;TYPE=VOICE,WORK<?php if (!$pref_phone) echo ',PREF' ?>:<?php echo h($profile['Profile']['phone_office']) ?> 
<?php endif; ?>
END:VCARD

<?php endforeach; ?>
<?php unset($profile); ?>
