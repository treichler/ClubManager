<!-- File: /app/View/Elements/group_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Html->script('image_upload_preview');
  echo $this->Html->script('ckeditor/ckeditor');
  echo $this->Form->input('kind_id', array('label' => 'Art der Gruppe', 'empty'=>true));
  echo $this->Form->input('name', array('label' => 'Bezeichnung der Gruppe'));
  echo $this->Form->input('sorting', array('label' => 'Reihenfolge/Sortierung'));
  echo $this->Form->input('file', array('type' => 'file', 'label' => 'Bild der Gruppe'));
  echo $this->Form->input('info', array('rows' => '3', 'label' => 'Text', 'class' => 'ckeditor'));
  echo $this->Form->input('show_members', array('label' => 'Aktive Mitglieder öffentlich zeigen'));

//  echo $this->Form->input('Membership.Membership', array('empty' => true, 'label' => 'Mitglieder'));

  echo $this->Form->input('Membership',array(
    'label' => __('Mitglieder',true),
    'type' => 'select',
    'multiple' => 'checkbox',
    'selected' => $this->Html->value('Membership.Membership'),
  ));
?>

<script type="text/javascript">
var file_preview = {
  file_input         : document.getElementById('<?php echo Inflector::classify($this->request->params["controller"]) ?>File'),
  max_width          : <?php echo Configure::read('image_landscape_geometry.width') ?>,
  max_height         : <?php echo Configure::read('image_landscape_geometry.height') ?>,
  button_text        : 'Durchsuchen...',
  delete_button_text : 'Bild Löschen',
  preview_info       : 'Bildvorschau -- Bild ziehen zum Ausrichten',
};

ImagePreviewUploader( file_preview );
</script>
