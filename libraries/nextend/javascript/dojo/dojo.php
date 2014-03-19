<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendJavascriptDojo {
    
    var $_js;
    
    var $_jsFiles;
    
    function NextendJavascriptDojo() {
        $this->_js = '';

        $this->_jsFiles = array();
    }
    
    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            $instance = new NextendJavascriptDojo();
            $instance->addJsLibraryFile('dojo/dojo.js');
        }
        return $instance;
    }
    
    /*
     * Inline script
     */
    function addJs($js, $first = false){
        if($first){
            $this->_js= $js.PHP_EOL.$this->_js;
        }else{
            $this->_js.= $js.PHP_EOL;
        }
    }
    
    /*
     * Relative path to root
     */
    function addJsFile($file) {

        if (!in_array($file, $this->_jsFiles)) {
            $this->_jsFiles[] = $file;
        }
    }

    /*
    * Dojo folder
    */
    
    function addJsLibraryFile($file) {

        $file = NextendFilesystem::getBasePath().NextendFilesystem::getLibraryPath() . 'javascript/dojo/1.6.1/' . $file;
        $this->addJsFile($file);
    }

    /*
    * Assets folder
    */
    
    function addJsAssetsFile($file) {

        $this->addJsFile(NEXTENDLIBRARYASSETS . 'js' . DIRECTORY_SEPARATOR . $file);
    }
    
    function generateJs() {
        $js = NextendJavascript::getInstance();
        if (count($this->_jsFiles)) {
            foreach($this->_jsFiles AS $file) {
                $js->addJsFile($file);
            }
        }
        $this->serveJs();
    }
    
    function generateJsList(){
        if (count($this->_jsFiles)) {
            return $this->_jsFiles;
        }
    }
    
    function serveJs($clear = true){
        $js = NextendJavascript::getInstance();
        $inline = '(function(dojo){ ';
        $inline.= 'dojo.addOnLoad(function(){';
        $inline.= $this->_js;
        $inline.= '});';
        $inline.= ' })(ndojo);';
        $js->addJs($inline);
    }
}
