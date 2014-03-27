<?php
/**
 * @version    $Id: themeslider.php 16726 2012-10-05 10:59:46Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'classes' . DS . 'jsn_is_factory.php';
class plgJSNImageshowThemeSlider extends JPlugin
{
	var $_showcaseThemeName = 'themeslider';
	var $_showcaseThemeType = 'jsnimageshow';
	var $_pathAssets 		= 'plugins/jsnimageshow/themeslider/assets/';
	var $_tableName			= 'theme_slider';

	public function onLoadJSNShowcaseTheme($name, $themeID = 0)
	{
		if ($name != $this->_showcaseThemeName)
		{
			return false;
		}

		JPlugin::loadLanguage('plg_' . $this->_showcaseThemeType . '_' . $this->_showcaseThemeName);

		ob_start();

		JHTML::stylesheet($this->_pathAssets . 'css/' . 'style.css');
		//	JHTML::script('jsn_is_slidertheme.js', $this->_pathAssets . 'js/');
		JHTML::script($this->_pathAssets . 'js/' . 'jsn_is_themeslider.js');
		include_once dirname(__FILE__) . DS . 'helper' . DS . 'helper.php';
		include_once dirname(__FILE__) . DS . 'views' . DS . 'default.php';

		return ob_get_clean();
	}

	public function onExtensionBeforeUninstall($eid)
	{
		$query 	= 'DROP TABLE IF EXISTS `#__imageshow_' . $this->_tableName . '`';
		$db 	= JFactory::getDbo();
		$db->setQuery($query);
		$db->query();
	}

	public function getLanguageJSNPlugin()
	{
		$language = array();
		$language['admin']['files'] = array('plg_' . $this->_showcaseThemeType . '_' . $this->_showcaseThemeName . '.ini');
		$language['admin']['path'] 	= array(dirname(__FILE__).DS.'languages');

		return $language;
	}

	public function onDisplayJSNShowcaseTheme($args)
	{
		if ($args->theme_name != $this->_showcaseThemeName)
		{
			return false;
		}

		JHTML::stylesheet($this->_pathAssets.'css/' . 'style.css');
		JPlugin::loadLanguage('plg_' . $this->_showcaseThemeType . '_' . $this->_showcaseThemeName);
		$basePath 		 = JPATH_PLUGINS.DS.$this->_showcaseThemeType.DS.$this->_showcaseThemeName;
		$objThemeDisplay = JSNISFactory::getObj('classes.jsn_is_sliderdisplay', null ,null, $basePath);
		$result			 = $objThemeDisplay->display($args);
		return $result;
	}

	public function listThemesliderTable()
	{
		return array('#__imageshow_theme_slider');
	}
}