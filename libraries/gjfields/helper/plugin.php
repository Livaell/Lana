<?php
/**
 * A wrapper class to extend common joomla class with GJFields methods
 *
 * @package		GJFields
 * @author Gruz <arygroup@gmail.com>
 * @copyright	Copyleft - All rights reversed
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

class JPluginGJFields extends JPlugin {

	function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
		$jinput = JFactory::getApplication()->input;
		if ($jinput->get('option',null) == 'com_dump') { return; }


		// Load languge for frontend
		$this->plg_name = $config['name'];
		$this->plg_type = $config['type'];
		$this->plg_full_name = 'plg_'.$config['type'].'_'.$config['name'];
		$this->langShortCode = null;//is used for building joomfish links
		$this->default_lang = JComponentHelper::getParams('com_languages')->get('site');
		$language = JFactory::getLanguage();
		$this->plg_path = JPATH_PLUGINS.'/'.$this->plg_type.'/'.$this->plg_name.'/';

		$language->load($this->plg_full_name, $this->plg_path, 'en-GB', true);
		$language->load($this->plg_full_name, $this->plg_path, $this->default_lang, true);

	}


	/**
	 * Parses parameters of gjfileds (variablefileds) into a convinient arrays
	 *
	 * @author Gruz <arygroup@gmail.com>
	 * @param	string	$group_name	Name of the group in the XML file
	 * @return	type			Description
	 */
	function getGroupParams ($group_name) {
		$jinput = JFactory::getApplication()->input;
		if ($jinput->get('option',null) == 'com_dump') { return; }

		if (!isset($GLOBALS[$this->plg_name]['variable_group_name'][$group_name])) {
			$GLOBALS[$this->plg_name]['variable_group_name'][$group_name] = true;
		}
		else {
			return;
		}
		// Get all parameters
		$params = $this->params->toObject();
		$pparams = array();
		/*
		if (empty($params->{$group_name})) {
			$override_parameters = array (
				'ruleEnabled'=>$this->paramGet('ruleEnabled'),
				'menuname'=>$this->paramGet('menuname'),
				'show_articles'=>$this->paramGet('show_articles'),
				'categories'=>$this->paramGet('categories'),
				'regeneratemenu'=>$this->paramGet('regeneratemenu')
			);
			$pparams[] = $override_parameters;
		}
		*/
		if (empty($params->{$group_name})) {
			$params->{$group_name} = array();
		}
		$pparams_temp  = $params->{$group_name};

		foreach ($pparams_temp as $fieldname=>$values) {
			$group_number = 0;
			$values = (array) $values;
			foreach ($values as $n=>$value) {
				if ($value == 'variablefield::'.$group_name) {
					$group_number++;
				}
				else if (is_array($value) && $value[0] == 'variablefield::'.$group_name){
					if (!isset($pparams[$group_number][$fieldname])) {
						$pparams[$group_number][$fieldname] = array();
					}
					$group_number++;
				}
				else if (is_array($value) ) {
					$pparams[$group_number][$fieldname][] = $value[0];
				}
				else if ( $fieldname == $group_name ) {
					$pparams[$group_number][$fieldname][] = $value;
				}
				else {
					$pparams[$group_number][$fieldname] = $value;
				}
			}
		}
		$this->pparams = $pparams;
	}

	/**
	 * Sets some default values
	 *
	 * In J1.7+ the default values written in the XML file are not passed to the script
	 * till first time save the plugin options. The defaults are used only to show values when loading
	 * the setting page for the first time. And if a user just publishes the plugin from the plugin list,
	 * ALL the fields doesn't have values set. So this function
	 * is created to avoid duplicating the defaults in the code.
	 * Usage:
	 * Instead of
	 * <code>$this->params->get( 'some_field_name', 'default_value' )</code>
	 * use
	 * <code>$this->paramGet( 'some_field_name',[optional 'default_value'])</code>
	 *
	 * @author Gruz <arygroup@gmail.com>
	 * @param type $name Description
	 * @return type Description
	 */
	function paramGet($name,$default=null) {
		$hash = get_class();
		$session = JFactory::getSession();
		$params = $session->get('DefaultParams',false,$hash);
		if (empty($params)) {
			//$xmlfile = dirname(__FILE__).'/'.basename(__FILE__,'.php').'.xml';
			$xmlfile = $this->plg_path.'/'.$this->plg_name.'.xml';
			$xml = simplexml_load_file($xmlfile);
			if ( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				unset ($xml->scriptfile);
				$field = 'field';
				$xpath = 'config/fields/fieldset';
			}
			else {
				$field = 'param';
				$xpath = 'params';

			}
			foreach ($xml->xpath('//'.$xpath.'/'.$field) as $f) {
				if (isset($f['default']) ) {
					if (preg_match('~[0-9]+,[0-9]*~',(string)$f['default'])) {
						$params[(string)$f['name']] = explode (',',(string)$f['default']);
					}
					else {
						$params[(string)$f['name']] = (string)$f['default'];
					}
				}
			}
			$session->set('DefaultParams',$params,$hash);
		}
		if (!isset ($params[$name])) {
			$params[$name] = $default;
		}
		return $this->params->get( $name,$params[$name]);
	}


}
