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
		<label for="data[payment][payment_params][order_status]">
			<?php echo JText::_( 'ORDER_STATUS' ); ?>
		</label>
	</td>
	<td>
		<?php echo $this->data['category']->display("data[payment][payment_params][order_status]",@$this->element->payment_params->order_status); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][status_notif_email]">
			<?php echo JText::_( 'ORDER_STATUS_NOTIFICATION' ); ?>
		</label>
	</td>
	<td>
		<?php echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][status_notif_email]",'',@$this->element->payment_params->status_notif_email);?>
	</td>
</tr>
