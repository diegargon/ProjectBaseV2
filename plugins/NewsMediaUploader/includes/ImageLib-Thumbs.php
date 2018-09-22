<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

class ImageLib_Thumbs_Thread extends Thread {

    private $filepath;
    private $thumbpath;
    private $thumbnail_width;

    public function __construct($filepath, $thumbpath, $thumbnail_width) {
        $this->filepath = $filepath;
        $this->thumbpath = $thumbpath;
        $this->thumbnail_width = $thumbnail_width;
    }

    public function run() {
        //SLEEP WHILE server load.
        list($original_width, $original_height, $original_type) = getimagesize($this->filepath);

        if ($original_width > $this->thumbnail_width) {
            $ratio = $original_width / $this->thumbnail_width;
            $new_width = $this->thumbnail_width;
            $new_height = $original_height / $ratio;
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

        $old_image = $imgcreatefrom($this->filepath);
        $new_image = imagecreatetruecolor($new_width, $new_height);

        imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($new_image, $this->thumbpath);
        return file_exists($this->thumbpath);
    }

}
