<?php
/**
* @package Joomla! 2.5
* @version 4.x
* @author 2008-2012 (c)  Denys Nosov
* @author web-site: www.joomla-ua.org
* @copyright This module is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License.
**/

defined('_JEXEC') or die;

require_once(dirname(__FILE__). '/helper.php');

$list       = modJUNewsUltraHelper::getList($params);
$app        = JFactory::getApplication('site');
$document   = JFactory::getDocument();

$version    = new JVersion;
$joomla     = substr($version->getShortVersion(), 0, 3);

if($params->get('empty_mod', 0) == 1) if(!count($list)) return;

$layoutpath = JModuleHelper::getLayoutPath('mod_junewsultra', $params->def('template') );

if($joomla >= '3.0') {
    if($params->def('jquery') == 1) {
        JHtml::_('jquery.framework');
    }
    if($params->def('bootstrap_js') == 1) {
        JHtml::_('bootstrap.framework');
    }
    if($params->def('bootstrap_css') == 1)
    {
        $lang       = JFactory::getLanguage();
        $direction  = ($lang->isRTL() ? 'rtl' : 'ltr');
        JHtmlBootstrap::loadCss($includeMaincss = true, $direction);
    }
} else {
    if($params->def('jquery') == 1) {
        $document->addScript(JURI::base() .'media/mod_junewsultra/js/jquery-1.8.3.min.js');
    }
    if($params->def('bootstrap_js') == 1) {
        $document->addScript(JURI::base() .'media/mod_junewsultra/js/bootstrap/js/bootstrap.min.js');
    }
    if($params->def('bootstrap_css') == 1)
    {
		$bscss = 'media/mod_junewsultra/js/bootstrap/css/bootstrap.min.css';
        $document->addStylesheet(JURI::base() . $bscss);
    }
}

if($params->get('cssstyle') == 1)
{
    $tpl        = explode(":", $params->def('template'));

    if($tpl[0] == '_') {
        $jtpl   = $app->getTemplate();
    } else {
        $jtpl   = $tpl[0];
    }

	if (is_file(JPATH_SITE . '/modules/mod_junewsultra/tmpl/'. $tpl[1] .'/css/style.css')) {
		$css = 'modules/mod_junewsultra/tmpl/'. $tpl[1] .'/css/style.css';
        $document->addStylesheet(JURI::base() . $css);
	}
	if (is_file(JPATH_SITE . '/templates/'. $jtpl .'/html/mod_junewsultra/'. $tpl[1] .'/css/style.css')) {
		$css = 'templates/'. $jtpl .'/html/mod_junewsultra/'. $tpl[1] .'/css/style.css';
        $document->addStylesheet(JURI::base() . $css);
	}                                                  
}

if( file_exists($layoutpath) )
{
    if ($params->def('all_in') == 1)
    {
        if($params->def('custom_heading') == 1)
        {
            $heading        = trim( $params->get( 'text_all_in' ) );
            $heading_link   = trim( $params->get( 'link_all_in' ) );
        } else {
            $application    = JFactory::getApplication();
            $menu           = $application->getMenu();

            $text_all_in2   = trim( $params->get( 'text_all_in2' ) );
            $heading        = ($text_all_in2 ? $text_all_in2 : JRoute::_($menu->getItem( $params->get('link_menuitem') )->title) );
            $heading_link   = JRoute::_($menu->getItem( $params->get('link_menuitem') )->link .'&amp;Itemid='. $params->get('link_menuitem'));
        }

        if($heading_link) {
            $heading_link   = '<a href="'. $heading_link .'">'. $heading .'</a>';
        } else {
            $heading_link   = $heading;
        }

        $item_heading   = trim( $params->get( 'item_heading' ) );
        $class_all_in   = trim( $params->get( 'class_all_in' ) );
    	$read_all       = '<'. $item_heading . ($class_all_in ? ' class="'.$class_all_in.'"' : '') .'>'. $heading_link .'</'. $item_heading .'>';
    }

    if ($params->def('all_in') == 1 && $params->def('all_in_position') == 0) {
        echo $read_all;
    }

	require($layoutpath);

    if ($params->def('all_in') == 1 && $params->def('all_in_position') == 1) {
        echo $read_all;
    }

    if($params->def('copy', 1) == 1 ) {
    	echo '<span style="clear:both;text-align:right;display:block;line-height:10px;width:100%;font-size:10px;"><a href="http://joomla-ua.org" style="color:#ccc;text-decoration:none;" target="_blank">Joomla! Україна</a></span>';
    }

} else {
    $tpl = explode(":", $params->def('template'));

    echo JText::_("<strong>Template <span style=\"color: green;\">$tpl</span> do is not found!</strong><br />Please, upload new template to <em>modules/mod_junewsultra/tmpl</em> or <em>templates/$tpl[0]/html/mod_junewsultra/</em> folder or select other template from back-end!");
}