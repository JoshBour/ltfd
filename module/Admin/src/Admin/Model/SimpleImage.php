<?php
namespace Admin\Model;
/*
 * File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/
class SimpleImage{

	var $image;
	var $image_width;
	var $image_height;
	var $image_type;

	/**
	 * @desc
	 * Loads an image and saves the basic info (dimensions,type etc)
	 * 
	 * @param string $filename
	 * The full path to the image, path/to/image.jpg
	 */
	public function load($filename) {
		list($this->image_width,$this->image_height,$this->image_type) = getimagesize($filename);
		if( $this->image_type == IMAGETYPE_JPEG ) {

			$this->image = \imagecreatefromjpeg($filename);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {

			$this->image = \imagecreatefromgif($filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {

			$this->image = \imagecreatefrompng($filename);
		}
	}
	
	/**
	 * @desc
	 * Creates a thumbnail image of dimnesions $size x $size
	 * 
	 * @param int $size
	 * The desired size of the thumbnail.
	 * 
	 * @param string $name
	 * The name of the thumbnail.
	 * 
	 * @return
	 * A new image created by imagecreatetruecolor.
	 */
	public function createThumbnail($sizeX,$name,$sizeY = null){
		if ($this->image_width > $this->image_height) {
			$y = 0;
			$x = ($this->image_width - $this->image_height) / 2;
			$smallestSide = $this->image_height;
		} else {
			$x = 0;
			$y = ($this->image_height - $this->image_width) / 2;
			$smallestSide = $this->image_width;
		}
		if(empty($sizeY)) $sizeY = $sizeX;
		$thumb = imagecreatetruecolor($sizeX, $sizeY);
		imagecopyresampled($thumb, $this->image, 0, 0, $x, $y, $sizeX, $sizeY, $smallestSide, $smallestSide);	
		
		$this->save($name,$thumb);
	}
	
	/**
	 * @desc
	 * Saves the file to the desired directory as specified by $filename
	 * 
	 * @param string $filename
	 * The full path to the file save directory with the extension, e.g. path/to/image.jpg
	 * @param $image
	 * Should the default loaded image not wanted to be used, a new one can be set
	 * @param constant $image_type
	 * Accepts an image type and replaces the default one
	 * @param int $compression
	 * Compression size in case of jpg
	 * @param int $permissions
	 * The permissions to the file
	 */
	function save($filename, $image = "", $image_type = "", $compression=75, $permissions=null) {
		if(empty($image_type)){
			$image_type = $this->image_type;
		}
		if(empty($image)){
			$image = $this->image;
		}
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($image,$filename,$compression);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($image,$filename);
		} elseif( $image_type == IMAGETYPE_PNG ) {

			imagepng($image,$filename);
		}
		if( $permissions != null) {

			chmod($filename,$permissions);
		}
	}
	function output($image_type=IMAGETYPE_JPEG) {

		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} elseif( $image_type == IMAGETYPE_GIF ) {

			imagegif($this->image);
		} elseif( $image_type == IMAGETYPE_PNG ) {

			imagepng($this->image);
		}
		imagedestroy($this->image);
	}
	function getWidth() {

		return imagesx($this->image);
	}
	function getHeight() {

		return imagesy($this->image);
	}
	function resizeToHeight($height) {

		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}

	function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}

	function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}

	function resize($width,$height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
	}
}