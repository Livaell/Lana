<?php
/**
 * @version    $Id: helper.php 16756 2012-10-08 05:11:02Z giangnd $
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
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
JHtml::addIncludePath(JPATH_ROOT . DS . 'administrator' . 'components' . 'com_imageshow' .DS . '/elements/html');

$objJSNShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
$objJSNShowcaseTheme->importTableByThemeName($this->_showcaseThemeName);
$objJSNShowcaseTheme->importModelByThemeName($this->_showcaseThemeName);
$modelShowcaseTheme = JModelLegacy::getInstance($this->_showcaseThemeName);
$items = $modelShowcaseTheme->getTable($themeID);

JSNISFactory::importFile('classes.jsn_is_htmlselect');

/**
 * /////////////////////////////////////////////////////////Image Panel Begin////////////////////////////////////////////////////////////////////////////
 */
$imgTransitionEffect = array(
		'0' => array('value' => 'cube', 'text' => JText::_('THEME_SLIDER_CUBE')),
		'1' => array('value' => 'cubeRandom', 'text' => JText::_('THEME_SLIDER_CUBE_RANDOM')),
		'2' => array('value' => 'block', 'text' => JText::_('THEME_SLIDER_BLOCK')),
		'3' => array('value' => 'cubeStop', 'text' => JText::_('THEME_SLIDER_CUBE_STOP')),
		'4' => array('value' => 'cubeHide', 'text' => JText::_('THEME_SLIDER_CUBE_HIDE')),
		'5' => array('value' => 'cubeSize', 'text' => JText::_('THEME_SLIDER_CUBE_SIZE')),
		'6' => array('value' => 'horizontal', 'text' => JText::_('THEME_SLIDER_HORIZONTAL')),
		'7' => array('value' => 'showBars', 'text' => JText::_('THEME_SLIDER_SHOWBARS')),
		'8' => array('value' => 'showBarsRandom', 'text' => JText::_('THEME_SLIDER_SHOWBARS_RANDOM')),
		'9' => array('value' => 'tube', 'text' => JText::_('THEME_SLIDER_TUBE')),
		'10' => array('value' => 'fade', 'text' => JText::_('THEME_SLIDER_FADE')),
		'11' => array('value' => 'fadeFour', 'text' => JText::_('THEME_SLIDER_FADE_FOUR')),
		'12' => array('value' => 'paralell', 'text' => JText::_('THEME_SLIDER_PARALELL')),
		'13' => array('value' => 'blind', 'text' => JText::_('THEME_SLIDER_BLIND')),
		'14' => array('value' => 'blindHeight', 'text' => JText::_('THEME_SLIDER_BLIND_HEIGHT')),
		'15' => array('value' => 'directionTop', 'text' => JText::_('THEME_SLIDER_DIRECTION_TOP')),
		'16' => array('value' => 'directionBottom', 'text' => JText::_('THEME_SLIDER_DIRECTION_BOTTOM')),
		'17' => array('value' => 'directionRight', 'text' => JText::_('THEME_SLIDER_DIRECTION_RIGHT')),
		'18' => array('value' => 'directionLeft', 'text' => JText::_('THEME_SLIDER_DIRECTION_LEFT')),
		'19' => array('value' => 'cubeStopRandom', 'text' => JText::_('THEME_SLIDER_CUBE_STOP_RANDOM')),
		'20' => array('value' => 'cubeSpread', 'text' => JText::_('THEME_SLIDER_CUBE_SPREAD')),
		'21' => array('value' => 'cubeJelly', 'text' => JText::_('THEME_SLIDER_CUBE_JELLY')),
		'22' => array('value' => 'glassCube', 'text' => JText::_('THEME_SLIDER_GLASS_CUBE')),
		'23' => array('value' => 'glassBlock', 'text' => JText::_('THEME_SLIDER_GLASS_BLOCK')),
		'24' => array('value' => 'circles', 'text' => JText::_('THEME_SLIDER_CIRCLES')),
		'25' => array('value' => 'circlesInside', 'text' => JText::_('THEME_SLIDER_CIRCLES_INSIDE')),
		'26' => array('value' => 'circlesRotate', 'text' => JText::_('THEME_SLIDER_CIRCLES_ROTATE')),
		'27' => array('value' => 'cubeShow', 'text' => JText::_('THEME_SLIDER_CUBE_SHOW')),
		'28' => array('value' => 'upBars', 'text' => JText::_('THEME_SLIDER_UP_BARS')),
		'29' => array('value' => 'downBars', 'text' => JText::_('THEME_SLIDER_DOWN_BARS')),
		'30' => array('value' => 'random', 'text' => JText::_('THEME_SLIDER_RANDOM')),
		'31' => array('value' => 'randomSmart', 'text' => JText::_('THEME_SLIDER_RANDOM_SMART'))
);
$lists['imgTransitionEffect'] = JHTML::_('select.genericList', $imgTransitionEffect, 'img_transition_effect', 'class="inputbox imagePanel"', 'value', 'text', $items->img_transition_effect );

$toolbarNavigationArrowsPresentation = array(
		'0' => array('value' => 'hide', 'text' => JText::_('THEME_SLIDER_HIDE')),
		'1' => array('value' => 'show-always', 'text' => JText::_('THEME_SLIDER_SHOW_ALWAYS')),
		'2' => array('value' => 'show-on-mouse-over', 'text' => JText::_('THEME_SLIDER_SHOW_ON_MOUSE_OVER'))
);
$lists['toolbarNavigationArrowsPresentation'] = JHTML::_('select.genericList', $toolbarNavigationArrowsPresentation, 'toolbar_navigation_arrows_presentation', 'class="inputbox toolbarPanel"', 'value', 'text', $items->toolbar_navigation_arrows_presentation );

$thumbnailPresentationMode = array(
		'1' => array('value' => 'dots', 'text' => JText::_('THEME_SLIDER_SHOW_AS_DOTS')),
		'0' => array('value' => 'numbers', 'text' => JText::_('THEME_SLIDER_SHOW_AS_NUMBERS'))
);
$lists['thumbnailPresentationMode'] = JHTML::_('select.genericList', $thumbnailPresentationMode, 'thumbnail_presentation_mode', 'class="inputbox thumbnailPanel"', 'value', 'text', $items->thumbnail_presentation_mode );

$thumbnailPanelPosition = array(
		'0' => array('value' => 'left', 'text' => JText::_('THEME_SLIDER_LEFT')),
		'1' => array('value' => 'center', 'text' => JText::_('THEME_SLIDER_CENTER')),
		'2' => array('value' => 'right', 'text' => JText::_('THEME_SLIDER_RIGHT'))
);

$lists['thumbnailPanelPosition'] = JHTML::_('select.genericList', $thumbnailPanelPosition, 'thumnail_panel_position', 'class="inputbox thumbnailPanel"', 'value', 'text', $items->thumnail_panel_position );


$thumbnailPanelPresentation = array(
		'0' => array('value' => 'hide', 'text' => JText::_('THEME_SLIDER_HIDE')),
		'1' => array('value' => 'show', 'text' => JText::_('THEME_SLIDER_SHOW'))
);
$lists['thumbnailPanelPresentation'] = JHTML::_('select.genericList', $thumbnailPanelPresentation, 'thumbnail_panel_presentation', 'class="inputbox thumbnailPanel"  onChange="jQuery.JSNISThemeSlider.toggleThumbnailOptions(this);"', 'value', 'text', $items->thumbnail_panel_presentation );

$captionShowCaption = array(
		'0' => array('value' => 'hide', 'text' => JText::_('THEME_SLIDER_HIDE')),
		'1' => array('value' => 'show', 'text' => JText::_('THEME_SLIDER_SHOW'))
);

$lists['captionShowCaption'] 			= JHTML::_('select.genericList', $captionShowCaption, 'caption_show_caption', 'class="inputbox informationPanel" onChange="jQuery.JSNISThemeSlider.toggleCaptionOptions(this);"', 'value', 'text', $items->caption_show_caption );
$lists['captionShowTitle'] 				= JHTML::_('jsnselect.booleanlist', 'caption_title_show', 'class="inputbox informationPanel"', $items->caption_title_show, 'JYES', 'JNO', false, 'yes', 'no');
$lists['captionShowDescription'] 		= JHTML::_('jsnselect.booleanlist', 'caption_description_show', 'class="inputbox informationPanel"', $items->caption_description_show, 'JYES', 'JNO', false, 'yes', 'no');
$lists['captionShowLink'] 				= JHTML::_('jsnselect.booleanlist', 'caption_link_show', 'class="inputbox informationPanel"', $items->caption_link_show, 'JYES', 'JNO', false, 'yes', 'no');

$captionPosition = array(
		'0' => array('value' => 'top',
				'text' => JText::_('THEME_SLIDER_TOP')),
		'1' => array('value' => 'bottom',
				'text' => JText::_('THEME_SLIDER_BOTTOM'))
);

$lists['captionPosition'] = JHTML::_('select.genericList', $captionPosition, 'caption_position', 'class="inputbox informationPanel"', 'value', 'text', $items->caption_position);

$toolbarSlideshowPlayerPresentation = array(
		'0' => array('value' => 'hide', 'text' => JText::_('THEME_SLIDER_HIDE')),
		'1' => array('value' => 'show', 'text' => JText::_('THEME_SLIDER_SHOW_ALWAYS')),
		'2' => array('value' => 'show-on-mouse-over', 'text' => JText::_('THEME_SLIDER_SHOW_ON_MOUSE_OVER'))
);

$lists['toolbarSlideshowPlayerPresentation'] 	= JHTML::_('select.genericList', $toolbarSlideshowPlayerPresentation, 'toolbar_slideshow_player_presentation', 'class="inputbox slideshowPanel"', 'value', 'text', $items->toolbar_slideshow_player_presentation );
$lists['slideShowAutoPlay'] 	 				= JHTML::_('jsnselect.booleanlist', 'slideshow_auto_play', 'class="inputbox slideshowPanel"', $items->slideshow_auto_play, 'JYES', 'JNO', false, 'yes', 'no');
$lists['slideshowPauseOnMouseOver'] 			= JHTML::_('jsnselect.booleanlist', 'slideshow_pause_on_mouseover', 'class="inputbox slideshowPanel"', $items->slideshow_pause_on_mouseover, 'JYES', 'JNO', false, 'yes', 'no');

$clickAction = array(
	'0' => array('value' => 'no_action', 'text' => JText::_('THEME_SLIDER_CLICK_ACTION_NO_ACTION')),
	'1' => array('value' => 'open_image_link', 'text' => JText::_('THEME_SLIDER_CLICK_ACTION_OPEN_IMAGE_LINK'))
);
$lists['clickAction'] = JHTML::_('select.genericList', $clickAction, 'click_action', 'class="inputbox"', 'value', 'text', ($items->click_action == '')?'no_action':$items->click_action);

$openLinkIn = array(
	'0' => array('value' => 'current_browser', 'text' => JText::_('THEME_SLIDER_OPEN_LINK_IN_CURRENT_BROWSER')),
	'1' => array('value' => 'new_browser', 'text' => JText::_('THEME_SLIDER_OPEN_LINK_IN_NEW_BROWSER'))
);
$lists['openLinkIn'] = JHTML::_('select.genericList', $openLinkIn, 'open_link_in', 'class="inputbox"', 'value', 'text', ($items->open_link_in == '')?'current_browser':$items->open_link_in);