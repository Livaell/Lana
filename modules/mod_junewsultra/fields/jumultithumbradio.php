<?php
/**
* @package Joomla! 2.5
* @version 4.x
* @author 2008-2012 (c)  Denys Nosov (aka Dutch)
* @author web-site: www.joomla-ua.org
* @copyright This module is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License.
**/

defined('JPATH_PLATFORM') or die;

class JFormFieldJUMultiThumbRadio extends JFormField
{
	protected $type = 'JUMultiThumbRadio';

	protected function getInput()
	{
		$html = array();

		$class = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio"';

        $jumultithumb   = JPATH_SITE . '/plugins/content/jumultithumb/jumultithumb.php';
        if (!file_exists($jumultithumb)) {
    	    $disabled   = ' disabled="disabled"';
    		$color      = 'color: #999;';
            $tips       = ' <sup class="label label-inverse">'. JText::_('MOD_JUNEWS_NOTINSTALL') .'</sup>';
        } else {
            $disabled   = '';
            $color      = '';
            $tips       = '';
        }

	   	$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';

		$options = $this->getOptions();

		foreach ($options as $i => $option)
		{
			$checked        = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class          = !empty($option->class) ? ' class="' . $option->class . '"' : '';

			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

			$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . '/>';

			$html[] = '<label for="' . $this->id . $i . '" id="' . $this->id . $i . '" style="'. $color .'">'
				. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';

		}

	   	$html[] = $tips . '</fieldset>';

		return implode($html);
	}

	protected function getOptions()
	{
		$options = array();

		foreach ($this->element->children() as $option)
		{
			if ($option->getName() != 'option')
			{
				continue;
			}

			$tmp = JHtml::_(
				'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
				((string) $option['disabled'] == 'true')
			);

			$tmp->class = (string) $option['class'];

			$tmp->onclick = (string) $option['onclick'];

			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
