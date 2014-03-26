<?php

/**
* VirtueMart Source class
* @package News Show Pro GK4
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 4.0.0 $
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

class NSP_GK4_VM_Source {
	// Method to get sources of articles
	function getSources($config) {
      	$db = JFactory::getDBO();
		// if source type is section / sections
		$source = false;
		$where1 = '';
		$where2 = '';
		//
        $lang = '';
        
        // get front-end language
        jimport('joomla.language.helper');
        $languages = JLanguageHelper::getLanguages('lang_code');
		$siteLang = JFactory::getLanguage()->getTag();
		$lang = strtolower(strtr($siteLang,'-','_'));
        // small validation 
        if($lang == '') $lang = 'en_gb';

        
		if($config['data_source'] == 'vm_categories'){
			$source = $config['vm_categories'];
			$where1 = ' c.virtuemart_category_id = ';
			$where2 = ' OR c.virtuemart_category_id = ';
		} else {
			$source = strpos($config['vm_products'],',') !== false ? explode(',', $config['vm_products']) : $config['vm_products'];
			$where1 = ' content.virtuemart_product_id = ';
			$where2 = ' OR content.virtuemart_product_id = ';	
		}
		//	
		$where = ''; // initialize WHERE condition
		// generating WHERE condition
		for($i = 0;$i < count($source);$i++){
			if(count($source) == 1) $where .= ($i == 0) ? $where1.$source[0] : $where2.$source[0];
			else $where .= ($i == 0) ? $where1.$source[$i] : $where2.$source[$i];		
		}
        
		//
		$query_name = '
			SELECT DISTINCT 
				c.virtuemart_category_id AS ID,  
				c.category_name AS name
			FROM 
				#__virtuemart_product_categories AS cx
			LEFT JOIN 
                #__virtuemart_categories_'.$lang.' AS c
                ON
                cx.virtuemart_category_id = c.virtuemart_category_id
			LEFT JOIN 
				#__virtuemart_products_'.$lang.' AS content 
				ON 
				cx.virtuemart_product_id = content.virtuemart_product_id 
            LEFT JOIN
                #__virtuemart_categories AS cat
                ON
                c.virtuemart_category_id = cat.virtuemart_category_id
			WHERE 
				( '.$where.' ) 
		';
        
		// Executing SQL Query
		$db->setQuery($query_name);
		 
		return $db->loadObjectList();
        
        
	}
	// Method to get articles in standard mode 
	function getProducts($categories, $config, $amount) {	
        
        // get front-end language
        $languages = JLanguageHelper::getLanguages('lang_code');
		$siteLang = JFactory::getLanguage()->getTag();
		$lang = strtolower(strtr($siteLang,'-','_'));
        // small validation 
        if($lang == '') $lang = 'en_gb';
    
        
		$sql_where = '';
		//
		if($categories) {				
			$j = 0;
			// getting categories ItemIDs
			foreach ($categories as $item) {
				$sql_where .= ($j != 0) ? ' OR category.virtuemart_category_id = '.$item->ID : ' category.virtuemart_category_id = '.$item->ID;
				$j++;
			}	
		}		
		// Arrays for content
		$content_id = array();
		$content_cid = array();
		$content_title = array();
		$content_text = array();
		$content_date = array();
		$content_date_publish = array();
		$content_price = array();
		$content_price_currency = array();
		$content_discount_amount = array();
		$content_discount_is_percent = array();
        $content_discount_start = array();
		$content_discount_end = array();
        $content_tax = array();
		$content_cat_name = array();
		$content_manufacturer = array();
		$content_manufacturer_id = array();
		$content_product_image = array();
		$news_amount = 0;
		// Initializing standard Joomla classes and SQL necessary variables
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$aid = $user->get('aid', 0);
		$date = JFactory::getDate("now", $config['time_offset']);
		$now  = $date->toMySQL();
		$nullDate = $db->getNullDate();
		// Overwrite SQL query when user set IDs manually
		if($config['data_source'] == 'vm_products' && $config['vm_products'] != ''){
			// initializing variables
			$sql_where = '';
			$ids = explode(',', $config['vm_products']);
			//
			for($i = 0; $i < count($ids); $i++ ){	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.virtuemart_product_id = '.$ids[$i] : ' content.virtuemart_product_id = '.$ids[$i];
			}
		}		
		// if some data are available
		if(count($categories) > 0){
			// when showing only frontpage articles is disabled
			$featured_con = ($config['only_frontpage'] == 0) ? (($config['news_frontpage'] == 0) ? ' AND contentR.product_special = \'1\' ' : '' ) : ' AND contentR.product_special = \'1\' ';
			$since_con = '';
			if($config['news_since'] !== '') $since_con = ' AND contentR.created_on >= ' . $db->Quote($config['news_since']);
			// Ordering string
			$order_options = '';
			// When sort value is random
			if($config['news_sort_value'] == 'random') {
				$order_options = ' RAND() '; 
			}else{ // when sort value is different than random
                $sort_value = '';
                if($config['news_sort_value'] == 'created') $sort_value = 'created_on';
                else if($config['news_sort_value'] == 'title') $sort_value = 'product_name';
                else $sort_value = 'virtuemart_product_id';
                
                if($config['news_sort_value'] == 'title') { $order_options = ' content.'.$sort_value.' '.$config['news_sort_order'].' '; }
				else { $order_options = ' contentR.'.$sort_value.' '.$config['news_sort_order'].' '; }
			}	
			//
			$shopper_group_con = '';
			//
            if($config['vm_shopper_group'] != -1) {
                $shopper_group_con = ' AND sgroup.virtuemart_shoppergroup_id = ' . $config['vm_shopper_group'] . ' ';
			}
			//
			$out_of_stock_con = '';
			//
			if($config['vm_out_of_stock'] != 1) {
                $out_of_stock_con = ' AND contentR.product_in_stock > 0 ';
			}
			// creating SQL query
			$query_news = '
			SELECT DISTINCT
                content.virtuemart_product_id AS ID,
                content.product_name AS title,
                content.product_desc AS text,
                contentR.modified_on AS date,
                contentR.created_on AS date_publish,
				manufacturer.mf_name AS manufacturer,
				manufacturer.virtuemart_manufacturer_id AS manufacturer_id
			FROM 
				#__virtuemart_products_'.$lang.' AS content 
                LEFT JOIN
                    #__virtuemart_product_categories AS category
                    ON
                    category.virtuemart_product_id = content.virtuemart_product_id
                
                LEFT JOIN
                    #__virtuemart_product_manufacturers AS manufacturer_x
                    ON
                    content.virtuemart_product_id = manufacturer_x.virtuemart_product_id
                LEFT JOIN
                    #__virtuemart_manufacturers_'.$lang.' AS manufacturer
                    ON
                    manufacturer_x.virtuemart_manufacturer_id = manufacturer.virtuemart_manufacturer_id
                LEFT JOIN
                    #__virtuemart_products AS contentR	
                    ON
                    contentR.virtuemart_product_id = content.virtuemart_product_id
                LEFT JOIN
                    #__virtuemart_product_shoppergroups AS psgroup
                    ON 
                    psgroup.virtuemart_product_id = content.virtuemart_product_id
                LEFT JOIN
                    #__virtuemart_shoppergroups AS sgroup
                    ON 
                    sgroup.virtuemart_shoppergroup_id = psgroup.virtuemart_shoppergroup_id
			WHERE
                contentR.product_parent_id = 0
                AND contentR.published = \'1\'  
				AND ( '.$sql_where.' ) 
				'.$featured_con.' 
				'.$since_con.'
				'.$shopper_group_con.'
				'.$out_of_stock_con.'
			ORDER BY 
				'.$order_options.'
			LIMIT
				'.($config['startposition']).','.$amount.';
			';

			// run SQL query
			$db->setQuery($query_news);
            
			// when exist some results
			if($news = $db->loadObjectList()) {
				// generating tables of news data
				foreach($news as $item) {		
				  
                    $content_id[] = $item->ID;
                    $content_cid[] = 'null';
                    $content_title[] = $item->title;
                    $content_text[] = $item->text;
                    $content_date[] = $item->date;
                    $content_date_publish[] = $item->date_publish;
                    $content_price[] = '';
                    $content_price_currency[] = '';
                    $content_discount_amount[] = '';
                    $content_discount_is_percent[] = '1';
                    $content_discount_start[] ='';
                    $content_discount_end[] = '';
                    $content_tax[] = '';
                    $content_cat_name[] = '';
                    $content_manufacturer[] = $item->manufacturer;
                    $content_manufacturer_id[] = $item->manufacturer_id;
                    $news_amount++;
				}
			}
			$sql_where2 = '';
			// generating IDs			
			for($i = 0; $i < count($content_id); $i++ ){	
				// linking string with content IDs
				$sql_where2 .= ($i != 0) ? ' OR content.virtuemart_product_id = '.$content_id[$i] : ' content.virtuemart_product_id = '.$content_id[$i];
			}
			// creating SQL query
			$query_news2 = '
			SELECT DISTINCT
			    content.virtuemart_product_id AS ID,
				cat.virtuemart_category_id AS CID,
				cat.category_name AS cat_name
			FROM 
				#__virtuemart_products AS content 
				LEFT JOIN 
					#__virtuemart_product_categories AS category_xref
					ON 
			        category_xref.virtuemart_product_id = content.virtuemart_product_id 
				LEFT JOIN 
					#__virtuemart_categories AS category 
					ON 
			        category_xref.virtuemart_category_id = category.virtuemart_category_id 	
                LEFT JOIN
                    #__virtuemart_categories_'.$lang.' AS cat
                    ON
                    category_xref.virtuemart_category_id = cat.virtuemart_category_id
			WHERE
				('.$sql_where2.')
				AND category.published = \'1\' 
			ORDER BY 
				content.virtuemart_product_id ASC
			';
			// run second SQL query
			$db->setQuery($query_news2);

			// when exist some results
			if($news = $db->loadObjectList()) {
				$max = count($content_id);	
				// generating tables of news data
				foreach($news as $item) {
					for($i = 0; $i < $max; $i++) {									
                        if($content_id[$i] == $item->ID) {
			        		$content_cid[$i] = $item->CID;
			        		$content_cat_name[$i] = $item->cat_name;
			        	} 
			        }
				}
			}

          $query2 = $db->getQuery(true);
          $query2->select('`m`.`file_url` AS `file`, `content`.`virtuemart_product_id` AS `tid`');
          $query2->from('#__virtuemart_products AS content');
          $query2->leftJoin('#__virtuemart_product_medias AS `pm` ON `pm`.`virtuemart_product_id` = `content`.`virtuemart_product_id`');
          $query2->leftJoin('#__virtuemart_medias AS `m` ON `m`.`virtuemart_media_id` = `pm`.`virtuemart_media_id`');
          $query2->where($sql_where2);
          $query2->order('`pm`.`ordering` ASC');
          $db->setQuery((string)$query2);
          $pimages = $db->loadObjectList();
          $images = array();

          // get the first products images
          if ($pimages) {
            foreach($pimages as $image) {
                if(!isset($images[$image->tid])) {
                    $images[$image->tid] = $image->file;
                }
            }
          }
          
          for($i=0; $i < count($content_id); $i++) {
            $content_product_image[$i] = $images[$content_id[$i]];
          }
          
		}
        
               
		// Returning data in hash table
		return array(
			"ID" => $content_id,
			"CID" => $content_cid,
			"title" => $content_title,
			"text" => $content_text,
			"date" => $content_date,
			"date_publish" => $content_date_publish,
			"price" => $content_price,
			"price_currency" => $content_price_currency,
			"discount_amount" => $content_discount_amount,
			"discount_is_percent" => '1',
            "discount_start" => $content_discount_start,
			"discount_end" => $content_discount_end,
			"tax" => $content_tax,
            "cat_name" => $content_cat_name,
			"manufacturer" => $content_manufacturer,
			"manufacturer_id" => $content_manufacturer_id,
			"product_image" => $content_product_image,
			"news_amount" => $news_amount
		);
	}
    
	// Method to get amount of the product comments 
	function getComments($content, $config) {
		// 
		$db =& JFactory::getDBO();
		$counters_tab = array();
		// 
		if(count($content) > 0) {
			// initializing variables
			$sql_where = '';
			$ids = $content['ID'];
			//
			for($i = 0; $i < count($ids); $i++ ) {	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.product_id = '.$ids[$i] : ' content.product_id = '.$ids[$i];
			}
			// creating SQL query
			$query_news = '
			SELECT 
				content.product_id AS id,
				COUNT(comments.product_id) AS count			
			FROM 
				#__vm_product AS content 
				LEFT JOIN 
					#__vm_product_reviews AS comments
					ON 
                    comments.product_id = content.product_id 		
			WHERE 
				comments.published
				AND ( '.$sql_where.' ) 
			GROUP BY 
				comments.product_id
			;';
			// run SQL query
			$db->setQuery($query_news);
			// when exist some results
			if($counters = $db->loadObjectList()) {
				// generating tables of news data
				foreach($counters as $item) {						
					$counters_tab['product'.$item->id] = $item->count;
				}
			}
		}

		return $counters_tab;
	}	
}

/* EOF */