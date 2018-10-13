<!-- File: /app/View/Galleries/index.ctp -->

<?php // This file contains PHP ?>

<h1>Galerie</h1>

<?php if ($this->Html->hasPrivileg($this_user, array('Gallery upload'))): ?>
<p><?php echo $this->Html->link('Neue Galerie', array('controller' => 'galleries', 'action' => 'add')); ?></p>
<?php endif; ?>

<div class="gallery">
<?php foreach ($galleries as $gallery): ?>
  <div class="galleryItem">
    <h6><?php  // title
      echo $this->Html->link($gallery['Gallery']['title'],
             array('controller' => 'galleries', 'action' => 'view', $gallery['Gallery']['id']));
    ?></h6>
    <?php  // thumbnail
      $link  = Router::url(array('controller' => 'galleries', 'action' => 'view', $gallery['Gallery']['id']), true);
      $photo = Router::url(array('controller' => 'photos', 'action' => 'thumb', $gallery['Gallery']['photo_id']), true);
      echo '<a href="' . $link . '"><img src="' . $photo . '" /></a>';
    ?> 
    <div class="galleryInfo"><?php  // count pictures
      if ($gallery['Gallery']['photo_count']) {
        $text = $gallery['Gallery']['photo_count'];
        $text .= ($gallery['Gallery']['photo_count'] > 1) ? ' Bilder' : 'Bild';
      } else {
        $text = 'keine Bilder';
      }
      echo $this->Html->link($text, array('controller' => 'galleries', 'action' => 'view', $gallery['Gallery']['id']));
    ?></div>
    <div class="galleryInfo"><?php  // date
      echo $this->Html->getDate($gallery['Gallery']['date_stamp'], array('year' => true))
    ?></div>
    <div><?php  // administrative links
      if ($this->Html->hasPrivileg($this_user, array('Gallery upload', 'Gallery modify', 'Gallery delete'))) {
        $links = [];
        if ($this->Html->hasPrivileg($this_user, array('Gallery upload'))) {
          echo $this->Html->link('hochladen',
                 array('controller' => 'galleries', 'action' => 'upload', $gallery['Gallery']['id']));
        }
        if ($this->Html->hasPrivileg($this_user, array('Gallery modify'))) {
          echo ' ' . $this->Html->link('bearbeiten',
                 array('controller' => 'galleries', 'action' => 'edit', $gallery['Gallery']['id']));
        }
        if ($this->Html->hasPrivileg($this_user, array('Gallery delete'))) {
          echo ' ' . $this->Form->postLink('löschen',
                 array('action' => 'delete', $gallery['Gallery']['id']),
                 array('confirm' => 'Soll die Galerie "' . $gallery['Gallery']['title'] . '" tatsächlich gelöscht werden?'));
        }
      }
    ?></div>
  </div>
<?php endforeach; ?>
<?php unset($gallery); ?>
</div>

<ul class="pagination">
<?php for ($i = 0; $i < $pages; $i++): ?>
  <?php if ($i == $page): ?>
    <li class="current"><?php echo $i + 1; ?></li>
  <?php else: ?>
    <li><?php echo $this->Html->link($i+1, array('action' => 'index', '?' => array('page' => $i))); ?></li>
  <?php endif; ?>
<?php endfor; ?>
</ul>

