<?php

/**
* News class
* @package News Show Pro GK4
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.0 $
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

class NSP_GK4_Redshop_Source {	
	// Method to get sources of articles
	
	function getSources($config) {
		//
		
		$db = JFactory::getDBO();
		// if source type is section / sections
		$source = false;
		$where1 = '';
		$where2 = '';
		//
		if($config['data_source'] == 'redshop_categories'){
			$source = $config['redshop_categories'];
			$where1 = ' c.category_id = ';
			$where2 = ' OR c.category_id = ';
		} else {
			$source = strpos($config['redshop_products'],',') !== false ? explode(',', $config['redshop_products']) : $config['redshop_products'];
			$where1 = ' content_xref.category_id = ';
			$where2 = ' OR content_xref.category_id = ';
		} 

		// generating WHERE condition 
        $where = ''; // initialize WHERE condition
       	// generating WHERE condition
       	for($i = 0;$i < count($source);$i++){
       		if(count($source) == 1) $where .= (is_array($source)) ? $where1.$source[0] : $where1.$source;
       		else $where .= ($i == 0) ? $where1.$source[$i] : $where2.$source[$i];		
       	}
        if($config['data_source'] != 'all_redshop_products') {
		//
		$query_name = '
			SELECT DISTINCT 
				c.category_id AS CID,
                c.category_name AS cat_name
			FROM 
				#__redshop_category AS c
			LEFT JOIN 
				#__redshop_product_category_xref AS content_xref 
				ON 
				c.category_id = content_xref.category_id 	
			WHERE 
				( '.$where.' ) 
				AND 
				c.published = 1
            ';	
        } else {
            $query_name = '
			SELECT DISTINCT 
				c.category_id AS CID,
                c.category_name AS cat_name
			FROM 
				#__redshop_category AS c
			LEFT JOIN 
				#__redshop_product_category_xref AS content_xref 
				ON 
				c.category_id = content_xref.category_id 	
			AND 
				c.published = 1
            ';	
        }
		// Executing SQL Query
		$db->setQuery($query_name);
		//
		return $db->loadObjectList();
	}

	// Method to get articles in standard mode 
	function getArticles($categories, $config, $amount) {	
		//
        
		$sql_where = '';
		//
		if($categories) {		
			$j = 0;
			// getting categories ItemIDs
            if($config['data_source'] == 'redshop_categories' ){
    			foreach ($categories as $item) {
    				$sql_where .= ($j != 0) ? ' OR category.category_id = '.$item->CID : ' category.category_id = '.$item->CID;
    				$j++;
    			}	
            } else {
                foreach ($categories as $item) {
    				$sql_where .= ($j != 0) ? ' OR category.category_id = '.$item->CID : 'category.category_id = '.$item->CID;
    				$j++;
    			}
                $sql_where = 'AND ('.$sql_where.' )';
            }
		}
		// Overwrite SQL query when user set IDs manually
		if($config['data_source'] == 'redshop_products' && $config['redshop_products'] != ''){
			// initializing variables
			$sql_where = '';
			$ids = explode(',', $config['redshop_products']);
			//
			for($i = 0; $i < count($ids); $i++ ){	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.product_id = '.$ids[$i] : ' content.product_id = '.$ids[$i];
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
        $content_discount_price = array();
        $content_discount_start = array();
		$content_discount_end = array();
        $content_tax = array();
		$content_cat_name = array();
		$content_manufacturer = array();
		$content_manufacturer_id = array();
		$content_product_image = array();
        $content_manufacturer_id = array();
		$news_amount = 0;
		// Initializing standard Joomla classes and SQL necessary variables
		$db = JFactory::getDBO();
		$date = JFactory::getDate("now", $config['time_offset']);
		$now  = $date->toMySQL();
		$nullDate = $db->getNullDate();
		// if some data are available
		if(count($categories) > 0 || $config['data_source'] == 'redshop_products') {
			// when showing only frontpage articles is disabled
			$since_con = '';
			if($config['news_since'] !== '') $since_con = ' AND content.publish_date >= ' . $db->Quote($config['news_since']);
			// Ordering string
			$order_options = '';
			// When sort value is random
			if($config['news_sort_value'] == 'random') {
				$order_options = ' RAND() '; 
            }
			else if($config['news_sort_value'] == 'created') {
                $order_options = ' content.publish_date';
			}				
            else if($config['news_sort_value'] == 'title') {
                $order_options = ' content.product_name ';
            }
            else if($config['news_sort_value'] == 'fordering' || $config['news_sort_value'] == 'ordering') {
                $order_options = ' content.product_id ';     
            }
            else {
                $order_options = ' content.visited ';
            }
            
			if($config['data_source'] != 'all_redshop_products') {
				$sql_where = ' AND ( ' . $sql_where . ' ) ';
			}
            //
			$shopper_group_con = '';
            if($config['rs_shopper_group'] != -1) {
                $shopper_group_con = ' AND price.shopper_group_id = ' . $config['rs_shopper_group'] . ' ';
			}
			$out_of_stock_con = '';
			if($config['rs_out_of_stock'] != 1) {
                $out_of_stock_con = ' AND content.product_in_stock > 0 ';
			}
 		    
            // creating SQL query
			$query_news = '
            SELECT DISTINCT
                content.product_id AS ID,
                category.category_id AS CID,
                content.product_name AS title,
                content.product_s_desc AS text,
                content.update_date AS date,
                content.publish_date AS date_publish,
                content.product_price AS price,
                content.discount_price AS discount_price,
                content.discount_stratdate AS discount_start,
                content.discount_enddate AS discount_end,
                tax.tax_rate AS tax,
				category.category_name AS cat_name,
                manufacturer.manufacturer_id AS manufacturer_id,
				manufacturer.manufacturer_name AS manufacturer,
				content.manufacturer_id AS manufacturer_id,
                content.product_full_image AS product_image
			FROM 
				#__redshop_product AS content 
				LEFT JOIN 
                    #__redshop_product_category_xref AS category_xref 
                    ON category_xref.product_id = content.product_id 
                LEFT JOIN 
                    #__redshop_category AS category 
                    ON category_xref.category_id = category.category_id 
                LEFT JOIN 
                    #__redshop_product_price AS price 
                    ON price.product_id = content.product_id 
                LEFT JOIN 
                    #__redshop_manufacturer AS manufacturer 
                    ON manufacturer.manufacturer_id = content.manufacturer_id 
                LEFT JOIN 
                    #__redshop_tax_group AS tax_group 
                    ON tax_group.tax_group_id = content.product_tax_group_id
                LEFT JOIN
                    #__redshop_tax_rate AS tax 
                    ON tax.tax_group_id = tax_group.tax_group_id	
			WHERE
                content.product_parent_id = 0
                AND content.published = \'1\'   
				'.$sql_where.'
			ORDER BY 
				'.$order_options.'
			LIMIT
				'.($config['startposition']).','.($amount + (int)$config['startposition']).';
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
                    $content_price[] = $item->price;
                    $content_discount_price[] = $item->discount_price;
                    $content_discount_start[] = $item->discount_start;
                    $content_discount_end[] = $item->discount_end;
                    $content_tax[] = $item->tax;
                    $content_cat_name[] = '';
                    $content_manufacturer[] = $item->manufacturer;
                    $content_manufacturer_id[] = $item->manufacturer_id;
                    $content_product_image[] = $item->product_image;
                    $content_manufacturer_id[] = $item->manufacturer_id;
                    $news_amount++;
				}
			}
            $sql_where='';
            // generating IDs			
			for($i = 0; $i < count($content_id); $i++ ){	
				// linking string with content IDs
				$sql_where .= ($i != 0) ? ' OR content.product_id = '.$content_id[$i] : ' content.product_id = '.$content_id[$i];
			}
			// creating SQL query
			$query_news2 = '
			SELECT DISTINCT
			    content.product_id AS ID,
				category.category_id AS CID,
				category.category_name AS cat_name
			FROM 
				#__redshop_product AS content 
				LEFT JOIN 
					#__redshop_product_category_xref AS category_xref
					ON 
			        category_xref.product_id = content.product_id 
				LEFT JOIN 
					#__redshop_category AS category 
					ON 
			        category_xref.category_id = category.category_id 	
			WHERE
				('.$sql_where.')
				AND category.published = \'1\' 
			ORDER BY 
				content.product_id ASC
			';
			// run second SQL query
			$db->setQuery($query_news2);
			// when exist some results
			if($news = $db->loadObjectList()) {
				$max = count($content_id);	
				// generating tables of news data
				foreach($news as $item) {	
					for($i = 0; $i < $max; $i++) {
			        	if($content_id[$i] == $item->ID && $content_cid[$i] === 'null') {
                            $content_cid[$i] = $item->CID;
  					      $content_cat_name[$i] = $item->cat_name;
			        	}
			        }
				}
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
            "discount_price" => $content_discount_price,
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
}

/* EOF */