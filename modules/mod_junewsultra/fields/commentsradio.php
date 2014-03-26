<?php
/**
* @package Joomla! 2.5
* @version 4.x
* @author 2008-2012 (c)  Denys Nosov (aka Dutch)
* @author web-site: www.joomla-ua.org
* @copyright This module is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License.
**/

defined('JPATH_PLATFORM') or die;

class JFormFieldCommentsRadio extends JFormField
{
	protected $type = 'CommentsRadio';

	protected function getInput()
	{
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio"';

		// Start the radio field output.
	   	$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';

		// Get the field options.
		$options = $this->getOptions();

		// Build the radio field output.
		foreach ($options as $i => $option)
		{

			// Initialize some option attributes.
			$checked        = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class          = !empty($option->class) ? ' class="' . $option->class . '"' : '';

            $commets_system = htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8');
            $comments       = JPATH_SITE . '/components/com_'. $commets_system .'/'. $commets_system .'.php';
            if (!file_exists($comments)) {
    			$disabled   = ' disabled="disabled"';
    			$color      = 'color: #999;';
                $tips       = ' <sup class="label label-inverse">'. JText::_('MOD_JUNEWS_NOTINSTALL') .'</sup>';
                $check      = '';
            } else {
                $disabled   = '';
                $color      = '';
                $tips       = '';
                $check      = $checked;
            }

			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

          //  $html[] = '<div style="clear: both;">';

			$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $check . $class . $onclick . $disabled . '/>';

			$html[] = '<label for="' . $this->id . $i . '" id="' . $this->id . $i . '" style="'. $color .'">'
				. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . $tips . '</label>';

            $html[] = '<div style="clear: both;"></div>';
		}

		// End the radio field output.
	   	$html[] = '</fieldset>';

		return implode($html);
	}

	protected function getOptions()
	{
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
