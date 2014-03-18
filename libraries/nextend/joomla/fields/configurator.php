<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
defined('JPATH_PLATFORM') or die;

class JFormFieldConfigurator extends JFormField {
    
    protected $type = 'Configurator';
    
    protected function getInput() {
        $html = '';
        jimport('nextend.library');
        nextendimport('nextend.css.css');
        nextendimport('nextend.javascript.javascript');
        
        $css = NextendCss::getInstance();
        $js = NextendJavascript::getInstance();
        
        $css->addCssLibraryFile('common.css');
        $css->addCssLibraryFile('window.css');
        $css->addCssLibraryFile('configurator.css');
        
        $attribs = $this->element->attributes();
        
        $configurationXmlFile = JPATH_SITE.(string)$attribs->xml;
        
        if(NextendFilesystem::fileexists($configurationXmlFile)){
            
            $js->loadLibrary('dojo');
            
            $js->addLibraryJsLibraryFile('dojo', 'dojo/window.js');
            $js->addLibraryJsAssetsFile('dojo', 'window.js');
            $js->addLibraryJs('dojo', '
                new NextendWindow({
                  button: dojo.byId("nextend-configurator-button"),
                  node: dojo.byId("nextend-configurator-lightbox"),
                  save: dojo.byId("nextend-configurator-save"),
                  message: dojo.byId("nextend-configurator-message"),
                  onHide: function(){
                    this.message.innerHTML = "Now you should save the module settings to apply changes!";
                  }
                });
            ');
            $html.= '<div id="nextend-configurator-lightbox" class="gk_hack nextend-window '.(string)$attribs->identifier.'">';
            $html.= '<div class="gk_hack nextend-window-container">';
            $html.= '<div class="gk_hack nextend-topbar"><div class="gk_hack nextend-topbar-logo"></div>';
            
            $manual = (string)$attribs->manual;
            if($manual != ""){
              $html.= '<a href="'.$manual.'" target="_blank" class="gk_hack nextend-topbar-button nextend-topbar-manual">Manual</a>';
            }
            
            $support = (string)$attribs->support;
            if($support != ""){
              $html.= '<a href="'.$support.'" target="_blank" class="gk_hack nextend-topbar-button nextend-topbar-support">Support</a>';
            }
            $html.= '<div id="nextend-configurator-save" class="nextend-window-save"><div class="NextendWindowSave">APPLY</div></div>';
            $html.= '</div>';
            
            
            $html.= '<div class="gk_hack nextend-window-container-inner">';
            
            $html.= '<fieldset id="nextend-configurator-panels" class="gk_hack panelform">';
            $html.= '<div id="menu-pane" class="gk_hack pane-sliders">';
            
            nextendimport('nextend.form.form');
            $form = new NextendForm();
            $form->loadArray($this->value);
            
            $form->set('manual', $manual);
            $form->set('support', $support);
            
            $form->loadXMLFile($configurationXmlFile);
            
            ob_start();
            $form->render($this->name);
            
            $html.= ob_get_clean();
            
            $html.= '</div>';
            $html.= '</fieldset>';
            $html.= '</div>';
            
            $html.= '</div>';
            $html.= '</div>';
            
            $html.= '<a id="nextend-configurator-button" class="nextend-configurator-button" href="#">Configure<span></span></a>
                      <span id="nextend-configurator-message">&nbsp;</span>';

            $js->addLibraryJsAssetsFile('dojo', 'form.js');
            $js->addLibraryJs('dojo', '
                new NextendForm({
                  container: "nextend-configurator-lightbox",
                  data: '.json_encode($form->_data).',
                  xml: "'.NextendFilesystem::toLinux(NextendFilesystem::pathToRelativePath($configurationXmlFile)).'",
                  control_name: "'.$this->name.'",
                  url: "'.JUri::current().'",
                  loadedJSS: '.json_encode($js->generateArrayJs()).',
                  loadedCSS: '.json_encode($css->generateArrayCSS()).'
                });
            ', true);
            return $html;
        }else{
            return NextendText::_("Not found xml configuration: ").$configurationXmlFile;
        }
    }
}
