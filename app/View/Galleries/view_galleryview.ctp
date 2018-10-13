<!-- File: /app/View/Galleries/view.ctp -->

<?php // This file contains PHP ?>

<?php
  echo $this->Html->script('jquery-ui-1.8.18.min');

  echo $this->Html->script('jquery.timers-1.2');
  echo $this->Html->script('jquery.easing.1.3');

  echo $this->Html->script('jquery.galleryview-3.0-dev');
  echo $this->Html->css('jquery.galleryview-3.0-dev');
?>

<script>
$(function(){
  $('#myGallery').galleryView({
    panel_width: <?php echo Configure::read('photo_geometry.width') ?>,
    panel_height: <?php echo Configure::read('photo_geometry.height') ?>,
//    frame_width: <?php echo Configure::read('photo_geometry.thumbnail_width') ?>,
    frame_width: <?php echo Configure::read('photo_geometry.width') ?> / 10,
//    frame_width: 90,
//    frame_height: <?php echo Configure::read('photo_geometry.thumbnail_height') ?>,
    frame_height: <?php echo Configure::read('photo_geometry.height') / 10 ?>,
//    frame_height: 50,
    enable_overlays: true,
//    show_captions: true,
    show_filmstrip_nav: true
  });
});
</script>


<h1><?php echo h($gallery['Gallery']['title']); ?></h1>


<ul id="myGallery">
<?php
if (!empty($gallery['Photo'])):
  foreach ($gallery['Photo'] as $photo):
?>
  <li><?php
      $description = 'Hogeladen am ' . $this->Html->getDate($photo['created'], array('year' => 'true'));
      if ($photo['is_creator']) {
        $description .= ', Urheber: ' . $user_names[$photo['user_id']];
      } else {
        $description .= ', von ' . $user_names[$photo['user_id']];
      }
      echo '<img data-frame="' . Router::url(array('controller' => 'photos', 'action' => 'thumb', $photo['id']), true) .
           '" src="' . Router::url(array('controller' => 'photos', 'action' => 'photo', $photo['id']), true) .
           '" title="' . $photo['title'] . '" data-description="' . $description . '" />';
    ?>
  </li>
<?php endforeach; ?>
<?php unset($photo); ?>
<?php endif; ?>
</ul>

<!--
<p>
Erstellt am <?php echo $this->Html->getDate($gallery['Gallery']['created'], array('year' => 'true')) ?>,
von <?php echo $user_names[$gallery['Gallery']['user_id']]; ?>
</p>
-->

