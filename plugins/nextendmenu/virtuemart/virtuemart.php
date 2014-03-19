<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
jimport( 'joomla.plugin.plugin' );

class plgNextendMenuVirtuemart extends JPlugin {
    
    var $_name = 'virtuemart';
    
    function onNextendMenuList(&$list){
    
        if (!class_exists('VmConfig') && NextendFilesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php')) {
            $list[$this->_name] = $this->getPath();
        }else if(NextendFilesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'compat.joomla1.5.php')){
            $list[$this->_name] = $this->getPath();
        }
    }
    
    function getPath(){
        return dirname(__FILE__).DIRECTORY_SEPARATOR.'virtuemart'.DIRECTORY_SEPARATOR;
    }
}