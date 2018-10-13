<!-- File: /app/View/Books/index.ctp -->

<?php // This file contains PHP ?>

<h1>Musikb&uuml;cher und Mappen</h1>

<?php if ($this->Html->hasPrivileg($this_user, array('Music book'))): ?>
  <p><?php echo $this->Html->link('Neues Buch', array('controller' => 'books', 'action' => 'add')); ?></p>
<?php endif; ?>

<table>
  <tr>
    <th>Bezeichnung</th>
    <th>Details</th>
  </tr>

  <?php foreach ($books as $book): ?>
  <tr>
    <td>
      <?php
        echo $this->Html->link($book['Book']['title'],
        array('controller' => 'books', 'action' => 'view', $book['Book']['id']));
      ?>
    </td>
    <td><?php echo $book['Book']['description']; ?></td>
    <?php if ($this->Html->hasPrivileg($this_user, array('Music book'))): ?>
    <td class="icon-edit"><?php
        echo $this->Html->link('bearbeiten',
        array('controller' => 'books', 'action' => 'edit', $book['Book']['id']),
        array('title' => 'Musikbuch/Mappe bearbeiten'));
    ?></td>
    <td class="icon-edit"><?php
        echo $this->Html->link('Inhalt bearbeiten',
        array('controller' => 'books', 'action' => 'content', $book['Book']['id']),
        array('title' => 'Inhalt bearbeiten'));
    ?></td>
    <td class="icon-delete"><?php
        echo $this->Form->postLink('löschen',
        array('action' => 'delete', $book['Book']['id']),
        array('confirm' => 'Soll "' . $book['Book']['title'] . '" tatsächlich gelöscht werden?',
              'title' => 'Musikbuch/Mappe löschen'));
    ?></td>
    <?php endif; ?>
  </tr>
  <?php endforeach; ?>
  <?php unset($book) ?>
</table>

