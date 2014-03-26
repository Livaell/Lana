<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_articles_news
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$document = JFactory::getDocument();
$document->addStyleSheet('modules/mod_avatar_articles_news/assets/css/default.css');
?>
<script type="text/javascript">
	var AvatarArticleNews = new Class(
		{
			options: {},
			
		    initialize: function(options)
		    {
			    this.options = Object.merge(this.options, options);
			    this.wrapper 	= $(this.options.wrapperID);
				this.listItem 	= this.wrapper.getElement('ul[class*="avatar-news-left"]');
				this.listPreview = this.wrapper.getElement('div[class*="avatar-news-right"]');
				this.items 	 = this.listItem.getElements('li[class*="item"]');
				this.previews = this.listPreview.getElements('li[class*="preview"]');
				this.currentIndex = 0;
				this.listItemCount = this.items.length;
				this.readMore = this.listPreview.getElement('a[class*=read-more]');
				
				this.previewM = new Fx.Morph(this.listPreview, {
				    duration: 1500,
				    unit: '%'
				});
				
				this.itemM = new Fx.Morph(this.listItem, {
				    duration: 3000,
				    unit: '%'
				});
				
				this.itemM.start({width: '45.5'}).chain(function(){
					this.itemSize = this.listItem.getSize();
					this.listPreview.style.height = this.itemSize.y + 'px';
		
					this.previewM.start({
						display: 'block',
						width: '53'
					});
					this.listPreview.setStyle('opacity', 1);
					this.effects(); 
					this.next.periodical(this.options.duration, this);
				}.bind(this));
			},
	
			effects: function(){
				this.items.each(function(el, index)
				{
					el.addEvent('click', function()
					{
						this.currentIndex = index;
						this.readMore.href = this.options.items[index].link;
						
						el.addClass('active');
	
						this.items.each(function(eli, i)
						{
							if (index != i) {
								eli.removeClass('active');
							} 
						}.bind(this));
	
						this.previews.each(function(elp, p)
						{
							elp.style.opacity = 1;
							
							if (index == p) {
								elp.slide('in');
							}
							
							if (index != p) {
								elp.slide('out');
							}
						}.bind(this));
					}.bind(this));
				}.bind(this));
			},

			next: function()
			{
				if (this.currentIndex >= this.listItemCount) {
					this.currentIndex = 0;
					
				}
				
				this.items[this.currentIndex].fireEvent('click');
				this.currentIndex++;
			}
	});
	
	window.addEvent('domready', function(){
		new AvatarArticleNews({wrapperID : 'avatar-articles-news', duration: <?php echo $duration; ?>, items: <?php echo json_encode($list)?>});
	});
</script>
<div id="avatar-articles-news" class="avatar-articles-news <?php echo $moduleclassSfx; ?>">

<?php 
	$leftHtml = '<ul class="avatar-news-left">';
	$rightHtml = '<div class="avatar-news-right"><ul class="list-preview remove-padding-margin">';
	
	foreach ($list as $k => $item) 
	{
		$leftHtml .= '<li class="item">';
		
		if ($params->get('item_title')) 
		{
			$leftHtml .= '<'.$params->get('item_heading'). ' class="item-title">';
			if ($params->get('link_titles') && $item->link != '') {
				$leftHtml .= '<a href="'.$item->link.'" target="_blank">'.$item->title.'</a>';
			} else {
				$leftHtml .= $item->title;
			}
			$leftHtml .= '</'.$params->get('item_heading').'>';
		}
	
		if (!$params->get('intro_only')) {
			$leftHtml .= $item->afterDisplayTitle;
		}
		
		$leftHtml .= '<p class="item-short-intro">'. substr(strip_tags($item->introtext), 0 , 100). '</p>';
		$leftHtml .= '</li>';
		
		$rightHtml .= '<li class="preview">';
		
		if ($item->images && $item->images->image_intro != '') {
			$rightHtml .= '<img src="'.$item->images->image_intro.'" alt="'.htmlspecialchars($item->images->image_intro_alt).'"/>';	
		}		
		
		$rightHtml .= $item->beforeDisplayContent;
	
		$rightHtml .= $item->introtext;
	
		if (isset($item->link) && $item->readmore) {
			$rightHtml .= '<a class="avatar-articles-new-readmore" href="'.$item->link.'">'.$item->linkText.'</a>';
		}
		
		$rightHtml .= '</li>';
	}
	
	$leftHtml .= '</ul>';
	$rightHtml .= '</ul>';
	$rightHtml .= '<a class="read-more" target="_blank">'.(($params->get('readmore') != '') ? $params->get('readmore') : $item->linkText).'</a>';
	$rightHtml .= '</div>';
	echo $leftHtml.$rightHtml. '<div class="clearbreak"></div>';
?>	
	<div class="avatar-copyright" style="width:100%;margin: 5px;text-align: center;">
		&copy; JoomAvatar.com
		<a target="_blank" href="http://joomavatar.com" title="Joomla Template & Extension">Joomla Extension</a>-
		<a target="_blank" href="http://joomavatar.com" title="Joomla Template & Extension">Joomla Template</a>
	</div>
</div>
