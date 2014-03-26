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
class JFormFieldBlockquote extends JFormFieldGJFields {
	/**
	 * The form field type
	 *
	 * @var		string
	 */
	public $type = 'blockquote';


	protected function getLabel() {
		return '';
	}

	function getInput() {

		$class = $this->def( 'class' );
		$html = '';
		if(version_compare(JVERSION,'3.0','ge')) {
			$html .= '</div><!-- controls !-->'.PHP_EOL;
			$html .= '</div><!-- control-group !-->'.PHP_EOL;
		}
		else {
			$html .= "\n".'</li></ul><br clear="both" >';
		}
		//$html .= '<div style="clear: both;"></div>'.PHP_EOL;

		$name = (string)$this->element['clean_name'];
		if (empty($name)) {
			$name = (string)$this->element['name'];
		}
		if (strpos($name,'jform[params]') !== false) {
			$name = explode ('][',$name);
			$name = $name[count($name) -2];
		}
		if (strpos($name,'{') === 0 || strpos($name,'jform[params][{') === 0 ) {
			$html .= PHP_EOL.'<!-- '.$this->element['name'].' !-->'.PHP_EOL.'<div class="blockquote '.$class.'">';
			if (!empty($this->element['label'])) {
				$html .= '<div class="title">'.JText::_($this->element['label']).'</div>';
			}
		}
		else {
			$html .= '</div><!-- '.$name.' !-->'.PHP_EOL;
		}

		if(version_compare(JVERSION,'3.0','ge')) {
			$html .= '<div><div>';
		}
		else {
			$html .= '<ul><li>';
		}
		return $html;
	}



}

