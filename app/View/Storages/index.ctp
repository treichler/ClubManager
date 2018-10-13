<!-- File: /app/View/Storages/index.ctp -->

<?php // This file contains PHP ?>

<h1>Storages</h1>
<table>
  <tr>
    <th>Id</th>
    <th>Name</th>
    <th>Extension</th>
    <th>Size</th>
    <th>Type</th>
    <th>Folder</th>
    <th>uuid</th>
    <th>created</th>
    <th>modified</th>
  </tr>

  <?php foreach($storages as $storage): ?>
  <tr>
    <td><?php echo $storage['Storage']['id']; ?></td>
    <td><?php echo $storage['Storage']['name']; ?></td>
    <td><?php echo $storage['Storage']['extension']; ?></td>
    <td><?php echo $storage['Storage']['size']; ?></td>
    <td><?php echo $storage['Storage']['type']; ?></td>
    <td><?php echo $storage['Storage']['folder']; ?></td>
    <td><?php echo $storage['Storage']['uuid']; ?></td>
    <td><?php echo $storage['Storage']['created']; ?></td>
    <td><?php echo $storage['Storage']['modified']; ?></td>
  </tr>
  <?php endforeach; ?>
  <?php unset($storage); ?>

</table>

