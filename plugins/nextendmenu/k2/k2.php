<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
jimport( 'joomla.plugin.plugin' );

class plgNextendMenuK2 extends JPlugin {
    
    var $_name = 'k2';
    
    function onNextendMenuList(&$list){
        if(NextendFilesystem::existsFolder(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_k2')){
            $list[$this->_name] = $this->getPath();
        }
    }
    
    function getPath(){
        return dirname(__FILE__).DIRECTORY_SEPARATOR.'k2'.DIRECTORY_SEPARATOR;
    }
}