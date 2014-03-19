<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.list');

class NextendElementJoomlamenu extends NextendElementList{
    
    function fetchElement() {
        
        if(version_compare(JVERSION,'1.6.0','ge')) {
            require_once( JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php' );
        } else {
            require_once( JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_menus'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php' );
        }
        $options = MenusHelper::getMenuTypes();
    
        for($i = 0; $i < count($options); $i++){
            $this->_xml->addChild('option', htmlspecialchars(ucfirst($options[$i])))->addAttribute('value', $options[$i]);
        }
        
        $this->_value = $this->_form->get($this->_name, $this->_default);
        
        $html = parent::fetchElement();
        
        return $html;
    }
    
}