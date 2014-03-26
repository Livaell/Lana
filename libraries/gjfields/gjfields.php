<?php
/**
 * @package     GJFileds
 *
 * @copyright   Copyright (C) All rights reversed.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL or later
 */

// No direct access
defined( '_JEXEC' ) or die();

/**
 * Base class to extend with gjfields fileds
 *
 */
class JFormFieldGJFields extends JFormField {
	function __construct($form = null) {
		parent::__construct($form);
		if (!isset($GLOBALS[$this->type.'_initialized'])) {
			$GLOBALS[$this->type.'_initialized'] = true;
			$url_to_assets = JURI::root().'libraries/gjfields/';
			$path_to_assets = JPATH_ROOT.'/libraries/gjfields/';
			$doc = JFactory::getDocument();

			$cssname = $url_to_assets.'css/common.css';
			$cssname_path = $path_to_assets.'css/common.css';
			if (file_exists($cssname_path)) {
				$doc->addStyleSheet($cssname);
			}
			$this->type = JString::strtolower($this->type);

			$cssname = $url_to_assets.'css/'.$this->type.'.css';
			$cssname_path = $path_to_assets.'css/'.$this->type.'.css';
			if (file_exists($cssname_path)) {
				$doc->addStyleSheet($cssname);
			}

			$jversion = new JVersion;
			$common_script = $url_to_assets.'js/script.js?v='.$jversion->RELEASE;
			$doc->addScript($common_script);

			$scriptname = $url_to_assets.'js/'.$this->type.'.js';
			$scriptname_path = $path_to_assets.'js/'.$this->type.'.js';
			if (file_exists($scriptname_path)) {
				$doc->addScript($scriptname);
			}
		}
	}
	function getInput() {
	}
	function def( $val, $default = '' )	{
		return ( isset( $this->element[$val] ) && (string) $this->element[$val] != '' ) ? (string) $this->element[$val] : $default;
	}

}
