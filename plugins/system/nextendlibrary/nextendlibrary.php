<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
jimport('joomla.plugin.plugin');

class plgSystemNextendLibrary extends JPlugin {

    var $compiled;
        
    function plgSystemNextendLibrary(&$subject, $config){
    
        $this->compiled = false;
        parent::__construct($subject, $config);
    }
    
    function onInitNextendLibrary(){
    
        nextendimport('nextend.data.data');
        $this->_data = new NextendData();
        $config = $this->params->toArray();
        if(!isset($config['config'])) $config['config'] = array();
        $this->_data->loadArray(version_compare(JVERSION, '1.6.0', 'l') ? $config : $config['config']);
        $cachetime = $this->_data->get('cachetime', 900);
        if($cachetime != 0){
            setNextend('cachetime', $cachetime);
        }
        $cachepath = '/'.trim($this->_data->get('cachepath', '/media/nextend/cache/'),'/').'/';
        if($cachepath != ''){
            $cachepath = rtrim(JPATH_SITE,DIRECTORY_SEPARATOR).str_replace('/', DIRECTORY_SEPARATOR, $cachepath);
            setNextend('cachepath', $cachepath);
        }
        setNextend('gzip', $this->_data->get('gzip', 0));
        
        if (isset($_GET['nextendclearcache'])) {
            $app = JFactory::getApplication();
            if($app->isAdmin()){
                nextendimport('nextend.uri.uri');
                nextendimport('nextend.filesystem.filesystem');
                nextendimport('nextend.cache.cache');
                $cache = new NextendCache();
                $cache->deleteCacheFolder();
            }
        }
    }
    
    function onBeforeCompileHead() {
    
        $this->compiled = true;
        if(class_exists('NextendCss')){
            $css = NextendCss::getInstance();
            $css->generateCSS();
        }
        if(class_exists('NextendJavascript')){
            $js = NextendJavascript::getInstance();
            $js->generateJs();
        }
    }
    
    function onAfterRender(){
        if($this->compiled === false){
            ob_start();
            if(class_exists('NextendCss')){
                $css = NextendCss::getInstance();
                $css->_echo = true;
                $css->generateCSS();
            }
            if(class_exists('NextendJavascript')){
                $js = NextendJavascript::getInstance();
                $js->_echo = true;
                $js->generateJs();
            }
            $head = ob_get_clean();
            if($head != ''){
              $body = JResponse::getBody();
          		$body = str_replace('<head>', '<head>'.$head, $body);
              JResponse::setBody($body);
            }
        }
    }
    
}

if (isset($_REQUEST['nextendajax'])) {
    jimport('nextend.library');
    jimport('nextend.ajax.ajax');
}
?>