<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.3.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2014 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
jimport('joomla.plugin.plugin');
class plgSystemHikashopuser extends JPlugin{

	function plgSystemHikashopuser(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('system', 'hikashopuser');
			if(version_compare(JVERSION,'2.5','<')){
				jimport('joomla.html.parameter');
				$this->params = new JParameter($plugin->params);
			} else {
				$this->params = new JRegistry($plugin->params);
			}
		}
		$app = JFactory::getApplication();
		$this->cart = $app->getUserState('com_hikashop.cart_id');
		$this->wishlist = $app->getUserState('com_hikashop.wishlist_id');
		$this->currency = $app->getUserState('com_hikashop.currency_id');
		$this->entries = $app->getUserState('com_hikashop.entries_fields');
		$this->checkout_fields_ok = $app->getUserState( 'com_hikashop.checkout_fields_ok',0);
		$this->checkout_fields = $app->getUserState( 'com_hikashop.checkout_fields');
	}

	function onUserBeforeSave($user, $isnew, $new){
		return $this->onBeforeStoreUser($user, $isnew);
	}
	function onUserAfterSave($user, $isnew, $success, $msg){
		return $this->onAfterStoreUser($user, $isnew, $success, $msg);
	}
	function onUserAfterDelete($user, $success, $msg){
		return $this->onAfterDeleteUser($user, $success, $msg);
	}
	function onUserLogin($user, $options){
		return $this->onLoginUser($user, $options);
	}

	function onBeforeStoreUser($user, $isnew){
		$this->oldUser = $user;
		return true;
	}
	function onAfterStoreUser($user, $isnew, $success, $msg){
		if($success===false || !is_array($user)) return false;
		if(!defined('DS'))
			define('DS', DIRECTORY_SEPARATOR);
		if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')) return true;
		$userClass = hikashop_get('class.user');
		$hikaUser = new stdClass();
		$hikaUser->user_email = trim(strip_tags($user['email']));
		$hikaUser->user_cms_id = (int)$user['id'];
		if(!empty($hikaUser->user_cms_id)){
			$hikaUser->user_id = $userClass->getID($hikaUser->user_cms_id,'cms');
		}
		if(empty($hikaUser->user_id) && !empty($hikaUser->user_email)){
			$hikaUser->user_id = $userClass->getID($hikaUser->user_email,'email');
		}
		$formData = JRequest::getVar('data', array(), '', 'array');
		if(!empty($formData)) {
			$oldUser = $userClass->get($hikaUser->user_id);
			$fieldsClass = hikashop_get('class.field');
			$element = $fieldsClass->getInput('user', $oldUser);
			if(!empty($element)){
				foreach($element as $key => $value) {
					$hikaUser->$key = $value;
				}
			}
		}
		$userClass->save($hikaUser,true);
		return true;
	}
	function onAfterDeleteUser($user, $success, $msg){
		if($success===false || !is_array($user)) return false;
		if(!defined('DS'))
			define('DS', DIRECTORY_SEPARATOR);
		if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')) return true;
		$userClass = hikashop_get('class.user');
		$user_id = $userClass->getID($user['email'],'email');
		if(!empty($user_id)){
			$userClass->delete($user_id,true);
		}
		return true;
	}

	function restoreSession(){
		$app = JFactory::getApplication();
			$cart = $app->getUserState('com_hikashop.cart_id');
		if(empty($cart) && !empty($this->cart)){
			$app->setUserState('com_hikashop.cart_id',$this->cart);
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')) return true;
			$cartClass = hikashop_get('class.cart');
			$cartClass->initCart();
		}
		$wishlist = $app->getUserState('com_hikashop.wishlist_id');
		if(empty($wishlist) && !empty($this->wishlist)){
			$app->setUserState('com_hikashop.wishlist_id',$this->wishlist);
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')) return true;
			$cartClass = hikashop_get('class.cart');
			$cart_type = JRequest::getString('cart_type','cart');
			JRequest::setVar('cart_type','wishlist');
			$cartClass->initCart();
			JRequest::setVar('cart_type',$cart_type);
		}
		$entries = $app->getUserState('com_hikashop.entries_fields');
		if(empty($entries) && !empty($this->entries)){
			$app->setUserState('com_hikashop.entries_fields',$this->entries);
		}
		$currency = $app->getUserState('com_hikashop.currency_id');
		if(empty($currency) && !empty($this->currency)){
			$app->setUserState('com_hikashop.currency_id',$this->currency);
		}
		$checkout_fields_ok = $app->getUserState('com_hikashop.checkout_fields_ok');
		if(empty($checkout_fields_ok) && !empty($this->checkout_fields_ok)){
			$app->setUserState('com_hikashop.checkout_fields_ok',$this->checkout_fields_ok);
		}
		$checkout_fields = $app->getUserState('com_hikashop.checkout_fields');
		if(empty($checkout_fields) && !empty($this->checkout_fields)){
			$app->setUserState('com_hikashop.checkout_fields',$this->checkout_fields);
		}
		if(!empty($this->checkout_fields)){
			foreach($this->checkout_fields as $k =>$v){
				if(!isset($_REQUEST['data']['order'][$k])){
					$_POST['data']['order'][$k] = $_REQUEST['data']['order'][$k] = $v;
				}
			}
		}
	}
	function onLoginUser($user, $options){

		$app = JFactory::getApplication();

		if($app->isAdmin()) return true;

		$this->restoreSession();

		if(empty($user['id'])){
			if(!empty($user['username'])){
				jimport('joomla.user.helper');
				$instance = new JUser();
				if($id = intval(JUserHelper::getUserId($user['username'])))  {
					$instance->load($id);
				}
				if ($instance->get('block') == 0) {
					$user_id=$instance->id;
				}
			}
		}else{
			$user_id = $user['id'];
		}

		if(!empty($user_id)){
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')) return true;
			$userClass = hikashop_get('class.user');
			$hika_user_id = $userClass->getID($user_id,'cms');
			if(!empty($hika_user_id)){
				$addressClass = hikashop_get('class.address');
				$addresses = $addressClass->getByUser($hika_user_id);
				if(!empty($addresses) && count($addresses)){
					$address = reset($addresses);
					$field = 'address_country';
					if(!empty($address->address_state)){
						$field = 'address_state';
					}
					$app->setUserState( HIKASHOP_COMPONENT.'.shipping_address', $address->address_id );
					$app->setUserState( HIKASHOP_COMPONENT.'.billing_address', $address->address_id );
					$zoneClass = hikashop_get('class.zone');
						$zone = $zoneClass->get($address->$field);
					if(!empty($zone)){
						$zone_id = $zone->zone_id;
						$app->setUserState( HIKASHOP_COMPONENT.'.zone_id', $zone->zone_id );
					}
				}

			}
		}

	}

	function onUserLogout($user){
		return $this->onLogoutUser($user);
	}

	function onLogoutUser($user){
		$options=null;
		return $this->onLoginUser($user, $options);
	}

	function onAfterRender(){

		$display = $this->params->get('fields_on_user_profile');

		if(empty($display) || $display=='0') return;

		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		$layout = JRequest::getCmd('layout');

		if (($option == 'com_user' && $view == 'user' && $task == 'edit') || ($option == 'com_users' && $view == 'profile' && $layout == 'edit')) {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php')) return true;
			$body = JResponse::getBody();
			if(strpos($body,'class="form-validate')!==false){

				$user = hikashop_loadUser(true);
				$fieldsClass = hikashop_get('class.field');
				$extraFields = array(
					'user' => $fieldsClass->getFields('frontcomp',$user,'user')
				);
				if(!empty($extraFields['user'])){
					$null = array();
					$fieldsClass->addJS($null,$null,$null);
					$fieldsClass->jsToggle($extraFields['user'],$user,0);

					$data = '';
					if(version_compare(JVERSION,'1.6.0','<')) {
						$data .= '<style type="text/css">'."\r\n".
								'fieldset.hikashop_user_edit { border: 1px solid rgb(204, 204, 204); margin: 10px 0 15px; padding: 0px 10px 0px 10px; }'."\r\n".
								'.hikashop_user_edit legend { font-size: 1em; font-weight: bold; }'."\r\n".
								'.hikashop_user_edit dt { padding: 5px 5px 5px 0px; width: 13em; clear:left; float:left; }'."\r\n".
								'.hikashop_user_edit dd { margin-left: 14em; }'."\r\n".
								'</style>';
					}
					if(HIKASHOP_J30){
						$data .= '<fieldset class="hikashop_user_edit"><legend>'.JText::_('HIKASHOP_USER_DETAILS').'</legend><dl>';
					}else{
						$data .= '<fieldset class="hikashop_user_edit"><legend>'.JText::_('HIKASHOP_USER_DETAILS').'</legend>';
					}
					foreach($extraFields['user'] as $fieldName => $oneExtraField) {
						if(HIKASHOP_J30){
							$data .= '<div class="control-group"><div class="control-label"><label>'.$fieldsClass->getFieldName($oneExtraField).'</label></div><div class="controls">';
						}else{
							$data .= '<dt><label>'.$fieldsClass->getFieldName($oneExtraField).'</label></dt><dd>';
						}
						$onWhat='onchange';
						if($oneExtraField->field_type=='radio')
							$onWhat='onclick';
						$data .= $fieldsClass->display($oneExtraField,@$user->$fieldName,'data[user]['.$fieldName.']',false,' '.$onWhat.'="hikashopToggleFields(this.value,\''.$fieldName.'\',\'user\',0);"');
						if(HIKASHOP_J30){
							$data .= '</div></div>';
						}else{
							$data .= '</dd>';
						}
					}
					if(HIKASHOP_J30){
						$data .= '</dl></fieldset>';
					}else{
						$data .= '</fieldset>';
					}

					$body = preg_replace('#(<form[^>]*class="form-validate.*"[^>]*>.*</(fieldset|table)>)#Uis','$1'.$data, $body,1);
					JResponse::setBody($body);
				}
			}
		}
	}
}
