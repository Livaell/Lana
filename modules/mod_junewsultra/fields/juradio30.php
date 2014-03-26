<?php
/**
* @package Joomla! 2.5
* @version 4.x
* @author 2008-2012 (c)  Denys Nosov (aka Dutch)
* @author web-site: www.joomla-ua.org
* @copyright This module is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License.
**/

defined('JPATH_PLATFORM') or die;

class JFormFieldJURadio30 extends JFormField
{
	protected $type = 'JURadio30';

	protected function getInput()
	{
        $version    = new JVersion;
        $joomla     = substr($version->getShortVersion(), 0, 3);

		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio"';

		    if($joomla >= '3.0') {
		        $disabled   = '';
		        $style      = '';
                $txt        = '';
		    } else {
		        $disabled   = ' disabled="disabled"';
                $style      = ' style="color: #999;"';
                $txt        = '<span class="label" title="'.JText::_('MOD_JUNEWS_NOTSUP_DESC').'">'.JText::_('MOD_JUNEWS_NOTSUP').'</span> <span class="label label-warning" title="'.JText::_('MOD_JUNEWS_FORJ30_DESC').'">'.JText::_('MOD_JUNEWS_FORJ30').'</span>';
		    }

		// Start the radio field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';

		// Get the field options.
		$options = $this->getOptions();

		// Build the radio field output.
		foreach ($options as $i => $option)
		{
			// Initialize some option attributes.
			$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';


			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

			$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . '/>';

			$html[] = '<label for="' . $this->id . $i . '"' . $class . $style . '>'
				. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';
		}

		// End the radio field output.
		$html[] = $txt . '</fieldset>';

		return implode($html);
	}

	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option)
		{

			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
				((string) $option['disabled'] == 'true')
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
