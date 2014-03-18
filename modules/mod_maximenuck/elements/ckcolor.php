<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

class JFormFieldCkcolor extends JFormField {

    protected $type = 'ckcolor';

    protected function getInput() {
        $path = 'modules/mod_maximenuck/elements/jscolor/';
        JHTML::_('script', $path.'jscolor.js');
        
        $styles = ' style="width:150px;'.$this->element['styles'].'"';

        $html = '<div style="display:inline-block;vertical-align:top;margin-top:4px;width:20px;"><img src="' . $this->getPathToElements() . '/images/color.png" style="margin-right:5px;" /></div><input class="'.$this->element['class'].' color {';
        $html.= 'required:false,';  // empty possible
        $html.= 'pickerPosition:\'top\',';    // or left / right / top
        $html.= 'pickerBorder:2,pickerInset:3,';    // or right / top
        $html.= 'hash:true';        // # behind value
        $html.= '}" type="text" id="' . $this->id . '" value="' . $this->value . '" name="' . $this->name . '"'.$styles.' />';
        return $html;
    }

    protected function getPathToElements() {
        $localpath = dirname(__FILE__);
        $rootpath = JPATH_ROOT;
        $httppath = trim(JURI::root(), "/");
        $pathtoimages = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
        return $pathtoimages;
    }

    /**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel()
	{
		$label = '';

		if ($this->hidden)
		{
			return $label;
		}

		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;

		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTip' : '';
		$class = $this->required == true ? $class . ' required' : $class;
		$class = !empty($this->labelClass) ? $class . ' ' . $this->labelClass : $class;

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description))
		{
			$label .= ' title="'
				. htmlspecialchars(
				trim($text, ':') . '::' . ($this->translateDescription ? JText::_($this->description) : $this->description),
				ENT_COMPAT, 'UTF-8'
			) . '"';
		}
        $width = $this->element['labelwidth'] ? $this->element['labelwidth'] : '150px';
        $styles = ' style="min-width:'.$width.';max-width:'.$width.';width:'.$width.';"';
		// Add the label text and closing tag.
		if ($this->required)
		{
			$label .= $styles.'>' . $text . '<span class="star">&#160;*</span></label>';
		}
		else
		{
			$label .= $styles.'>' . $text . '</label>';
		}

		return $label;
	}

}

