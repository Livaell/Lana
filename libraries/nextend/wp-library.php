<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

global $nextend_head;

$nextend_head = '';

if (!defined('NEXTENDLIBRARY')) {
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library.php');
}

add_action('print_footer_scripts', 'nextend_generate');
function nextend_generate() {
    global $nextend_head;
    ob_start();
    if (class_exists('NextendCss', false) || class_exists('NextendJavascript', false)) {
        $css = NextendCss::getInstance();
        $css->generateCSS();
        $js = NextendJavascript::getInstance();
        $js->generateJs();
    }
    $nextend_head = ob_get_clean();
    return true;
}

function nextend_render_end($buffer){
    global $nextend_head;
    if($nextend_head != ''){
        return preg_replace('/<\/(.*?)head>/', $nextend_head.'</${1}head>', $buffer, 1);
    }
    return $buffer;
}

ob_start("nextend_render_end");
?>
