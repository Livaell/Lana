<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendCache{
    
    var $_path;
    
    var $_subfolder = '';
    
    var $_url;
    
    var $_filetype;
    
    var $_files;
    
    var $_cacheTime;
    
    var $_prename;
    
    var $_gzip;
    
    function NextendCache(){
        $this->_gzip = 0;
        $this->_path = NextendFilesystem::getCachePath().$this->_subfolder;
        $this->createCacheFolder($this->_path);
        $this->_url = NextendFilesystem::pathToAbsoluteURL($this->_path);
        $this->_files = array();
        $this->_cacheTime = getNextend('cachetime', 900);
        $this->_prename = 'n';
    }
    
    function createCacheFolder($path){
        if(NextendFilesystem::existsFolder($path)) return;
        if(NextendFilesystem::createFolder($path)) return;
        echo NextendText::sprintf('Couldn\'t create the required cache dir: %s. Please make sure that the folder writeable by PHP!', $path);
        exit;
    }
    
    function deleteCacheFolder(){
        if($this->_path == '') return;
        if(NextendFilesystem::existsFolder($this->_path)){
            NextendFilesystem::deleteFolder($this->_path);
        }
    }
    
    function addFile($file){
        $this->_files[] = $file;
    }
    
    function getCache(){
        $time = time();
        $currentcachetime = $time-$time%$this->_cacheTime;
        $folder = $this->_path.$this->_prename.$currentcachetime.DIRECTORY_SEPARATOR;
        $this->createCacheSubFolder($folder, $currentcachetime);
        $hash = $this->createHash();
        $cachefile = $folder.$hash.'.'.$this->_filetype;
        $cachefilegzip = $folder.$hash.'.php';
        if(!NextendFilesystem::existsFile($cachefile)){
            $cached = "/* ".date('l jS \of F Y h:i:s A')."*/\n\n";
            for($i = 0; $i < count($this->_files); $i++){
                $cached.= $this->parseFile(NextendFilesystem::readFile($this->_files[$i]),$this->_files[$i], $i);
            }
            NextendFilesystem::createFile($cachefile, $this->parseCached($cached));
            if($this->_gzip){
                $php = '<?php '
                        . $this->getgzipHeader($currentcachetime)
                        . 'if (extension_loaded("zlib") && (ini_get("output_handler") != "ob_gzhandler")) {'
                        . 'ini_set("zlib.output_compression", 1);'
                        . '}'
                        . 'readfile("'.$cachefile.'");';
                NextendFilesystem::createFile($cachefilegzip, $php);
            }
        }
        if($this->_gzip){
            return NextendFilesystem::pathToAbsoluteURL($cachefilegzip);
        }
        return NextendFilesystem::pathToAbsoluteURL($cachefile);
    }
    
    function parseFile($content, $path, $i){
        return $content;
    }
    
    function parseCached($cached){
        return $cached;
    }
    
    function createCacheSubFolder($path, $currentcachetime){
        if(NextendFilesystem::existsFolder($path)) return;
        $previouscachetime = $currentcachetime-$this->_cacheTime;
        $remove = NextendFilesystem::folders($this->_path);
        if($remove !== false){
            for($i = 0; $i < count($remove) && $remove[$i] != $this->_prename.$previouscachetime; $i++){
                NextendFilesystem::deleteFolder($this->_path.$remove[$i]);
            }
        }
        if(NextendFilesystem::createFolder($path)) return;
        echo NextendText::sprintf('Couldn\'t create the required cache dir: %s Please make sure that the folder writeable by PHP!', $path);
        exit;
    }
    
    function createHash(){
        $hash = '';
        for($i = 0; $i < count($this->_files); $i++){
            $hash.=$this->_files[$i].filemtime($this->_files[$i]);
        }
        return md5($this->parseHash($hash));
    }
    
    function parseHash($hash){
        return $hash;
    }
    
    function getgzipHeader($currentcachetime){
        return '$time = '.$currentcachetime.';'
        . 'if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"]) && strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]) >= $time) {'
                . 'header("HTTP/1.1 304 Not Modified", true);'
                . 'header("Last-Modified: " . date("D M j G:i:s T Y", $time), true);'
                . $this->getContentHeader()
                . 'die();'
        . '}else{'
                . 'header("Last-Modified: " . date("D M j G:i:s T Y", $time), true);'
                . 'header("Cache-Control: must-revalidate", true);'
                . $this->getContentHeader()
        . '}';
    }
    
    function getContentHeader(){
        return '';
    }
}