<?php
// No direct access to this file
defined('_JEXEC') or die;


//require_once JPATH_COMPONENT . '/helpers/route.php';



$controller = JControllerLegacy::getInstance('iNews');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
