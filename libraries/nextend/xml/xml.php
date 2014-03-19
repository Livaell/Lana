<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

function NextendXmlGetAttribute(&$xml, $attribute) {

    if (isset($xml[$attribute])) return (string)$xml[$attribute];
    return '';
}
