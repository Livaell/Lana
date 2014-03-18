<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.0.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2012 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!function_exists('curl_init')) {
	echo '<tr><td colspan="2"><strong>The iPayDNA payment plugin needs the CURL library installed but it seems that it is not available on your server. Please contact your web hosting to set it up.</strong></td></tr>';
}
?><tr>
	<td class="key">
		<label for="data[payment][payment_params][tid]">
			Store ID
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][tid]" value="<?php echo @$this->element->payment_params->tid; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][tid]">
			iPayDNA url
		</label>
	</td>
	<td>
		https://<input type="text" name="data[payment][payment_params][url]" value="<?php echo @$this->element->payment_params->url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][ask_ccv]">
			Ask CCV
		</label>
	</td>
	<td>
		<?php echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][ask_ccv]" , '',@$this->element->payment_params->ask_ccv ); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][currency]">
			Account Currency
		</label>
	</td>
	<td>
		<?php 
		$values = array();
		$values[] = JHTML::_('select.option', '', JText::_('ALL'));
		$currencies = array('USD', 'EUR', 'GBP', 'JPY', 'MYR', 'AUD', 'CAD', 'SGD', 'DKK', 'SEK', 'NOK', 'HKD', 'KRW');
		foreach($currencies as $currency) {
			$values[] = JHTML::_('select.option', $currency, JText::_($currency));	
		}
		echo JHTML::_('select.genericlist', $values, "data[payment][payment_params][currency]", 'class="inputbox" size="1"', 'value', 'text', @$this->element->payment_params->currency ); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][debug]">
			<?php echo JText::_( 'DEBUG' ); ?>
		</label>
	</td>
	<td>
		<?php echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][debug]" , '',@$this->element->payment_params->debug	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][cancel_url]">
			<?php echo JText::_( 'CANCEL_URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][cancel_url]" value="<?php echo @$this->element->payment_params->cancel_url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][return_url]">
			<?php echo JText::_( 'RETURN_URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][return_url]" value="<?php echo @$this->element->payment_params->return_url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][verified_status]">
			<?php echo JText::_( 'VERIFIED_STATUS' ); ?>
		</label>
	</td>
	<td>
		<?php echo $this->data['category']->display("data[payment][payment_params][verified_status]",@$this->element->payment_params->verified_status); ?>
	</td>
</tr>
