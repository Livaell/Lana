<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.form.element.list');

class NextendElementCobaltmenuitems extends NextendElementList {

    function fetchElement() {

        $db = JFactory::getDBO();
        
        $query = "SELECT id, name FROM #__js_res_sections ORDER BY name ASC";
        
        $db->setQuery($query);
        $options = $db->loadObjectList();

        if (count($options)) {
            foreach ($options AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->name))->addAttribute('value', $option->id);
            }
        }
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $html = parent::fetchElement();
        return $html;
    }

}
