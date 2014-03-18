<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.0.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2012 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if(!HIKASHOP_J30) { ?>
<div id="page-vote">
<?php } else { ?>
<div id="page-vote" class="row-fluid">
	<div class="span12">
<?php } ?>
	<fieldset class="adminform">
		<legend><?php echo JText::_('VOTE')." & ".JText::_('COMMENT'); ?></legend>
		<table class="admintable table" cellspacing="1">
			<tr id="vote_display_line">
				<td class="key">
					<?php echo JText::_('DISPLAY_VOTE_OF_PRODUCTS');?>
				</td>
				<td>
					<?php echo JHTML::_('hikaselect.booleanlist', 'config[default_params][show_vote_product]' , '',@$this->default_params['show_vote_product']); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
						<?php echo JText::_('ENABLE_STATUS'); ?>
				</td>
				<td>
					<?php
						$arr = array(
									JHTML::_('select.option', 'nothing', JText::_('Nothing') ),
									JHTML::_('select.option', 'vote', JText::_('Vote only') ),
									JHTML::_('select.option', 'comment', JText::_('Comment only') ),
									JHTML::_('select.option', 'two', JText::_('Vote & Comment') ),
									JHTML::_('select.option', 'both', JText::_('Vote & Comment connected') )
							);
						echo JHTML::_('hikaselect.genericlist', $arr, "config[enable_status_vote]", 'class="inputbox" size="1"', 'value', 'text', $this->config->get('enable_status_vote',0));
					?>
				</td>
			</tr>
			<tr>
				<td class="key">
						<?php echo JText::_('ACCESS_VOTE'); ?>
				</td>
				<td>
					<?php
						$arr = array(
									JHTML::_('select.option', 'public', JText::_('Public') ),
									JHTML::_('select.option', 'registered', JText::_('Registered') ),
									JHTML::_('select.option', 'buyed', JText::_('Bought') )
							);
						echo JHTML::_('hikaselect.genericlist', $arr, "config[access_vote]", 'class="inputbox" size="1"', 'value', 'text', $this->config->get('access_vote',0));
					?>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('VOTE'); ?></legend>
		<table class="admintable table" cellspacing="1">
			<tr>
				<td class="key" >
					<?php echo JText::_('STAR_NUMBER'); ?>
				</td>
				<td>
					<input class="inputbox" type="text" name="config[vote_star_number]" value="<?php echo $this->config->get('vote_star_number',5);?>" />
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('COMMENT'); ?></legend>
		<table class="admintable table" cellspacing="1">
			<tr>
				<td class="key" >
					<?php echo JText::_('EMAIL_COMMENT'); ?>
				</td>
				<td>
					<?php echo JHTML::_('hikaselect.booleanlist', "config[email_comment]" , '', $this->config->get('email_comment',0)); ?>
				</td>
			</tr>
			<tr>
				<td class="key" >
					<?php echo JText::_('PUBLISHED_COMMENT'); ?>
				</td>
				<td>
					<?php echo JHTML::_('hikaselect.booleanlist', "config[published_comment]" , '', $this->config->get('published_comment',1)); ?>
				</td>
			</tr>
			<tr>
				<td class="key" >
					<?php echo JText::_('EMAIL_NEW_COMMENT'); ?>
				</td>
				<td>
					<input class="inputbox" type="text" name="config[email_each_comment]" value="<?php echo $this->config->get('email_each_comment');?>" />
				</td>
			</tr>
			<tr>
				<td class="key" >
					<?php echo JText::_('COMMENT_BY_PERSON_BY_PRODUCT'); ?>
				</td>
				<td>
					<input class="inputbox" type="text" name="config[comment_by_person_by_product]" value="<?php echo $this->config->get('comment_by_person_by_product',5);?>" />
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('COMMENT')." ".JText::_('LISTING'); ?></legend>
		<table class="admintable table" cellspacing="1">
			<tr>
				<td class="key" >
					<?php echo JText::_('NUMBER_COMMENT_BY_PRODUCT'); ?>
				</td>
				<td>
					<input class="inputbox" type="text" name="config[number_comment_product]" value="<?php echo $this->config->get('number_comment_product',30); ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
						<?php echo JText::_('VOTE_COMMENT_SORT'); ?>
				</td>
				<td>
					<?php
						$arr = array(
									JHTML::_('select.option', 'date', JText::_('DATE') ),
									JHTML::_('select.option', 'helpful', JText::_('HELPFUL') ),
							);
						echo JHTML::_('hikaselect.genericlist', $arr, "config[vote_comment_sort]", 'class="inputbox" size="1"', 'value', 'text', $this->config->get('vote_comment_sort',0));
					?>
				</td>
			</tr>
			<tr>
				<td class="key" >
					<?php echo JText::_('VOTE_COMMENT_SORT_FRONTEND'); ?>
				</td>
				<td>
					<?php echo JHTML::_('hikaselect.booleanlist', "config[vote_comment_sort_frontend]" , '', $this->config->get('vote_comment_sort_frontend',0)); ?>
				</td>
			</tr>
			<tr>
				<td class="key" >
					<?php echo JText::_('SHOW_LISTING_COMMENT'); ?>
				</td>
				<td>
					<?php echo JHTML::_('hikaselect.booleanlist', "config[show_listing_comment]" , '', $this->config->get('show_listing_comment',0)); ?>
				</td>
			</tr>
			<tr>
				<td class="key" >
					<?php echo JText::_('SHOW_COMMENT_DATE'); ?>
				</td>
				<td>
					<?php echo JHTML::_('hikaselect.booleanlist', "config[show_comment_date]" , '', $this->config->get('show_comment_date',0)); ?>
				</td>
			</tr>
			<tr>
				<td class="key" >
					<?php echo JText::_('USEFUL_RATING'); ?>
				</td>
				<td>
					<?php echo JHTML::_('hikaselect.booleanlist', "config[useful_rating]" , '', $this->config->get('useful_rating',1)); ?>
				</td>
			</tr>
			<tr>
				<td class="key" >
					<?php echo JText::_('REGISTER_NOTE_COMMENT'); ?>
				</td>
				<td>
					<?php echo JHTML::_('hikaselect.booleanlist', "config[register_note_comment]" , '', $this->config->get('register_note_comment',0)); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
						<?php echo JText::_('VOTE_USEFUL_STYLE'); ?>
				</td>
				<td>
					<?php
						$arr = array(
									JHTML::_('select.option', 'helpful', JText::_('3 of 5 find it helpful') ),
									JHTML::_('select.option', 'thumbs', JText::_('3 up 2 down') ),
							);
						echo JHTML::_('hikaselect.genericlist', $arr, "config[vote_useful_style]", 'class="inputbox" size="1"', 'value', 'text', $this->config->get('vote_useful_style',0));
					?>
				</td>
			</tr>
		</table>
	</fieldset>
<?php if(HIKASHOP_J30) { ?>
	</div>
<?php } ?>
</div>
