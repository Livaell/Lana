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

defined('_JEXEC') or die('Restricted access'); ?>

<div class="hikashop_<?php echo $this->payment_name; ?>_end" id="hikashop_<?php echo $this->payment_name; ?>_end">
	<span id="hikashop_<?php echo $this->payment_name; ?>_end_message" class="hikashop_<?php echo $this->payment_name; ?>_end_message">
		<?php echo JText::sprintf('PLEASE_WAIT_BEFORE_REDIRECTION_TO_X', $method->payment_name).'<br/>'.JText::_('CLICK_ON_BUTTON_IF_NOT_REDIRECTED'); ?>
	</span>
	<span id="hikashop_<?php echo $this->payment_name; ?>_end_spinner" class="hikashop_<?php echo $this->payment_name; ?>_end_spinner hikashop_checkout_end_spinner"></span>
	<br/>
	<form id="hikashop_<?php echo $this->payment_name; ?>_form" name="hikashop_<?php echo $this->payment_name; ?>_form" action="<?php echo $method->payment_params->url; ?>" method="post">
		<div id="hikashop_<?php echo $this->payment_name; ?>_end_image" class="hikashop_<?php echo $this->payment_name; ?>_end_image">
			<input id="hikashop_<?php echo $this->payment_name; ?>_button" type="submit" value="<?php echo JText::_('PAY_NOW'); ?>" name="" alt="<?php echo JText::_('PAY_NOW'); ?>" />
		</div>
		<?php
			foreach ($vars as $name => $value)
				echo '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars((string)$value).'" />';

			JRequest::setVar('noform', 1);
		?>
	</form>
	<script type="text/javascript">
		<!--
		document.getElementById('hikashop_<?php echo $this->payment_name; ?>_form').submit();
		//-->
	</script>
</div>
