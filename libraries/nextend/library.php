<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
define('NEXTENDLIBRARY', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/*
* nextend.form
*/

function nextendimport($key) {

    $keys = explode('.', $key);
    array_shift($keys);
    require_once (NEXTENDLIBRARY . implode(DIRECTORY_SEPARATOR, $keys) . '.php');
}

function nextendIsJoomla(){
    return defined('_JEXEC');
}

function nextendIsWordPress(){
    return defined('ABSPATH');
}

function nextendIsMagento(){
    return class_exists('Mage');
}

nextendimport('nextend.configuration');

if(nextendIsJoomla()){
    nextendimport('nextend.joomla');
}else if(nextendIsWordPress()){
    nextendimport('nextend.wordpress');
}else if(nextendIsMagento()){
    nextendimport('nextend.magento');
}
if(!defined('NEXTENDLIBRARYASSETS')) define('NEXTENDLIBRARYASSETS', NEXTENDLIBRARY . 'assets' . DIRECTORY_SEPARATOR);
nextendimport('nextend.language.language');
nextendimport('nextend.uri.uri');
nextendimport('nextend.filesystem.filesystem');

?>