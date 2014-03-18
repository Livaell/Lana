<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendFontsGoogle {
    
    var $_fonts;
    
    function NextendFontsGoogle() {
        $this->_fonts = '';
    }
    
    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            $instance = new NextendFontsGoogle();
        }

        return $instance;
    }
    
    function addFont($family, $style='400', $subset='latin'){
        if(!isset($this->_fonts[$family])){
            $this->_fonts[$family] = array($style, $subset);
        }
        $this->_fonts[$family][0].=','.$style;
        $this->_fonts[$family][1].=','.$subset;
    }
    
    function generateFonts(){
        nextendimport('nextend.css.css');
        $css = NextendCss::getInstance();
        $css->addCssFile($this->getFontUrl());
    }
    
    function getFontUrl(){
        $url = 'https://fonts.googleapis.com/css?family=';
        $subset = '';
        if(count($this->_fonts)){
            foreach($this->_fonts AS $family => $font){
                $style = explode(',',$font[0]);
                $style = array_filter(array_unique($style));
                $url.=$family.':'.implode(',', $style).'|';
                $subset.= $font[1].',';
            }
        }
        $url = substr($url, 0, -1);
        $subset = explode(',',$subset);
        $subset = array_filter(array_unique($subset));
        $url.='&amp;subset='.implode(',', $subset);
        return $url;
    }
}
