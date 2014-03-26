<?php
/**
* Utils class
* @package News Show Pro GK4
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.0 $
**/

// no direct access
defined('_JEXEC') or die('Restricted access');
class NSP_GK4_Utils {
	// Method to cut text with specified limit value and type (characters/words)
	function cutText($text, $limit_value, $limit_type, $at_end) {
		// solved problem from: https://www.gavick.com/support/forums/47/12309.html?p=57464#p57464
		$cck_path = JPATH_BASE . DS . 'components' . DS . 'com_cck';
		if (file_exists($cck_path)) {
			if(JComponentHelper::isEnabled('com_cck', true)){
				// Force parsing plugin if SEBLOD is used
				if($this->config['parse_plugins'] == FALSE) {
					 $text = JHtml::_('content.prepare', $text);
				}
				$text = trim(substr(strip_tags( $text,"<br /><br><strong></strong><p></p><i></i><b></b><span></span><ul></ul><li></li><blockquote></blockquote>"),0));
			}
		}
		if($limit_type == 'words' && $limit_value > 0){
			$temp = explode(' ',$text);
			
			if(count($temp) > $limit_value){
				for($i=0; $i<$limit_value; $i++) $cutted[$i] = $temp[$i];
				$cutted = implode(' ', $cutted);
				$text = $cutted.$at_end;
			}
		} elseif($limit_type == 'words' && $limit_value == 0) {
			return '';
		} else {
			if(JString::strlen($text) > $limit_value){
				$text = JString::substr($text, 0, $limit_value) . $at_end;
			}
		}
		// replace unnecessary entities at end of the cutted text
		$toReplace = array('&&', '&a&', '&am&', '&amp&', '&q&', '&qu&', '&quo&', '&quot&', '&ap&', '&apo&', '&apos&');
		$text = str_replace($toReplace, '&', $text);
		//
		return $text;
	}
	// Method to get Gravatar avatar
	function avatarURL($email, $size){
		return 'http://www.gravatar.com/avatar/'.md5($email).'?s='.$size.'&amp;default='.urlencode(JURI::root().'modules/mod_news_pro_gk4/interface/images/avatar.png');
	}
}
/* EOF */