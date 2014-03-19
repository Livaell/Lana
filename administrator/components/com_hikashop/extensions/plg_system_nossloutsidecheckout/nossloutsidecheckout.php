<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.3.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2014 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class plgSystemNossloutsidecheckout extends JPlugin
{
	function onAfterRoute(){
		$app = JFactory::getApplication();
		if ($app->isAdmin() || @$_REQUEST['tmpl']=='component') return true;
		if(@$_REQUEST['option']!='com_hikashop' || (@$_REQUEST['ctrl']!='checkout' && !(@$_REQUEST['ctrl']=='order' && @$_REQUEST['task']=='pay') )){
			if (!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')) return true;
			if (hikashop_isSSL()){
				$app->setUserState('com_hikashop.ssl_redirect',0);
				$app->redirect(str_replace('https://','http://',hikashop_currentURL()));
			}
		}
		return true;
	}
}
