/**
 * @file image_upload_preview.js
 *
 * Example for assigning image_upload_preview to a file input element:
 *
 * var file_preview = {
 *   file_input         : document.getElementById('BlogFile'),
 *   max_width          : 1280,
 *   max_height         : 800,
 *   button_text        : 'Durchsuchen...',
 *   delete_button_text : 'Bild Löschen',
 *   preview_info       : 'Bildvorschau -- Bild ziehen zum Ausrichten',
 * };
 *
 * ImagePreviewUploader( file_preview );
 */


/**
 * calculate maximum width and height, constraining the proportions
 */
function rescaleProportional(w_src, h_src, w_target, h_target) {
  if( (w_src/h_src) < (w_target/h_target) ) {
    // scale width
    if( w_src > w_target ) {
      h_src = Math.round(h_src * w_target / w_src);
      w_src  = w_target;
    }
  } else {
    // scale height
    if( h_src > h_target ) {
      w_src  = Math.round(w_src * h_target / h_src);
      h_src = h_target;
    }
  }
  return [w_src, h_src];
}


/**
 * Resize and draw image
 */
function ResizeImage(image, preview) {
  width  = image.width;
  height = image.height;

  // calculate maximum width and height, constraining the proportions
  if( (image.width/image.height) < (preview.max_width/preview.max_height) ) {
    // scale width
    if( image.width > preview.max_width ) {
      width  = preview.max_width;
      height = Math.round(image.height * preview.max_width / image.width);
    }
  } else {
    // scale height
    if( image.height > preview.max_height ) {
      width  = Math.round(image.width * preview.max_height / image.height);
      height = preview.max_height;
    }
  }

  preview.img_width  = width;
  preview.img_height = height;

  // resize the canvas and draw the image to preview tag
  preview.canvas.width  = preview.img_width;
  preview.canvas.height = preview.img_height;
  var ctx = preview.canvas.getContext( "2d" );
  ctx.drawImage( image, 0, 0, preview.img_width, preview.img_height );
}


/**
 * Process the selected file from fileinput
 */
function FilePreview(file, uploader) {
  // check file type
  if( !( /image/i ).test( file.type ) ) {
    alert( "File "+ file.name +" is not an image." );
    uploader.file = null;
    return false;
  }
  uploader.file = file;

  // read the file
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
    image.onload = function() {
      // have to wait till it's loaded
      uploader.image = image;
      ResizeImage(image, uploader);
    };
  };
}


/**
 * prepare form for upload
 */
function PrepareFormForUpload( uploader ) {
// TODO do proper geometry calculation if canvas is scaled by CSS
// 
// NOTE
// get scaling of canvas and round it to x-decimal precision
// (file_preview.canvas.getBoundingClientRect().width / file_preview.canvas.clientWidth).toFixed(2)

  if( uploader.file ) {
    // calculate resizing geometry
    if( (uploader.image.width/uploader.image.height) < (uploader.max_width/uploader.max_height) ) {
      // we have y offset
      sx = 0;
      sy = Math.round(uploader.offset_y*uploader.image.width/uploader.max_width);
      sw = uploader.image.width;
      sh = Math.round(uploader.max_height*uploader.image.width/uploader.max_width);
    } else {
      // we have x offset
      sx = Math.round(uploader.offset_x*uploader.image.height/uploader.max_height);
      sy = 0;
      sw = Math.round(uploader.max_width*uploader.image.height/uploader.max_height);
      sh = uploader.image.height;
    }

    // resize the canvas and draw the image to preview tag
    uploader.canvas.style.marginLeft = "0px";
    uploader.canvas.style.marginTop  = "0px";
    uploader.canvas.width  = uploader.max_width;
    uploader.canvas.height = uploader.max_height;
    ctx = uploader.canvas.getContext( "2d" );
    ctx.drawImage( uploader.image, sx, sy, sw, sh, 0, 0, uploader.max_width, uploader.max_height );

    // get the data from canvas as 95% JPG (can be also PNG, etc.)
    console.debug("save image blob with offset x=" + String(uploader.offset_x) + ", y=" + String(uploader.offset_y) );
    uploader.resized_file_input.data.value = uploader.canvas.toDataURL("image/jpeg",0.95).replace(/^data:image\/(png|jpeg);base64,/, "");
    uploader.resized_file_input.container.appendChild(uploader.resized_file_input.data);
    uploader.resized_file_input.name.value = uploader.file.name;
    uploader.resized_file_input.container.appendChild(uploader.resized_file_input.name);
    uploader.resized_file_input.size.value = uploader.file.size;
    uploader.resized_file_input.container.appendChild(uploader.resized_file_input.size);
    uploader.resized_file_input.type.value = uploader.file.type;
    uploader.resized_file_input.container.appendChild(uploader.resized_file_input.type);

    // remove original file from file input
    uploader.file_input.value = "";
  } else {
    // clear hidden resized-file-input
    uploader.resized_file_input.container.innerHTML = null
  }
}


/**
 * Initialize image preview uploader
 */
function ImagePreviewUploader( uploader ) {
  // set some defaults
  uploader.offset_x   = 0;
  uploader.offset_y   = 0;
  uploader.img_width  = 0;
  uploader.img_height = 0;
  uploader.canvas     = document.createElement('canvas');
  uploader.file       = null;

  // find related form element
  p = uploader.file_input.parentElement;
  var i;
  var max_depth = 5;
  for( i = 0; i < max_depth; i ++) {
    if( 'FORM' == p.nodeName )
      break;
    p = p.parentElement;
  }
  if( (i == max_depth) && ('FORM' != p.nodeName) ) {
    alert('Maximum recursion depth reached and no form found');
    return false;
  }
  uploader.form = p;
  if( uploader.form.onsubmit ) {
    alert('There is already an event assigned to form.onsubmit');
    return false;
  }

  // assign upload preparation function to onsubmit event
  uploader.form.onsubmit = function() {
    try {
      PrepareFormForUpload( uploader );
    }
    catch(err) {
      alert('Some error occurred during image processing.');
      return false;
    }
  };

  // create file preview if not specified
  if( ! uploader.hasOwnProperty('file_preview') ) {
    uploader.file_preview = document.createElement("div");
    uploader.file_preview.id = 'preview';
    uploader.file_input.insertAdjacentElement('afterend', uploader.file_preview);
  }

  // append canvas to preview element
  uploader.file_preview.appendChild(uploader.canvas);

  // append description to preview element
  d = document.createElement("div");
  if( uploader.hasOwnProperty('preview_info') )
    d.innerHTML = '<div>' + uploader.preview_info + '</div>';
  else
    d.innerHTML = '<div>Image preview - Drag picture to align</div>';
  uploader.file_preview.appendChild(d);

  // create button for file opening and hide original file input
  uploader.file_open_button = document.createElement("input")
  uploader.file_open_button.type = 'button';
  if( uploader.hasOwnProperty('button_text') )
    uploader.file_open_button.value = uploader.button_text;
  else
    uploader.file_open_button.value = 'Browse...';
  uploader.file_input.insertAdjacentElement('beforebegin', uploader.file_open_button);
  uploader.file_open_button.onclick = function(){ uploader.file_input.click(); };
  uploader.file_input.hidden = true;

/*
  // TODO create button for file delete
  uploader.file_delete_button = document.createElement("input")
  uploader.file_delete_button.type = 'button';
  if( uploader.hasOwnProperty('delete_button_text') )
    uploader.file_delete_button.value = uploader.delete_button_text;
  else
    uploader.file_delete_button.value = 'Delete Image';
  uploader.file_input.insertAdjacentElement('beforebegin', uploader.file_delete_button);
  uploader.file_delete_button.onclick = function() {
    // TODO delete image
    console.debug('TODO: delete Image');
  };
*/

  // append file preview function to file input
  uploader.file_input.onchange = function(e) {
    uploader.offset_x = 0;
    uploader.offset_y = 0;
    uploader.canvas.style.marginLeft = "0px";
    uploader.canvas.style.marginTop  = "0px";
    FilePreview( this.files[0], uploader );
  };

  // move image on mouse down/move
  uploader.file_preview.addEventListener( "mousedown" , function(event) {
    var x0 = event.clientX + uploader.offset_x;
    var y0 = event.clientY + uploader.offset_y;
    // allow movement over the whole document
    document.onmousemove = function (e) {
      if( uploader.img_width == uploader.max_width ) {
        // move y
        uploader.offset_y = y0 - e.clientY;
        if( uploader.offset_y < 0 )
          uploader.offset_y = 0;
        if( uploader.offset_y > (uploader.img_height - uploader.max_height) )
          uploader.offset_y = uploader.img_height - uploader.max_height;
        uploader.canvas.style.marginTop = "-" + String(uploader.offset_y) + "px"
      } else if( uploader.img_height == uploader.max_height ) {
        // move x
        uploader.offset_x = x0 - e.clientX;
        if( uploader.offset_x < 0 )
          uploader.offset_x = 0;
        if( uploader.offset_x > (uploader.img_width - uploader.max_width) )
          uploader.offset_x = uploader.img_width - uploader.max_width;
        uploader.canvas.style.marginLeft = "-" + String(uploader.offset_x) + "px"
      }
    };
  });

  // release image on mouseup -- at any position
  document.addEventListener( "mouseup" , function(event) {
    document.onmousemove = null;
  });

  // create hidden input fields for uploading resized picture
  uploader.resized_file_input = {};
  uploader.resized_file_input.container = document.createElement("div");
//  uploader.resized_file_input.container.style.visibility = 'hidden'
  uploader.file_input.insertAdjacentElement('beforebegin', uploader.resized_file_input.container);
  if( uploader.hasOwnProperty('resized_file_input_id') ) {
    resized_file_input_id = uploader.resized_file_input_id;
  } else {
    resized_file_input_id = uploader.file_input.id + 'Resized';
  }
  if( uploader.hasOwnProperty('resized_file_input_name') ) {
    hidden_file_input_name = uploader.resized_file_input_name;
  } else {
    l = uploader.file_input.name.length;
    if( ']' == uploader.file_input.name[l-1] )
      resized_file_input_name = uploader.file_input.name.substring(0, l-1) + '_resized]';
    else
      resized_file_input_name = uploader.file_input.name + '_resized';
  }
  uploader.resized_file_input.data = document.createElement("input");
  uploader.resized_file_input.data.type = 'hidden';
  uploader.resized_file_input.data.name = resized_file_input_name + '[data]';
  uploader.resized_file_input.data.id   = resized_file_input_id + 'Data';
  uploader.resized_file_input.name = document.createElement("input");
  uploader.resized_file_input.name.type = 'hidden';
  uploader.resized_file_input.name.name = resized_file_input_name + '[name]';
  uploader.resized_file_input.name.id   = resized_file_input_id + 'Name';
  uploader.resized_file_input.size = document.createElement("input");
  uploader.resized_file_input.size.type = 'hidden';
  uploader.resized_file_input.size.name = resized_file_input_name + '[size]';
  uploader.resized_file_input.size.id   = resized_file_input_id + 'Size';
  uploader.resized_file_input.type = document.createElement("input");
  uploader.resized_file_input.type.type = 'hidden';
  uploader.resized_file_input.type.name = resized_file_input_name + '[type]';
  uploader.resized_file_input.type.id   = resized_file_input_id + 'Type';
}

