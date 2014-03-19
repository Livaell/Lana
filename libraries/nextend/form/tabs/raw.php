<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

nextendimport('nextend.form.tab');

class NextendTabRaw extends NextendTab {

    function decorateGroupStart() {
        
    }

    function decorateGroupEnd() {

        echo "</div>";
    }

    function decorateElement(&$el, $out, $i) {

        echo $out[1];
    }

}