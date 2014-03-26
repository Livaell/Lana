<?php
/**
* @package Joomla! 2.5
* @version 4.x
* @author 2008-2012 (c)  Denys Nosov (aka Dutch)
* @author web-site: www.joomla-ua.org
* @copyright This module is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License.
**/

defined('JPATH_BASE') or die;

$version    = new JVersion;
$joomla     = substr($version->getShortVersion(), 0, 3);
$document   = JFactory::getDocument();

$adm_url	= str_replace('/administrator', '', JURI::base());
$tmpl	    = $adm_url .'modules/mod_junewsultra/fields/edittemplate.php?file=';

$document->addStyleSheet( $adm_url . 'modules/mod_junewsultra/assets/css/junewsultra.css?v=6' );

$db	= JFactory::getDBO();
$db->setQuery(
	'SELECT params' .
	' FROM #__modules' .
	' WHERE id = '.(int) $_GET["id"]
);
$rows       = $db->loadResult();

$curent_tmp = json_decode($rows, true);
$tmpl_link	= $tmpl. $curent_tmp['template'] .'.php';

if($joomla >= '3.0') {
    $joomla_support = '
            // Social
            $("#jusocial").appendTo(".span6 blockquote h4").show();
			SqueezeBox.assign($$(\'a#donation\'), {
			    parse: \'rel\'
    		});
    ';
} else {
     $joomla_support = '

            //--- Uprade
            // tabs
            $("#module-form .nav-tabs li:nth-child(3) a").append(updatetag);

            // bootstrap
            $("#jform_params_bootstrap_css-lbl, #jform_params_bootstrap_js-lbl").append(updatetag);

            // Social
            $("#jusocial").prependTo(".mod-desc").show();
			SqueezeBox.assign($$(\'a#donation\'), {
			    parse: \'rel\'
    		});
     ';
}

$snipets = '
    jQuery.noConflict();
    (function($) {
        $(function() {
            var newtag      = " <sup class=\"label label-success\">'.JText::_('MOD_JUNEWS_NEW').'</sup>";
            var updatetag   = " <sup class=\"label label-info\">'.JText::_('MOD_JUNEWS_UPD').'</sup>";



            // Change template
            $("#jform_params_template").bind("change", function () {
                var tpl = $(this).val();
                if(tpl) {
                    $("#change_tmp").attr({href: "'. $tmpl .'"+tpl+".php"});
                    $("#change_tmp .edit-template-now").remove();
                    $("#change_tmp").append(" <span style=\"color: green;\" class=\"edit-template-now\">'.JText::_('MOD_JUNEWS_EDIT_TEMPLATE').'</span>");
                }
                return false;
            });
            $("#jform_params_template").css("float","left");
            $("#jform_params_template'. ($joomla >= '3.0' ? '_chzn' : '') .'").after("<a class=\"modal btn\" id=\"change_tmp\" style=\"margin: -17px 0 0 10px; padding: 3px 4px;\" href=\"'. $tmpl_link .'\" rel=\"{handler: \'iframe\', size: {x: 1000, y: 700}}\" title=\"'.JText::_('MOD_JUNEWS_TEMPLATE_BUTTON').'\"><img src=\"'. $adm_url .'modules/mod_junewsultra/assets/gear.png\" alt=\"\" /></a>");
            SqueezeBox.initialize({});
			SqueezeBox.assign($$(\'a#change_tmp\'), {
			    parse: \'rel\'
    		});

            // Placeholder
            $("#jform_params_rmtext").attr({placeholder: "'.JText::_('MOD_JUNEWS_READ_MORE_TITLE').'"});
            $("#jform_params_text_all_in2").attr({placeholder: "'.JText::_('MOD_JUNEWS_ALL_NEWS_TITLE').'"});

'. $joomla_support .'
        });
    })(jQuery);
';

if($joomla >= '3.0') {
	JHtml::_('jquery.framework');
    $document->addScriptDeclaration( $snipets );
    $margindonat = 'padding: 15px 0;';
    $donat = 'margin: -3px 0 0 -15px;';
} else {
    $document->addScript($adm_url . 'media/mod_junewsultra/js/jquery-1.8.3.min.js');
    $document->addCustomTag('<script type="text/javascript">
    '.$snipets.'
</script>');

    $margindonat = 'padding: 5px 0;';
    $donat = 'margin: -8px 0 0 -15px;';

    //JError::raiseNotice( 100, 'Please update your JUNewsUltra Pro templates. Replace <b style-"font-size: 15px!important;">$params->def(\'JC\')</b> to <b style-"font-size: 15px!important;">$params->def(\'use_comments\')</b> for allow display comments information in your modules. Thank you!' );
}

?>
<div id="jusocial" style="<?php echo $margindonat; ?>clear:both;display:none;">
    <div class="fb-like" style="float:left; padding-right:15px;" data-href="http://www.facebook.com/pages/JUNewsUltra/493645320647922" data-send="false" data-layout="button_count" data-width="455" data-show-faces="false"></div>
	<div id="plusone" style="width:75px; float:left;">
		<g:plusone size="medium" href="http://extensions.joomla.org/index.php?option=com_mtree&amp;task=viewlink&amp;link_id=17771" count="true"></g:plusone>
	</div>
	<div id="twitter" style="float:left;">
        <a href="https://twitter.com/share" class="twitter-share-button" data-text="JUNewsUltra Pro - Joomla! Extensions Directory" data-url="http://extensions.joomla.org/extensions/news-display/articles-display/frontend-news/17771">&nbsp;</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</div>
    <div id="donat" style="float:left;<?php echo $donat; ?>">
        <a id="donation" href="<?php echo $adm_url .'modules/mod_junewsultra/fields/donate.php'; ?>" rel="{handler: 'iframe', size: {x: 350, y: 500}}"><img src="<?php echo $adm_url;?>modules/mod_junewsultra/assets/btn_donate.gif" alt="" /></a>
    </div>
</div>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=192653987484860";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>