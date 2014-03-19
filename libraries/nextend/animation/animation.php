<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendAnimation {
    
    function dojoEasingToCSSEasing($easing) {

        switch ($easing) {
            case "dojo.fx.easing.linear":
                return 'linear';
            case "dojo.fx.easing.quadIn":
                return 'cubic-bezier(0.550, 0.085, 0.680, 0.530)';
            case "dojo.fx.easing.quadOut":
                return 'cubic-bezier(0.250, 0.460, 0.450, 0.940)';
            case "dojo.fx.easing.quadInOut":
                return 'cubic-bezier(0.455, 0.030, 0.515, 0.955)';
            case "dojo.fx.easing.cubicIn":
                return 'cubic-bezier(0.550, 0.055, 0.675, 0.190)';
            case "dojo.fx.easing.cubicOut":
                return 'cubic-bezier(0.215, 0.610, 0.355, 1.000)';
            case "dojo.fx.easing.cubicInOut":
                return 'cubic-bezier(0.645, 0.045, 0.355, 1.000)';
            case "dojo.fx.easing.quartIn":
                return 'cubic-bezier(0.895, 0.030, 0.685, 0.220)';
            case "dojo.fx.easing.quartOut":
                return 'cubic-bezier(0.165, 0.840, 0.440, 1.000)';
            case "dojo.fx.easing.quartInOut":
                return 'cubic-bezier(0.770, 0.000, 0.175, 1.000)';
            case "dojo.fx.easing.quintIn":
                return 'cubic-bezier(0.755, 0.050, 0.855, 0.060)';
            case "dojo.fx.easing.quintOut":
                return 'cubic-bezier(0.230, 1.000, 0.320, 1.000)';
            case "dojo.fx.easing.quintInOut":
                return 'cubic-bezier(0.860, 0.000, 0.070, 1.000)';
            case "dojo.fx.easing.sineIn":
                return 'cubic-bezier(0.470, 0.000, 0.745, 0.715)';
            case "dojo.fx.easing.sineOut":
                return 'cubic-bezier(0.390, 0.575, 0.565, 1.000)';
            case "dojo.fx.easing.sineInOut":
                return 'cubic-bezier(0.445, 0.050, 0.550, 0.950)';
            case "dojo.fx.easing.expoIn":
                return 'cubic-bezier(0.950, 0.050, 0.795, 0.035)';
            case "dojo.fx.easing.expoOut":
                return 'cubic-bezier(0.190, 1.000, 0.220, 1.000)';
            case "dojo.fx.easing.expoInOut":
                return 'cubic-bezier(1.000, 0.000, 0.000, 1.000)';
            case "dojo.fx.easing.circIn":
                return 'cubic-bezier(0.600, 0.040, 0.980, 0.335)';
            case "dojo.fx.easing.circOut":
                return 'cubic-bezier(0.075, 0.820, 0.165, 1.000)';
            case "dojo.fx.easing.circInOut":
                return 'cubic-bezier(0.785, 0.135, 0.150, 0.860)';
            case "dojo.fx.easing.backIn":
                return 'cubic-bezier(0.600, -0.280, 0.735, 0.045)';
            case "dojo.fx.easing.backOut":
                return 'cubic-bezier(0.175, 0.885, 0.320, 1.275)';
            case "dojo.fx.easing.backInOut":
                return 'cubic-bezier(0.680, -0.550, 0.265, 1.550)';
            case "dojo.fx.easing.bounceIn":
                return 'ease-in';
            case "dojo.fx.easing.bounceOut":
                return 'ease-out';
            case "dojo.fx.easing.bounceInOut":
                return 'ease';
            default:
                return 'ease-in-out';
        }
    }
}
