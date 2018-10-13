<!-- File: /app/View/Galleries/upload.ctp -->

<?php // This file contains PHP ?>

<script>window.FileAPI = { staticPath: '<?php echo Router::url("/js/FileAPI/"); ?>' };</script>
<?php echo $this->Html->script('FileAPI/FileAPI.min'); ?>


<!-- ******************************************************************************** -->

<h1>Fotos hochladen</h1>

<p>
  <b>Galerie:</b>
  <?php
    echo $this->Html->link(h($gallery['Gallery']['title']),
    array('controller' => 'galleries', 'action' => 'view', $gallery['Gallery']['id']));
  ?> |
  <?php
    echo $this->Html->link('bearbeiten',
    array('controller' => 'galleries', 'action' => 'edit', $gallery['Gallery']['id']));
  ?>
</p>

<p>
  Galerie erstllt am <?php echo h($gallery['Gallery']['created']); ?>,
  von <?php echo h($user_names[$gallery['Gallery']['user_id']]); ?>
</p>


  <div>
    <!-- "js-fileapi-wrapper" -- required class -->
    <div class="js-fileapi-wrapper upload-btn">
      <div class="upload-btn__txt">Bilder auswählen (minimale Auflösung: <res id="resolution">undefined</res>)</div>
      <input id="choose" name="files" type="file" multiple />
    </div>
    <div id="images" class="gallery"><!-- previews --></div>
    <button id="uploadFiles">Bilder hochladen</button>
    <div>Fortschritt: <progress id="uploadProgress" value="0" max="1">0%</progress></div>
  </div>


<script>
$(document).ready(function(){
  $('#uploadFiles').click(function(e){ upload(); });
//  $('#stopupload').click(function(e){ uploader.stop(); });
  resolution.innerHTML = min_width + 'x' + min_height;
});

var min_width    = <?php echo Configure::read('photo_geometry.orig_width'); ?>;
var min_height   = <?php echo Configure::read('photo_geometry.orig_height'); ?>;
var thumb_width  = <?php echo Configure::read('photo_geometry.thumbnail_width'); ?>;
var thumb_height = <?php echo Configure::read('photo_geometry.thumbnail_height'); ?>;

function upload() {
  if (files.length == 0) {
    alert('Nichts zum hochladen.');
    return;
  }

  // Uploading Files
  FileAPI.upload({
    url: '<?php echo Router::url(array(), true); ?>' + '/ctrl.php',
    files: { images: files },
    complete: function (err, xhr){
      if (err) {
        alert('Dateien konnten nicht hochgeladen werden.\r\nFehler: ' + err);
        window.location.replace("<?php echo Router::url(array('action' => 'index'), true); ?>");
      } else {
        alert('Dateien wurden erfolgreich hochgeladen');
        window.location.replace("<?php echo Router::url(array('action' => 'edit', $gallery['Gallery']['id']), true); ?>");
      }
    },
    progress: function (evt/**Object*/, file/**Object*/, xhr/**Object*/, options/**Object*/){
      ratio = evt.loaded/evt.total;
      uploadProgress.innerHTML = Math.floor(ratio * 100) + '%';
      uploadProgress.value = ratio;
    },
    imageTransform: {
      width: min_width,
      height: min_height,
//      rotate: 45,
      preview: true,
//      type: 'image/png',
      type: 'image/jpeg',
      quality: 0.95
    }
  });

  // remove the upload button to prevent multiple uploads
  uploadFiles.remove();
}


var files = [];

FileAPI.event.on(choose, 'change', function (evt){

//  b.forEach(function(x){ a.push(x); })
//  files = FileAPI.getFiles(evt); // Retrieve file list
  FileAPI.getFiles(evt).forEach(function(file){
    files.push(file);
  }); // Retrieve file list

  fileFilterAndPreview(files);
});


function fileFilterAndPreview(files) {
  // clear preview
  images.innerHTML='';

  // filter files and process preview
  FileAPI.filterFiles(files, function (file, info/**Object*/){
    if( /^image/.test(file.type) ){
      size_ok = info.width >= min_width && info.height >= min_height;
      if (!size_ok)
        console.log('picture is too small');
      return size_ok;
    }
    return  false;
  }, function (files/**Array*/, rejected/**Array*/){
    if( files.length ){
      // Make preview 100x100
      FileAPI.each(files, function (file, index){
        FileAPI.Image(file).preview(thumb_width, thumb_height).get(function (err, img){
          container = document.createElement('div');
          container.className = 'galleryItem';
          info = document.createElement('div');
          info.innerHTML  = '<div>' + file.name + '</div>';
          info.innerHTML += '<a href="javascript:removeFile(' + index + ')">entfernen</a>';
          container.appendChild(img);
          container.appendChild(info);
          images.appendChild(container);
        });
      });
    }
  });
}


function removeFile(index) {
  if (index >= files.length || index < 0) {
    return;
  }
  files = files.slice(0,index).concat(files.slice(index+1));
  fileFilterAndPreview(files);
}
</script>


<!--

<div id="container">
  <div id="filelist"></div>
  <br />
  <button id="pickfiles">Dateien ausw&auml;hlen</button>
  <button id="uploadfiles">Dateien hochladen</button>
  <button id="stopupload">Hochladen stoppen</button>
</div>

-->

<script type="text/javascript">
/*

$(document).ready(function(){
  uploader.init();
  $('#uploadfiles').click(function(e){ uploader.start(); });
  $('#stopupload').click(function(e){ uploader.stop(); });
});

var uploader = new plupload.Uploader({
  runtimes : 'html5,flash,silverlight,html4',
  browse_button : 'pickfiles',
  drop_element : 'container',
  container: 'container',
  max_file_size : '10mb',
  url : '<?php echo Router::url(array(), true); ?>',
  resize : {
    width : <?php echo Configure::read('photo_geometry.width') ?>,
    height : <?php echo Configure::read('photo_geometry.height') ?>,
    quality : 90,
    crop : true
  },
  flash_swf_url : '<?php echo $this->webroot; ?>js/Moxie.swf',
  silverlight_xap_url : '<?php echo $this->webroot; ?>js/Moxie.xap',
  filters : [
    {title : "Image files", extensions : "jpg,gif,png"}
  ]
});

uploader.bind('FilesAdded', function(up, files) {
  for (var i in files) {
    $('#filelist').append('<div id="' + files[i].id + '">' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ') <b></b><a href="javascript:void(0)" onclick="deleteFile(\'' + files[i].id + '\')">l&ouml;schen</a></div>');
  }
});

uploader.bind('UploadProgress', function(up, file) {
  $('#' + file.id + ' b').html('<span>' + file.percent + '%</span>');
  $('#' + file.id + ' a').remove();
});

uploader.bind('FileUploaded', function(up, file) {
  $('#' + file.id + ' b').html('<span>100%</span>');
});

uploader.bind('UploadComplete', function(up, files) {
  $('#pickfiles').remove();
  $('#uploadfiles').remove();
  $('#stopupload').remove();
  $('#container input').remove();
  $('#container').append('<p>Dateien wurden hochgeladen.</p>')
});

function deleteFile(id) {
  file = uploader.getFile(id);
  uploader.removeFile(file);
  $('#' + id).remove();
}

*/
</script>

