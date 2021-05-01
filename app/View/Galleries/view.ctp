<!-- File: /app/View/Galleries/view.ctp -->

<?php // This file contains PHP ?>

<?php echo $this->Html->script('galleria-1.6.1.min'); ?>

<h1><?php echo h($gallery['Gallery']['title']); ?></h1>

<div id="galleria">
<?php
if (!empty($gallery['Photo'])):
  foreach ($gallery['Photo'] as $photo):
  $thumb = Router::url(array('controller' => 'photos', 'action' => 'thumb', $photo['id']), true);
  $image = Router::url(array('controller' => 'photos', 'action' => 'photo', $photo['id']), true);
  $description = 'Hogeladen am ' . $this->Html->getDate($photo['created'], array('year' => 'true'));
  if ($photo['is_creator']) {
    $description .= ', Urheber: ' . $user_names[$photo['user_id']];
  } else {
    $description .= ', von ' . $user_names[$photo['user_id']];
  }
?>
  <a href="<?php echo $image; ?>"><img src="<?php echo $thumb; ?>" data-title="<?php echo h($photo['title']); ?>" data-description="<?php echo $description; ?>"></a>
<?php
  endforeach;
  unset($photo);
endif;
?>
</div>

<script>
  // Load the classicmod theme
  Galleria.loadTheme('<?php echo $this->Html->webroot("js/galleria-classicmod/galleria.classicmod.js"); ?>');

  // Initialize Galleria
  Galleria.run('#galleria');
</script>


