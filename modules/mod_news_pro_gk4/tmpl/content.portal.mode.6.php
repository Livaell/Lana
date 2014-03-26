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

<?php if($news_amount >= 5) : ?>
<div class="nspMainPortalMode6" id="nsp-<?php echo $this->parent->config['module_id']; ?>">
	<div class="nspSpeakers">
		<div class="nspSpeakerBig speakerHide">
			<div><?php echo $news_image_tab[2];?></div>
			<?php echo $news_title_tab[2];?>
		</div>
		
		<div class="nspSpeakersSmallLeft">
			<div class="nspSpeakerSmall speakerHide">
				<div><?php echo $news_image_tab[0];?></div>
				<?php echo $news_title_tab[0];?>
			</div>
			
			<div class="nspSpeakerSmall speakerHide">
				<div><?php echo $news_image_tab[1];?></div>
				<?php echo $news_title_tab[1];?>
			</div>
		</div>
		
		<div class="nspSpeakersSmallRight">
			<div class="nspSpeakerSmall speakerHide">
				<div><?php echo $news_image_tab[3];?></div>
				<?php echo $news_title_tab[3];?>
			</div>
			
			<div class="nspSpeakerSmall speakerHide">
				<div><?php echo $news_image_tab[4];?></div>
				<?php echo $news_title_tab[4];?>
			</div>
		</div>
	</div>
	
	<div class="nspRestSpeakers">
		<?php for($i = 0; $i < count($news_image_tab); $i++) : ?>
		<div class="nspSpeaker">
			<div><?php echo $news_image_tab[$i];?></div>
			<?php echo $news_title_tab[$i];?>
		</div>
		<?php endfor; ?>
	</div>
</div>
<?php else : ?>
<p>You have to specify at least 5 speakers.</p>
<?php endif; ?>