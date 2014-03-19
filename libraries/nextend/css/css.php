<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendCss {
    
    var $_css;
    
    var $_cssFiles;
    
    var $_cacheenabled;
    
    var $_cache;
    
    var $_lesscache;
    
    var $_echo;
    
    function NextendCss() {

        $this->_css = '';
        $this->_cssFiles = array();
        $this->_cacheenabled = 1;
        $this->_lesscache = false;
        $this->_echo = false;
        if($this->_cacheenabled){
            nextendimport('nextend.cache.css');
            $this->_cache = new NextendCacheCss();
        }
    }
    
    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            if (nextendIsJoomla()) {
                nextendimport('nextend.css.joomla');
                $instance = new NextendCssJoomla();
            } elseif (nextendIsWordPress()) {
                nextendimport('nextend.css.wordpress');
                $instance = new NextendCssWordPress();
            } elseif (nextendIsMagento()) {
                nextendimport('nextend.css.magento');
                $instance = new NextendCssMagento();
            }
        }
        return $instance;
    }
    
    function enableLess(){
        nextendimport('nextend.cache.less');
        $this->_lesscache = new NextendCacheLess();
    }
    
    function addLessImportDir($dir){
        $this->_lesscache->_less->addImportDir($dir);
    }
    
    function addCss($css) {

        $this->_css.= $css . PHP_EOL;
    }
    
    function addCssFile($file) {
        if(is_string($file)){
            $this->_cssFiles[$file] = $file;
        }else if(is_array($file)){
            $this->_cssFiles[$file[0]] = $file;
        }
    }
    
    function addCssLibraryFile($file) {

        $this->addCssFile(NEXTENDLIBRARYASSETS . 'css' . DIRECTORY_SEPARATOR . $file);
    }
    
    function generateCSS() {
        if(class_exists('NextendFontsGoogle')){
            $fonts = NextendFontsGoogle::getInstance();
            $fonts->generateFonts();
        }
        if (count($this->_cssFiles)) {
            foreach($this->_cssFiles AS $file) {
                if(is_array($file)){ // LESS
                    $this->_lesscache->addContext($file[1], $file[2]);
                }else if(substr($file, 0, 4) == 'http'){
                    $this->serveCSSFile($file);
                }else{
                    if($this->_cacheenabled){
                        $this->_cache->addFile($file);
                    }else{
                        $url = NextendFilesystem::pathToAbsoluteURL($file);
                        $this->serveCSSFile($url);
                    }
                }
            }
        }
        if($this->_cacheenabled){
            if($this->_lesscache){
                $this->_cache->addFile(NextendFilesystem::absoluteURLToPath($this->_lesscache->getCache()));
            }
            $this->serveCSSFile($this->_cache->getCache());
        }else{
            if($this->_lesscache){
                $this->serveCSSFile($this->_lesscache->getCache());
            }
        }
        $this->serveCSS();
    }

    /*
    * Abstract, must redeclare
    * This one only for testing purpose!
    */
    
    function serveCSS($clear = true) {
        if($this->_css != ''){
            echo "<style type='text/css'>";
            echo $this->_css;
            echo "</style>";
            if ($clear) $this->_css = '';
        }
    }

    /*
    * Abstract, must redeclare
    * This one only for testing purpose!
    */
    
    function serveCSSFile($url) {

        echo '<link rel="stylesheet" href="' . $url . '" type="text/css" />';
    }
    
    function generateAjaxCSS($loadedCSS) {
        $css = '';
        if(count($this->_cssFiles)){
            foreach($this->_cssFiles AS $file){
                if (!in_array($file, $loadedCSS)) {
                    $css.= NextendFilesystem::readFile($file);
                }
            }
        }
        $css.= $this->_css;
        return $css;
    }
    
    function generateArrayCSS(){
        $css = array();
        $css = array_merge($css, $this->_cssFiles);
        return $css;
    }
}
