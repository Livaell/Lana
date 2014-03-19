<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendText{
    static function _($text){
        return $text;
    }
    
    static function sprintf($text){
        $args = func_get_args();
        if (count($args) > 0){
            $args[0] = NextendText::_($args[0]);
            return call_user_func_array('printf', $args);
        }
        return $text;
    }
}