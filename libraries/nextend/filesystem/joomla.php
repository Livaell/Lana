<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

class NextendFilesystem extends NextendFilesystemAbstract{
    
    function NextendFilesystem(){
        $this->_basepath = JPATH_SITE.DIRECTORY_SEPARATOR;
        $this->_cachepath = getNextend('cachepath', JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'nextend'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);
        $this->_librarypath = str_replace($this->_basepath, '', NEXTENDLIBRARY);
    }
    
    static function fileexists($file){
        return JFile::exists($file);
    }
    
    static function folders($path){
        return JFolder::folders($path);
    }
    
    static function createFolder($path){
        return JFolder::create($path);
    }
    
    static function deleteFolder($path){
        return JFolder::delete($path);
    }
    
    static function existsFolder($path){
        return JFolder::exists($path);
    }
    
    static function files($path){
        return JFolder::files($path);
    }
    
    static function existsFile($path){
        return JFile::exists($path);
    }
    
    static function createFile($path, $buffer){
        return JFile::write($path, $buffer);
    }
    
    static function readFile($path){
        return JFile::read($path);
    }
}