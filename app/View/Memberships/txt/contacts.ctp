<?php // File: /app/View/Memberships/txt/contacts.ctp ?>
<?php foreach ($contacts as $profile): ?>
<?php if ($profile['User']['email']): ?>
<?php echo $profile['User']['email']; ?>;
<?php endif; ?>
<?php endforeach; ?>
<?php unset($profile); ?>
