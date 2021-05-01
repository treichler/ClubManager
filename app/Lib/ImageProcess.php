<?php
// app/Lib/ImageProcess.php

class ImageProcess {

  public function getOriginalImage() {
    return $this->original_image;
  }


  public function getProcessedImage() {
    return $this->processed_image;
  }


  public function saveProcessedImage($file, $mime = null) {
    if (!$mime)
      $mime = $this->original_image_info['mime'];

    switch ($mime) {
/* XXX is deprecated because of LZW-compression
      case 'image/gif':
        imageGIF($this->processed_image, $file);
        return true;
*/
      case 'image/jpeg':
        imageJPEG($this->processed_image, $file, 95);
        return true;
      case 'image/png':
        imagePNG($this->processed_image, $file);
        return true;
      case 'image/wbmp':
        imageWBMP($this->processed_image, $file);
        return true;
      default:
        return false;
        break;
    }
  }


  public function landscape($dst_w = 900, $dst_h = 300) {
    $width = imagesx($this->original_image);
    $height = imagesy($this->original_image);

    if (($dst_w / $dst_h) > ($width / $height)) {
      // cut top and bottom
      $src_w = $width;
      $src_h = round($width * $dst_h / $dst_w);
      $src_x = 0;
      $src_y = round(($height - $src_h) / 2);
    } else {
      // cut left and right
      $src_w = round($height * $dst_w / $dst_h);
      $src_h = $height;
      $src_x = round(($width - $src_w) / 2);
      $src_y = 0;
    }

    if($this->processed_image);
      unset($this->processed_image);
    $this->processed_image = imageCreateTrueColor($dst_w, $dst_h);

    imageCopyResampled($this->processed_image, $this->original_image, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    return true;
  }


  public function portrait($dst_w = 120, $dst_h = 160) {
    return $this->landscape($dst_w, $dst_h);
  }


  public function thumbnail($t_wd = 133, $t_ht = 100) {
    $cut_margin = 0.25; // must be less than 0.5 !!!

    $o_wd = imagesx($this->original_image);
    $o_ht = imagesy($this->original_image);

    if ($o_wd/$o_ht > $t_wd/$t_ht) {
      $s_wd = round((1 - (2 * $cut_margin)) * $o_wd);
      if ($s_wd/$o_ht <= $t_wd/$t_ht) {
        $s_ht = round($t_ht/$t_wd * $s_wd);
      } else {
        $s_ht = $o_ht;
        $s_wd = round($t_wd/$t_ht * $s_ht);
      }
    } else {
      $s_ht = round((1 - (2 * $cut_margin)) * $o_ht);
      if ($s_ht/$o_wd <= $t_ht/$t_wd) {
        $s_wd = round($t_wd/$t_ht * $s_ht);
      } else {
        $s_wd = $o_wd;
        $s_ht = round($t_ht/$t_wd * $s_wd);
      }
    }

    $s_x = round(($o_wd - $s_wd) / 2);
    $s_y = round(($o_ht - $s_ht) / 2);

    // thumbnail width = target * original width / original height
//    $t_wd = round($o_wd * $t_ht / $o_ht) ; 

    if($this->processed_image);
      unset($this->processed_image);
    $this->processed_image = imageCreateTrueColor($t_wd, $t_ht);

//( resource dst_im, resource src_im, int dstX, int dstY, int srcX, int srcY, int dstW, int dstH, int srcW, int srcH)

    imageCopyResampled($this->processed_image, $this->original_image, 0, 0, $s_x, $s_y, $t_wd, $t_ht, $s_wd, $s_ht);
//    imageCopyResampled($this->processed_image, $this->original_image, 0, 0, 0, 0, $t_wd, $t_ht, $s_wd, $s_ht);

//    imageJPEG($t_im,$t_file);

//    imageDestroy($o_im);
//    imageDestroy($t_im);
    return true;
  }


  public function watermark($file = null) {

    // load watermark
    if (! $file)
      $file = 'img/watermark.png';
//    $watermark = imagecreatefrompng($file);
    $watermark = $this->load($file);

    // set margin, height and width of watermark
    $marge_right = 10;
    $marge_bottom = 10;
    $sx = imagesx($watermark);
    $sy = imagesy($watermark);

    $width = imagesx($this->original_image);
    $height = imagesy($this->original_image);

    if($this->processed_image);
      unset($this->processed_image);
    $this->processed_image = imageCreateTrueColor($width, $height);
    imagecopy($this->processed_image, $this->original_image, 0, 0, 0, 0, $width, $height);

//( resource dst_im, resource src_im, int dst_x, int dst_y, int src_x, int src_y, int src_w, int src_h)

    // merge image and watermark
    imagecopy($this->processed_image, $watermark, imagesx($this->processed_image) - $sx - $marge_right,
              imagesy($this->processed_image) - $sy - $marge_bottom, 0, 0, $sx, $sy);

    imagedestroy($watermark);
//    return $this->processed_image;
    return true;
  }


  private function load($file) {
    switch (getImageSize($file)['mime']) {
      case 'image/gif':
        if (imagetypes() & IMG_GIF)  { // not the same as IMAGETYPE
          $im = imageCreateFromGIF($file) ;
        } else {
          return false;
        }
        break;
      case 'image/jpeg':
        if (imagetypes() & IMG_JPG)  {
          $im = imageCreateFromJPEG($file) ;
        } else {
          return false;
        }
        break;
      case 'image/png':
        if (imagetypes() & IMG_PNG)  {
          $im = imageCreateFromPNG($file) ;
        } else {
          return false;
        }
        break;
      case 'image/wbmp':
        if (imagetypes() & IMG_WBMP)  {
          $im = imageCreateFromWBMP($file) ;
        } else {
          return false;
        }
        break;
      default:
        return false;
        break;
    }
    return $im;
  }


  function __construct($file) {
//    $this->original_image = imagecreatefromjpeg($file);
    $im = $this->load($file);
    if ($im) {
      $this->original_image = $im;
      $this->original_image_info = getImageSize($file) ; // see EXIF for faster way
      return true;
    }
    return false;
  }


  function __destruct() {
    unset($this->original_image);
    unset($this->original_image_info);
    unset($this->processed_image);
  }


  private $original_image = null;
  private $original_image_info = null;
  private $processed_image = null;
}

