<?php
/**
* Default template
* @package Gavick News Show Pro GK4
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 4.3.0.0 $
**/
// no direct access
defined('_JEXEC') or die('Restricted access');
$news_amount = $this->parent->config['news_portal_mode_amount'];
?>
<?php if($news_amount > 0) : ?>
<div class="nspMainPortalMode1<?php if($this->parent->config['autoanim'] == TRUE) echo ' autoanim'; ?> nspFs<?php echo $this->parent->config['module_font_size']; ?>" id="nsp-<?php echo $this->parent->config['module_id']; ?>" style="width:<?php echo $this->parent->config['module_width']; ?>%;">
	<?php if($this->parent->config['news_portal_mode_amount'] > 0) : ?>
	<div class="nspTopInterface">
		<span class="nspPrev"><?php echo JText::_('MOD_NEWS_PRO_GK4_NSP_PREV'); ?></span>
		<span class="nspNext"><?php echo JText::_('MOD_NEWS_PRO_GK4_NSP_NEXT'); ?></span>
	</div>
	<div class="nspArts" style="height:<?php echo $this->parent->config['portal_mode_1_module_height']; ?>px;">
		<?php for($i = 0; $i < count($news_html_tab); $i++) : ?>
			<div class="nspArt">
				<div style="padding:<?php echo $this->parent->config['art_padding']; ?>">
					<?php echo $news_html_tab[$i];?>
				</div>
			</div>
		<?php endfor; ?>	
	</div>
	<?php endif; ?>
</div>
<?php else : ?>
<p><?php echo JText::_('MOD_NEWS_PRO_GK4_NSP_ERROR'); ?></p>
<?php endif; ?>