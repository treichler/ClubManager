<!-- File: /app/View/Groups/details.ctp -->

<?php // This file contains PHP ?>

<p>
<?php
  echo $this->Html->link('bearbeiten',
  array('controller' => 'groups', 'action' => 'edit', $group['Group']['id']));
?>
 | 
<?php
  echo $this->Html->link('Index',
  array('controller' => 'groups', 'action' => 'organize'));
?>
</p>

<h1><?php echo $group['Group']['name']?></h1>

<?php
  if ($group['Group']['storage_id']) {
    $src = Router::url(array('controller' => 'groups', 'action' => 'attachment', $group['Group']['id']), true);
    echo '<img src="' . $src . '" />';
  }
?>

<p><?php echo $group['Group']['info']?></p>

<h4>Zugeordnete Personen (<?php echo count($memberships) ?>)</h4>

<?php
  function array_has_key($arr, $key) {
    if (is_array($arr)) {
      foreach ($arr as $k => $a) {
        if ($k == $key)
          return true;
      }
    }
    return false;
  }
?>

<div class="gallery">
<?php
  foreach ($memberships as $membership):
    // check if profile should be shown
//    if (!$membership['State']['show_public'] || !$membership['Profile']['show_name'])
//      continue;
?>
  <div class="galleryItem">
    <h6><?php echo $this->Html->formatName($membership['Profile']); // name ?></h6>
    <?php // photo
      $src = Router::url(array('controller' => 'profiles', 'action' => 'attachment', $membership['Profile']['id'] ), true);
      echo '<img src="' . $src . '" />';
    ?> 
    <div><?php // info
      $group_names = [];
      $group_names[] = $membership['State']['name'];
      foreach ($membership['Group'] as $g) {
        if (array_has_key($kinds, $g['kind_id']))
          $group_names[] = $g['name'];
      }
      echo implode($group_names, ', ');
    ?></div>
  </div>
<?php endforeach; ?>
<?php unset($membership); ?>
</div>

