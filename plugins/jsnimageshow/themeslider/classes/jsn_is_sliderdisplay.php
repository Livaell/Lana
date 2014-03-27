<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Slider
 * @version $Id: jsn_is_sliderdisplay.php 16827 2012-10-10 05:03:46Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
class JSNISSliderDisplay extends JObject
{
	var $_themename 	= 'themeslider';
	var $_themetype 	= 'jsnimageshow';
	var $_assetsPath 	= 'plugins/jsnimageshow/themeslider/assets/';
	function JSNISSliderDisplay() {}

	function standardLayout($args)
	{
		$objJSNShowlist	= JSNISFactory::getObj('classes.jsn_is_showlist');
		$showlistInfo 	= $objJSNShowlist->getShowListByID($args->showlist['showlist_id'], true);
		$dataObj 		= $objJSNShowlist->getShowlist2JSON($args->uri, $args->showlist['showlist_id']);
		$images			= $dataObj->showlist->images->image;
		$document 		= JFactory::getDocument();

		if (!count($images)) return '';

		switch ($showlistInfo['image_loading_order'])
		{
			case 'backward':
				krsort($images);
				$tmpImageArray = $images;
				$images = array_values($images);
				break;
			case 'random':
				shuffle($images);
				break;
			case 'forward':
			default:
				ksort($images);
				break;
		}

		JHTML::stylesheet($this->_assetsPath.'css/skitter/' . 'skitter.styles.css');
		$this->loadjQuery();
		JHTML::script($this->_assetsPath.'js/' . 'jsn_is_conflict.js');
		JHTML::script($this->_assetsPath.'js/skitter/' . 'jquery.easing.1.3.js');
		JHTML::script($this->_assetsPath.'js/skitter/' . 'jquery.animate-colors-min.js');
		JHTML::script($this->_assetsPath.'js/skitter/' . 'jquery.skitter.js');
		$themeData			= $this->getThemeDataStandard($args);
		$openLinkIn			= ($themeData->open_link_in == 'current_browser')?'':'target="_blank"';
		$width				= (strpos($args->width, '%') === false) ? $args->width.'px' : $args->width;
		$titleCaptionClass	= 'jsn-'.$this->_themename.'-caption-title';
		$descCaptionClass	= 'jsn-'.$this->_themename.'-caption-description';
		$linkCaptionClass	= 'jsn-'.$this->_themename.'-caption-link';
		$wrapClass			= 'jsn-'.$this->_themename.'-skitter-'.$args->random_number;
		$jsResize			= '';
		if (strpos($args->width, '%') === false) {
			$jsResize		= 'var wrap_width = parseInt('.$args->width.');
							var window_width = parseInt(jsnThemeSliderjQuery(window).width());
							if (wrap_width > window_width) {
								jsnThemeSliderjQuery(\'.'.$wrapClass.'\').css("width", "100%");
							} else {
								jsnThemeSliderjQuery(\'.'.$wrapClass.'\').css("width", "'.$width.'");
							}';
		}
		$themeData->skitter_class_id = $wrapClass;
		$themeDataJson		= json_encode($themeData);
		$html = '<div id="'.$wrapClass.'">';
		$html .= '<div style="width: '.$width.'; height: '.$args->height.'px;" class="box_skitter jsn-'.$this->_themename.'-gallery '.$wrapClass.'">'."\n";
		$html .= '<ul>';

		foreach ($images as $image)
		{
			if ($themeData->click_action != 'no_action') {
				$href = 'href="'.$image->link.'"';
			} else {
				$openLinkIn = '';
				$href = 'href="javascript:void(0);"';
			}
			$html .= '<li>
			   			<a '.$href.' '.$openLinkIn.'>
			   				<img src="'.$image->image.'" alt="'.htmlspecialchars($image->title).'"/>
			   			</a>
			   			<div class="label_text">';

			$html .= ($image->title != '' && $themeData->caption_title_show) ? '<p class="'.$titleCaptionClass.'">'.$image->title.'</p>' : '';
			$html .= ($image->description != '' && $themeData->caption_description_show) ? '<p class="'.$descCaptionClass.'">'.strip_tags($image->description, '<b><i><s><strong><em><strike><u><br>').'</p>' : '';
			$html .= ($image->link != '' && $themeData->caption_link_show) ? '<p><a class="'.$linkCaptionClass.'" href="'.$image->link.'" target="_blank">'.$image->link.'</a></p>' : '';

			$html .=	'</div>
					 </li>';
		}

		$html .= '</ul>';
		$html .= '<input type="hidden" class="cache_skitter_index" value="" />';
		$html .= '</div>'."\n";
		$html .= '</div>';
		$html .= '<script type="text/javascript">
						jsnThemeSliderjQuery(function() {
						jsnThemeSliderjQuery(document).ready(function(){
							'.$jsResize.'
							var oldHTML = jsnThemeSliderjQuery(\'#'.$wrapClass.'\').html();
							var options = '.$themeDataJson.';
							options.base_height = '.$args->height.';
							jsnThemeSliderjQuery(\'.'.$wrapClass.'\').skitter(options);
							jsnThemeSliderjQuery(\'.'.$wrapClass.' .image_main\').css("max-width", "");
							var jsn_'.$args->random_number.' = jsnThemeSliderjQuery(window).width();
							var cacheResize;
							jsnThemeSliderjQuery(window).resize(function (e) {
								if (jsn_'.$args->random_number.' != jsnThemeSliderjQuery(window).width())
								{
									clearTimeout(cacheResize);
									cacheResize = "";
									var oldCacheIndex = jsnThemeSliderjQuery(\'#'.$wrapClass.' .cache_skitter_index\').attr(\'value\');
									jsnThemeSliderjQuery(\'#'.$wrapClass.'\').html(oldHTML);
									'.$jsResize.'
									jsnThemeSliderjQuery(\'#'.$wrapClass.' .cache_skitter_index\').attr(\'value\', oldCacheIndex);
									var options = '.$themeDataJson.';
									options.base_height = '.$args->height.';
									cacheResize = setTimeout(function () {
										jsnThemeSliderjQuery(\'.'.$wrapClass.'\').skitter(options);
										jsnThemeSliderjQuery(\'.'.$wrapClass.' .image_main\').css("max-width", "");
									}, 500);
									jsn_'.$args->random_number.' = jsnThemeSliderjQuery(window).width();
								}
							});
						})});
				</script>';
		$css = '.'.$wrapClass.' .label_skitter {'.$themeData->caption_caption_opacity.'}';
		$css .=	'.'.$wrapClass.' .label_skitter p.'.$titleCaptionClass.' {'.$themeData->caption_title_css.'}';
		$css .=	'.'.$wrapClass.' .label_skitter p.'.$descCaptionClass.' {'.$themeData->caption_description_css.'}';
		$css .=	'.'.$wrapClass.' .label_skitter a.'.$linkCaptionClass.' {'.$themeData->caption_link_css.'}';

		if ($themeData->label)
		{
			if ($themeData->caption_position == 'top')
			{
				$css .= '.'.$wrapClass.' .label_skitter {top: 0;}';
				$css .= '.'.$wrapClass.' .info_slide {bottom: 15px;}';
				$css .= '.'.$wrapClass.' .info_slide_dots {bottom: 15px;}';
			}
			else
			{
				$css .= '.'.$wrapClass.' .label_skitter {bottom: 0;}';
				$css .= '.'.$wrapClass.' .info_slide {top: 15px;}';
				$css .= '.'.$wrapClass.' .info_slide_dots {top: 15px;}';
			}
		}
		else
		{
			$css .= '.'.$wrapClass.' .info_slide {top: 15px;}';
			$css .= '.'.$wrapClass.' .info_slide_dots {top: 15px;}';
		}
		if (isset($themeData->dots) && $themeData->dots == true) {
			//$css .= '.jsn-'.$this->_themename.'-skitter-'.$args->random_number;
		}

		$document->addStyleDeclaration($css);
		return $html;
	}

	function displayAlternativeContent()
	{
		$html    = '<div class="jsn-'.$this->_themename.'-msgnonflash">'."\n";
		$html   .= '<p>'.JText::_('SITE_SHOW_YOU_NEED_FLASH_PLAYER').'</p>'."\n";
		$html   .= '<p>'."\n";
		$html   .= '<a href="http://www.adobe.com/go/getflashplayer">'."\n";
		$html   .= JText::_('SITE_SHOW_GET_FLASH_PLAYER')."\n";
		$html   .='</a>'."\n";
		$html   .='</p>'."\n";
		$html   .='</div>'."\n";
		return $html;
	}

	function displaySEOContent($args)
	{
		$html    = '<div class="jsn-'.$this->_themename.'-seocontent">'."\n";
		if ($args->edition == 'free')
		{
			$html	.= '<p>Joomla gallery extension by <a href="http://www.joomlashine.com" title="Joomla gallery">joomlashine.com</a></p>'."\n";
		}
		if (count($args->images))
		{
			$html .= '<div>';
			$html .= '<p>'.@$args->showlist['showlist_title'].'</p>';
			$html .= '<p>'.@$args->showlist['description'].'</p>';
			$html .= '<ul>';

			for ($i = 0, $n = count($args->images); $i < $n; $i++)
			{
				$row 	=& $args->images[$i];
				$html  .= '<li>';
				if ($row->image_title != '')
				{
					$html .= '<p>'.$row->image_title.'</p>';
				}
				if ($row->image_description != '')
				{
					$html .= '<p>'.$row->image_description.'</p>';
				}
				if ($row->image_link != '')
				{
					$html .= '<p><a href="'.htmlspecialchars($row->image_link).'">'.htmlspecialchars($row->image_link).'</a></p>';
				}
				$html .= '</li>';
			}
			$html .= '</ul></div>';
		}
		$html   .='</div>'."\n";
		return $html;
	}

	function display($args)
	{
		$objUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
		$device     = $objUtils->checkSupportedFlashPlayer();
		$string		= '';
		$args->uri	= JURI::base();
		$string .= $this->standardLayout($args);
		$string .= $this->displaySEOContent($args);
		return $string;
	}

	function getThemeDataStandard($args)
	{
		if (is_object($args))
		{
			$path = JPath::clean(JPATH_PLUGINS.DS.$this->_themetype.DS.$this->_themename.DS.'models');
			JModelLegacy::addIncludePath($path);

			$model 		= JModelLegacy::getInstance($this->_themename);
			$themeData  = $model->getTable($args->theme_id);

			$sliderOptions = new stdClass();
			$sliderOptions->animation = $themeData->img_transition_effect;

			if ($themeData->toolbar_navigation_arrows_presentation == 'hide') {
				$sliderOptions->navigation = false;
			}

			if ($themeData->toolbar_navigation_arrows_presentation == 'show-always') {
				$sliderOptions->navigation = true;
			}

			if ($themeData->toolbar_navigation_arrows_presentation == 'show-on-mouse-over') {
				$sliderOptions->navigation = true;
				$sliderOptions->navShowOnMouseOver = true;
			}

			if ($themeData->thumbnail_panel_presentation == 'hide') {
				$sliderOptions->dots = false;
				$sliderOptions->numbers = false;
			}

			if ($themeData->thumbnail_presentation_mode == 'numbers' && $themeData->thumbnail_panel_presentation == 'show') {
				$sliderOptions->dots = false;
				$sliderOptions->numbers = true;
			}

			if ($themeData->thumbnail_presentation_mode == 'dots' && $themeData->thumbnail_panel_presentation == 'show') {
				$sliderOptions->dots = true;
				$sliderOptions->numbers = false;
			}

			if ($themeData->thumbnail_panel_presentation != '' && $themeData->thumbnail_panel_presentation != 'hide')
			{
				if ($themeData->thumnail_panel_position == 'left') {
					$sliderOptions->numbers_align = 'left';
				}

				if ($themeData->thumnail_panel_position == 'center') {
					$sliderOptions->numbers_align = 'center';
				}

				if ($themeData->thumnail_panel_position == 'right') {
					$sliderOptions->numbers_align = 'right';
				}
			}

			$sliderOptions->caption_title_css = $themeData->caption_title_css;
			$sliderOptions->caption_description_css = $themeData->caption_description_css;
			$sliderOptions->caption_link_css = $themeData->caption_link_css;
			$sliderOptions->caption_position = $themeData->caption_position;

			$sliderOptions->caption_caption_opacity = 'filter:alpha(opacity='.$themeData->caption_caption_opacity.');';
			$sliderOptions->caption_caption_opacity .= 'opacity: '.round($themeData->caption_caption_opacity / 100, 2).';';

			if ($themeData->slideshow_slide_timming != '') {
				$sliderOptions->interval = (int) $themeData->slideshow_slide_timming*1000;
			}

			if ($themeData->toolbar_slideshow_player_presentation == 'hide') {
				$sliderOptions->controls = false;
			}

			if ($themeData->toolbar_slideshow_player_presentation == 'show') {
				$sliderOptions->controls = true;
			}

			if ($themeData->toolbar_slideshow_player_presentation == 'show-on-mouse-over') {
				$sliderOptions->controls = true;
				$sliderOptions->controlShowOnMouseOver = true;
			}

			if ($themeData->slideshow_pause_on_mouseover == 'yes') {
				$sliderOptions->stop_over = true;
			} else {
				$sliderOptions->stop_over = false;
			}

			if ($themeData->slideshow_auto_play == 'yes') {
				$sliderOptions->auto_play = true;
			} else {
				$sliderOptions->auto_play = false;
			}

			if ($themeData->caption_title_show == 'yes') {
				$sliderOptions->caption_title_show = true;
			} else {
				$sliderOptions->caption_title_show = false;
			}

			if ($themeData->caption_description_show == 'yes') {
				$sliderOptions->caption_description_show = true;
			} else {
				$sliderOptions->caption_description_show = false;
			}

			if ($themeData->caption_link_show == 'yes') {
				$sliderOptions->caption_link_show = true;
			} else {
				$sliderOptions->caption_link_show = false;
			}

			if ($themeData->caption_show_caption == 'show' && ($sliderOptions->caption_link_show || $sliderOptions->caption_description_show || $sliderOptions->caption_title_show)) {
				$sliderOptions->label = true;
			} else {
				$sliderOptions->label = false;
			}

			if ($themeData->thumbnail_active_state_color != '')
			{
				$sliderOptions->animateNumberActive = array('backgroundColor'=>$themeData->thumbnail_active_state_color, 'color'=>'#fff');
			}

			$sliderOptions->click_action		= $themeData->click_action;
			$sliderOptions->open_link_in		= $themeData->open_link_in;
			return $sliderOptions;
		}

		return false;
	}

	function loadjQuery()
	{
		$objUtils = JSNISFactory::getObj('classes.jsn_is_utils');

		if (method_exists($objUtils, 'loadJquery'))
		{
			$objUtils->loadJquery();
		}
		else
		{
			JHTML::script($this->_assetsPath . 'js/jsn_is_jquery_safe.js');
			JHTML::script('https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
		}
	}
}