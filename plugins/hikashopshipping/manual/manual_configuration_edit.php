<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.0.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2012 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>				<tr>
					<td class="key">
						<label for="data[shipping][shipping_published]"><?php
							echo JText::_( 'HIKA_PUBLISHED' );
						?></label>
					</td>
					<td>
						<input type="hidden" name="subtask" value="<?php echo JRequest::getCmd('subtask','');?>"/>
						<?php echo JHTML::_('hikaselect.booleanlist', "data[shipping][shipping_published]" , '',@$this->element->shipping_published); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="shipping_tax_id"><?php
							echo JText::_( 'TAXATION_CATEGORY' );
						?></label>
					</td>
					<td><?php
						echo $this->data['categoryType']->display('data[shipping][shipping_tax_id]',@$this->element->shipping_tax_id,true);
					?></td>
				</tr>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_price]"><?php
							echo JText::_( 'PRICE' );
						?></label>
					</td>
					<td>
						<input type="text" name="data[shipping][shipping_price]" value="<?php echo @$this->element->shipping_price; ?>" /><?php echo $this->data['currency']->display('data[shipping][shipping_currency_id]',@$this->element->shipping_currency_id); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_price][shipping_percentage]"><?php
							echo JText::_( 'DISCOUNT_PERCENT_AMOUNT' );
						?></label>
					</td>
					<td>
						<input type="text" name="data[shipping][shipping_params][shipping_percentage]" value="<?php echo (float)@$this->element->shipping_params->shipping_percentage; ?>" />%
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_per_product]"><?php
							echo JText::_( 'USE_PRICE_PER_PRODUCT' );
						?></label>
					</td>
					<td><?php
						if(!isset($this->element->shipping_params->shipping_per_product))
							$this->element->shipping_params->shipping_per_product = false;
						echo JHTML::_('hikaselect.booleanlist', "data[shipping][shipping_params][shipping_per_product]" , ' onchange="hikashop_switch_tr(this,\'hikashop_shipping_per_product_\',2)"', @$this->element->shipping_params->shipping_per_product);
					?></td>
				</tr>
				<tr id="hikashop_shipping_per_product_1"<?php if($this->element->shipping_params->shipping_per_product == false) { echo ' style="display:none;"';}?>>
					<td class="key">
						<label for="data[shipping][shipping_price_per_product]"><?php
							echo JText::_( 'PRICE_PER_PRODUCT' );
						?></label>
					</td>
					<td>
						<input type="text" name="data[shipping][shipping_params][shipping_price_per_product]" value="<?php echo @$this->element->shipping_params->shipping_price_per_product; ?>" />
					</td>
				</tr>
<?php
?>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_params][shipping_min_price]"><?php
							echo JText::_( 'SHIPPING_MIN_PRICE' );
						?></label>
					</td>
					<td>
						<input type="text" name="data[shipping][shipping_params][shipping_min_price]" value="<?php echo @$this->element->shipping_params->shipping_min_price; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_params][shipping_max_price]"><?php
							echo JText::_( 'SHIPPING_MAX_PRICE' );
						?></label>
					</td>
					<td>
						<input type="text" name="data[shipping][shipping_params][shipping_max_price]" value="<?php echo @$this->element->shipping_params->shipping_max_price; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_params][shipping_price_use_tax]"><?php
							echo JText::_( 'WITH_TAX' );
						?></label>
					</td>
					<td>
						<?php
						if(!isset($this->element->shipping_params->shipping_price_use_tax)) $this->element->shipping_params->shipping_price_use_tax=1;
						echo JHTML::_('select.booleanlist', "data[shipping][shipping_params][shipping_price_use_tax]" , '', $this->element->shipping_params->shipping_price_use_tax); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_params][shipping_virtual_included]"><?php echo JText::_( 'INCLUDE_VIRTUAL_PRODUCTS_PRICE' ); ?></label>
					</td>
					<td><?php
						if(!isset($this->element->shipping_params->shipping_virtual_included)){
							$this->element->shipping_params->shipping_virtual_included=1;
						}
						echo JHTML::_('hikaselect.booleanlist', "data[shipping][shipping_params][shipping_virtual_included]" , '',$this->element->shipping_params->shipping_virtual_included);
					?></td>
				</tr>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_params][shipping_override_address]"><?php
							echo JText::_( 'OVERRIDE_SHIPPING_ADDRESS' );
						?></label>
					</td>
					<td><?php
						$values = array();
						$values[] = JHTML::_('select.option', '0', JText::_('HIKASHOP_NO'));
						$values[] = JHTML::_('select.option', '1', JText::_('STORE_ADDRESS'));
						$values[] = JHTML::_('select.option', '2', JText::_('HIKA_HIDE'));
						$values[] = JHTML::_('select.option', '3', JText::_('TEXT_VERSION'));
						$values[] = JHTML::_('select.option', '4', JText::_('HTML_VERSION'));

						echo JHTML::_('select.genericlist', $values, "data[shipping][shipping_params][shipping_override_address]" , 'onchange="hika_shipping_override(this);"', 'value', 'text', @$this->element->shipping_params->shipping_override_address );
					?></td>
				</tr>
				<script type="text/javascript">
				function hika_shipping_override(el) {
					var t = document.getElementById('hikashop_shipping_override_text');
					if(!t) return;
					if(el.value == 3 || el.value == 4) {
						t.style.display = '';
					} else {
						t.style.display = 'none';
					}
				}
				</script>
				<tr id="hikashop_shipping_override_text" style="<?php
						$override = (int)@$this->element->shipping_params->shipping_override_address;
						if( $override != 3 && $override != 4 ) { echo 'display:none;'; }
					?>">
					<td class="key">
						<label for="data[shipping][shipping_params][shipping_override_address_text]"><?php
							echo JText::_( 'OVERRIDE_SHIPPING_ADDRESS_TEXT' );
						?></label>
					</td>
					<td>
						<textarea name="data[shipping][shipping_params][shipping_override_address_text]"><?php
							echo @$this->element->shipping_params->shipping_override_address_text;
						?></textarea>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_params][shipping_min_weight]"><?php
							echo JText::_( 'SHIPPING_MIN_WEIGHT' );
						?></label><br/>
						<label for="data[shipping][shipping_params][shipping_max_weight]"><?php
							echo JText::_( 'SHIPPING_MAX_WEIGHT' );
						?></label>
					</td>
					<td>
						<div style="float:left;">
								<input type="text" name="data[shipping][shipping_params][shipping_min_weight]" value="<?php echo @$this->element->shipping_params->shipping_min_weight; ?>"/>
								<br/>
								<input type="text" name="data[shipping][shipping_params][shipping_max_weight]" value="<?php echo @$this->element->shipping_params->shipping_max_weight; ?>"/>
						</div>
						<div style="float:left;"><?php
							echo $this->data['weight']->display('data[shipping][shipping_params][shipping_weight_unit]',@$this->element->shipping_params->shipping_weight_unit);
						?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_params][shipping_zip_prefix]"><?php
							echo JText::_( 'SHIPPING_PREFIX' );
						?></label><br/>
						<label for="data[shipping][shipping_params][shipping_min_zip]"><?php
							echo JText::_( 'SHIPPING_MIN_ZIP' );
						?></label><br/>
						<label for="data[shipping][shipping_params][shipping_max_zip]"><?php
							echo JText::_( 'SHIPPING_MAX_ZIP' );
						?></label><br/>
						<label for="data[shipping][shipping_params][shipping_zip_suffix]"><?php
							echo JText::_( 'SHIPPING_SUFFIX' );
						?></label>
					</td>
					<td>
						<div style="float:left;">
								<input type="text" name="data[shipping][shipping_params][shipping_zip_prefix]" value="<?php echo @$this->element->shipping_params->shipping_zip_prefix; ?>"/>
								<br/>
								<input type="text" name="data[shipping][shipping_params][shipping_min_zip]" value="<?php echo @$this->element->shipping_params->shipping_min_zip; ?>"/>
								<br/>
								<input type="text" name="data[shipping][shipping_params][shipping_max_zip]" value="<?php echo @$this->element->shipping_params->shipping_max_zip; ?>"/>
								<br/>
								<input type="text" name="data[shipping][shipping_params][shipping_zip_suffix]" value="<?php echo @$this->element->shipping_params->shipping_zip_suffix; ?>"/>
						</div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="data[shipping][shipping_params][shipping_min_volume]"><?php
							echo JText::_( 'SHIPPING_MIN_VOLUME' );
						?></label><br/>
						<label for="data[shipping][shipping_params][shipping_max_volume]"><?php
							echo JText::_( 'SHIPPING_MAX_VOLUME' );
						?></label>
					</td>
					<td>
						<div style="float:left;">
							<input type="text" name="data[shipping][shipping_params][shipping_min_volume]" value="<?php echo @$this->element->shipping_params->shipping_min_volume; ?>"/>
							<br/>
							<input type="text" name="data[shipping][shipping_params][shipping_max_volume]" value="<?php echo @$this->element->shipping_params->shipping_max_volume; ?>"/>
						</div>
						<div style="float:left;"><?php
							echo $this->data['volume']->display('data[shipping][shipping_params][shipping_size_unit]',@$this->element->shipping_params->shipping_size_unit);
						?></div>
					</td>
				</tr>
<script type="text/javascript">
function hikashop_switch_tr(el, name, num) {
	var d = document, s = (el.value == '1');
	if(!el.checked) { s = !s; }
	if(num === undefined) {
		var e = d.getElementById(name);
		if(!e) return;
		e.style.display = (s?'':'none');
		return;
	}
	var e = null;
	for(var i = num; i >= 0; i--) {
		var e = d.getElementById(name + i);
		if(e) {
			e.style.display = (s?'':'none');
		}
	}
}
</script>
