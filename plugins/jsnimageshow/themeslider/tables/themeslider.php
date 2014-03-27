<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: themeslider.php 11782 2012-03-19 08:10:29Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
class TableThemeSlider extends JTable
{
	var $theme_id 					  			= null;
	var $img_transition_effect 		  			= 'random';
	var $toolbar_navigation_arrows_presentation = 'show-on-mouse-over';
	var $toolbar_slideshow_player_presentation	= 'hide';
	var $caption_show_caption 		  			= 'yes';
	var $caption_title_css 		  	  			= "font-family: Verdana;\nfont-size: 12px;\nfont-weight: bold;\ntext-align: left;\ncolor: #E9E9E9;";
	var $caption_description_css  	 			= "font-family: Arial;\nfont-size: 11px;\nfont-weight: normal;\ntext-align: left;\ncolor: #AFAFAF;";
	var $caption_link_css 	 	  	  			= "font-family: Verdana;\nfont-size: 11px;\nfont-weight: bold;\ntext-align: right;\ncolor: #E06614;";
	var $caption_caption_opacity 	  			= '75';
	var $caption_title_show						= 'yes';
	var $caption_description_show				= 'yes';
	var $caption_link_show			  			= 'no';
	var $caption_position			  			= 'bottom';	
	var $slideshow_slide_timming 	  			= '6';
	var $slideshow_pause_on_mouseover 			= 'yes';
	var $slideshow_auto_play 		  			= 'yes';
	var $thumnail_panel_position	 			= 'right';
	var $thumbnail_panel_presentation 			= 'show';
	var $thumbnail_presentation_mode 			= 'numbers';
	var $thumbnail_active_state_color			= '#CC3333';
	var $click_action				 			= 'no_action';
	var $open_link_in				 			= 'current_browser';

	function __construct(& $db) {
		parent::__construct('#__imageshow_theme_slider', 'theme_id', $db);
	}
}
?>