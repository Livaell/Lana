<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.image.color');
nextendimport('nextend.parse.parse');

class NextendParseFont{
    
    var $_font;
    
    function NextendParseFont($font){
        $this->_font = json_decode($font, true);
    }
    
    function printTab($tab = ''){
        if($tab == '') $tab = $this->_font['firsttab'];
        $tab = &$this->_font[$tab];
        $style = '';
        foreach($tab AS $k => $v){
            $style.= $this->parse($k, $v);
        }
        return $style;
    }
    
    function mixinTab($target, $source = ''){
        if($source == '') $source = $this->_font['firsttab'];
        $this->_font[$target] = array_merge($this->_font[$source], $this->_font[$target]);
    }
    
    function parse($property, $value){
        $fn = 'parse'.$property;
        return $this->$fn($value);
    }
    
    function parseColor($v){
        $hex = NextendColor::hex82hex($v);
        $style= 'color: #'.$hex[0].';';
        if($hex[1] != 'ff'){
            $rgba = NextendColor::hex2rgba($v);
            $style.= 'color: RGBA('.$rgba[0].','.$rgba[1].','.$rgba[2].','.round($rgba[3]/127, 2).');';
        }
        return $style;
    }
    
    function parseSize($v){
        return  'font-size:'.NextendParse::parse($v,'').';';
    }
    
    function parseTshadow($v){
        $v = NextendParse::parse($v);
        $rgba = NextendColor::hex2rgba($v[3]);
        if($v[0] == 0 && $v[1] == 0 && $v[2] == 0) return 'text-shadow: none;';
        return 'text-shadow: '.$v[0].'px '.$v[1].'px '.$v[2].'px RGBA('.$rgba[0].','.$rgba[1].','.$rgba[2].','.round($rgba[3]/127, 2).');';
    }
    
    function parseAfont($v){
        return 'font-family: '.$this->loadFont($v).';';
    }
    
    function parseLineheight($v){
        if($v == '') return '';
        return 'line-height: '.$v.';';
    }
    
    function parseBold($v){
        if($v == '1') return 'font-weight: bold;';
        return 'font-weight: normal;';
    }
    
    function parseItalic($v){
        if($v == '1') return 'font-style: italic;';
        return 'font-style: normal;';
    }
    
    function parseUnderline($v){
        if($v == '1') return 'text-decoration: underline;';
        return 'text-decoration: none;';
    }
    
    function parsePaddingleft($v){
        return 'padding-left: '.$v.'px;';
    }
    
    function parseAlign($v){
        return 'text-align: '.$v.';';
    }
    
    function parsereset($v){
        return '';
    }
    
    function loadFont($families){
        preg_match_all("/google\(.*?family=(.*?)\);\)/", $families, $out,  PREG_SET_ORDER);
        foreach($out AS $f){
            nextendimport('nextend.fonts.google');
            $fonts = NextendFontsGoogle::getInstance();
            preg_match('/(.*?)(:(.*?)(&subset=(.*))?)?$/', $f[1], $g);
            $family = str_replace('+', ' ', $g[1]);
            $style = 400;
            if(isset($g[3])){
                $style = $g[3];
            }
            $subset = 'latin';
            if(isset($g[5])){
                $subset = $g[5];
            }
            $fonts->addFont($family, $style, $subset);
            
            $families = str_replace($f[0], "'".$family."'", $families);
        }
        return $families;
    }
}