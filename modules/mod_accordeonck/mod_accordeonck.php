<?php

/**
 * @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Accordeon CK
 * @license		GNU/GPL
 * Adapted from the original mod_menu on Joomla.site - Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__) . '/helper.php');

// retrieve menu items
$thirdparty = $params->get('thirdparty', 'none');
switch ($thirdparty) :
	default:
	case 'none':
		// Include the syndicate functions only once
		// require_once dirname(__FILE__).'/helper.php';
		$list = modAccordeonckHelper::getItems($params, $module);
		break;
	case 'virtuemart':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_virtuemart.php')) {
			require_once dirname(__FILE__) . '/helper_virtuemart.php';
			$list = modAccordeonckvirtuemartHelper::getItems($params, $params->get('vmcategoryroot', '0'), '1');
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_virtuemart.php not found ! Please download the patch for Accordeonmenu - Virtuemart on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
	case 'hikashop':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_hikashop.php')) {
			require_once dirname(__FILE__) . '/helper_hikashop.php';
			$list = modAccordeonckhikashopHelper::getItems($params, false);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_hikashop.php not found ! Please download the patch for Accordeonmenu - Hikashop on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
	case 'articles':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_articles.php')) {
			require_once dirname(__FILE__) . '/helper_articles.php';
			$list = modAccordeonckarticlesHelper::getItems($params);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_articles.php not found ! Please download the patch for Accordeonmenu - Articles on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
	case 'k2':
		// Include the syndicate functions only once
		if (JFile::exists(dirname(__FILE__) . '/helper_k2.php')) {
			require_once dirname(__FILE__) . '/helper_k2.php';
			$list = modAccordeonckk2Helper::getItems($params);
		} else {
			echo '<p style="color:red;font-weight:bold;">File helper_k2.php not found ! Please download the patch for Accordeonmenu - K2 on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';
			return false;
		}
		break;
endswitch;

// $list = ModaccordeonckHelper::getMenu($params);
if (!$list)
	return false;

// retrieve parameters from the module
$startlevel = $params->get('startLevel', '0');
$endlevel = $params->get('endLevel', '10');
$menuID = $params->get('tag_id', 'accordeonck' . $module->id);
$mooduration = $params->get('mooduration', 500);
$mootransition = $params->get('mootransition', 'linear');
$imageplus = $params->get('imageplus', 'modules/mod_accordeonck/assets/plus.png');
$imageminus = $params->get('imageminus', 'modules/mod_accordeonck/assets/minus.png');
$imageposition = $params->get('imageposition', 'right');
$eventtype = $params->get('eventtype', 'click');
$fadetransition = $params->get('fadetransition', 'false');
$theme = $params->get('theme', 'default');

// laod the css and js in the page	
$document = JFactory::getDocument();
//JHTML::_("behavior.framework", true);
JHTML::_("jquery.framework", true);
JHTML::_("jquery.ui");
//$document->addScript(JURI::base(true) . '/modules/mod_accordeonck/assets/jquery.ui.1.8.js');
$document = JFactory::getDocument();
$document->addScript(JURI::base(true) . '/modules/mod_accordeonck/assets/mod_accordeonck.js');

if ($params->get('usestyles') == 1) {
	$menucss = ModaccordeonckHelper::createCss($params, 'menu');
	$level1linkcss = ModaccordeonckHelper::createCss($params, 'level1link');
	$level2linkcss = ModaccordeonckHelper::createCss($params, 'level2link');
	$level3linkcss = ModaccordeonckHelper::createCss($params, 'level3link');

	$document->addStylesheet(JURI::base(true) . '/modules/mod_accordeonck/themes/' . $theme . '/mod_accordeonck_css.php?cssid=' . $menuID);
	if ($params->get('useplusminusimages', '1')) {
		$css = "#" . $menuID . " li a.toggler { outline: none;background: url(" . JURI::root() . $imageplus . ") " . $imageposition . " center no-repeat !important; }
	#" . $menuID . " li.open > a.toggler { background: url(" . JURI::root() . $imageminus . ") " . $imageposition . " center no-repeat !important; }";
	} else {
		$css = '';
	}
	$css .= "#" . $menuID . " li ul li ul li ul { border:none !important; padding-top:0px !important; padding-bottom:0px !important; }";
	$document->addStyleDeclaration($css);
	$document->addStyleDeclaration("#" . $menuID . " { " . implode('', $menucss) . " } ");
	// first level items
	$document->addStyleDeclaration("#" . $menuID . " li.level1 { " . $level1linkcss['padding'] . $level1linkcss['margin'] . $level1linkcss['background'] . $level1linkcss['gradient'] . $level1linkcss['borderradius'] . $level1linkcss['shadow'] . $level1linkcss['border'] . " } ");
	$document->addStyleDeclaration("#" . $menuID . " li.level1 > a { " . $level1linkcss['fontcolor'] . $level1linkcss['fontsize'] . " } ");
	$document->addStyleDeclaration("#" . $menuID . " li.level1 > a:hover { " . $level1linkcss['fontcolorhover'] . " } ");
	$document->addStyleDeclaration("#" . $menuID . " li.level1 > a span.accordeonckdesc { " . $level1linkcss['descfontcolor'] . $level1linkcss['descfontsize'] . " } ");
	// second level items
	$document->addStyleDeclaration("#" . $menuID . " li.level2 { " . $level2linkcss['padding'] . $level2linkcss['margin'] . $level2linkcss['background'] . $level2linkcss['gradient'] . $level2linkcss['borderradius'] . $level2linkcss['shadow'] . $level2linkcss['border'] . " } ");
	$document->addStyleDeclaration("#" . $menuID . " li.level2 > a { " . $level2linkcss['fontcolor'] . $level2linkcss['fontsize'] . " } ");
	$document->addStyleDeclaration("#" . $menuID . " li.level2 > a:hover { " . $level2linkcss['fontcolorhover'] . " } ");
	$document->addStyleDeclaration("#" . $menuID . " li.level2 > a span.accordeonckdesc { " . $level2linkcss['descfontcolor'] . $level2linkcss['descfontsize'] . " } ");
	// third and more level items
	$document->addStyleDeclaration("#" . $menuID . " li.level3 { " . $level3linkcss['padding'] . $level3linkcss['margin'] . $level3linkcss['background'] . $level3linkcss['gradient'] . $level3linkcss['borderradius'] . $level3linkcss['shadow'] . $level3linkcss['border'] . " } ");
	$document->addStyleDeclaration("#" . $menuID . " li.level3 a { " . $level3linkcss['fontcolor'] . $level3linkcss['fontsize'] . " } ");
	$document->addStyleDeclaration("#" . $menuID . " li.level3 a:hover { " . $level3linkcss['fontcolorhover'] . " } ");
	$document->addStyleDeclaration("#" . $menuID . " li.level3 a span.accordeonckdesc { " . $level3linkcss['descfontcolor'] . $level3linkcss['descfontsize'] . " } ");
}

$document->addScript(JURI::base(true) . '/modules/mod_accordeonck/assets/jquery.easing.1.3.js');
$js = "
       jQuery(document).ready(function(jQuery){
        jQuery('#" . $menuID . "').accordeonmenuck({"
		. "fadetransition : " . $fadetransition . ","
		. "eventtype : '" . $eventtype . "',"
		. "transition : '" . $mootransition . "',"
		. "menuID : '" . $menuID . "',"
		. "imageplus : '" . JURI::base(true) . '/' . $imageplus . "',"
		. "imageminus : '" . JURI::base(true) . '/' . $imageminus . "',"
		. "defaultopenedid : '" . $params->get('defaultopenedid') . "',"
		. "duree : " . $mooduration
		. "});
}); ";

$document->addScriptDeclaration($js);

// $list = ModaccordeonckHelper::getMenu($params);
$app = JFactory::getApplication();
$menu = $app->getMenu();
$active = $menu->getActive();
$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
$path = isset($active) ? $active->tree : array();
$showAll = 1;
$class_sfx = htmlspecialchars($params->get('class_sfx'));

if (count($list)) {
	require JModuleHelper::getLayoutPath('mod_accordeonck', $params->get('layout', 'default'));
}