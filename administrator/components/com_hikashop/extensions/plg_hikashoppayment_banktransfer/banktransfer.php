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
class plgHikashoppaymentBanktransfer extends hikashopPaymentPlugin {
	var $name = 'banktransfer';
	var $multiple = true;
	var $pluginConfig = array(
		 'order_status' => array('ORDER_STATUS', 'orderstatus', 'verified'),
		 'status_notif_email' => array('ORDER_STATUS_NOTIFICATION', 'boolean','0'),
		 'information' => array('BANK_ACCOUNT_INFORMATION', 'big-textarea')
	);

	function onAfterOrderConfirm(&$order,&$methods,$method_id){
		$method =& $methods[$method_id];
		$this->modifyOrder($order, $method->payment_params->order_status, @$method->payment_params->status_notif_email, false);

		$this->removeCart = true;

		$this->information = $method->payment_params->information;
		if(preg_match('#^[a-z0-9_]*$#i',$this->information)){
			$this->information = JText::_($this->information);
		}
		$currencyClass = hikashop_get('class.currency');
		$this->amount = $currencyClass->format($order->order_full_price,$order->order_currency_id);
		$this->order_number = $order->order_number;

		$this->return_url =& $method->payment_params->return_url;

		return $this->showPage('end');

	}

	function getPaymentDefaultValues(&$element) {
		$element->payment_name='Bank transfer';
		$element->payment_description='You can pay by sending us a bank transfer.';
		$element->payment_images='Bank_transfer';

		$element->payment_params->information='Account owner: XXXXX<br/>
<br/>
Owner address:<br/>
<br/>
XX XXXX XXXXXX<br/>
<br/>
XXXXX XXXXXXXX<br/>
<br/>
IBAN International Bank Account Number:<br/>
<br/>
XXXX XXXX XXXX XXXX XXXX XXXX XXX<br/>
<br/>
BIC swift Bank Identification Code:<br/>
<br/>
XXXXXXXXXXXXXX<br/>
<br/>
Bank name: XXXXXXXXXXX<br/>
<br/>
Bank address:<br/>
<br/>
XX XXXX XXXXXX<br/>
<br/>
XXXXX XXXXXXXX';
		$element->payment_params->order_status='created';
	}
}
