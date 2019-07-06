<?php

/**
 *  NewsmediaUploader - ImageLib
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage NewsMediaUploadeer
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */

/**
 * Class ImageLib
 */
class ImageLib {

    private $engine_info;

    public function __construct() {
        $this->set_engine_info();
    }

    private function set_engine_info() {
        if (( extension_loaded('gd')) && function_exists('gd_info')) {
            $info = gd_info();
            preg_match('/\d/', $info['GD Version'], $version);
            ($version[0] >= 2) ? $truecolor = 1 : $truecolor = 0;
            $this->engine_info['SUPPORT'] = 1;
            $this->engine_info['TYPE'] = 'GD';
            $this->engine_info['VERSION'] = $version;
            $this->engine_info['TRUCOLOR'] = $truecolor;
        } else {
            $this->engine_info['SUPPORT'] = 0;
            return false;
        }
    }

    function jpeg_support() {
        return $this->engine_info['JPEG Support'];
    }

    function gif_support() {
        return $this->engine_info['GIF Create Support'];
    }

    function png_support() {
        return $this->engine_info['PNG Support'];
    }

    // Git @ pedroppinheiro
    function resize($filepath, $thumbpath, $thumbnail_width, $thumbnail_height, $background = false) {
        list($original_width, $original_height, $original_type) = getimagesize($filepath);
        if ($original_width > $original_height) {
            $new_width = $thumbnail_width;
            $new_height = intval($original_height * $new_width / $original_width);
        } else {
            $new_height = $thumbnail_height;
            $new_width = intval($original_width * $new_height / $original_height);
        }
        $dest_x = intval(($thumbnail_width - $new_width) / 2);
        $dest_y = intval(($thumbnail_height - $new_height) / 2);

        if ($original_type === 1) {
            $imgt = 'ImageGIF';
            $imgcreatefrom = 'ImageCreateFromGIF';
        } else if ($original_type === 2) {
            $imgt = 'ImageJPEG';
            $imgcreatefrom = 'ImageCreateFromJPEG';
        } else if ($original_type === 3) {
            $imgt = 'ImagePNG';
            $imgcreatefrom = 'ImageCreateFromPNG';
        } else {
            return false;
        }

        $old_image = $imgcreatefrom($filepath);
        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height); // creates new image, but with a black background
        // figuring out the color for the background
        if (is_array($background) && count($background) === 3) {
            list($red, $green, $blue) = $background;
            $color = imagecolorallocate($new_image, $red, $green, $blue);
            imagefill($new_image, 0, 0, $color);
            // apply transparent background only if is a png image
        } else if ($background === 'transparent' && $original_type === 3) {
            imagesavealpha($new_image, TRUE);
            $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
            imagefill($new_image, 0, 0, $color);
        }

        imagecopyresampled($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($new_image, $thumbpath);
        return file_exists($thumbpath);
    }

    function do_thumb($filepath, $thumbpath, $thumbnail_width) {
        list($original_width, $original_height, $original_type) = getimagesize($filepath);

        if ($original_width > $thumbnail_width) {
            $ratio = $original_width / $thumbnail_width;
            $new_width = $thumbnail_width;
            $new_height = $original_height / $ratio;
        } else {
            $new_width = $original_width;
            $new_height = $original_height;
        }

        if ($original_type === 1) {
            $imgt = 'ImageGIF';
            $imgcreatefrom = 'ImageCreateFromGIF';
        } else if ($original_type === 2) {
            $imgt = 'ImageJPEG';
            $imgcreatefrom = 'ImageCreateFromJPEG';
        } else if ($original_type === 3) {
            $imgt = 'ImagePNG';
            $imgcreatefrom = 'ImageCreateFromPNG';
        } else {
            return false;
        }

        $old_image = $imgcreatefrom($filepath);
        $new_image = imagecreatetruecolor($new_width, $new_height); // creates new image, but with a black background

        imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($new_image, $thumbpath);
        return file_exists($thumbpath);
    }

}
