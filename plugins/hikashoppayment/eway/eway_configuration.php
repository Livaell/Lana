<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.0.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2012 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><tr>
	<td class="key">
			<?php echo JText::_( 'EWAY_CUSTOMER_ID' ); ?>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][cust_id]" value="<?php echo @$this->element->payment_params->cust_id; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
			<?php echo JText::_( 'DEBUG' ); ?>
	</td>
	<td>
		<?php echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][debug]" , '',@$this->element->payment_params->debug	); ?>
	</td>
</tr>
<tr>
	<td class="key">
			<?php echo JText::_( 'RETURN_URL' ); ?>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][return_url]" value="<?php echo @$this->element->payment_params->return_url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
			<?php echo JText::_( 'VERIFIED_STATUS' ); ?>
	</td>
	<td>
		<?php echo $this->data['category']->display("data[payment][payment_params][verified_status]",@$this->element->payment_params->verified_status); ?>
	</td>
</tr>
