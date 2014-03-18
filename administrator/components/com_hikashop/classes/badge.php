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
class hikashopBadgeClass extends hikashopClass {
	var $tables = array('badge');
	var $pkeys = array('badge_id');
	var $toggle = array('badge_published'=>'badge_id');

	function saveForm() {
		$element = new stdClass();
		$element->badge_id = hikashop_getCID('badge_id');
		$formData = JRequest::getVar( 'data', array(), '', 'array' );
		foreach($formData['badge'] as $column => $value) {
			hikashop_secureField($column);
			$element->$column = strip_tags($value);
		}
		if(!empty($element->badge_start)){
			$element->badge_start = hikashop_getTime($element->badge_start);
		}
		if(!empty($element->badge_end)){
			$element->badge_end = hikashop_getTime($element->badge_end);
		}
		$class = hikashop_get('class.file');
		$element->badge_image=$class->saveFile();
		if(empty($element->badge_image))
			unset($element->badge_image);
		$status = $this->save($element);

		return $status;
	}

	function loadBadges(&$row) {
		$discount=new stdClass();
		$qty = 0;
		if(isset($row->main)){
			if(@$row->main->discount) $discount =& $row->main->discount;
			$product_id = $row->main->product_id;
			$qty = $row->main->product_quantity;
		}else{
			if(@$row->discount) $discount =& $row->discount;
			$product_id = $row->product_id;
			$qty = $row->product_quantity;
		}
		$badge_filters=array('a.badge_start <= '.time(),'( a.badge_end >= '.time().' OR a.badge_end =0 )','a.badge_published=1','(a.badge_quantity=\'\' OR a.badge_quantity='.(int)$qty.')');
		if($discount){
			$badge_filters[]='(badge_discount_id='.(int)@$discount->discount_id.' OR badge_discount_id=0 )';
		}else{
			$badge_filters[]='badge_discount_id=0 ';
		}


		$categories=array();
		$categoryClass = hikashop_get('class.category');
		$productClass = hikashop_get('class.product');
		$loadedCategories = $productClass->getCategories($product_id);

		if(!empty($loadedCategories)){
			foreach($loadedCategories as $cat){
				$categories['originals'][$cat]=$cat;
			}
		}

		$parents = $categoryClass->getParents($loadedCategories);
		if(!empty($parents) && is_array($parents)) {
			foreach($parents as $parent) {
				$categories['parents'][$parent->category_id] = $parent->category_id;
			}
		}

		$badge_filters = implode(' AND ',$badge_filters);

		if(!empty($categories)) {
			$categories_filter = array('AND ((badge_category_childs = 0 AND (badge_category_id = 0');
			if(!empty($categories['originals'])) {
				foreach($categories['originals'] as $cat) {
					$categories_filter[] = 'badge_category_id = '.(int)$cat;
				}
			}
			$badge_filters .= implode(' OR ',$categories_filter).'))';

			$categories_filter = array('OR (badge_category_childs = 1 AND (badge_category_id=0');
			if(!empty($categories['parents'])) {
				foreach($categories['parents'] as $cat) {
					$categories_filter[] = 'badge_category_id = '.(int)$cat;
				}
			}
			$badge_filters .= implode(' OR ',$categories_filter).')))';
		}

		static $badges = array();
		$key = sha1($badge_filters);
		if(!isset($badges[$key])){
			$query = ' FROM '.hikashop_table('badge').' AS a WHERE '.$badge_filters.' ORDER BY a.badge_ordering ASC,a.badge_id ASC';
			$this->database->setQuery('SELECT a.*'.$query);
			$badges[$key] = $this->database->loadObjectList();
		}
		$row->badges = $badges[$key];
	}

	function placeBadges(&$image, &$badges, $vertical, $horizontal, $echo = true){
		if(empty($badges))
			return;
		$position1 = 0;
		$position2 = 0;
		$position3 = 0;
		$position4 = 0;
		$backup_main_x = $image->main_thumbnail_x;
		$backup_main_y = $image->main_thumbnail_y;
		$width_real= $image->thumbnail_x;
		$height_real= $image->thumbnail_y;
		$html = '';

		foreach($badges as $badge){
			if($badge->badge_published == 1) {
				if(!empty($badge->badge_keep_size)){
					list($badge_width, $badge_height) = getimagesize($image->getPath(@$badge->badge_image,false));
				}else{
					$badge_width = intval(($width_real * $badge->badge_size) / 100);
					$badge_height = intval(($height_real * $badge->badge_size) / 100);
				}
				$position = $badge->badge_position;
				$position_top = $badge->badge_vertical_distance + $vertical;
				$position_right = $badge->badge_horizontal_distance + $horizontal;
				$position_left = $badge->badge_horizontal_distance + $horizontal;
				$position_bottom = $badge->badge_vertical_distance + $vertical;
				$styletopleft="position: absolute; z-index:2; top: ".$position_top."px; left: ".$position_left."px;margin-top:10px;";
				$styletopright="position: absolute; z-index:3; top: ".$position_top."px; right: ".$position_right."px;margin-top:10px;";
				$stylebottomleft="position: absolute; z-index:4; bottom: ".$position_bottom."px; left: ".$position_left."px;margin-bottom:10px;";
				$stylebottomright="position: absolute; z-index:5; bottom: ".$position_bottom."px; right: ".$position_right."px;margin-bottom:10px;";
				if(!empty($badge->badge_url)){
					$imageDisplayed='<a href="'.hikashop_cleanURL($badge->badge_url).'">'. $image->display(@$badge->badge_image,false,@$badge->badge_name,'','', $badge_width, $badge_height).'</a>';
				}else{
					$imageDisplayed=$image->display(@$badge->badge_image,false,@$badge->badge_name,'','', $badge_width, $badge_height);
				}
				if($position == 'topleft' && ($position1 == 0 || $badge->badge_ordering < $position1)) {
					$html .= '<div class="hikashop_badge_topleft_div" style="' . $styletopleft . '">'.$imageDisplayed.'</div>';
					$position1 = $badge->badge_ordering;
				}
				elseif($position == 'topright' && ($position2 == 0 || $badge->badge_ordering < $position2)) {
					$html .= '<div class="hikashop_badge_topright_div" style="' . $styletopright . '">'.$imageDisplayed.'</div>';
					$position2 = $badge->badge_ordering;
				}
				elseif($position == 'bottomright' && ($position3 == 0 || $badge->badge_ordering < $position3)) {
					$html .= '<div class="hikashop_badge_bottomright_div" style="' . $stylebottomright . '">'.$imageDisplayed.'</div>';
					$position3 = $badge->badge_ordering;
				}
				elseif($position == 'bottomleft' && ($position4 == 0 || $badge->badge_ordering < $position4)) {
					$html .= '<div class="hikashop_badge_bottomleft_div" style="' . $stylebottomleft . '">'.$imageDisplayed.'</div>';
					$position4 = $badge->badge_ordering;
				}
			}
		}
		$image->main_thumbnail_x = $backup_main_x;
		$image->main_thumbnail_y = $backup_main_y;
		if($echo)
			echo $html;
		else
			return $html;
	}

	function save(&$element) {
		$isNew = empty($element->badge_id);
		$status = parent::save($element);
		if(!$status) {
			return false;
		}
		if($isNew) {
			$element->badge_id = $status;
			$orderClass = hikashop_get('helper.order');
			$orderClass->pkey = 'badge_id';
			$orderClass->table = 'badge';
			$orderClass->orderingMap = 'badge_ordering';
			$orderClass->reOrder();
		}
		return $status;
	}
}
