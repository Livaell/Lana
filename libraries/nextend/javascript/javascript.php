<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendJavascript {
    
    var $_js;
    
    var $_inlinejs;
    
    var $_jsFiles;
    
    var $_loadedLibraries;
    
    var $_cacheenabled;
    
    var $_cache;
    
    var $_echo;
    
    function NextendJavascript() {
        $this->_js = '';
        $this->_inlinejs = '';
        $this->_jsFiles = array();
        $this->_loadedLibraries = array();
        $this->_cacheenabled = true;
        $this->_echo = false;
        if($this->_cacheenabled){
            nextendimport('nextend.cache.javascript');
            $this->_cache = new NextendCacheJavascript();
        }
    }
    
    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            if(nextendIsJoomla()){
                nextendimport('nextend.javascript.joomla');
                $instance = new NextendJavascriptJoomla();
            }elseif(nextendIsWordPress()){
                nextendimport('nextend.javascript.wordpress');
                $instance = new NextendJavascriptWordPress();
            }elseif(nextendIsMagento()){
                nextendimport('nextend.javascript.magento');
                $instance = new NextendJavascriptMagento();
            }
        }

        return $instance;
    }
    
    /*
     * Simple JS, allowed to build into a javascript file
     */
    function addJs($js){
        $this->_js.= $js.PHP_EOL;
    }
    
    
    /*
     * Simple JS, not allowed to build into a javascript file. Inline!
     */
    function addInlineJs($js){
        $this->_inlinejs.= $js.PHP_EOL;
    }
    
    /*
     * Include javascript file
     */
    function addJsFile($file){
        $this->_jsFiles[$file] = $file;
    }
    
    /*
     * Add Js file from assets folder
     */
    function addJsAssetsFile($file){
        $this->addJsFile(NEXTENDLIBRARYASSETS.'js'.DIRECTORY_SEPARATOR.$file);
    }
    
    function loadLibrary($library){
        if(!isset($this->_loadedLibraries[$library])){
            nextendimport('nextend.javascript.'.$library.'.'.$library);
            $class = 'NextendJavascript'.$library;
            $class = new $class();
            $this->_loadedLibraries[$library] = $class->getInstance();
        }
    }
    
    /*
     * Include simple JS from external library
     */
    function addLibraryJs($library, $js, $first = false){
        $this->_loadedLibraries[$library]->addJs($js, $first);
    }
    
    /*
     * Include simple JS file to external library
     */
    function addLibraryJsFile($library, $file){
        $this->_loadedLibraries[$library]->addJsFile($file);
    }
    
    /*
     * Add file from the external library
     */
    function addLibraryJsLibraryFile($library, $file){
        $this->_loadedLibraries[$library]->addJsLibraryFile($file);
    }
    
    /*
     * Add file to the library from assets folder
     */
    function addLibraryJsAssetsFile($library, $file){
        $this->_loadedLibraries[$library]->addJsAssetsFile($file);
    }
    
    function generateLibraryJs(){
        foreach($this->_loadedLibraries AS $library){
            $library->generateJs();
        }
    }
    
    function generateJs(){
        $this->generateLibraryJs();
        if(count($this->_jsFiles)){
            foreach($this->_jsFiles AS $file){
                //$url = NextendFilesystem::pathToAbsoluteURL($file);
                //$this->serveCSSFile($url);
            }
        }
        $this->serveJs();
        $this->serveInlineJs();
    }
    
    /*
     * Abstract, must redeclare
     * This one only for testing purpose!
     */
    function serveJs($clear = true){
        if($this->_js == '') return;
        echo "<script type='text/javascript'>";
        echo $this->_js;
        echo "</script>";
        if($clear) $this->_js = '';
    }
    
    /*
     * Abstract, must redeclare
     * This one only for testing purpose!
     */
    function serveInlineJs($clear = true){
        if($this->_inlinejs == '') return;
        echo "<script type='text/javascript'>";
        echo $this->_inlinejs;
        echo "</script>";
        if($clear) $this->_inlinejs = '';
    }
    
    /*
     * Abstract, must redeclare
     * This one only for testing purpose!
     */
    function serveJsFile($url){
        echo '<script type="text/javascript" src="'.$url.'"></script>';   
    }
    
    function generateAjaxJs($loadedJSS){
        $js = '';
         $this->generateLibraryJs();
         if(count($this->_jsFiles)){
            foreach($this->_jsFiles AS $file){
                if(!in_array($file, $loadedJSS)){
                    $js.=file_get_contents($file);
                }
            }
        }
        $js.=$this->_js;
        $js.=$this->_inlinejs;
        return $js;
    }
    
    function generateArrayJs(){
        $jss = array();
        $jss = array_merge($jss, $this->_jsFiles);
        foreach($this->_loadedLibraries AS $library){
            $jss = array_merge($jss, $library->generateJsList());
        }
        
        return $jss;
    }
}
