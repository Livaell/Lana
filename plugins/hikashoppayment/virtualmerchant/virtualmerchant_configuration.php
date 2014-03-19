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
	echo '<tr><td colspan="2"><strong>The VirtualMerchant payment plugin needs the CURL library installed but it seems that it is not available on your server. Please contact your web hosting to set it up.</strong></td></tr>';
}
?><tr>
	<td class="key">
		<label for="data[payment][payment_params][merchant_id]">
			<?php echo JText::_( 'ATOS_MERCHANT_ID' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][merchant_id]" value="<?php echo @$this->element->payment_params->merchant_id; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][user_id]">
			<?php echo JText::_( 'HKASHOP_USER_ID' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][user_id]" value="<?php echo @$this->element->payment_params->user_id; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][pin]">
			<?php echo JText::_( 'PIN' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][pin]" value="<?php echo @$this->element->payment_params->pin; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][currency]">
			<?php echo JText::_( 'CURRENCY' ); ?>
		</label>
	</td>
	<td>
		<?php
		$values = array();
		$values[] = JHTML::_('select.option', 'USD', JText::_('USD'));
		$values[] = JHTML::_('select.option', 'EUR', JText::_('EUR'));
		echo JHTML::_('select.genericlist', $values, "data[payment][payment_params][currency]" , 'class="inputbox" size="1"', 'value', 'text', @$this->element->payment_params->currency ); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][ask_ccv]">
			<?php echo JText::_( 'CARD_VALIDATION_CODE' ); ?>
		</label>
	</td>
	<td>
		<?php echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][ask_ccv]" , '',@$this->element->payment_params->ask_ccv ); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][use_avs]">
			<?php echo 'Add AVS information'; ?>
		</label>
	</td>
	<td>
		<?php echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][use_avs]" , '',@$this->element->payment_params->use_avs ); ?>
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
		<label for="data[payment][payment_params][invalid_status]">
			<?php echo JText::_( 'INVALID_STATUS' ); ?>
		</label>
	</td>
	<td>
		<?php echo $this->data['category']->display("data[payment][payment_params][invalid_status]",@$this->element->payment_params->invalid_status); ?>
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
