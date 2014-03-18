<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.accordionmenu.treebase');

class NextendTreebaseJoomla extends NextendTreebase {

    function initConfig() {
        parent::initConfig();
        $this->loadposition = $this->_data->get('loadposition', 0);

        if ($this->loadposition) {
            nextendimport('nextend.accordionmenu.joomla.loadmodule');
        }
    }

    function filterItemTree(&$item, &$content) {
        if ($this->loadposition == 1) {
            $regex = '/{loadposition\s+(.*?)}/i';
            preg_match($regex, $item->nname, $match);

            // No matches, skip this
            if ($match) {
                $item->p = true;
                $item->nname = str_replace($match[0], '', $item->nname);
                $content = "<div>" . NextendLoadModule::parse($match[1]) . "</div>";
            }
        }
    }

}