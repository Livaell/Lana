<?php
/**
 * SimpleForm2
 *
 * @version 1.0.7
 * @package SimpleForm2
 * @author ZyX (allforjoomla.ru)
 * @copyright (C) 2010 by ZyX (http://www.allforjoomla.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
 * If you fork this to create your own project,
 * please make a reference to allforjoomla.ru someplace in your code
 * and provide a link to http://www.allforjoomla.ru
 **/
define('ZYX_START_TIME',microtime());
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$base = dirname(__FILE__);
$base = str_replace(DS.'modules'.DS.'mod_simpleform2','',$base);
define('JPATH_BASE', $base );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$app = JFactory::getApplication('site');
$app->initialise();

$language = JFactory::getLanguage();
$language->load('mod_simpleform2', JPATH_SITE);
$user	= JFactory::getUser();
$task = JRequest::getCmd('task');
if($task=='captcha' || $task=='sendForm'){
	$moduleID = (int)JRequest::getInt('moduleID',0);
	if($moduleID==0) sfEcho('!'.JText::_('Form not found'));
	$module = JTable::getInstance('module');
	$module->load($moduleID);
	if(!$module->id||$module->id!=$moduleID) sfEcho('!'.JText::_('Form not found'));
	
	if(class_exists('JParameter')){
		$params = new JParameter( $module->params );
	}
	else{
		$params = new JRegistry;
		$params->loadString($module->params);
	}
	require_once ( JPATH_BASE .DS.'modules'.DS.'mod_simpleform2'.DS.'simpleform2.class.php' );
	$form = new simpleForm2();
	$form->set('moduleID',$module->id);
	$form->parse($params->get('simpleCode',''));
	require_once(JPATH_BASE.DS.'modules'.DS.'mod_simpleform2'.DS.'kcaptcha'.DS.'kcaptcha.php');
}
switch($task){
	case 'captcha':
		@ob_end_clean();
		$captcha = null;
		foreach($form->elements as $elem){
			if($elem->type=='captcha'){
				$captcha = $elem;
				break;
			}
		}
		if(is_null($captcha)) sfEcho('!'.JText::_('Form has no captcha'));
		$width = ((int)$captcha->width>0?(int)$captcha->width:200);
		$height = ((int)$captcha->height>0?(int)$captcha->height:60);
		$color = ($captcha->color!=''?$captcha->color:null);
		$background = ($captcha->background!=''?$captcha->background:null);
		$captchaObj = new simpleCAPTCHA($width,$height,$color,$background);
		$session = JFactory::getSession();
		$session->set('simpleform2_'.$form->get('moduleID').'.captcha', $captchaObj->getKeyString());
		die();
	break;
	case 'sendForm':
		$form->set('defaultError',JText::_('Enter value for'));
		$post = (array)JRequest::get('post');
		$result = $form->processRequest($post);
		if($result!==false){
			$ok = $form->sendEmail($result,$params);
			if($ok){
				sfEcho('='.$params->get('okText',JText::_('Form succeed')));
			}
			else sfEcho('!'.$form->getError());
		}
		else{
			sfEcho('!'.$form->getError());
		}
	break;
}

function sfEcho($txt){
	header('Content-type: text/html; charset="utf-8"',true);
	echo $txt;
	die();
}
 
