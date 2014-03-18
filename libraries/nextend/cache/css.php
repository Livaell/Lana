<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.cache.cache');

class NextendCacheCss extends NextendCache{
    
    function NextendCacheCss(){
        $this->_subfolder = 'css'.DIRECTORY_SEPARATOR;
        parent::NextendCache();
        $this->_filetype = 'css';
        $this->_gzip = getNextend('gzip', 0);
    }
    
    function parseFile($content, $path, $i){
        return preg_replace('#url\([\'"]([^"\'\)]+)[\'"]\)#', 'url('.NextendFilesystem::pathToAbsoluteURL(dirname($path)).'/$1)', $content);
    }
    
    function getContentHeader(){
        return 'header("Content-type: text/css", true);';
    }
}