<!-- File: /app/View/Elements/blog_form.ctp -->

<?php // This file holds the common form elements for add.ctp and edit.ctp
  echo $this->Html->script('ckeditor');

  echo $this->Form->input('title', array('label' => 'Titel'));
  echo $this->Form->input('body', array('rows' => '3', 'label' => 'Text', 'class' => 'ckeditor'));

  // HABTM field: multiple select
//  echo $this->Form->input('Tag.Tag');

/*
  // HABTM field: checkboxes
  echo $this->Form->input('Tag',array(
    'label' => __('Tags',true),
    'type' => 'select',
    'multiple' => 'checkbox',
//    'options' => $tags,
    'selected' => $this->Html->value('Tag.Tag'),
  ));
*/

/*
  // list related HABTM relations
  if ($this->Html->value('Tag')) {
    echo '<ul>';
    foreach($this->Html->value('Tag') as $tag) {
      echo '<li>';
      echo $tag['name'];
      echo '</li>';
    }
    echo '</ul>';
  }
*/

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
//  echo $this->Form->input('file_tmp', array('type' => 'file', 'label' => 'Titelbild'));
?>
<div id="preview"></div>
<?php
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

//var fileinput = document.getElementById('fileinput');
var fileinput = document.getElementById('BlogFile');

//var max_width = fileinput.getAttribute('data-maxwidth');
//var max_height = fileinput.getAttribute('data-maxheight');
//var max_width = 300;
//var max_height = 200;
var max_width = <?php echo Configure::read('image_landscape_geometry.width') ?>;
var max_height = <?php echo Configure::read('image_landscape_geometry.width') ?>;

var preview = document.getElementById('preview');

//var form = document.getElementById('form');
// TODO needs to be initialized different since both forms are used 'add' and 'edit'
var form = document.getElementById('BlogEditForm');

var hidden_file_input_name = 'data[Blog][file_resized]';
var hidden_file_input_id = 'BlogFileResized';

function processfile(file) {
  
    if( !( /image/i ).test( file.type ) )
        {
            alert( file.name +" ist keine Bilddatei." );
//            alert( "File "+ file.name +" is not an image." );
            return false;
        }

    // read the files
    var reader = new FileReader();
    reader.readAsArrayBuffer(file);
    
    reader.onload = function (event) {
      // blob stuff
      var blob = new Blob([event.target.result]); // create blob...
      window.URL = window.URL || window.webkitURL;
      var blobURL = window.URL.createObjectURL(blob); // and get it's URL
      
      // helper Image object
      var image = new Image();
      image.src = blobURL;
      //preview.appendChild(image); // preview commented out, I am using the canvas instead
      image.onload = function() {
        // have to wait till it's loaded
        var resized = resizeMe(image); // send it to canvas
        var new_input_data = document.createElement("input");
        new_input_data.type = 'hidden';
        new_input_data.name = hidden_file_input_name + '[data]';
        new_input_data.id   = hidden_file_input_id + 'Data';
        new_input_data.value = resized; // put result from canvas into new hidden input
        form.appendChild(new_input_data);

        var new_input_name = document.createElement("input");
        new_input_name.type = 'hidden';
        new_input_name.name = hidden_file_input_name + '[name]';
        new_input_name.id   = hidden_file_input_id + 'Name';
        new_input_name.value = file.name;
        form.appendChild(new_input_name);

        var new_input_size = document.createElement("input");
        new_input_size.type = 'hidden';
        new_input_size.name = hidden_file_input_name + '[size]';
        new_input_size.id   = hidden_file_input_id + 'Size';
        new_input_size.value = file.size;
        form.appendChild(new_input_size);

        var new_input_type = document.createElement("input");
        new_input_type.type = 'hidden';
        new_input_type.name = hidden_file_input_name + '[type]';
        new_input_type.id   = hidden_file_input_id + 'Type';
        new_input_type.value = file.type;
        form.appendChild(new_input_type);
      }
    };
}

function readfiles(files) {
  
    // remove the existing canvases and hidden inputs if user re-selects new pics
    var existing_inputs_data = document.getElementsByName(hidden_file_input_name + '[data]');
    var existing_inputs_name = document.getElementsByName(hidden_file_input_name + '[name]');
    var existing_inputs_size = document.getElementsByName(hidden_file_input_name + '[size]');
    var existing_inputs_type = document.getElementsByName(hidden_file_input_name + '[type]');
    var existingcanvases = document.getElementsByTagName('canvas');
    while (existing_inputs_data.length > 0) { // it's a live list so removing the first element each time
      // DOMNode.prototype.remove = function() {this.parentNode.removeChild(this);}
      form.removeChild(existing_inputs_data[0]);
      form.removeChild(existing_inputs_name[0]);
      form.removeChild(existing_inputs_size[0]);
      form.removeChild(existing_inputs_type[0]);
      preview.removeChild(existingcanvases[0]);
    }

    for (var i = 0; i < files.length; i++) {
      processfile(files[i]); // process each file at once
    }
    fileinput.value = ""; //remove the original files from fileinput
    // TODO remove the previous hidden inputs if user selects other files
}

// this is where it starts. event triggered when user selects files
fileinput.onchange = function(){
  if ( !( window.File && window.FileReader && window.FileList && window.Blob ) ) {
//    alert('The File APIs are not fully supported in this browser.');
    alert('Datei API wird in diesem Browser nicht unterstützt. Upload dauert daher länger bzw kann bei großen Dateien fehlschlagen');
    return false;
    }
  readfiles(fileinput.files);
//  return false;
}

// === RESIZE ====

function resizeMe(img) {
  
  var canvas = document.createElement('canvas');

  var width = img.width;
  var height = img.height;

  // calculate the width and height, constraining the proportions
  if (width > height) {
    if (width > max_width) {
      //height *= max_width / width;
      height = Math.round(height *= max_width / width);
      width = max_width;
    }
  } else {
    if (height > max_height) {
      //width *= max_height / height;
      width = Math.round(width *= max_height / height);
      height = max_height;
    }
  }
  
  // resize the canvas and draw the image data into it
  canvas.width = width;
  canvas.height = height;
  var ctx = canvas.getContext("2d");
  ctx.drawImage(img, 0, 0, width, height);
  
  preview.appendChild(canvas); // do the actual resized preview
  
//  return canvas.toDataURL("image/jpeg",0.7); // get the data from canvas as 70% JPG (can be also PNG, etc.)
  return canvas.toDataURL("image/jpeg",0.7).replace(/^data:image\/(png|jpeg);base64,/, "");
}

</script>

