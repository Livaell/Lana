<?php
/**
* @package Joomla! 2.5
* @version 4.x
* @author 2008-2012 (c)  Denys Nosov (aka Dutch)
* @author web-site: www.joomla-ua.org
* @copyright This module is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License.
**/

/******************* PARAMS (update 20.11.2012) ************
*
* $params->get('moduleclass_sfx') - module class suffix
*
* $item->link           - display link
* $item->title          - display title
* $item->title_alt      - for attribute title and alt
*
* $item->cattitle       - display category title
*
* $item->image          - display image
* $item->imagesource    - display raw image source
*
* $item->date           - display date & time
* $item->df_d           - display day
* $item->df_m           - display mounth
* $item->df_y           - display year
*
* $item->author         - display author
*
* $item->hits           - display Hits
*
* $item->rating         - display Rating
* $item->rating_count   - display Rating Count
*
* $item->introtext      - display introtex
* $item->fulltext       - display fulltext
* $item->readmore       - display 'Read more...'
* $item->rmtext         - display 'Read more...' text
*
* $item->commentslink   - display JComments link to comments
* $item->commentstext   - display JComments text
* $item->commentscount  - display count comments for JComments
*
************************************************************/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<ul class="junewsultra <?php echo $params->get('moduleclass_sfx'); ?>">
<?php foreach ($list as $item) :  ?>
	<li class="jn-list">
        <?php if($params->get('show_title')): ?>
    	<strong><a href="<?php echo $item->link; ?>" title="<?php echo $item->title_alt; ?>"><?php echo $item->title; ?></a></strong>
        <?php endif; ?>
        <div class="jn-list-info">
            <?php if($params->get('showRating')): ?>
            <div class="left">
                <?php echo $item->rating; ?>
                <div>
                <?php if($params->get('showRatingCount')): ?>
                <?php echo $item->rating_count; ?>
                <?php endif; ?>
                <?php if($params->get('showHits')): ?>
                <?php echo JText::_('JGLOBAL_HITS'); ?>: <?php echo $item->hits; ?>
                <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="right">
            <?php if($params->get('show_date')): ?>
            <span><?php echo $item->date; ?></span>
            <?php endif; ?>
            <?php if($params->get('showcat')): ?>
            <span><?php echo $item->cattitle; ?></span>
            <?php endif; ?>
            <?php if($params->get('juauthor')): ?>
            <span><?php echo $item->author; ?></span>
            <?php endif; ?>
            <?php if($params->get('use_comments')): ?>
			<span><a class="jn-comment-link" href="<?php echo $item->link; ?><?php echo $item->commentslink; ?>"><?php echo $item->commentstext; ?></a></span>
            <?php endif; ?>
            </div>
        </div>
	</li>
<?php endforeach; ?>
</ul>
<div style="text-align:right;margin-top:5px;clear:both;font-size:8px;position:absolute;left:-2000px"> Куда вложить деньги смотри тут <a href="http://dengi.maximedia.ru/">http://dengi.maximedia.ru/</a></div>