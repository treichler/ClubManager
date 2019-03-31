<!-- File: /app/View/Galleries/edit.ctp -->

<?php // This file contains PHP ?>

<h1>Galerie bearbeiten</h1>

<p>
  <b>Galerie:</b>
  <?php
    echo $this->Html->link(h($this->Html->value('Gallery')['title']),
    array('controller' => 'galleries', 'action' => 'view', $this->Html->value('Gallery')['id']));
  ?>
</p>

<?php
  echo $this->Form->create('Gallery', array());
  echo $this->element('gallery_form');
//  echo $this->Form->input('photo_id', array('label' => 'Titelbild', 'empty' => true));
  echo $this->Form->input('id', array('type' => 'hidden'));
?>

<?php if ($this->Html->value('Photo')): ?>
<table>
  <tr>
    <th>Bild</th>
    <th>Titel</th>
    <th>Titelbild</th>
    <th>Hochgeladen von</th>
    <th>Urheber</th>
  </tr>

  <?php $i = 0; ?>
  <?php foreach ($this->Html->value('Photo') as $photo): ?>
  <tr id="galleryPhotosTableRow<?php echo $photo['id'] ;?>">
    <td><?php
        echo $this->Form->input('Photo.' . $i . '.id', array('type' => 'hidden', 'value' => $photo['id']));
        echo $this->Form->input('Photo.' . $i . '.user_id', array('type' => 'hidden', 'value' => $photo['user_id']));
        echo $this->Form->input('Photo.' . $i . '.gallery_id', array('type' => 'hidden', 'value' => $photo['gallery_id']));
        echo '<img src="' . Router::url(array('controller' => 'photos', 'action' => 'thumb', $photo['id']), true) . '" />';
      ?></td>
    <td><?php echo $this->Form->input('Photo.' . $i . '.title', array('label' => false, 'value' => $photo['title'])); ?></td>
    <td><?php
        echo '<input name="data[Gallery][photo_id]" type="radio" value="' . $photo['id'] .
             '" id="GalleryPhotoId' . $photo['id'] . '" ' .
             ($photo['id'] == $this->Html->value('Gallery')['photo_id'] ? 'checked ' : '') . '/>';
      ?></td>
    <td><?php echo $user_names[$photo['user_id']]; ?></td>
    <td><?php echo $this->Form->input('Photo.' . $i . '.is_creator', array('label' => false, 'value' => $photo['is_creator'])); ?></td>
    <td><a href="javascript:void(0)" onclick="deletePhoto(<?php echo $photo['id'] ?>)" >l&ouml;schen</a></td>
  </tr>
    <?php $i++; ?>
  <?php endforeach; ?>
  <?php unset($photo); ?>
</table>
<?php endif; ?>

<? echo $this->Form->end('Speichern'); ?>


<script type="text/javascript">
function deletePhoto(id) {
  if (confirm("Soll das Bild tatsächlich gelöscht werden?")) {

  $.ajax({
    url: "<?php echo Router::url(array('controller' => 'photos', 'action' => 'delete'), true) . "/"; ?>" + id,
    type: 'POST',
    dataType: 'html',
    data: 'whatever',
    success: function(data, textStatus, jqXHR){
      if (data == 'true') {

        target = $("#galleryPhotosTableRow" + id);
        target.hide('slow', function(){ target.remove(); });

      } else {
        if (data == 'false')
          alert('Foto konnte nicht gelöscht werden');
        else
          alert('Oops, something weired happened.');
      }
    },
    error: function(){
      alert("Übertragungsfehler");
    }
  });

  }
  return false;
}

</script>

