<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.form.form' );

class JFormFieldCkstylesmanager extends JFormField {

    protected $type = 'ckstylesmanager';

    protected function createFields($form, $identifier, $fieldName) {
        echo '<style type="text/css">div#ckpopup_'.$identifier.' {position:absolute;}</style>';
        $fields = $form->getFieldset();
        if (!count($fields)) return false;
        // get the label
        $text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
        $text = JText::_($text);
        
        
        echo '<div id="ckpopup_'.$identifier.'" style="display:none;" class="ckpopup">';
        echo '<div id="ckpopup_'.$identifier.'_title" class="ckpopup_title">'.$text.'</div>';
        echo '<div id="ckpopup_'.$identifier.'_save" class="ckpopup_save ckpopup_button" style="" onclick="javascript:saveStylesCK(this.getParent(), \''.$fieldName.'\', \''.$identifier.'\');">SAVE</div>';
        echo '<div id="ckpopup_'.$identifier.'_cancel" class="ckpopup_cancel ckpopup_button" style="" onclick="javascript:this.getParent().setStyle(\'display\',\'none\');">CANCEL</div>';
        foreach ($fields as $key => $field) {
            echo '<div class="ckpopup_row">';
            echo $form->getLabel(str_replace($identifier."_","",$key), $identifier);
            echo $form->getInput(str_replace($identifier."_","",$key), $identifier);
            echo '</div>';

        }
        echo '</div>';
    }
    
    
    protected function getInput() {

        $identifier = $this->element['identifier'];
        
        $form = new JForm($identifier);
        JForm::addFormPath(JPATH_SITE . '/plugins/system/maximenuckparams/elements/ckstylesmanager');
        // $form->load('test.xml');
        if (!$formexists = $form->loadFile($identifier, false)) {
            echo '<p style="color:red">'.JText::_('Problem loading the file : '.$identifier.'.xml').'</p>';
            return '';
        }
        // $test = $form->getInput('maximenu_colbgcolor333', 'testt'); // fonctionne
        $this->createFields($form, $identifier, $this->name);



        $document = JFactory::getDocument();
        $document->addScriptDeclaration("JURI='" . JURI::root() . "'");
        $path = 'plugins/system/maximenuckparams/elements/ckstylesmanager/';
        JHTML::_('behavior.modal');
        JHTML::_('script', $path.'ckstylesmanager.js');
        JHTML::_('stylesheet', $path.'ckstylesmanager.css');

        $html = '<input name="' . $this->name . '" id="' . $this->name . '" type="hidden" value="' . $this->value . '" />'
                . '<input name="' . $this->name . '_button" id="' . $this->name . '_button" class="ckstylesmanager_button" type="button" value="' . JText::_('MOD_MAXIMENUCK_CKSTYLESEDIT_'.strtoupper($identifier)) . '" onclick="javascript:loadStylesCK($(\'ckpopup_'.$identifier.'\'),$(\''.$this->name.'\'));"/>'
                //.'<input name="ckaddfromfolder" id="ckaddfromfolder" type="button" value="Import from a folder" onclick="javascript:addfromfolderck();"/>'
                //.'<input name="ckstoreslide" id="ckstoreslide" type="button" value="Save" onclick="javascript:storeslideck();"/>'
                ;

        return $html;
    }

    protected function getPathToElements() {
        $localpath = dirname(__FILE__);
        $rootpath = JPATH_ROOT;
        $httppath = trim(JURI::root(), "/");
        $pathtoelements = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
        return $pathtoelements;
    }

    protected function getLabel() {

        return '';
    }

    protected function getArticlesList() {
        $db = & JFactory::getDBO();

        $query = "SELECT id, title FROM #__content WHERE state = 1 LIMIT 2;";
        $db->setQuery($query);
        $row = $db->loadObjectList('id');
        var_dump($row);
        return json_encode($row);
    }

}

