<?php
/**
* @package Joomla! 2.5
* @version 4.x
* @author 2008-2012 (c)  Denys Nosov (aka Dutch)
* @author web-site: www.joomla-ua.org
* @copyright This module is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License.
**/

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldUpload extends JFormField
{

	protected $type = 'Upload';

	protected function getInput()
	{
        if(!isset($_GET["id"])){
          echo JText::_('MOD_JUNEWS_NOT_EDIT_TEMPLATE');
          return;
        }

        $version = new JVersion;
        $joomla = substr($version->getShortVersion(), 0, 3);

		JHtml::_('behavior.modal', 'a.modal');

		$html	= array();
		$link	= str_replace('/administrator', '', JURI::base()).'modules/mod_junewsultra/fields/uploadimg.php';

        if($joomla >= '3.0') {

     		$html[] = '<a class="modal btn"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 330, y: 180}}"><i class="icon-upload"></i> '.JText::_('MOD_JUNEWS_IMAGE_UPLOAD').'</a>';

        } else {

     		$html[] = '<div class="button2-left">';
     		$html[] = '  <div class="blank">';
     		$html[] = '	<a class="modal"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 330, y: 180}}">'.JText::_('MOD_JUNEWS_IMAGE_UPLOAD').'</a>';
     		$html[] = '  </div>';
     		$html[] = '</div>';

        }

		return implode("\n", $html);
	}
}