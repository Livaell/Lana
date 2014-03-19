<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.list');

class NextendElementZooApps extends NextendElementList{
    
    function fetchElement() {
        foreach($this->parent->apps AS $app){
            $this->_xml->addChild('option', htmlspecialchars(ucfirst($app->name)))->addAttribute('value', $app->id);
        }
        
        $this->_value = $this->_form->get($this->_name, $this->_default);
        
        $html = parent::fetchElement();
        
        return $html;
    }
    
}