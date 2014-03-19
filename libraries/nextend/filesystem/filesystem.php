<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendFilesystemAbstract {
    
    /* /home/path/www/path/ */
    var $_basepath;
    
    /* libraries/nextend/ */
    var $_librarypath;
    
    var $_cachepath;
    
    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            $instance = new NextendFilesystem();
        } // if

        return $instance;
    }
    
    static function toLinux($path) {
        return str_replace(DIRECTORY_SEPARATOR, '/', $path);
    }
    
    static function getBasePath() {
        $i = NextendFilesystem::getInstance();
        return $i->_basepath;
    }
    
    static function setBasePath($path) {
        $i = NextendFilesystem::getInstance();
        $i->_basepath = $path;
    }
    
    static function getLibraryPath() {
        $i = NextendFilesystem::getInstance();
        return $i->_librarypath;
    }
    
    static function setLibraryPath($path) {
        $i = NextendFilesystem::getInstance();
        $i->_librarypath = $path;
    }
    
    static function getCachePath() {
        $i = NextendFilesystem::getInstance();
        return $i->_cachepath;
    }
    
    static function setCachePath($path) {
        $i = NextendFilesystem::getInstance();
        $i->_cachepath = $path;
    }
    
    static function pathToAbsoluteURL($path) {
        return NextendUri::pathToUri($path);
    }
    
    static function pathToRelativePath($path) {
        $i = NextendFilesystem::getInstance();
        return str_replace($i->_basepath, '', str_replace('/', DIRECTORY_SEPARATOR, $path));
    }
    
    static function absoluteURLToPath($url) {
        $i = NextendFilesystem::getInstance();
        return str_replace(NextendUri::getBaseUri(), $i->_basepath, $url);
    }
    
    static function fileexists($file){
        return is_file($file);
    }
    
    static function folders($dir){
        if (!is_dir($dir)) return false;
        $folders = array();
        foreach (scandir($dir) as $file) { 
            if ($file == '.' || $file == '..') continue; 
            if(is_dir($dir . DIRECTORY_SEPARATOR . $file)) $folders[] = $file;
        } 
        return $folders;
    }
    
    static function createFolder($path){
        return mkdir($path, 0777, true);
    }
    
    static function deleteFolder($dir){
        if (!is_dir($dir) || is_link($dir)) return unlink($dir); 
        foreach (scandir($dir) as $file) { 
            if ($file == '.' || $file == '..') continue; 
            if (!self::deleteFolder($dir . DIRECTORY_SEPARATOR . $file)) { 
                chmod($dir . DIRECTORY_SEPARATOR . $file, 0777); 
                if (!self::deleteFolder($dir . DIRECTORY_SEPARATOR . $file)) return false; 
            }; 
        } 
        return rmdir($dir); 
    }
    
    static function existsFolder($path){
        return is_dir($path);
    }
    
    static function files($path){
        $files = array();
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    $files[] = $file;
                }
                closedir($dh);
            }
        }
        return $files;
    }
    
    static function existsFile($path){
        return file_exists($path);
    }
    
    static function createFile($path, $buffer){
        return file_put_contents($path, $buffer);
    }
    
    static function readFile($path){
        return file_get_contents($path);
    }
}

if (nextendIsJoomla()) {
    nextendimport('nextend.filesystem.joomla');
} elseif (nextendIsWordPress()) {
    nextendimport('nextend.filesystem.wordpress');
}elseif (nextendIsMagento()) {
    nextendimport('nextend.filesystem.magento');
}else{
    nextendimport('nextend.filesystem.default');
}
