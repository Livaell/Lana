<?php
/**
 * SimpleForm2
 *
 * @package SimpleForm2
 * @author ZyX (allforjoomla.com)
 * @copyright (C) 2010 by ZyX (http://www.allforjoomla.com)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
 * If you fork this to create your own project,
 * please make a reference to allforjoomla.com someplace in your code
 * and provide a link to http://www.allforjoomla.com
 **/
defined('_JEXEC') or die(':)');
require_once ( dirname(__FILE__).'/simpleform2.class.php' );
$id = 'simpleForm2_'.$module->id;
$userFunc = $params->get('userCheckFunc','');
$userResultFunc = $params->get('userResultFunc','');
$okText = addslashes($params->get('okText','OK'));
$config = JFactory::getConfig();
$cache = $params->get('cache',0);
$sysCache = $config->get('caching');
$inclJquery = (int)$params->get('inclJquery',1);
$jqueryNoConflict = (int)$params->get('jqueryNoConflict',0);
$loadScriptsMode = $params->get('loadScriptsMode','body');

if($loadScriptsMode=='body'){
	$cache = 1;
	$sysCache = 1;
}

$script = '';
if(!defined('SIMPLEFORM2')&&$jqueryNoConflict) $script.= 'jQuery.noConflict();';
$script.= 'jQuery(document).ready(function(){jQuery("form#'.$id.'").simpleform({url:"'.JURI::root().'modules/mod_simpleform2/index.php",loaderImg:"'.JURI::root().'modules/mod_simpleform2/images/loading.gif",'.($userFunc!=''?'checkCallBack:'.$userFunc.',':'').($userResultFunc!=''?'resultCallBack:'.$userResultFunc.',':'').'});});';

if($cache==1&&$sysCache==1){
	if(!defined('SIMPLEFORM2')){
		if($inclJquery) echo '<script type="text/javascript" src="'.JURI::root().'modules/mod_simpleform2/ajax/jquery.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.JURI::root().'modules/mod_simpleform2/ajax/jquery.form.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.JURI::root().'modules/mod_simpleform2/ajax/jquery.simpleform.js"></script>'."\n";
	}
	echo '<script type="text/javascript">
	'.$script.'
	</script>';
}
else{
	$doc = JFactory::getDocument();
	if(!defined('SIMPLEFORM2')){
		if($inclJquery) $doc->addScript(JURI::root().'modules/mod_simpleform2/ajax/jquery.js');
		$doc->addScript(JURI::root().'modules/mod_simpleform2/ajax/jquery.form.js');
		$doc->addScript(JURI::root().'modules/mod_simpleform2/ajax/jquery.simpleform.js');
	}
	$doc->addScriptDeclaration($script);
}
defined('SIMPLEFORM2') or define('SIMPLEFORM2',1);

$task = JRequest::getCmd('task');
$post = (array)JRequest::get('post');
$moduleID = (int)JRequest::getInt('moduleID');

$form = new simpleForm2();
$form->set('id',$id);
$form->set('moduleID',$module->id);
$form->set('_key',$params->get('domainKey',''));
$form->parse($params->get('simpleCode',''));

if($task=='sendForm'&&count($post)>0&&$moduleID==(int)$module->id){
	$form->set('defaultError',JText::_('Enter value for'));
	$result = $form->processRequest($post);
	if($result!==false){
		$ok = $form->sendEmail($result,$params);
		if($ok){
			echo $params->get('okText',JText::_('Form succeed'));
			return true;
		}
		else echo '<script type="text/javascript">jQuery(document).ready(function(){alert("'.$form->getError().'");});</script>';
	}
	else{
		echo '<script type="text/javascript">jQuery(document).ready(function(){alert("'.$form->getError().'");});</script>';
	}
}

$form->render();
