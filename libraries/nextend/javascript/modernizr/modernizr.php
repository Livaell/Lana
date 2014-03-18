<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendJavascriptModernizr {
    
    var $_jsFiles;
    
    function NextendJavascriptModernizr() {

        $this->_jsFiles = array();
    }
    
    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            $instance = new NextendJavascriptModernizr();
            $instance->addJsLibraryFile('modernizr.js');
        }
        return $instance;
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
    * jQuery folder
    */
    
    function addJsLibraryFile($file) {

        $file = NextendFilesystem::getBasePath().NextendFilesystem::getLibraryPath() . 'javascript/modernizr/' . $file;
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
    }
    
    function generateJsList(){
        if (count($this->_jsFiles)) {
            return $this->_jsFiles;
        }
    }
}
