<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.accordionmenu.joomla.treebase');

if (class_exists('VmConfig') || file_exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php')) {
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'virtuemart2.php';
}else if(file_exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'compat.joomla1.5.php')){
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'virtuemart1.php';
}
