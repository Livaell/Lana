<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
jimport('nextend.library');
nextendimport('nextend.accordionmenu.joomla.menu');

$menu = new NextendMenuJoomla($module, $params, dirname(__FILE__));
$menu->render();
?>