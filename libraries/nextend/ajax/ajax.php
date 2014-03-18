<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendAjax {

    function parseRequest() {
        if (!isset($_REQUEST['mode']))
            return;
        switch ($_REQUEST['mode']) {
            case 'subform':
                $this->subform();
                break;
            default:
                break;
        }
    }

    function subform() {
        $response = array();
        if (!isset($_POST['data'])) {
            echo json_encode(array('error' => 'Post not OK!'));
            exit;
        }
        if (get_magic_quotes_gpc() || nextendIsWordPress()) {
            $_POST['data'] = stripslashes($_POST['data']);
        }
        $data = json_decode($_POST['data'], true);
        $configurationXmlFile = rtrim(NextendFilesystem::getBasePath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $data['xml'];

        if (NextendFilesystem::fileexists($configurationXmlFile)) {
            nextendimport('nextend.css.css');
            nextendimport('nextend.javascript.javascript');
            $css = NextendCSS::getInstance();
            $js = NextendJavascript::getInstance();
            $js->loadLibrary('dojo');

            nextendimport('nextend.form.form');
            $form = new NextendForm();
            $form->loadArray($data['orig']);
            $form->loadArray(array($data['name'] => $data['value']));
            $form->loadXMLFile($configurationXmlFile);

            ob_start();
            $subform = $form->getSubform($data['tab'], $data['name']);
            $subform->initAjax($data['control_name']);
            echo $subform->renderForm();
            echo "<style>";
            echo $css->generateAjaxCSS($data['loadedCSS']);
            echo "</style>";
            $scripts = $js->generateAjaxJs($data['loadedJSS']);

            $html = ob_get_clean();

            $response = array(
                'html' => $html,
                'scripts' => $scripts
            );
        } else {
            $response = array('error' => 'Configuration file not found');
        }

        echo json_encode($response);
        exit;
    }

}

if (isset($_REQUEST['nextendajax'])) {
    $ajax = new NextendAjax();
    $ajax->parseRequest();
}
?>