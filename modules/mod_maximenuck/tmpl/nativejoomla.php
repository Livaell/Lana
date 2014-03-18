<?php
/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');
if ($params->get('style', 'moomenu') == 'clickclose') {
    $close = '<span class="maxiclose">' . JText::_('MAXICLOSE') . '</span>';
} else {
    $close = '';
}

?>

<ul class="menu<?php echo $class_sfx;?>"<?php
	$tag = '';
	if ($params->get('tag_id')!=NULL) {
		$tag = $params->get('tag_id').'';
		echo ' id="'.$tag.'"';
	}
?>>
<?php
if ($logoimage) {
	$logoheight = $logoheight ? ' height="' . $logoheight . '"' : '';
	$logowidth = $logowidth ? ' width="' . $logowidth . '"' : '';
	$logofloat = ($params->get('orientation', '0') == 1) ? '' :  'float: '.$params->get('logoposition','left').';';
	$styles = 'style="' .$logofloat . 'margin: '.$params->get('logomargintop','0').'px '.$params->get('logomarginright','0').'px '.$params->get('logomarginbottom','0').'px '.$params->get('logomarginleft','0').'px' . '"';
	$logolinkstart = $logolink  ? '<a href="'. JRoute::_($logolink).'" style="margin-bottom: 0 !important;margin-left: 0 !important;margin-right: 0 !important;margin-top: 0 !important;padding-bottom: 0 !important;padding-left: 0 !important;padding-right: 0 !important;padding-top: 0 !important;background: none !important;">' : '';
	$logolinkend = $logolink  ? '</a>' : '';
	?>
	<li class="maximenucklogo" style="margin-bottom: 0 !important;margin-left: 0 !important;margin-right: 0 !important;margin-top: 0 !important;">
		<?php echo $logolinkstart ?><img src="<?php echo $logoimage ?>" alt="<?php echo $params->get('logoalt','') ?>" <?php echo $logowidth.$logoheight.$styles ?> /><?php echo $logolinkend ?>
	</li>
<?php } ?>
<?php
$zindex = 12000;

foreach ($items as $i => &$item) :
	// load a module
	if (isset($item->content) AND $item->content) {
        echo '<li class="' . $item->classe . ' level' . $item->level .' '.$item->liclass . '">' . $item->content;
		$item->ftitle = '';
    }
	if ($item->ftitle != "") {
		$title = $item->anchor_title ? ' title="'.$item->anchor_title.'"' : '';
		$description = $item->desc ? '<span class="itemdescription">' . $item->desc . '</span>' : '';
		// manage HTML encapsulation
		$item->tagcoltitle = $item->params->get('maximenu_tagcoltitle', 'none');
		$classcoltitle = $item->params->get('maximenu_classcoltitle', '') ? ' class="'.$item->params->get('maximenu_classcoltitle', '').'"' : '';
		$opentag = (isset($item->tagcoltitle) AND $item->tagcoltitle != 'none') ? '<'.$item->tagcoltitle.$classcoltitle.'>' : '';
		$closetag = (isset($item->tagcoltitle) AND $item->tagcoltitle != 'none') ? '</'.$item->tagcoltitle.'>' : '';

		// manage image
		if ($item->menu_image) {
			// manage image rollover
			$menu_image_split = explode('.', $item->menu_image);
			$imagerollover = '';
			if (isset($menu_image_split[1])) {
                                // manage active image
                                if (isset($item->active) AND $item->active) {
                                    $menu_image_active = $menu_image_split[0] . $params->get('imageactiveprefix', '_active') . '.' . $menu_image_split[1];
                                    if (JFile::exists(JPATH_ROOT . '/' . $menu_image_active)) {
					$item->menu_image = $menu_image_active;
                                    }
                                }
                                // manage hover image
                                $menu_image_hover = $menu_image_split[0] . $params->get('imagerollprefix', '_hover') . '.' . $menu_image_split[1];
				if (isset($item->active) AND $item->active AND JFile::exists(JPATH_ROOT . '/' . $menu_image_split[0] . $params->get('imageactiveprefix', '_active') . $params->get('imagerollprefix', '_hover') . '.' . $menu_image_split[1])) {
					$imagerollover = ' onmouseover="javascript:this.src=\'' . JURI::base(true) . '/' . $menu_image_split[0] . $params->get('imageactiveprefix', '_active') . $params->get('imagerollprefix', '_hover') . '.' . $menu_image_split[1] . '\'" onmouseout="javascript:this.src=\'' . JURI::base(true) . '/' . $item->menu_image . '\'"';
				} else if (JFile::exists(JPATH_ROOT . '/' . $menu_image_hover)) {
					$imagerollover = ' onmouseover="javascript:this.src=\'' . JURI::base(true) . '/' . $menu_image_hover . '\'" onmouseout="javascript:this.src=\'' . JURI::base(true) . '/' . $item->menu_image . '\'"';
				}
			}

			$imagesalign = ($item->params->get('maximenu_images_align', 'moduledefault') != 'moduledefault') ? $item->params->get('maximenu_images_align', 'top') : $params->get('menu_images_align', 'top');
			if ($item->params->get('menu_text', 1) AND !$params->get('imageonly', '0')) {
				switch ($imagesalign) :
					default:
                                        case 'default':
                                            $linktype = '<img src="'.$item->menu_image.'" alt="'.$item->ftitle.'" align="left"'.$imagerollover.'/><span class="titreck">'.$item->ftitle.$description.'</span> ' ;
                                        break;
                                        case 'bottom':
						$linktype = '<span class="titreck">'.$item->ftitle.$description.'</span><img src="'.$item->menu_image.'" alt="'.$item->ftitle.'" style="display: block; margin: 0 auto;"'.$imagerollover.' /> ' ;
					break;
					case 'top':
						$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->ftitle.'" style="display: block; margin: 0 auto;"'.$imagerollover.' /><span class="titreck">'.$item->ftitle.$description.'</span> ' ;
					break;
					case 'rightbottom':
						$linktype = '<span class="titreck">'.$item->ftitle.$description.'</span><img src="'.$item->menu_image.'" alt="'.$item->ftitle.'" align="top"'.$imagerollover.'/> ' ;
					break;
					case 'rightmiddle':
						$linktype = '<span class="titreck">'.$item->ftitle.$description.'</span><img src="'.$item->menu_image.'" alt="'.$item->ftitle.'" align="middle"'.$imagerollover.'/> ' ;
					break;
					case 'righttop':
						$linktype = '<span class="titreck">'.$item->ftitle.$description.'</span><img src="'.$item->menu_image.'" alt="'.$item->ftitle.'" align="bottom"'.$imagerollover.'/> ' ;
					break;
					case 'leftbottom':
						$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->ftitle.'" align="top"'.$imagerollover.'/><span class="titreck">'.$item->ftitle.$description.'</span> ' ;
					break;
					case 'leftmiddle':
						$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->ftitle.'" align="middle"'.$imagerollover.'/><span class="titreck">'.$item->ftitle.$description.'</span> ' ;
					break;
					case 'lefttop':
						$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->ftitle.'" align="bottom"'.$imagerollover.'/><span class="titreck">'.$item->ftitle.$description.'</span> ' ;
					break;
				endswitch;
			} else {
				$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->ftitle.'"'.$imagerollover.'/>' ;
			}
		}
		else {
			$linktype = '<span class="titreck">'.$item->ftitle.$description.'</span>';
		}

        if ($params->get('imageonly', '0') == '1')
            $item->ftitle = '';
        echo '<li class="' . $item->classe . ' level' . $item->level .' '.$item->liclass . '" style="z-index : ' . $zindex . ';">';
        switch ($item->type) :
            default:
                echo $opentag.'<a class="' . $item->anchor_css . '" href="' . $item->flink . '"' . $title . $item->rel . '>' . $linktype . '</a>'.$closetag;
                break;
            case 'separator':
                echo $opentag.'<span class="separator ' . $item->anchor_css . '">' . $linktype . '</span>'.$closetag;
                break;
            case 'url':
            case 'component':
                switch ($item->browserNav) :
                    default:
                    case 0:
                        echo $opentag.'<a class="' . $item->anchor_css . '" href="' . $item->flink . '"' . $title . $item->rel . '>' . $linktype . '</a>'.$closetag;
                        break;
                    case 1:
                        // _blank
                        echo $opentag.'<a class="' . $item->anchor_css . '" href="' . $item->flink . '" target="_blank" ' .$title . $item->rel . '>' . $linktype . '</a>'.$closetag;
                        break;
                    case 2:
                        // window.open
                        //$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,'.$this->_params->get('window_open');
                        echo $opentag.'<a class="' . $item->anchor_css . '" href="' . $item->flink . '&tmpl=component" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes\');return false;" ' . $title . $item->rel . '>' . $linktype . '</a>'.$closetag;
                        break;
                endswitch;
                break;
        endswitch;
    }

	// The next item is deeper.
	if ($item->deeper) {
		echo '<ul>';
	}
	// The next item is shallower.
	elseif ($item->shallower) {
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	// The next item is on the same level.
	else {
		echo '</li>';
	}
endforeach;
?></ul>
