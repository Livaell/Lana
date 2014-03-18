<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
jimport( 'joomla.plugin.plugin' );

class plgNextendMenuthemeBold extends JPlugin {
    
    var $_name = 'bold';
    
    function onNextendMenuThemeList(&$list){
        $list[$this->_name] = $this->getPath();
    }
    
    function getPath(){
        return dirname(__FILE__).DIRECTORY_SEPARATOR.'bold'.DIRECTORY_SEPARATOR;
    }
}