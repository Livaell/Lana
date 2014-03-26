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
 * Toggler Element
 *
 * To use this, make a start xml param tag with the param and value set
 * And an end xml param tag without the param and value set
 * Everything between those tags will be included in the slide
 *
 * Available extra parameters:
 * param			The name of the reference parameter
 * value			a comma separated list of value on which to show the framework
 */
if (!class_exists('JFormFieldGJFields')) {include ('gjfields.php');}
class JFormFieldToggler extends JFormFieldGJFields {
	/**
	 * The form field type
	 *
	 * @var		string
	 */
	public $type = 'toggler';

	protected function getLabel() {
		if (!isset($GLOBALS[$this->type.'_initialized'])) {
			$GLOBALS[$this->type.'_initialized'] = true;

			$path_to_assets = JURI::root().'libraries/gjfields/';
			/*
			// It's my development need. If I use the same file for developing J2.5 and J3.0 I cannot properly determine the path, so I assume it's a default one (else)
			if (strpos(__DIR__,JPATH_SITE)) {
				$baseurl = str_replace('administrator/','',JURI::base());
				$path_to_assets = JPath::clean(str_replace($baseurl,'',$baseurl . str_replace(JPATH_SITE,'',__DIR__).'/'));
			}
			else {
				$path_to_assets = '/libraries/gjfields/';
			}
			*/



			$doc =& JFactory::getDocument();

			$cssname = $path_to_assets.'css/'.$this->type.'.css';
			$doc->addStyleSheet($cssname);

			$jversion = new JVersion;
			$common_script = $path_to_assets.'js/script.js?v='.$jversion->RELEASE;
			$doc->addScript($common_script);
			$scriptname = $path_to_assets.'js/'.$this->type.'.js';
			$doc->addScript($scriptname);
		}

	}

	function getInput() {

		$param = $this->def( 'param' );
		$value = $this->def( 'value' );
		$nofx = $this->def( 'nofx' );
		$horz = $this->def( 'horizontal' );
		$method = $this->def( 'method' );
		$overlay = $this->def( 'overlay' );
		$casesensitive = $this->def( 'casesensitive' );
		$class = $this->def( 'class' );

		$param = preg_replace( '#^\s*(.*?)\s*$#', '\1', $param );
		$param = preg_replace( '#\s*\|\s*#', '|', $param );

		$html = PHP_EOL;
		if ( $param != '' ) {
			$param = preg_replace( '#[^a-z0-9-\.\|\@]#', '_', $param );
			$set_groups = explode( '|', $param );
			$set_values = explode( '|', $value );
			$ids = array();
			foreach ( $set_groups as $i => $group ) {
				$count = $i;
				if ( $count >= count( $set_values ) ) {
					$count = 0;
				}
				$value = explode( ',', $set_values[$count] );
				foreach ( $value as $val ) {
					$ids[] = $group.'.'.$val;
				}
			}
			if (!empty($this->element['label'])) {
				$class .= ' blockquote';
			}

			$id = '___'.implode( '___', $ids );
			$html .= '<div id="'.rand( 1000000, 9999999 ).$id.'" class="gjtoggler options'.$id;
			if ( $nofx ) {
				$html .= ' gjtoggler_nofx';
			}
			if ( $horz ) {
				$html .= ' gjtoggler_horizontal';
			}
			if ( $method == 'and' ) {
				$html .= ' gjtoggler_and';
			}
			if ( $overlay ) {
				$html .= ' gjtoggler_overlay';
			}
			if ( $casesensitive ) {
				$html .= ' gjtoggler_casesensitive';
			}
			//$html .= '" style="visibility: hidden;">';
			$html .= ' '.$class.'" >';
			if (!empty($this->element['label'])) {
				$html .= '<div class="title">'.JText::_($this->element['label']).'</div>';
			}
			if(version_compare(JVERSION,'3.0','ge')) {
				$html = '</div>'.PHP_EOL.'<!-- controls !-->'.PHP_EOL.$html.PHP_EOL.'<div><div>';
			}
			else {
				$html .= '<ul><li>'.PHP_EOL;
			}
		} else {
			if(version_compare(JVERSION,'3.0','ge')) {
				$html .= '</div><!-- controls !-->'.PHP_EOL;
				$html .= '</div><!-- control-group !-->'.PHP_EOL;
			}
			else {
				$html .= "\n".'</li></ul>';
			}
			$html .= '<div style="clear: both;"></div>'.PHP_EOL;
		}

		return $html;
	}



}

