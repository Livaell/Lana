<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.form.element.mixed');
nextendimport('nextend.form.element.joomlamenu');
nextendimport('nextend.form.element.joomlamenuitems');


require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_zoo'.DIRECTORY_SEPARATOR.'config.php');


class NextendElementZooAppsWithCategories extends NextendElementMixed {
    function fetchElement() {
        
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/menuwithitems.js');
        
        $html = '';
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $value = explode('|*|', $this->_value);
        
        $zoo = App::getInstance('zoo');
    		$table = $zoo->table->application;
        $this->apps = $table->all(array('order' => 'name'));
        if(!isset($this->apps[$value[0]])){
          $keys = array_keys($this->apps);
          $this->_form->set($this->_name, $keys[0].'|*|0');
        }
        $html.= parent::fetchElement();
        
        $this->groupedList = array();
        foreach($this->apps AS $app){
            $this->groupedList[$app->id] = array();
    				$categories = $app->getCategories(true, null, true);
            if (count($categories)) {
                $this->cats = array();
                foreach ($categories AS $category) {
                    if(!isset($this->cats[$category->parent])) $this->cats[$category->parent] = array();
                    $this->cats[$category->parent][] = $category;
                }
                $this->renderCategory(0, '', $app->id);
            }
        }
        
        $js->addLibraryJs('dojo', '
            new NextendElementMenuWithItems({
              hidden: "' . $this->_id . '",
              options: ' . json_encode($this->groupedList) . ',
              value: "'.$this->_value.'"
            });
        ');
        
        return $html;
    }

    
    function renderCategory($parent, $pre, $appid){
      if(isset($this->cats[$parent])){
          foreach($this->cats[$parent] AS $category){
              $this->groupedList[$appid][] = array($category->id, $pre.$category->name);
              $this->renderCategory($category->id, $pre.' - ', $appid);
          }
      }
    }
}