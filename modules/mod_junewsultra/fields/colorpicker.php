<?php
/**
* @package Joomla! 2.5
* @version 4.x
* @author 2008-2012 (c)  Denys Nosov (aka Dutch)
* @author web-site: www.joomla-ua.org
* @copyright This module is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License.
**/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldColorpicker extends JFormField
{
	protected $type = 'Colorpicker';

	protected function getInput()
	{
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$class		= ' class="color-picker"';
		$document   = JFactory::getDocument();

        $adm_url	= str_replace('/administrator', '', JURI::base());

        $document->addStyleSheet( $adm_url . 'modules/mod_junewsultra/assets/js/minicolors/jquery.miniColors.css' );
        $document->addScript($adm_url . 'modules/mod_junewsultra/assets/js/minicolors/jquery.miniColors.min.js');

		$js = '
    (function($) {
        $(function() {
            $(".color-picker").miniColors();
        });
    })(jQuery);
';
        $document->addScriptDeclaration($js);

		return '<input type="text" name="'.$this->name.'" id="'.$this->id.'"' .
				' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"' .
				$class . $size .'/>';
	}
}
