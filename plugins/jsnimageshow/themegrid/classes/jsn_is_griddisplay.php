<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: jsn_is_griddisplay.php 16894 2012-10-11 04:49:55Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
class JSNISGridDisplay extends JObject
{
	var $_themename 	= 'themegrid';
	var $_themetype 	= 'jsnimageshow';
	var $_assetsPath 	= 'plugins/jsnimageshow/themegrid/assets/';
	function JSNISGridDisplay() {}

	function standardLayout($args)
	{
		$objJSNShowlist	= JSNISFactory::getObj('classes.jsn_is_showlist');
		$showlistInfo 	= $objJSNShowlist->getShowListByID($args->showlist['showlist_id'], true);
		$dataObj 		= $objJSNShowlist->getShowlist2JSON($args->uri, $args->showlist['showlist_id']);
		$images			= $dataObj->showlist->images->image;
		$document 		= JFactory::getDocument();
		$plugin			= false;

		if (!count($images)) return '';

		$pluginOpenTagDiv 	= '';
		$pluginCloseTagDiv 	= '';

		if (isset($args->plugin) && $args->plugin == true)
		{
			$plugin = true;
		}

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
				ksort($images);
		}

		JHTML::stylesheet($this->_assetsPath.'css/' . 'prettyPhoto.css',array('media'=>'screen','charset'=>'utf-8'));
		$this->loadjQuery();
		JHTML::script($this->_assetsPath.'js/' . 'jsn_is_conflict.js');
		JHTML::script($this->_assetsPath.'js/jquery/' . 'jquery.kinetic.js');
		JHTML::script($this->_assetsPath.'js/jquery/' . 'jquery.masonry.min.js');
		JHTML::script($this->_assetsPath.'js/jquery/' . 'jquery.prettyPhoto.js');
		JHTML::script($this->_assetsPath.'js/' . 'jsn_is_gridtheme.js');
		JHTML::script($this->_assetsPath.'js/' . 'jsn_is_gridthemelightbox.js');

		$percent  			= strpos($args->width, '%');

		$pluginOpenTagDiv = '<div style="max-width:'.$args->width.((!$percent)?'px':'').'; margin: 0 auto;">';
		$pluginCloseTagDiv = '</div>';
		$percent = true;
		$args->width = '100%';

		$themeData 		   	= $this->getThemeDataStandard($args);
		$imageSource		= ($themeData->image_source == 'thumbnail') ? 'thumbnail' : 'image';
		$objAllows			= new stdClass;
		$objAllows->show_caption 		= $themeData->show_caption;
		$objAllows->show_description	= $themeData->caption_show_description;
		$objAllows->show_close			= $themeData->show_close;
		$objAllows->show_thumbs			= $themeData->show_thumbs;
		$imageLink			= ($themeData->click_action == 'show_original_image')?'image':'link';
		$openLinkIn			= ($themeData->open_link_in == 'current_browser')?'':'target="_blank"';
		$themeDataJson		= json_encode($themeData);
		$width 			   	= ($percent === false) ? $args->width.'px' : $args->width;
		$wrapClass 		   	= 'jsn-'.$this->_themename.'-container-'.$args->random_number;

		if ($themeData->container_height_type == 'auto')
		{
			$html  = $pluginOpenTagDiv.'<div style="width: '.$width.'; border:none" class="jsn-themegrid-container '.$wrapClass.'">';
		}
		else
		{
			$html  = $pluginOpenTagDiv.'<div style="width: '.$width.'; height: '.$args->height.'px;border:none;overflow:hidden;" class="jsn-themegrid-container '.$wrapClass.'">';
		}


		$i=1;
		foreach ($images as $image)
		{
			if ($themeData->click_action != 'no_action') {
				if ($imageLink == 'image') {
					$rel = 'rel="prettyPhoto['.$args->random_number.']"';
					$href = 'href="'.$image->image.'"';
				} else {
					$rel = '';
					$href = 'href="'.$image->link.'"';
				}
			} else {
				$rel = $openLinkIn = '';
				$href = 'href="javascript:void(0);"';
			}
			$html .= '<div id="'.$args->random_number.'_'.$i.'" class="jsn-themegrid-box jsn-themegrid-image">';
			$html .= '<a '.$href.' '.$openLinkIn.' '.$rel.' rev="'.htmlspecialchars(strip_tags(trim($image->description), '<b><i><s><strong><em><strike><u><br>')).'" title="'.htmlspecialchars($image->title).'">';
			$html .= '<img id="img_'.$args->random_number.'_'.$i++.'" src="' . $image->$imageSource . '" border="0" alt="'.$image->title.'"/>';
			$html .= '</a></div>';
		}
		$html .= '</div>'.$pluginCloseTagDiv;
		$html .= '<input type="hidden" id="data_allow_grid_'.$args->random_number.'" value="'.htmlspecialchars(json_encode($objAllows)).'"/>';
		$html .= '<script type="text/javascript">
						jsnThemeGridjQuery(function() {
						jsnThemeGridjQuery(window).load(function(){
							jsnThemeGridjQuery(".'.$wrapClass.'").gridtheme('.$themeDataJson.');
							jsnThemeGridjQuery(".'.$wrapClass.'").gridtheme.lightbox({rand:"'.$args->random_number.'"});
						})});
				</script>';
		return $html;
	}

	function displayAlternativeContent()
	{
		return '';
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
					$html .= '<p><a href="'.$row->image_link.'">'.$row->image_link.'</a></p>';
				}
				$html .= '</li>';
			}
			$html .= '</ul></div>';
		}
		$html   .='</div>'."\n";
		return $html;
	}
	function mobileLayout($args){
		return '';
	}
	function display($args)
	{
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
			$gridOptions = new stdClass();
			$gridOptions->key				= $args->random_number;

			$gridOptions->background_color	= $themeData->background_color;
			$gridOptions->layout			= $themeData->img_layout;
			$gridOptions->thumbnail_width	= $themeData->thumbnail_width;
			$gridOptions->thumbnail_height	= $themeData->thumbnail_height;
			$gridOptions->thumbnail_space	= $themeData->thumbnail_space;
			$gridOptions->thumbnail_border	= $themeData->thumbnail_border;
			$gridOptions->image_source		= $themeData->image_source;
			$gridOptions->show_caption		= $themeData->show_caption;
			$gridOptions->show_close		= $themeData->show_close;
			$gridOptions->show_thumbs		= $themeData->show_thumbs;
			$gridOptions->click_action		= $themeData->click_action;
			$gridOptions->open_link_in		= $themeData->open_link_in;
			$gridOptions->caption_show_description 	= $themeData->caption_show_description;
			$gridOptions->thumbnail_rounded_corner	= $themeData->thumbnail_rounded_corner;
			$gridOptions->thumbnail_border_color	= $themeData->thumbnail_border_color;
			$gridOptions->thumbnail_shadow	= $themeData->thumbnail_shadow;

			if ($themeData->container_height_type != 'auto')
			{
				$gridOptions->height = $args->height;
			}
			$gridOptions->container_height_type	= $themeData->container_height_type;

			return $gridOptions;
		}
		return false;
	}

	function getThemeDataMobile($args)
	{
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