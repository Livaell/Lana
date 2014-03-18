<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendElementClearcache extends NextendElement {
    
    function fetchElement() {
        $html = '<a href="'.$_SERVER['REQUEST_URI'].'&nextendclearcache=1" class="nextend-button-css nextend-font-export">'.$this->_label.'</a>';
        return $html;
    }
}
