<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
global $nextend;

$nextend = array(
    'cachetime' => 900,
    'cachepath' => null,
    'gzip' => 0
);

function getNextend($prop, $default = ''){
    global $nextend;
    if(isset($nextend[$prop]) && $nextend[$prop] !== null) return $nextend[$prop];
    return $default;
}

function setNextend($prop, $value){
    global $nextend;
    $nextend[$prop] = $value;
}