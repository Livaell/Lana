<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
require_once dirname(__FILE__) . '/helper.php';

// retrieve menu items
$thirdparty = $params->get('thirdparty', 'none');
switch ($thirdparty) :
	default:
	case 'none':
		// Include the syndicate functions only once
		// require_once dirname(__FILE__).'/helper.php';
		$items = modMaximenuckHelper::getItems($params);
		break;
	case 'virtuemart':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_virtuemart.php')) {
			require_once dirname(__FILE__) . '/helper_virtuemart.php';
			$items = modMaximenuckvirtuemartHelper::getItems($params);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_virtuemart.php not found ! Please download the patch for Maximenu - Virtuemart on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
	case 'hikashop':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_hikashop.php')) {
			require_once dirname(__FILE__) . '/helper_hikashop.php';
			$items = modMaximenuckhikashopHelper::getItems($params);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_hikashop.php not found ! Please download the patch for Maximenu - Hikashop on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
	case 'articles':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_articles.php')) {
			require_once dirname(__FILE__) . '/helper_articles.php';
			$items = modMaximenuckhikashopHelper::getItems($params);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_articles.php not found ! Please download the patch for Maximenu - Joomla articles on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
	case 'k2':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_k2.php')) {
			require_once dirname(__FILE__) . '/helper_k2.php';
			$items = modMaximenuckk2Helper::getItems($params);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_k2.php not found ! Please download the patch for Maximenu - k2 on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
endswitch;

// if no item in the menu then exit
if (!count($items) OR !$items)
	return false;


$document = JFactory::getDocument();
$app = JFactory::getApplication();
$menu = $app->getMenu();
$active = $menu->getActive();
$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
$path = isset($active) ? $active->tree : array();
$class_sfx = htmlspecialchars($params->get('class_sfx'));
jimport('joomla.plugin.helper');

// get the language direction
$langdirection = $document->getDirection();

// page title management
if ($active) {
	$pagetitle = $document->getTitle();
	$title = $pagetitle;
	if (preg_match("/||/", $active->title)) {
		$title = explode("||", $active->title);
		$title = str_replace($active->title, $title[0], $pagetitle);
	}
	if (preg_match("/\[/", $active->title)) {
		if (!$title)
			$title = $active->title;
		$title = explode("[", $title);
		$title = str_replace($active->title, $title[0], $pagetitle);
	}
	$document->setTitle($title); // returns the page title without description
}


// retrieve parameters from the module
// params for the script
$fxduration = $params->get('fxduration', 500);
$fxtransition = $params->get('fxtransition', 'linear');
// $usemootools = $params->get('usemootools', '1');
$orientation = $params->get('orientation', 'horizontal');
$testoverflow = $params->get('testoverflow', '0');
$opentype = $params->get('opentype', 'open');
$fxdirection = $params->get('direction', 'normal');
$directionoffset1 = $params->get('directionoffset1', '30');
$directionoffset2 = $params->get('directionoffset2', '30');

$behavior = $params->get('behavior', 'moomenu');
$usecss = $params->get('usecss', '1');
$menuID = $params->get('menuid', 'maximenuck');
$usefancy = $params->get('usefancy', '1');
$fancyduree = $params->get('fancyduration', 500);
$fancytransition = $params->get('fancytransition', 'linear');
$fancyease = $params->get('fancyease', 'easeOut');
$theme = $params->get('theme', 'default');
$fxtype = $params->get('fxtype', 'open');
$dureein = $params->get('dureein', 0);
$dureeout = $params->get('dureeout', 500);
$showactivesubitems = $params->get('showactivesubitems', '0');
$menubgcolor = $params->get('menubgcolor', '') ? "background:" . $params->get('menubgcolor', '') : '';
$ismobile = '0';
$logoimage = $params->get('logoimage', '');
$logolink = $params->get('logolink', '');
$logoheight = $params->get('logoheight', '');
$logowidth = $params->get('logowidth', '');
$usejavascript = $params->get('usejavascript', '1');
$effecttype = ($params->get('layout', 'default') == '_:pushdown') ? 'pushdown' : 'dropdown';

if ($effecttype == 'pushdown' && $orientation == 'vertical') {
	echo '<p style="color:red;font-weight:bold;">MAXIMENU MESSAGE : You can not use the Pushdown layout for a Vertical menu</p>';
	return false;
}

// detection for mobiles
if (isset($_SERVER['HTTP_USER_AGENT']) && (strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPad') || strstr($_SERVER['HTTP_USER_AGENT'], 'iPod') || strstr($_SERVER['HTTP_USER_AGENT'], 'Android'))) {
	$behavior = 'click';
	$ismobile = '1';
}

// add external stylesheets
if ($orientation == 'vertical') {
	if ($langdirection == 'rtl' && JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/moo_maximenuvck_rtl.css')) {
		$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/moo_maximenuvck_rtl.css');
	} else {
		$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/moo_maximenuvck.css');
	}
	if ($usecss == 1) {
		if ($langdirection == 'rtl' && JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/maximenuvck_rtl.php')) {
			$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuvck_rtl.php?monid=' . $menuID);
		} else {
			$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuvck.php?monid=' . $menuID);
		}
	}
} else {
	if ($langdirection == 'rtl' && JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/moo_maximenuhck_rtl.css')) {
		$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/moo_maximenuhck_rtl.css');
	} else {
		$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/moo_maximenuhck.css');
	}
	if ($usecss == 1) {
		if ($langdirection == 'rtl' && JFile::exists(dirname(__FILE__) . '/themes/' . $theme . '/css/maximenuhck_rtl.php')) {
			$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuhck_rtl.php?monid=' . $menuID);
		} else {
			$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuhck.php?monid=' . $menuID);
		}
	}
}

if (JFile::exists('modules/mod_maximenuck/themes/' . $theme . '/css/ie7.css')) {
	echo '
		<!--[if lte IE 7]>
		<link href="' . JURI::base(true) . '/modules/mod_maximenuck/themes/' . $theme . '/css/ie7.css" rel="stylesheet" type="text/css" />
		<![endif]-->';
}

$menuposition = $params->get('menuposition', '0');
if ($menuposition) {
	$fixedcssposition = ($menuposition == 'bottomfixed') ? "bottom: 0 !important;" : "top: 0 !important;";
	$fixedcss = "div#" . $menuID . ".maximenufixed {
        position: fixed !important;
        left: 0 !important;
        " . $fixedcssposition . "
        right: 0 !important;
        z-index: 1000 !important;
    }";
	if ($menuposition == 'topfixed') {
		$fixedcss .= "div#" . $menuID . ".maximenufixed ul.maximenuck {
            top: 0 !important;
        }";
	} else if ($menuposition == 'bottomfixed') {
		$fxdirection = 'inverse';
	}
//$topfixedmenu = $params->get('topfixedmenu', '0');
	//if ($topfixedmenu)
	$document->addStyleDeclaration($fixedcss);
}

// get the css from the plugin params and inject them
if (JPluginHelper::isEnabled('system', 'maximenuckparams')) {
	modMaximenuckHelper::injectModuleCss($params, $menuID);
}

// add compatibility css for templates
$templatelayer = $params->get('templatelayer', 'beez3-position1');
if ($usecss == 1 AND $templatelayer != -1)
	$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/templatelayers/' . $templatelayer . '.css');

// add responsive css
if ($orientation == 'horizontal' && $params->get('useresponsive', '1') == '1')
	$document->addStyleSheet(JURI::base(true) . '/modules/mod_maximenuck/assets/maximenuresponsiveck.css');

JHTML::_("jquery.framework", true);
JHTML::_("jquery.ui");

if ($usejavascript && $params->get('layout', 'default') != '_:flatlist' && $params->get('layout', 'default') != '_:nativejoomla' && $params->get('layout', 'default') != '_:dropselect') {
	$document->addScript(JURI::base(true) . '/modules/mod_maximenuck/assets/maximenuck.js');

	if ($fxtransition != 'linear' || $fancytransition != 'linear')
		$document->addScript(JURI::base(true) . '/modules/mod_maximenuck/assets/jquery.easing.1.3.js');
	if ($opentype == 'scale' || $opentype == 'puff' || $opentype == 'drop')
		$document->addScript(JURI::base(true) . '/modules/mod_maximenuck/assets/jquery.ui.1.8.js');

	$load = ($params->get('load', 'domready') == 'load') ? "jQuery(window).load(function(){jQuery" : "jQuery(document).ready(function($){\$";
	$js = $load . "('#" . $menuID . "').DropdownMaxiMenu({"
			. "fxtransition : '" . $fxtransition . "',"
			. "dureeIn : " . $dureein . ","
			. "dureeOut : " . $dureeout . ","
			. "menuID : '" . $menuID . "',"
			. "testoverflow : '" . $testoverflow . "',"
			. "orientation : '" . $orientation . "',"
			. "behavior : '" . $behavior . "',"
			. "opentype : '" . $opentype . "',"
			. "fxdirection : '" . $fxdirection . "',"
			. "directionoffset1 : '" . $directionoffset1 . "',"
			. "directionoffset2 : '" . $directionoffset2 . "',"
			. "showactivesubitems : '" . $showactivesubitems . "',"
			. "ismobile : " . $ismobile . ","
			. "menuposition : '" . $menuposition . "',"
			. "effecttype : '" . $effecttype . "',"
			. "fxduration : " . $fxduration . "});"
			. "});";

	$document->addScriptDeclaration($js);

// add fancy effect
	if ($orientation == 'horizontal' && $usefancy == 1) {
		$document->addScript(JURI::base(true) . '/modules/mod_maximenuck/assets/fancymenuck.js');
		$js = "jQuery(document).ready(function($){
            $('#" . $menuID . "').FancyMaxiMenu({"
				. "fancyTransition : '" . $fancytransition . "',"
				. "fancyDuree : " . $fancyduree . "});"
				. "});";
		$document->addScriptDeclaration($js);
	}
}

require JModuleHelper::getLayoutPath('mod_maximenuck', $params->get('layout', 'default'));