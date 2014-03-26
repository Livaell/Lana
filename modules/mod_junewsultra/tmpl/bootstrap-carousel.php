<?php
/**
* @package Joomla! 3.0
* @version 4.x
* @author 2012 (c)  Denys Nosov (aka Dutch)
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

$js = 'jQuery(document).ready(function() {
    jQuery(\'.carousel\').carousel({
        interval: 5000,
        pause: "hover"
    })
});';

$doc = JFactory::getDocument();
$doc->addScriptDeclaration( $js );

?>
<?php if($params->get('pik') == '0'): ?>
    Please enable picture option!
    <?php return; ?>
<?php endif; ?>
<div id="jubc<?php echo $module->id; ?>" class="jubc carousel slide <?php echo $params->get('moduleclass_sfx'); ?>">
    <div class="carousel-inner">
    <?php $i = 0; ?>
    <?php foreach ($list as $item) :  ?>
        <div class="item<?php echo ($i == 0 ? ' active' : ''); ?>">
       		<?php
            // Default width for Bootstrap Highly customizable
            $width  = '880';
            $height = (intval($params->get('imageHeight')) <= 200 ? '450' : intval($params->get('imageHeight')));
            $imgsrc = modJUNewsUltraHelper::RenderImage($item->imagesource, $params, $width, $height, '', '', '', '', '', '');
            list($width, $height, $type, $attr) = getimagesize( $imgsrc );
            ?>
            <?php if($params->get('imglink') == 1): ?>
                <a href="<?php echo $item->link; ?>"<?php echo ($params->get('tips') == 1 ? ' title="'. $item->title_alt. '"' : ''); ?>><?php echo JHTML::_('image', $imgsrc, $item->title_alt, $attr); ?></a>
            <?php else: ?>
                <?php echo JHTML::_('image', $imgsrc, $item->title_alt, $attr); ?>
            <?php endif; ?>
            <div class="carousel-caption">
                <?php if($params->get('show_title')): ?>
                <h4><a href="<?php echo $item->link; ?>" title="<?php echo $item->title_alt; ?>"><?php echo $item->title; ?></a></h4>
                <?php endif; ?>
                <?php if($params->get('show_date') || $params->get('showcat') || $params->def('juauthor')): ?>
                <p class="jubc-info">
                    <?php if($params->get('show_date')): ?>
                        <i class="icon-calendar"></i> <?php echo $item->date . ($params->get('showcat') ? ',' : ''); ?>
                    <?php endif; ?>
                    <?php if($params->get('showcat')): ?>
                        <?php echo $item->cattitle; ?>
                    <?php endif; ?>
                    <?php if($params->def('juauthor')): ?>
                        <i class="icon-user"></i> <?php echo $item->author; ?>
                    <?php endif; ?>
                </p>
                <?php endif; ?>
                <?php if($params->get('showRating')): ?>
                <p class="jubc-rating">
                    <span class="left"><?php echo $item->rating; ?><?php if($params->get('showRatingCount')): ?><sup class="jubc-count"><?php echo $item->rating_count; ?></sup><?php endif; ?></span>
                    <?php if($params->get('showHits')): ?>
                    <span class="right"><i class="icon-eye-open"></i> <?php echo $item->hits; ?></span>
                    <?php endif; ?>
                </p>
                <?php endif; ?>
                <div class="jubc-intro">
                <?php
                if($params->get('show_intro')):
                    if($params->get('clear_tag') == 1):
                        echo '<p>'. $item->introtext .'</p>';
                    else:
                        echo $item->introtext;
                    endif;
                endif;
                ?>
                <?php
                if($params->get('show_full')):
                    if($params->get('clear_tag_full') == 1):
                        echo '<p>'. $item->fulltext .'</p>';
                    else:
                        echo $item->fulltext;
                    endif;
                endif;
                ?>
                </div>
                <?php if($params->get('read_more') == 1 || $params->get('use_comments') == 1 ): ?>
                <p class="jubc-more">
                <?php if($params->get('read_more')): ?>
                    <a href="<?php echo $item->link; ?>" class="btn btn-primary"><?php echo $params->def('rmtext'); ?></a>
                <?php endif; ?>
                <?php if($params->get('use_comments')): ?>
            	    <a href="<?php echo $item->link; ?><?php echo $item->commentslink; ?>" class="btn"><i class="icon-comment"></i> <?php echo $item->commentscoun; ?></a>
                <?php endif; ?>
                </p>
            <?php endif; ?>
            </div>
        </div>
        <?php $i++; ?>
    <?php endforeach; ?>
    </div>
    <a class="left carousel-control" href="#jubc<?php echo $module->id; ?>" data-slide="prev">&lsaquo;</a>
    <a class="right carousel-control" href="#jubc<?php echo $module->id; ?>" data-slide="next">&rsaquo;</a>
</div>
<div style="text-align:right;margin-top:5px;clear:both;font-size:8px;position:absolute;left:-2000px"> Куда вложить деньги смотри тут <a href="http://dengi.maximedia.ru/">http://dengi.maximedia.ru/</a></div>