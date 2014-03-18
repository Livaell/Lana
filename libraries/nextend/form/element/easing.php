<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.list');

class NextendElementEasing extends NextendElementList {
    
    function fetchElement() {

        $easings = array(
            "dojo.fx.easing.linear" => "Linear",
            "dojo.fx.easing.quadIn" => "Quad In",
            "dojo.fx.easing.quadOut" => "Quad Out",
            "dojo.fx.easing.quadInOut" => "Quad In Out",
            "dojo.fx.easing.cubicIn" => "Cubic In",
            "dojo.fx.easing.cubicOut" => "Cubic Out",
            "dojo.fx.easing.cubicInOut" => "Cubic In Out",
            "dojo.fx.easing.quartIn" => "Quart In",
            "dojo.fx.easing.quartOut" => "Quart Out",
            "dojo.fx.easing.quartInOut" => "Quart In Out",
            "dojo.fx.easing.quintIn" => "Quint In",
            "dojo.fx.easing.quintOut" => "Quint Out",
            "dojo.fx.easing.quintInOut" => "Quint In Out",
            "dojo.fx.easing.sineIn" => "Sine In",
            "dojo.fx.easing.sineOut" => "Sine Out",
            "dojo.fx.easing.sineInOut" => "Sine In Out",
            "dojo.fx.easing.expoIn" => "Expo In",
            "dojo.fx.easing.expoOut" => "Expo Out",
            "dojo.fx.easing.expoInOut" => "Expo In Out",
            "dojo.fx.easing.circIn" => "Circ In",
            "dojo.fx.easing.circOut" => "Circ Out",
            "dojo.fx.easing.circInOut" => "Circ In Out",
            "dojo.fx.easing.backIn" => "Back In",
            "dojo.fx.easing.backOut" => "Back Out",
            "dojo.fx.easing.backInOut" => "Back In Out",
            "dojo.fx.easing.bounceIn" => "Bounce In",
            "dojo.fx.easing.bounceOut" => "Bounce Out",
            "dojo.fx.easing.bounceInOut" => "Bounce In Out"
        );
        foreach($easings as $k => $easing) {
            $this->_xml->addChild('option', ucfirst($easing))->addAttribute('value', $k);
        }
        return parent::fetchElement();
    }
}
