<!-- File: /app/View/Elements/blog_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Html->script('image_upload_preview');

  echo $this->Html->script('ckeditor');

  echo $this->Form->input('title', array('label' => 'Titel'));
  echo $this->Form->input('body', array('rows' => '3', 'label' => 'Text', 'class' => 'ckeditor'));

  // extract current tags
  $tags = array();
  if ($this->Html->value('Tag')) {
    foreach($this->Html->value('Tag') as $tag) {
      $tags[] = $tag['name'];
    }
  }
  // In case of a comma separated text input, the tags are transported to the
  // web-server by a temporary input.
  // On the server-side this field is handled by the corresponding Blog-model.
  echo $this->Form->input('temp_tags', array(
    'label' => 'Tags (mit Komma trennen)',
    'value' => implode(', ',$tags)
  ));
  unset($tag, $tags);

  echo $this->Form->input('file', array('type' => 'file', 'label' => 'Titelbild'));

  echo $this->Form->input('time_stamp', array(
    'label' => 'Datum, das zum Eintrag passt',
    'class' => 'date-time',
    'dateFormat' => 'DMY',
    'minYear' => date('Y') - 2,
    'maxYear' => date('Y'),
    'timeFormat'=> '24',
    'interval' => 15
  ));
  if ($this->Html->hasPrivileg($this_user, array('Blog expiry'))) {
    echo $this->Form->input('expiry', array(
      'label' => 'Ende der hohen Priorität',
      'class' => 'date-time',
      'dateFormat' => 'DMY',
      'minYear' => date('Y'),
      'maxYear' => date('Y') + 2,
    ));
  }
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

