<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.cache.cache');
nextendimport('nextend.image.color');

class NextendImage extends NextendCache {
    
    var $_folder;
    
    var $_im = null;
    
    function NextendImage() {

        $this->_subfolder = 'image' . DIRECTORY_SEPARATOR;
        parent::NextendCache();
        $this->_filetype = 'png';
        $time = time();
        $currentcachetime = $time - $time % $this->_cacheTime;
        $this->_folder = $this->_path . $this->_prename . $currentcachetime . DIRECTORY_SEPARATOR;
        $this->createCacheSubFolder($this->_folder, $currentcachetime);
    }
    
    function loadLess(&$less) {

        $this->_less = & $less->_less;
        $this->_less->registerFunction("transparentcolor", array(
            $this,
            "lessTransparentcolor"
        ));
        $this->_less->registerFunction("colorizeimage", array(
            $this,
            "lessColorizeimage"
        ));
    }
    
    function transparentcolor($color) {

        $cachefile = $this->_folder . 'transparentcolor' . $color . '.' . $this->_filetype;
        if (!NextendFilesystem::existsFile($cachefile)) {
            $rgba = NextendColor::hex2rgba($color);
            $this->createIm(1, 1);
            $fillcolor = imagecolorallocatealpha($this->_im, $rgba[0], $rgba[1], $rgba[2], 127 - $rgba[3]);
            imagefilledrectangle($this->_im, 0, 0, 1, 1, $fillcolor);
            $this->saveIm($cachefile);
        }
        return NextendFilesystem::pathToAbsoluteURL($cachefile);
    }
    
    function lessTransparentcolor($arg) {

        if (isset($arg[2]) && isset($arg[2][1]) && isset($arg[2][1][1])) {
            return $this->url($this->transparentcolor($arg[2][1][1]));
        }
        return '';
    }
    
    function colorizeImage($img, $targetColor, $baseColor) {
        $cachefile = $this->_folder . 'colorize' . md5($img) . $targetColor . $baseColor . '.' . $this->_filetype;
        if (!NextendFilesystem::existsFile($cachefile)) {
            $targetHexArr = NextendColor::hex82hex($targetColor);
            $targetColor = $targetHexArr[0];
            $alpha = hexdec($targetHexArr[1]);
            $c1 = NextendColor::hex2hsl($baseColor);
            $c2 = NextendColor::hex2hsl($targetColor);
            $im = imagecreatefrompng($img);
            $width = imagesx($im);
            $height = imagesy($im);
            $this->createIm($width, $height);
            
            $rgb = NextendColor::rgb2array($targetColor);
            
            for ($x = 0;$x < $width;$x++) {
                for ($y = 0;$y < $height;$y++) {
                    $rgba = ImageColorAt($im, $x, $y);
                    $rgb = array(
                        (($rgba >> 16) & 0xFF) ,
                        (($rgba >> 8) & 0xFF) ,
                        $rgba & 0xFF
                    );
                    $hsl = NextendColor::rgb2hsl($rgb);
                    $a[0] = $hsl[0] + ($c2[0] - $c1[0]);
                    $a[1] = $hsl[1] * ($c2[1] / $c1[1]);
                    if ($a[1] > 1) $a[1] = 1;
                    $a[2] = exp(log($hsl[2]) * log($c2[2]) / log($c1[2]));
                    if ($a[2] > 1) $a[2] = 1;
                    $rgb = NextendColor::hsl2rgb($a);
                    $A = 0xFF - (($rgba >> 24) * 2) & 0xFF;
                    $A = (int)($A * ($alpha / 0xFF));
                    if ($A > 0xFF) $A = 0xFF;
                    $A = (int)((0xFF - $A) / 2);
                    imagesetpixel($this->_im, $x, $y, imagecolorallocatealpha($this->_im, $rgb[0], $rgb[1], $rgb[2], $A));
                }
            }
            $this->saveIm($cachefile);
            imagedestroy($im);
        }
        return NextendFilesystem::pathToAbsoluteURL($cachefile);
    }
    
    function lessColorizeimage(&$arg) {

        if (isset($arg[2])) {
            $arg = & $arg[2];
            if (isset($arg[0])) {
                $colorize = $this->getFromEscapedArgs($arg[0]);
            }
            if (isset($arg[1])) {
                $image = $this->getFromEscapedArgs($arg[1]);
                if($image == '-1') return 'none';
            }
            if (isset($arg[2])) {
                $color = $this->getFromEscapedArgs($arg[2]);
            }
            $url = '';
            if ($colorize == 0) {
                $url = NextendFilesystem::pathToAbsoluteURL(NextendFilesystem::getBasePath() . $image);
            }else{
                if( realpath($image) !== false){
                    $image = realpath($image);
                }else{
                    $image = NextendFilesystem::getBasePath() . $image;
                }
                $url = $this->colorizeImage($image, $color, '548722');
            }
            return $this->url($url);
        }
    }
    
    function createIm($x, $y) {

        $this->_im = imagecreatetruecolor($x, $y);
        imagealphablending($this->_im, false);
        imagesavealpha($this->_im, true);
        $transparent = imagecolorallocatealpha($this->_im, 255, 255, 255, 127);
        imagefilledrectangle($this->_im, 0, 0, $x, $y, $transparent);
    }
    
    function saveIm($file) {

        ob_start();
        imagepng($this->_im);
        NextendFilesystem::createFile($file, ob_get_clean());
        imagedestroy($this->_im);
        $this->_im = null;
    }
    
    function url($url) {

        return 'url(' . $url . ')';
    }
    
    function getFromEscapedArgs(&$arg) {

        if (isset($arg) && isset($arg[2]) && isset($arg[2][1]) && isset($arg[2][1][2]) && isset($arg[2][1][2][0])) {
            return $arg[2][1][2][0];
        }
        return false;
    }
}
