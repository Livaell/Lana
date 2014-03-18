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
defined('_JEXEC') or die(':)');

class simpleForm2 extends JObject{
	var $code = '';
	var $elements = array();
	var $attachments = array();
	var $id = null;
	var $_key = '';
	var $hasCaptcha = false;
	var $hasSubmit = false;
	var $side = 'backend';
	var $moduleID = null;
	var $template = 'default';
	var $defaultError = '%s';
	
	function simpleForm2($simpleCode=''){
		if($simpleCode!='') return $this->parse($simpleCode);
		else return true;
	}
	
	function parse($code){
		$this->code = $code;
		$paramNames = array('regex','label','error','onclick','onchange','value','type','class','required','multiple','width','height','extensions','maxsize','color','background','placeholder');
		$optionParamNames = array('label','value','selected','onclick','onchange');
		$params2mask = array('regex','label','error','onclick','onchange','value','placeholder');
		foreach($params2mask as $param2mask){
			$this->code = preg_replace("/({[^}]+)(".$param2mask.")\=[\'\"](.*?)(?=[\'\"] )[\'\"]/sie",'"\\1\\2=\"".base64_encode("\\3")."\""',$this->code);
		}
		preg_match_all("/{element (.*?)(?=[\/ \'\"]})(?:[ \'\"]}(.*?)(?={\/element}))?/is",$this->code,$matches);
		if(!is_array($matches[1])||count($matches[1])==0){
			$this->setError(JText::_('No elements found in code'));
			return false;
		}
		foreach($matches[1] as $key=>$paramsText){
			//$name = md5(serialize($paramsText)).$key;
			$elem = new simpleForm2Element();
			$elem->code = $matches[0][$key];
			preg_match_all("/(".implode('|',$paramNames).")=[\'\"]([^\'\"]+)/is",$paramsText,$matchesP);
			if(!is_array($matchesP[1])||count($matchesP[1])==0){
				$this->setError(JText::_('Element without parameters found'));
				return false;
			}
			foreach($matchesP[1] as $keyP=>$paramName){
				if(in_array($paramName,$paramNames)){
					$elem->$paramName = $matchesP[2][$keyP];
					if(in_array($paramName,$params2mask)) $elem->$paramName = base64_decode($elem->$paramName);
					if($paramName=='label'){
						$elem->name = $elem->id = 'sf2_'.$this->get('moduleID').'_'.$this->toTranslit($elem->$paramName);
					}
				}
			}
			if(is_null($elem->id)) $elem->name = $elem->id = md5(serialize($paramsText)).$key;
			$elem->required = (bool)($elem->required=='required');
			$elem->multiple = (bool)($elem->multiple=='multiple');
			if(isset($elem->value)) $elem->values[] = $elem->value;
			preg_match_all("/{option (.*?)(?=})/is",$matches[2][$key],$matchesO);
			if(is_array($matchesO[1])&&count($matchesO[1])>0){
				$paramsText = null;
				foreach($matchesO[1] as $keyO=>$paramsText){
					preg_match_all("/(".implode('|',$optionParamNames).")=[\'\"]([^\'\"]+)/is",$paramsText,$matchesOP);
					if(is_array($matchesOP[1])&&count($matchesOP[1])>0){
						$option = new stdclass;
						foreach($matchesOP[1] as $keyP=>$paramName){
							if(in_array($paramName,$optionParamNames)){
								$option->$paramName = $matchesOP[2][$keyP];
								if(in_array($paramName,$params2mask)) $option->$paramName = base64_decode($option->$paramName);
								$option->selected = (bool)(@$option->selected=='selected');
							}
						}
						$option->code = $matchesO[0][$keyO].'}';
						$elem->values[] = $option->value;
						$elem->options[] = $option;
					}
				}
				$elem->code.= '{/element}';
			}
			else $elem->code.= '/}';
			if($elem->type=='captcha'){
				if(!isset($elem->color)||!preg_match("/\#?[0-9ABCDEFabcdef]{6}/",$elem->color)) $elem->color = '';
				if(!isset($elem->background)||!preg_match("/\#?[0-9ABCDEFabcdef]{6}/",$elem->background)) $elem->background = '';
				$elem->required = true;
				$session = JFactory::getSession();
				$elem->values[] = $session->get('simpleform2_'.$this->get('moduleID').'.captcha', null);
				if($this->hasCaptcha) $elem = null;
				$this->hasCaptcha = true;
			}
			else if($elem->type=='submit'){
				if($this->hasSubmit) $elem = null;
				$this->hasSubmit = true;
			}
			else if($elem->type=='file'){
				$exts = array();
				if(@$elem->extensions!=''){
					$tmpExts = explode(',',$elem->extensions);
					if(is_array($tmpExts)&&count($tmpExts)>0){
						foreach($tmpExts as $tmpExt){
							$tmpExt = trim($tmpExt);
							if(preg_match('/^[a-zA-Z0-9]{2,4}$/',$tmpExt)) $exts[] = $tmpExt;
						}
					}
				}
				$elem->extensions = $exts;
				$maxSize = 0;
				if(@$elem->maxsize!=''){
					$measure = strtolower(substr($elem->maxsize,-2));
					$size = (int)substr($elem->maxsize,0,-2);
					if($size>0&&($measure=='kb'||$measure=='mb')){
						if($measure=='mb') $maxSize = $size*1024*1024;
						else $maxSize = $size*1024;
					}
				}
				$elem->maxsize = $maxSize;
			}
			if($elem) $this->elements[] = $elem;
		}
		return true;
	}
		
	function render(){
		if(count($this->elements)==0) return false;
		$id = $this->id;
		$code = $this->code;
		$form = '';
		$uri = JURI::getInstance();
		$formBegin = '<form method="post" id="'.$id.'" name="'.$id.'" enctype="multipart/form-data" class="simpleForm">';
		$formBegin.= '<input type="hidden" name="moduleID" value="'.$this->moduleID.'" />';
		$formBegin.= '<input type="hidden" name="task" value="sendForm" />';
		$formBegin.= '<input type="hidden" name="Itemid" value="'.JRequest::getInt( 'Itemid').'" />';
		$formBegin.= '<input type="hidden" name="url" value="'.$uri->toString().'" />';
		$formEnd = '</form>'."\n";
		foreach($this->elements as $elem){
			$code = preg_replace('`'.preg_quote($elem->code,'`').'`', $this->renderElement($elem), $code, 1);
		}
		if(!preg_match('/\{form\}/i',$code)) $code = '{form}'.$code;
		if(!preg_match('/\{\/form\}/i',$code)) $code.= '{/form}';
		$code = str_replace(array('{form}','{/form}'),array($formBegin,$formEnd),$code);
		$code.= ($this->checkDomain()?'':base64_decode('PGRpdiBzdHlsZT0iYm9yZGVyLXRvcDoxcHggc29saWQgI2NjYzt0ZXh0LWFsaWduOnJpZ2h0OyI+PGEgdGFyZ2V0PSJfYmxhbmsiIHRpdGxlPSJzaW1wbGVGb3JtMiIgaHJlZj0iaHR0cDovL3d3dy5hbGxmb3Jqb29tbGEucnUiIHN0eWxlPSJ2aXNpYmlsaXR5OnZpc2libGU7ZGlzcGxheTppbmxpbmU7Y29sb3I6I2NjYzsiPnNpbXBsZUZvcm0yPC9hPjwvZGl2Pg=='));
		echo $code;
	}
	
	function processRequest($request){
		if(count($this->elements)==0){
			$this->setError(JText::_('No elements found in code'));
			return false;
		}
		$result = '';
		foreach($this->elements as $elem){
			if($elem->check($this,$request)!==true){
				$error = $elem->getError();
				$this->setError(($error?$error:sprintf($this->defaultError,$elem->label)));
				return false;
			}
			if(count($elem->requests)) $result.= $this->getTemplate('mail_form_item',array('label'=>$elem->label,'value'=>implode(', ',$elem->requests)));
		}
		return $result;
	}
	
	function renderElement($elem){
		$result = $elem->code;
		$result = preg_replace("/{\/?element(.*?)(?=})}/i",'',$result);
		$name = $elem->name;
		$id = $elem->id;
		$class = @$elem->class;
		$default = @$elem->value;
		$placeholder = @$elem->placeholder;
		$label = '';
		if($elem->label!='') $label = '<label for="'.$elem->id.'">'.$elem->label.($elem->required?' <span>*</span>':'').'</label> ';
		switch($elem->type){
			case 'text':
				$onchange = @$elem->onchange;
				if(count($elem->requests)) $default = $elem->requests[0];
				$attribs = array();
				$attribs[] = 'name="'.$name.'"';
				$attribs[] = 'id="'.$id.'"';
				if($class) $attribs[] = 'class="'.$class.'"';
				if($onchange) $attribs[] = 'onchange="'.$onchange.'"';
				if($placeholder) $attribs[] = 'placeholder="'.$placeholder.'"';
				$result.= '<input type="text" '.implode(' ',$attribs).' value="'.htmlspecialchars($default).'" />';
			break;
			case 'textarea':
				$onchange = @$elem->onchange;
				if(count($elem->requests)) $default = $elem->requests[0];
				$attribs = array();
				$attribs[] = 'name="'.$name.'"';
				$attribs[] = 'id="'.$id.'"';
				if($class) $attribs[] = 'class="'.$class.'"';
				if($onchange) $attribs[] = 'onchange="'.$onchange.'"';
				if($placeholder) $attribs[] = 'placeholder="'.$placeholder.'"';
				$result.= '<textarea '.implode(' ',$attribs).' >'.htmlspecialchars($default).'</textarea>';
			break;
			case 'select':
				$multi = @$elem->multiple;
				$onchange = @$elem->onchange;
				$result = '<select'.($multi?' multiple="multiple"':'').' name="'.$name.($multi?'[]':'').'" id="'.$id.'"'.($class?' class="'.$class.'"':'').($onchange?' onchange="'.$onchange.'"':'').'>'.$result;
				foreach($elem->options as $option){
					$sel = '';
					if($option->selected || (count($elem->requests)&&in_array($option->value,$elem->requests))) $sel = ' selected="selected"';
					$optionCode = '<option value="'.$option->value.'"'.$sel.'>'.$option->label.'</option>';
					$result = str_replace($option->code,$optionCode,$result);
				}
				$result.= '</select>';
			break;
			case 'radio':
				foreach($elem->options as $option){
					$id = md5($name.'_'.$option->label);
					$onclick = @$option->onclick;
					$sel = '';
					if($option->selected || (count($elem->requests)&&in_array($option->value,$elem->requests))) $sel = ' checked="checked"';
					$optionCode = '<input type="radio" name="'.$name.'" id="'.$id.'" value="'.$option->value.'"'.($class?' class="'.$class.'"':'').($onclick?' onclick="'.$onclick.'"':'').$sel.' /><label for="'.$id.'">'.$option->label.'</label>';
					$result = str_replace($option->code,$optionCode,$result);
				}
			break;
			case 'button':
				$default = @$elem->value;
				$onclick = @$elem->onclick;
				$result.= '<input type="button"'.($class?' class="'.$class.'"':'').($onclick?' onclick="'.$onclick.'"':'').' value="'.$default.'" />';
			break;
			case 'submit':
				$default = @$elem->value;
				$result.= '<input'.($class?' class="'.$class.'"':'').' type="submit" value="'.$default.'" />';
			break;
			case 'reset':
				$default = @$elem->value;
				$onclick = @$elem->onclick;
				$result.= '<input type="reset"'.($name?' name="'.$name.'"':'').($class?' class="'.$class.'"':'').($onclick?' onclick="'.$onclick.'"':'').' value="'.$default.'" />';
			break;
			case 'checkbox':
				$default = @$elem->value;
				$single = false;
				if(count($elem->options)==0){
					$elem->options = array($elem);
					$single = true;
				}
				foreach($elem->options as $option){
					$elid = $id;
					if(!$single){
						$elid = md5($name.'_'.$option->label);
						$default = @$option->value;
					}
					$onclick = @$option->onclick;
					$sel = '';
					if($option->selected || (count($elem->requests)&&in_array($option->value,$elem->requests))) $sel = ' checked="checked"';
					$optionCode = '<input type="checkbox" name="'.$name.(!$single?'[]':'').'" id="'.$elid.'"'.($class?' class="'.$class.'"':'').($onclick?' onclick="'.$onclick.'"':'').$sel.' value="'.$default.'" />';
					if($single) $result.= $optionCode;
					else{
						$optionCode.= ' <label for="'.$elid.'">'.$option->label.'</label>';
						$result = str_replace($option->code,$optionCode,$result);
					}
				}
			break;
			case 'captcha':
				$user = JFactory::getUser();
				if((int)$user->get('id')>0){
					$result = '';
					$label = '';
				}
				else{
					$default = @$elem->value;
					$urlAdd = array();
					$urlAdd[] = 'moduleID='.$this->moduleID;
					$urlAdd[] = 'rand='.rand(1,99999);
					$onclick = 'this.src=\''.JURI::root().'modules/mod_simpleform2/index.php?task=captcha'.(count($urlAdd)?'&'.implode('&',$urlAdd):'').'&rand=\'+Math.random();';
					
					$attribs = array();
					$attribs[] = 'name="'.$name.'"';
					$attribs[] = 'id="'.$id.'"';
					if($class) $attribs[] = 'class="'.$class.'"';
					if($placeholder) $attribs[] = 'placeholder="'.$placeholder.'"';
					
					$result.= '<img class="sf2Captcha" src="'.JURI::root().'modules/mod_simpleform2/index.php?task=captcha'.(count($urlAdd)?'&'.implode('&',$urlAdd):'').'" alt="'.JText::_('Click to refresh').'" title="'.JText::_('Click to refresh').'" onclick="'.$onclick.'"'.' style="cursor:pointer;" />
					<div><input type="text" '.implode(' ',$attribs).' value="'.$default.'" /></div>';
				}
			break;
			case 'file':
				$onchange = @$elem->onchange;
				
				$attribs = array();
				$attribs[] = 'name="'.$name.'"';
				$attribs[] = 'id="'.$id.'"';
				if($class) $attribs[] = 'class="'.$class.'"';
				if($onchange) $attribs[] = 'onchange="'.$onchange.'"';
				if($placeholder) $attribs[] = 'placeholder="'.$placeholder.'"';
				
				$result.= '<input type="file" '.implode(' ',$attribs).' />';
			break;
		}
		if($label!='') $result = $label.$result;
		return $result;
	}
		
	function checkDomain(){
		if(!function_exists('bcpowmod')) return true;
		$URI=JURI::getInstance();$keys=explode('|',$this->_key);foreach($keys as $key){$m=str_replace('www.','',$URI->getHost()).':ZyX_SF2';$e=5;$n='159378341817953177';$s=5;$coded='';$max=strlen($m);$packets=ceil($max/$s);for($i=0;$i<$packets;$i++){$packet=substr($m, $i*$s, $s);$code='0';for($j=0; $j<$s; $j++){$code=@bcadd($code, bcmul(ord($packet[$j]), bcpow('256',$j)));}$code=bcpowmod($code, $e, $n);$coded.=$code.' ';}$coded=str_replace(' ','-',trim($coded));if($key==$coded)return true;}return false;
	}
	
	function getUserIp() { 
		if (getenv('REMOTE_ADDR')) $ip = getenv('REMOTE_ADDR'); 
		elseif(getenv('HTTP_X_FORWARDED_FOR')) $ip = getenv('HTTP_X_FORWARDED_FOR'); 
		else $ip = getenv('HTTP_CLIENT_IP');
		return $ip;
	}
	
	function getTemplate($tmpl,$vars){
		global $mainframe;
		jimport('joomla.application.module.helper');
		$path = JModuleHelper::getLayoutPath('mod_simpleform2', $tmpl);
		unset($tmpl);
		unset($tPath);
		unset($bPath);
		extract($vars);
		ob_start();
		include($path);
		$content = ob_get_clean();
		return $content;
	}
	
	function sendEmail($result,$params){

		$mailFrom = $params->get('sfMailForm',null);
		$mailTo = $params->get('sfMailTo',null);
		$subject = $params->get('sfMailSubj','--== SimpleForm2 e-mail ==--');
		$subject = html_entity_decode($subject, ENT_QUOTES);
		$now = JFactory::getDate();
		$url = JURI::root();
		$url = str_replace('modules/mod_simpleform2/','',$url);
		$url = JRequest::getVar('url',$url);
		$date = $now->format('d.m.Y H:i:s');
		$ip = $this->getUserIp();
		$body = $this->getTemplate('mail_form',array('url'=>$url,'date'=>$date,'ip'=>$ip,'rows'=>$result));
		$body = stripslashes(html_entity_decode($body, ENT_QUOTES));
		if(!$mailFrom||!$mailTo){
			$this->setError(JText::_('Form not configured'));
			return false;
		}
		$mail = JFactory::getMailer();
		$mail->setSender(array($mailFrom, $mailFrom));
		$mail->setSubject($subject);
		$mail->setBody($body);
		if(preg_match('~<~',$body)&&preg_match('/>/',$body)) $mail->IsHTML(true);
		$recieps = array();
		$tmpR = explode(',',$mailTo);
		foreach($tmpR as $tmpRr){
			$tmpRr = trim($tmpRr);
			if($tmpRr!='') $recieps[] = $tmpRr;
		}
		if(count($recieps)<1){
			$this->setError(JText::_('Form not configured'));
			return false;
		}
		foreach($recieps as $reciep){
			$mail->addRecipient($reciep);
		}
		$mail->addCC(null);$mail->addBCC(null);
		foreach($this->attachments as $attachment){
			$mail->AddStringAttachment(file_get_contents($attachment->file),$attachment->name);
		}
		ob_start();
		$ok = $mail->Send();
		ob_end_clean();
		if(is_object($ok)){
			$this->setError($ok->getError());
			return false;
		}
		else return true;
	}
	function toTranslit($var){
		$letters = array( 
		'~а~u'=>'a','~б~u'=>'b','~в~u'=>'v','~г~u'=>'g','~д~u'=>'d','~е~u'=>'e','~з~u'=>'z',
		'~и~u'=>'i','~к~u'=>'k','~л~u'=>'l','~м~u'=>'m','~н~u'=>'n','~о~u'=>'o','~п~u'=>'p',
		'~р~u'=>'r','~с~u'=>'s','~т~u'=>'t','~у~u'=>'u','~ф~u'=>'f','~ц~u'=>'c','~ы~u'=>'y',
		"~й~u" => "jj", "~ё~u" => "jo", "~ж~u" => "zh", "~х~u" => "kh", "~ч~u" => "ch", 
		"~ш~u" => "sh", "~щ~u" => "shh", "~э~u" => "je", "~ю~u" => "ju", "~я~u" => "ja",
		"~ъ~u" => "", "~ь~u" => "");
		$var = JString::strtolower(trim(strip_tags($var)));
		$var = preg_replace('~\s+~ms','_',$var);
		$var = preg_replace(array_keys($letters),array_values($letters),$var);
		$var = preg_replace('~[^a-z0-9_\-]+~mi', '', $var);
		return $var;
	}
}

class simpleForm2Element extends JObject{
	var $code = null;
	var $name = null;
	var $id = null;
	var $label = '';
	var $value = null;
	var $values = array();
	var $regex = null;
	var $error = null;
	var $type = null;
	var $requests = array();
	var $options = array();
	var $required = false;
	var $multiple = false;
	
	function simpleForm2Element($name='',$id=''){
		if($name!='') $this->name = $name;
		if($id!='') $this->id = $id;
	}
	
	function check(&$form,$request){
		$checkVal = $this->getParam($request,$this->name,null);
		if(in_array($this->type,array('text','textarea'))){
			$checkVal = trim($checkVal);
			if(($this->required&&$checkVal=='')||($this->regex!=''&&!preg_match($this->regex,$checkVal))){
				$this->setError($this->error);
				return false;
			}
			$this->requests[] = $checkVal;
		}
		else if(in_array($this->type,array('select','radio','checkbox'))){
			if(is_array($checkVal)){
				$has = array_intersect($checkVal,$this->values);
				if($this->required&&count($has)==0||(count($checkVal)>0&&count($has)==0)){
					$this->setError($this->error);
					return false;
				}
				$this->requests = $checkVal;
			}
			else if(is_null($checkVal)){
				$this->requests[] = '';
				if($this->required){
					$this->setError($this->error);
					return false;
				}
			}
			else{
				$checkVal = trim($checkVal);
				if(($this->required&&$checkVal=='')||(count($this->values)>0&&!in_array($checkVal,$this->values))){
					$this->setError($this->error);
					return false;
				}
				$this->requests[] = $checkVal;
			}
		}
		else if(in_array($this->type,array('button','submit','reset'))){
			return true;
		}
		else if($this->type=='captcha'){
			$user = JFactory::getUser();
			if((int)$user->get('id')>0) return true;
			$session = JFactory::getSession();
			$session->set('simpleform2_'.$form->get('moduleID').'.captcha', null);
			$checkVal = trim($checkVal);
			if($checkVal==''||!in_array($checkVal,$this->values)){
				$this->setError($this->error);
				return false;
			}
		}
		else if($this->type=='file'){
			$fileData = $_FILES[$this->name];
			if($this->required&&!is_file($fileData['tmp_name'])){
				$this->setError($this->error);
				return false;
			}
			else if(!is_file($fileData['tmp_name'])) return true;
			if($this->maxsize>0&&$fileData['size']>$this->maxsize){
				$fSize = round($fileData['size']/1024,2);
				$error = sprintf(JText::_('File size is too big'),$fileData['name'].' ('.$fSize.'Kb)',round($this->maxsize/1024,2).'Kb');
				$this->setError($error);
				return false;
			}
			if(count($this->extensions)>0){
				$match = false;
				foreach($this->extensions as $ext){
					if(preg_match("/\.".$ext."$/",$fileData['name'])){
						$match = true;
						break;
					}
				}
				if(!$match){
					$this->setError(sprintf(JText::_('File extension is forbidden'),$fileData['name'],implode(', ',$this->extensions)));
					return false;
				}
			}
			$file = new stdclass;
			$file->file = $fileData['tmp_name'];
			$file->name = $fileData['name'];
			$form->attachments[] = $file;
		}
		
		return true;
	}
	
	function getParam( &$arr, $name, $def=null, $mask=0 ){
		static $noHtmlFilter	= null;
		static $safeHtmlFilter	= null;
	
		$var = JArrayHelper::getValue( $arr, $name, $def, '' );
	
		if (!($mask & 1) && is_string($var)) {
			$var = trim($var);
		}
	
		if ($mask & 2) {
			if (is_null($safeHtmlFilter)) {
				$safeHtmlFilter =  JFilterInput::getInstance(null, null, 1, 1);
			}
			$var = $safeHtmlFilter->clean($var, 'none');
		} elseif ($mask & 4) {
			$var = $var;
		} else {
			if (is_null($noHtmlFilter)) {
				$noHtmlFilter =  JFilterInput::getInstance(/* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
			}
			$var = $noHtmlFilter->clean($var, 'none');
		}
		return $var;
	}
}
