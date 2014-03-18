<?php
/**
 * @copyright	Copyright (C) 2011 C�dric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Accordeon CK
 * @license		GNU/GPL
 * Adapted from the original mod_menu on Joomla.site - Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * */

// No direct access.
defined('_JEXEC') or die;
$start = (int) $params->get('startLevel');
// Note. It is important to remove spaces between elements.
?>
<div class="accordeonck <?php echo $params->get('moduleclass_sfx'); ?>">
<ul class="menu<?php echo $class_sfx;?>" id="<?php echo $menuID; ?>">
<?php
foreach ($list as $i => &$item) :
	$itemlevel = ($start > 1) ? $item->level - $start + 1 : $item->level;
	$class = $item->classe;

	if ($item->deeper) {
		$class .= ' parent';
	}

	$class .= ' level' . $itemlevel;
	
	if (!empty($class)) {
		$class = ' class="accordeonck '.trim($class) .' '.$item->liclass.'"';
	}

	echo '<li id="item-'.$item->id.'"'.$class.' data-level="' . $itemlevel . '">';
        
	if ($item->content) {
        echo $item->content;
    } else {
		$style= '';
		$imageevent = "";
		// Note. It is important to remove spaces between elements.
		if ($item->deeper AND $params->get('eventtarget') == 'link') {
			$class = 'toggler toggler_'.($item->level-($params->get('startLevel')-1)).' '.$item->anchor_css.' ';
			if ($params->get('eventtype') == 'click')
				$item->flink = 'javascript:void(0);';
		} elseif($item->deeper AND $params->get('eventtarget') == 'image') {
			$class = $item->anchor_css ? $item->anchor_css.' ' : '';
			$imageevent = "<img src=\"".JURI::base(true) . '/' . $params->get('imageplus', 'modules/mod_accordeonck/assets/plus.png') . "\" class=\"toggler toggler_".($item->level-($params->get('startLevel')-1)) . "\" align=\"" . $imageposition . "\"/>";
		} else {
			$class = $item->anchor_css ? $item->anchor_css.' ' : '';
		}

		if (	$item->type == 'alias' &&
					in_array($item->params->get('aliasoptions'),$path)
				||	in_array($item->id, $path)) {
			  $class .= 'isactive ';
			}
			
			$class = (isset($class) AND $class) ? 'class="' . $class . '" ' : '';
			
		$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';
		if ($item->menu_image) {
				$menu_image_split = explode('.', $item->menu_image);
				$imagerollover = '';
				if (isset($menu_image_split[1])) {
					// manage active image
					if (isset($item->isactive) AND $item->isactive) {
						$menu_image_active = $menu_image_split[0] . $params->get('imageactiveprefix', '_active') . '.' . $menu_image_split[1];
						if (JFile::exists(JPATH_ROOT . '/' . $menu_image_active)) {
							$item->menu_image = $menu_image_active;
						}
					}
					// manage hover image
					$menu_image_hover = $menu_image_split[0] . $params->get('imagerollprefix', '_hover') . '.' . $menu_image_split[1];
					if (isset($item->isactive) AND $item->isactive AND JFile::exists(JPATH_ROOT . '/' . $menu_image_split[0] . $params->get('imageactiveprefix', '_active') . $params->get('imagerollprefix', '_hover') . '.' . $menu_image_split[1])) {
						$imagerollover = ' onmouseover="javascript:this.src=\'' . JURI::base(true) . '/' . $menu_image_split[0] . $params->get('imageactiveprefix', '_active') . $params->get('imagerollprefix', '_hover') . '.' . $menu_image_split[1] . '\'" onmouseout="javascript:this.src=\'' . JURI::base(true) . '/' . $item->menu_image . '\'"';
					} else if (JFile::exists(JPATH_ROOT . '/' . $menu_image_hover)) {
						$imagerollover = ' onmouseover="javascript:this.src=\'' . JURI::base(true) . '/' . $menu_image_hover . '\'" onmouseout="javascript:this.src=\'' . JURI::base(true) . '/' . $item->menu_image . '\'"';
					}
				}
							
				if ($params->get('imgalignement', 'none') != 'none') {
					$imgalignement = ( $params->get('imgalignement') == 'left' ) ? ' align="left"' : ' align="right"' ;
				} else {
					$imgalignement = '';
				}
				$item->params->get('menu_text', 1 ) ? 
				$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->ftitle.'"'. $imgalignement . $imagerollover .' /><span class="image-title">'.$item->ftitle.$item->desc.'</span> ' :
				$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->ftitle.'"'. $imgalignement . $imagerollover .' />';
		} 
		else { 
			$linktype = $item->ftitle.$item->desc;
		}
		// Render the menu item.
		switch ($item->type) :
			case 'separator':
				echo $imageevent; ?><a <?php echo $class.$item->rel; ?>href="javascript:void(0);"<?php echo $style; ?>><span class="separator"><?php echo $linktype; ?></span></a><?php
				break;
			case 'url':
			case 'component':
			default:
				switch ($item->browserNav) :
						default:
						case 0:
							echo $imageevent; ?><a <?php echo $class.$item->rel; ?>href="<?php echo $item->flink; ?>" <?php echo $title.$style; ?>><?php echo $linktype; ?></a><?php
							break;
						case 1:
							// _blank
							echo $imageevent; ?><a <?php echo $class.$item->rel; ?>href="<?php echo $item->flink; ?>" target="_blank" <?php echo $title.$style; ?>><?php echo $linktype; ?></a><?php
							break;
						case 2:
							// window.open
							$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,'.$params->get('window_open');
							echo $imageevent; ?><a <?php echo $class.$item->rel; ?>href="<?php echo $item->flink; ?>" onclick="window.open(this.href,'targetWindow','<?php echo $attribs;?>');return false;" <?php echo $title.$style; ?>><?php echo $linktype; ?></a><?php
							break;
					endswitch;	
				break;
		endswitch;
	}
	

	// The next item is deeper.
	if ($item->deeper) {
        $ulstyles = (!$item->isactive && $params->get('defaultopenedid') != $item->id) ? 'display:none;' : '';
		echo '<ul class="content_'.($item->level-($params->get('startLevel')-1)).'" style="'.$ulstyles.'">';
		// echo '<ul class="content_'.($item->level-($params->get('startLevel')-1)).'">';
	}
	// The next item is shallower.
	else if ($item->shallower) {
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	// The next item is on the same level.
	else {
		echo '</li>';
	}
endforeach;
?></ul></div>
