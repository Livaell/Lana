<?php
/**
* @package Joomla! 2.5
* @version 4.x
* @author 2008-2012 (c)  Denys Nosov (aka Dutch)
* @author web-site: www.joomla-ua.org
* @copyright This module is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License.
**/

defined( '_JEXEC' ) or die();

$version    = new JVersion;
$joomla     = $version->getShortVersion();

include('head.php'); 

if(substr($joomla, 0, 3) >= '3.0') {
    include('toggler30.php');
} else {
    include('toggler25.php');
}