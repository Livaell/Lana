<?php

/**
* Helper file
* @package News Show Pro GK4
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.3 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');
// import com_content route helper
require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');



// import JString class for UTF-8 problems
jimport('joomla.utilities.string'); 
jimport('joomla.application.component.helper');
// Main class
class NSP_GK4_Helper {
	var $config = array(); // configuration array
	var $content = array(); // array with generated content
	var $module_id = 0; // module id used in JavaScript
	var $k2store_params = 0; // params of K2Store component
	// module initialization
	function init($module, $params) {  
	
		// getting module ID - automatically (from Joomla! database) or manually
		$this->module_id = ($params->get('automatic_module_id',0) == 1) ? 'nsp_'.$module->id : $params->get('module_unique_id',0);
		$this->config['module_id'] = $this->module_id;
		// module dimensions
		$this->config["module_width"] = $params->get("module_width", 100);
		$this->config["links_margin"] = $params->get("links_margin", "0");    
 		$this->config["links_position"] = $params->get("links_position", "bottom");
 		$this->config["links_width"] = $params->get("links_width", 0);  
		// source settings
		$this->config["data_source"] = $params->get("data_source", "com_categories");
		$this->config["com_categories"] = $params->get("com_categories",'');
		$this->config["com_articles"] = $params->get("com_articles",'');
        $this->config["k2_categories"] = $params->get("k2_categories",'');
		$this->config["k2_tags"] = $params->get("k2_tags",'');
        $this->config["k2_articles"] = $params->get("k2_articles",'');
		$this->config['news_sort_value'] = $params->get('news_sort_value','created'); // Parameter for SQL Query - value of sort	
		$this->config['news_sort_order'] = $params->get('news_sort_order','DESC'); // Parameter for SQL Query - sort direct
		$this->config['news_frontpage'] = $params->get('news_frontpage',1);
		$this->config['unauthorized'] = $params->get('unauthorized', 0);
		$this->config['only_frontpage'] = $params->get('only_frontpage', 0);
		$this->config['startposition'] = $params->get('startposition', 0);
		// Settings of source amount
		$this->config['news_full_pages'] = $params->get('news_full_pages', 3); // max. amount of full articles to load
		$this->config['news_short_pages'] = $params->get('news_short_pages', 3); // max. amount of links to articles to load
		$this->config['news_column'] = $params->get('news_column', 1); // amount of news columns
		$this->config['news_rows'] = $params->get('news_rows', 1); // amount of news rows 
		$this->config['art_padding'] = $params->get('art_padding', '0 20px 20px 0'); // article block padding 	
		$this->config['links_amount'] = $params->get('links_amount', 3); // amount of links
		// Interface settings
		$this->config['top_interface_style'] = $params->get('top_interface_style','arrows');
		$this->config['bottom_interface_style'] = $params->get('bottom_interface_style','arrows');
		// Content settings
		$this->config['news_header_link'] = $params->get('news_header_link', 1); // add link to header ? (boolean)
		$this->config['news_image_link'] = $params->get('news_image_link', 1); // add link to image ? (boolean)
		$this->config['news_text_link'] = $params->get('news_text_link', 0); // add link to text ? (boolean)
		$this->config['info_format'] = $params->get('info_format', '%COMMENTS %DATE %HITS %CATEGORY %AUTHOR'); // date format
		$this->config['info2_format'] = $params->get('info2_format', ''); // date format
		$this->config['category_link'] = $params->get('category_link', 1); // showing category name
		$this->config['date_format'] = $params->get('date_format', '%d %b %Y'); // date format
		$this->config['date_publish'] = (bool) $params->get('date_publish', 0); // date publish or create ?
		$this->config['username'] = $params->get('username', 0);
		$this->config['user_avatar'] = $params->get('user_avatar', 1);
		$this->config['avatar_size'] = $params->get('avatar_size', 16);
		// Content positions
		$this->config['news_content_header_pos'] = $params->get('news_content_header_pos', 'left'); // text-align for news header
		$this->config['news_content_image_pos'] = $params->get('news_content_image_pos', 'left'); // text-align for news image
		$this->config['news_content_text_pos'] = $params->get('news_content_text_pos', 'left'); // text-align for news text
		$this->config['news_content_info_pos'] = $params->get('news_content_info_pos', 'left'); // text-align for news info
		$this->config['news_content_readmore_pos'] = $params->get('news_content_readmore_pos', 'right'); // text-align for news readmore button
		$this->config['news_content_info2_pos'] = $params->get('news_content_info2_pos', 'left'); // text-align for news info
		$this->config['news_content_header_float'] = $params->get('news_content_header_float', 'left'); // float for news header
		$this->config['news_content_image_float'] = $params->get('news_content_image_float', 'left'); // float for news image
		$this->config['news_content_text_float'] = $params->get('news_content_text_float', 'left'); // float for news text
		$this->config['news_content_info_float'] = $params->get('news_content_info_float', 'none'); // float for news info
		$this->config['news_content_info2_float'] = $params->get('news_content_info2_float', 'left'); // float for news info
		$this->config['news_header_order'] = $params->get('news_header_order', 1); // order of news header
		$this->config['news_image_order'] = $params->get('news_image_order', 3); // order of news image
		$this->config['news_text_order'] = $params->get('news_text_order', 4); // order of news text
		$this->config['news_info_order'] = $params->get('news_info_order', 2);
		$this->config['news_info2_order'] = $params->get('news_info2_order', 5);
		$this->config['news_header_enabled'] = $params->get('news_header_enabled', 1);
		$this->config['news_image_enabled'] = $params->get('news_image_enabled', 1);
		$this->config['news_text_enabled'] = $params->get('news_text_enabled', 1);
		$this->config['news_info_enabled'] = $params->get('news_info_enabled', 1);
		$this->config['news_info2_enabled'] = $params->get('news_info2_enabled', 1);
		$this->config['news_readmore_enabled'] = $params->get('news_readmore_enabled', 1);
		// Limits
		$this->config['news_limit_type'] = $params->get('news_limit_type', 'words'); // type of limit fo news text
		$this->config['news_limit'] = $params->get('news_limit', 30); // amount of limit "units"
		$this->config['title_limit_type'] = $params->get('title_limit_type', 'chars');
		$this->config['title_limit'] = $params->get('title_limit', 40); // amount of limit "units"
		$this->config['list_title_limit_type'] = $params->get('list_title_limit_type', 'words');
		$this->config['list_title_limit'] = $params->get('list_title_limit', 20); // amount of chars in list element title
		$this->config['list_text_limit_type'] = $params->get('list_text_limit_type', 'words'); 
		$this->config['list_text_limit'] = $params->get('list_text_limit', 30); // amount of chars in list element text		 	
		// Other content settings
		$this->config['clean_xhtml'] = $params->get('clean_xhtml', 1); // cleaning XHTML in news
		$this->config['more_text_value'] = $params->get('more_text_value','...'); // text overflow value
		$this->config['parse_plugins'] = (bool) $params->get('parse_plugins', 0);
		$this->config['clean_plugins'] = (bool) $params->get('clean_plugins', 0);
        $this->config['k2store_support'] = (bool) 0;
		// Thumbnails settings
		$this->config['create_thumbs'] = $params->get('create_thumbs', 0); // use generated thumbs
        $this->config['k2_thumbs'] = $params->get('k2_thumbs', 'first'); // use generated k2 thumbs
		$this->config['img_height'] = $params->get('img_height', 0); // image height
		$this->config['img_width'] = $params->get('img_width', 0); // image width
		$this->config['img_margin'] = $params->get('img_margin', '6px 14px 0 0'); // image margin
		$this->config['img_bg'] = $params->get('img_bg', '#000'); // image background
		$this->config['img_stretch'] = $params->get('img_stretch', 0); // image stretch
		$this->config['img_quality'] = $params->get('img_quality', 95); // image quality
		$this->config['cache_time'] = $params->get('cache_time', 30); // cache time
		// Animation settings
		$this->config['autoanim'] = (bool) $params->get('autoanim', 0); // autoanimation enabled ?
		$this->config['hover_anim'] = (bool) $params->get('hover_anim', 0); // hover animation enabled ?
		$this->config['animation_speed'] = $params->get('animation_speed', 400);
		$this->config['animation_interval'] = $params->get('animation_interval', 5000);
		$this->config['animation_function'] = $params->get('animation_function', 'Fx.Transitions.Expo.easeIn');
		// external file settings
		$this->config['useCSS'] = $params->get('useCSS', 1); 
		$this->config['useScript'] = $params->get('useScript', 2); // add script for this module to page 
		$this->config['counter_text'] = '<strong>'.JText::_('MOD_NEWS_PRO_GK4_NSP_PAGE').'</strong>';
		// new GK4 v.2.0 options
		$this->config['use_title_alias'] = $params->get('use_title_alias', 0); // use title alias as a title
		$this->config['show_list_description'] = $params->get('show_list_description', 1); // enable/disable list description
		$this->config['no_comments_text'] = $params->get('no_comments_text', 1); // showing of other text when article has no comments
		$this->config['module_font_size'] = $params->get('module_font_size', 100); // specify font-size inside the module
		$this->config['img_keep_aspect_ratio'] = $params->get('img_keep_aspect_ratio', 0); // keeping aspect ratio of images
		$this->config['news_since'] = $params->get('news_since', ''); // since date for source articles
		$this->config['time_offset'] = $params->get('time_offset', 0); // time offset for timezones problem
	    $this->config['links_columns_amount'] = $params->get('links_columns_amount', 1); // amount of links columns
		$this->config['module_mode'] = $params->get('module_mode', 'normal'); // select mode of the module i.e. one of the portal modes
        $this->config['simple_crop_top'] = $params->get('simple_crop_top', 10); // top crop in %
        $this->config['simple_crop_bottom'] = $params->get('simple_crop_bottom', 10); // bottom crop in %
        $this->config['simple_crop_left'] = $params->get('simple_crop_left', 10); // left crop in %
        $this->config['simple_crop_right'] = $params->get('simple_crop_right', 10); // right crop in %
        $this->config['crop_rules'] = $params->get('crop_rules', ''); // crop rules in format: [NAME|width:height]=top:right:bottom:left;
        $this->config['news_portal_mode_amount'] = $params->get('news_portal_mode_amount', 10); // amount of news in Portal Mode
        $this->config['news_portal_mode_3_open_first'] = $params->get('news_portal_mode_3_open_first', 1); // open first block automatically
        
        // new GK4 v.2.1 options
		$this->config['img_auto_scale'] = $params->get('img_auto_scale', 1); // image auto-scale
		
        // RedSHOP Component options (new GK4 v.2.4 options)
        $this->config['news_content_rs_pos'] = $params->get('news_content_rs_pos', 'left');         
        $this->config['news_content_rs_float'] = $params->get('news_content_rs_float', 'none');
        $this->config['news_rs_store_order'] = $params->get('news_rs_store_order',6);
        $this->config['news_rs_store_enabled'] = $params->get('news_rs_store_enabled', 1);
        $this->config['redshop_categories'] = $params->get('redshop_categories', '');
        $this->config['redshop_products'] = $params->get('redshop_products', '');
        $this->config['all_redshop_products'] = $params->get('all_redshop_products', '');
        $this->config['rs_out_of_stock'] = $params->get('rs_out_of_stock', '');
        $this->config['rs_add_to_cart'] = $params->get('rs_add_to_cart', '');
        $this->config['rs_price'] = $params->get('rs_price', '');
        $this->config['rs_price_text'] = $params->get('rs_price_text', '');
        $this->config['rs_currency_place'] = $params->get('rs_currency_place', '');
        $this->config['rs_price_with_vat'] = $params->get('rs_price_with_vat', '1');
        $this->config['rs_show_default_cart_button'] = $params->get('rs_show_default_cart_button', 0);
        // new GK4 v.2.5 options
        $this->config['memory_limit'] = $params->get('memory_limit', '128M');
        
        // GK4 v 3.0 options
        $this->config['vm_categories'] = $params->get('vm_categories', ''); 
        $this->config['vm_products'] = $params->get('vm_products', '');
        $this->config['vm_shopper_group'] = $params->get('vm_shopper_group', -1);
        $this->config['vm_out_of_stock'] = $params->get('vm_out_of_stock', 1);
        $this->config['vm_add_to_cart'] = $params->get('vm_add_to_cart', 0);
        $this->config['vm_show_tax'] = $params->get('vm_show_tax', 0);
        $this->config['vm_show_price_type'] = $params->get('vm_show_price_type', 'base');
        $this->config['vm_show_discount_amount'] = $params->get('vm_show_discount_amount', 0);
        $this->config['vm_show_price_with_tax'] = $params->get('vm_show_price_with_tax', 0);
        $this->config['vm_itemid'] = $params->get('vm_itemid', 9999);
        $this->config['vm_display_type'] = $params->get('vm_display_type', 'text_price');
        
		// image thumbnails options (in Joomla version > 2.5)
		$this->config['thumb_image_type'] = $params->get('thumb_image_type', 'full');
		
		// GK4 v3.1 options 
		$this->config['k2store_support'] = $params->get('k2store_support', 0); // enable K2Store support
		$this->config['k2store_show_cart'] = $params->get('k2store_show_cart', 1); // enable showing K2Store "show cart" button
		$this->config['k2store_add_to_cart'] = $params->get('k2store_add_to_cart', 1); // enable showing K2Store "add to cart" button
		$this->config['k2store_price'] = $params->get('k2store_price', 1); // enable showing K2Store item price
		$this->config['k2store_price_text'] = $params->get('k2store_price_text', 1); // enable showing text "Item price" with the item price
        $this->config['k2store_currency_place'] = $params->get('k2store_currency_place', 'before'); // specify a currency place
		
		// GK4 v.3.2 options
		$this->config['responsive_images'] = $params->get('responsive_images', 0); // enable responsive images support
		
		// GK4 v.3.4 options
		$this->config['useRTL'] = $params->get('useRTL', 0); 
		$this->config['wrapContent'] = $params->get('wrap_content', 0);
		
		// GK4 v.3.3.5 options
		$this->config['news_portal_mode_7_width'] = $params->get('news_portal_mode_7_width', 265);
		
		$this->config['news_portal_mode_3_open_first'] = $params->get('news_portal_mode_3_open_first', 1); // open first block automatically
        
        // small validation
		if($this->config['list_title_limit'] == 0 && $this->config['list_text_limit'] == 0){
			$this->config['news_short_pages'] = 0;
		}

		if($this->config['news_header_enabled'] == 0) $this->config['news_content_header_pos'] = 'disabled';
		if($this->config['news_image_enabled']  == 0) $this->config['news_content_image_pos'] = 'disabled';
		if($this->config['news_text_enabled']  == 0) $this->config['news_content_text_pos'] = 'disabled';
		if($this->config['news_info_enabled'] == 0) $this->config['news_content_info_pos'] = 'disabled';
		if($this->config['news_info2_enabled'] == 0) $this->config['news_content_info2_pos'] = 'disabled';
		if($this->config['news_readmore_enabled'] == 0) $this->config['news_content_readmore_pos'] = 'disabled';
        if($this->config['news_rs_store_enabled'] == 0) $this->config['news_rs_store_enabled'] = 'disabled';
  	
		// read K2Store params
		if($this->config['k2store_support'] && file_exists(JPATH_ROOT.DS.'components'.DS.'com_k2store')) {
            $this->k2store_params = JComponentHelper::getParams('com_k2store');
        }
		
        // parse the crop rules
		$temp_crop_rules = explode(';', $this->config['crop_rules']);
		$final_crop_rules = array();
		// parse every rule
		foreach($temp_crop_rules as $rule) {
			// divide the rule for the name and data
			$temp_rule = explode('=',$rule);
			// validation of format
			if(count($temp_rule) == 2) {
				// create the structure for rule
				$final_rule = array(
										'type' => $temp_rule[0],
										'top' => 0,
										'right' => 0,
										'bottom' => 0,
										'left' => 0 
									);
				// check the type of the rule - class-based or size-based					
				if(strpos($temp_rule[0], ':') !== FALSE) {
					// if the rule is size-based - divide the size
					$temp_size = explode(':', $temp_rule[0]);
					// validation of format
					if(count($temp_size) == 2) {
						// and put to the array the base size of image
						$final_rule['type'] = array(
														'width' => $temp_size[0],
														'height' => $temp_size[1]
													);
					}
				}
				// get the data about cropping
				$temp_crop_size = explode(':', $temp_rule[1]);
				// validation of format
				if(count($temp_crop_size) == 4) {
					// put the data to the structure
					$final_rule['top'] = $temp_crop_size[0];
					$final_rule['right'] = $temp_crop_size[1];
					$final_rule['bottom'] = $temp_crop_size[2];
					$final_rule['left'] = $temp_crop_size[3];
				}
				// override the old rule string with the array structure
				array_push($final_crop_rules, $final_rule);
			}
		}
		// override old string-based rules with the more readable array structures
		$this->config['crop_rules'] = $final_crop_rules;
	}
	// GETTING DATA
	function getDatas(){
		if($this->config['module_mode'] != 'normal') {
			if(!class_exists('NSP_GK4_'.$this->config['module_mode'])) {
				require_once (dirname(__FILE__).DS.'gk_classes'.DS.'portal_modes'.DS.'gk.'.strtolower($this->config['module_mode']).'.php');
			}	
			// 
			$method_name = $this->config['module_mode'].'_getData';
			$this->content = $method_name($this);
		} else {
			$db = JFactory::getDBO();
			
			if( $this->config["data_source"] == "com_categories" ||
		        $this->config["data_source"] == "com_articles" ||
		        $this->config["data_source"] == "com_all_articles"){
				// getting instance of Joomla! com_content source class
				$newsClass = new NSP_GK4_Joomla_Source();
				// Getting list of categories
				$categories = ($this->config["data_source"] != "com_all_articles") ? $newsClass->getSources($this->config) : false;
				// getting content
				$amountOfArts = ($this->config['news_column'] * $this->config['news_rows'] * $this->config['news_full_pages']) + ($this->config['links_amount'] * $this->config['news_short_pages'] * $this->config['links_columns_amount']);
				$this->content = $newsClass->getArticles($categories, $this->config, $amountOfArts);		   	
			} else if($this->config["data_source"] == "k2_categories" ||
		        $this->config["data_source"] == "k2_tags" ||
		        $this->config["data_source"] == "k2_articles" ||
                $this->config["data_source"] == "all_k2_articles" ) {  
                
				// getting insance of K2 source class
                $newsClass = new NSP_GK4_K2_Source();
				// Getting list of categories
				
				$categories = ($this->config["data_source"] != "all_k2_articles") ? $newsClass->getSources($this->config) : false;
				// getting content
				$amountOfArts = ($this->config['news_column'] * $this->config['news_rows'] * $this->config['news_full_pages']) + ($this->config['links_amount'] * $this->config['news_short_pages'] * $this->config['links_columns_amount']);
                $this->content = $newsClass->getArticles($categories, $this->config, $amountOfArts);	
				$this->content['comments'] = $newsClass->getComments($this->content, $this->config);
			} else if($this->config["data_source"] == "redshop_categories" ||
                      $this->config["data_source"] == "redshop_products" ||
                      $this->config["data_source"] == "all_redshop_products") {
				if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_redshop')) {
                $newsClass = new NSP_GK4_Redshop_Source(); 
                // get product helper
                if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'product.php')) {require_once( JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'product.php' ); }
                if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'helper.php')) {require_once( JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'helper.php' ); }
                if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'category.php')) {require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'category.php'); }
                if($this->config['rs_add_to_cart'] == 1){
                if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'redshop.js.php')) {require_once( JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'redshop.js.php' ); }
                }
                // include scripts
                if (file_exists('components/com_redshop/assets/js/') && $this->config['rs_add_to_cart'] == 1) {
				JHTML::Script('attribute.js', 'components/com_redshop/assets/js/',false);
				JHTML::Script('common.js', 'components/com_redshop/assets/js/',false);
				//JHTML::Script('jquery.tools.min.js', 'components/com_redshop/assets/js/',false); 
                }
                require_once(JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'redshop.js.php');
				$doc = & JFactory::getDocument ();
				$tmpl = JRequest::getCmd('tmpl');
				$view = JRequest::getCmd('view');
				$layout = JRequest::getCmd('layout');
				$for = JRequest::getWord("for",false);
				if($tmpl == 'component' && !$for)
			    	$doc->addStyleDeclaration('html { overflow:scroll; }');
				// 	Getting the configuration
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'redshop.cfg.php');
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'configuration.php');
				$Redconfiguration = new Redconfiguration();
				$Redconfiguration->defineDynamicVars();
			
				require_once(JPATH_SITE. DS .'components'.DS.'com_redshop'.DS.'helpers'.DS.'currency.php');
				$session = JFactory::getSession('product_currency');
			
				$post = JRequest::get('POST');
				$Itemid= JRequest::getVar('Itemid');
				if(isset($post['product_currency']))
					$session->set('product_currency',$post['product_currency']);
			
				$currency_symbol = 	REDCURRENCY_SYMBOL;
				$currency_convert = 1;
				if($session->get('product_currency')){
					$currency_symbol = $session->get('product_currency');
					$convertPrice = new convertPrice();
					$currency_convert = $convertPrice->convert(1);
				}
				
				
				$script = "window.site_url ='".JURI::root()."';
					window.AJAX_CART_BOX ='".AJAX_CART_BOX."';
					window.REDSHOP_VIEW ='".$view."';
					window.REDSHOP_LAYOUT ='".$layout."';
					window.AJAX_CART_URL ='".JRoute::_('index.php?option=com_redshop&view=cart&Itemid='.$Itemid,false)."';
					window.REDCURRENCY_SYMBOL ='".REDCURRENCY_SYMBOL."';
					window.CURRENCY_SYMBOL_CONVERT ='".$currency_symbol."';
					window.CURRENCY_CONVERT ='".$currency_convert."';
					window.PRICE_SEPERATOR ='".PRICE_SEPERATOR."';
					window.PRODUCT_OUTOFSTOCK_MESSAGE ='".JText::_('PRODUCT_OUTOFSTOCK_MESSAGE')."';
					window.PREORDER_PRODUCT_OUTOFSTOCK_MESSAGE ='".JText::_('PREORDER_PRODUCT_OUTOFSTOCK_MESSAGE')."';
					window.CURRENCY_SYMBOL_POSITION ='".CURRENCY_SYMBOL_POSITION."';
					window.PRICE_DECIMAL ='".PRICE_DECIMAL."';
					window.PASSWORD_MIN_CHARACTER_LIMIT ='".JText::_('PASSWORD_MIN_CHARACTER_LIMIT')."';
					window.THOUSAND_SEPERATOR ='".THOUSAND_SEPERATOR."';
					window.VIEW_CART ='".JText::_('VIEW_CART')."';
					window.CONTINUE_SHOPPING ='".JText::_('CONTINUE_SHOPPING')."';
					window.CART_SAVE ='".JText::_('CART_SAVE')."';
					window.IS_REQUIRED ='".JText::_('IS_REQUIRED')."';
					window.ENTER_NUMBER ='".JText::_('ENTER_NUMBER')."';
					window.USE_STOCKROOM ='".USE_STOCKROOM."';
					window.USE_AS_CATALOG ='".USE_AS_CATALOG."';
					window.AJAX_CART_DISPLAY_TIME ='".AJAX_CART_DISPLAY_TIME."';
					window.SHOW_PRICE ='".SHOW_PRICE."';
					window.DEFAULT_QUOTATION_MODE ='".DEFAULT_QUOTATION_MODE."';
					window.PRICE_REPLACE ='".PRICE_REPLACE."';
					window.PRICE_REPLACE_URL ='".PRICE_REPLACE_URL."';
					window.ZERO_PRICE_REPLACE ='".ZERO_PRICE_REPLACE."';
					window.ZERO_PRICE_REPLACE_URL ='".ZERO_PRICE_REPLACE_URL."';
					window.OPTIONAL_SHIPPING_ADDRESS ='".OPTIONAL_SHIPPING_ADDRESS."';
					window.SHIPPING_METHOD_ENABLE ='".SHIPPING_METHOD_ENABLE."';
					window.PRODUCT_ADDIMG_IS_LIGHTBOX ='".PRODUCT_ADDIMG_IS_LIGHTBOX."';
					window.ALLOW_PRE_ORDER ='".ALLOW_PRE_ORDER."';
					window.ATTRIBUTE_SCROLLER_THUMB_WIDTH ='".ATTRIBUTE_SCROLLER_THUMB_WIDTH."';
					window.ATTRIBUTE_SCROLLER_THUMB_HEIGHT ='".ATTRIBUTE_SCROLLER_THUMB_HEIGHT."';
					window.PRODUCT_DETAIL_IS_LIGHTBOX ='".PRODUCT_DETAIL_IS_LIGHTBOX."';
					window.PLEASE_ENTER_COMPANY_NAME ='".JText::_ ( 'PLEASE_ENTER_COMPANY_NAME', true )."';
					window.YOUR_MUST_PROVIDE_A_FIRSTNAME ='".JText::_ ( 'YOUR_MUST_PROVIDE_A_FIRSTNAME', true )."';
					window.YOUR_MUST_PROVIDE_A_LASTNAME ='".JText::_ ( 'YOUR_MUST_PROVIDE_A_LASTNAME', true )."';
					window.YOUR_MUST_PROVIDE_A_ADDRESS ='".JText::_ ( 'YOUR_MUST_PROVIDE_A_ADDRESS', true )."';
					window.YOUR_MUST_PROVIDE_A_ZIP ='".JText::_ ( 'YOUR_MUST_PROVIDE_A_ZIP', true )."';
					window.YOUR_MUST_PROVIDE_A_CITY ='".JText::_ ( 'YOUR_MUST_PROVIDE_A_CITY', true )."';
					window.YOUR_MUST_PROVIDE_A_PHONE ='".JText::_ ( 'YOUR_MUST_PROVIDE_A_PHONE', true )."';
					window.THIS_FIELD_REQUIRED ='".JText::_ ( 'THIS_FIELD_REQUIRED', true )."';
					window.THIS_FIELD_REMOTE ='".JText::_ ( 'THIS_FIELD_REMOTE', true )."';
					window.THIS_FIELD_URL='".JText::_ ( 'THIS_FIELD_URL', true )."';
					window.THIS_FIELD_DATE='".JText::_ ( 'THIS_FIELD_DATE', true )."';
					window.THIS_FIELD_DATEISO='".JText::_ ( 'THIS_FIELD_DATEISO', true )."';
					window.THIS_FIELD_NUMBER='".JText::_ ( 'THIS_FIELD_NUMBER', true )."';
					window.THIS_FIELD_DIGITS='".JText::_ ( 'THIS_FIELD_DIGITS', true )."';
					window.THIS_FIELD_CREDITCARD='".JText::_ ( 'THIS_FIELD_CREDITCARD', true )."';
					window.THIS_FIELD_EQUALTO='".JText::_ ( 'THIS_FIELD_EQUALTO', true )."';
					window.THIS_FIELD_ACCEPT='".JText::_ ( 'THIS_FIELD_ACCEPT', true )."';
					window.THIS_FIELD_MAXLENGTH='".JText::_ ( 'THIS_FIELD_MAXLENGTH', true )."';
					window.THIS_FIELD_MINLENGTH='".JText::_ ( 'THIS_FIELD_MINLENGTH', true )."';
					window.THIS_FIELD_RANGELENGTH='".JText::_ ( 'THIS_FIELD_RANGELENGTH', true )."';
					window.THIS_FIELD_RANGE='".JText::_ ( 'THIS_FIELD_RANGE', true )."';
					window.THIS_FIELD_MAX='".JText::_ ( 'THIS_FIELD_MAX', true )."';
					window.THIS_FIELD_MIN='".JText::_ ( 'THIS_FIELD_MIN', true )."';
					window.YOU_MUST_PROVIDE_LOGIN_NAME ='".JText::_ ( 'YOU_MUST_PROVIDE_LOGIN_NAME', true )."';
					window.PROVIDE_EMAIL_ADDRESS ='".JText::_ ( 'PROVIDE_EMAIL_ADDRESS', true )."';
					window.EMAIL_NOT_MATCH ='".JText::_ ( 'EMAIL_NOT_MATCH', true )."';
					window.PASSWORD_NOT_MATCH ='".JText::_ ( 'PASSWORD_NOT_MATCH', true )."';
					window.NOOF_SUBATTRIB_THUMB_FOR_SCROLLER ='".NOOF_SUBATTRIB_THUMB_FOR_SCROLLER."';
					window.NOT_AVAILABLE ='".JText::_ ( 'NOT_AVAILABLE', true )."';
					window.PLEASE_INSERT_HEIGHT ='".JText::_ ( 'PLEASE_INSERT_HEIGHT', true )."';
					window.PLEASE_INSERT_WIDTH ='".JText::_ ( 'PLEASE_INSERT_WIDTH', true )."';
					window.PLEASE_INSERT_DEPTH ='".JText::_ ( 'PLEASE_INSERT_DEPTH', true )."';
					window.PLEASE_INSERT_RADIUS ='".JText::_ ( 'PLEASE_INSERT_RADIUS', true )."';
					window.PLEASE_INSERT_UNIT ='".JText::_ ( 'PLEASE_INSERT_UNIT', true )."';
					window.THIS_FIELD_IS_REQUIRED ='".JText::_ ( 'THIS_FIELD_IS_REQUIRED', true )."';
					window.CREATE_ACCOUNT_CHECKBOX ='".CREATE_ACCOUNT_CHECKBOX."';
					window.SHOW_QUOTATION_PRICE ='".SHOW_QUOTATION_PRICE."';";
					$document = JFactory::getDocument();
					$document->addScriptDeclaration($script);
							
			        // Getting list of categories
			        $categories = $newsClass->getSources($this->config);
			        // getting content
			    	$amountOfProducts = ($this->config['news_column'] * $this->config['news_rows'] * $this->config['news_full_pages']) + ($this->config['links_amount'] * $this->config['news_short_pages'] * $this->config['links_columns_amount']); 
			    	$this->content = $newsClass->getArticles($categories, $this->config, $amountOfProducts);	
				}
			} else {
			     // VM block
                 $newsClass = new NSP_GK4_VM_Source();
				// Getting list of categories
                
				$categories = $newsClass->getSources($this->config);
				// getting content
				$amountOfProducts = ($this->config['news_column'] * $this->config['news_rows'] * $this->config['news_full_pages']) + ($this->config['links_amount'] * $this->config['news_short_pages'] * $this->config['links_columns_amount']); 
				$this->content = $newsClass->getProducts($categories, $this->config, $amountOfProducts);				
				$this->content['comments'] = $newsClass->getComments($this->content, $this->config);	  
			}
		}
	}
	// RENDERING LAYOUT
	function renderLayout() {	
		if($this->config['module_mode'] !== 'normal') {
			$this->render_portal_mode($this->config['module_mode']);
		} else {	
			$renderer = new NSP_GK4_Layout_Parts();
			// detecting mode - com_content or K2
			$k2_mode = false;
            $rs_mode = false;
            $vm_mode = false;
			
            $producthelper = '';
       	    $redhelper = '';
			//check the source
			if($this->config["data_source"] == 'k2_categories' || 
	          $this->config["data_source"] == 'k2_articles' ||
	          $this->config["data_source"] == 'all_k2_articles' || 
	          $this->config["data_source"] == 'k2_tags') {
	            if($this->config['k2_categories'] != -1){
					$k2_mode = true;
				}else{ // exception when K2 is not installed
					$this->content = array(
						"ID" => array(),
						"alias" => array(),
						"CID" => array(),
						"title" => array(),
						"text" => array(),
						"date" => array(),
						"date_publish" => array(),
						"author" => array(),
						"cat_name" => array(),
						"cat_alias" => array(),
						"hits" => array(),
						"news_amount" => 0,
						"rating_sum" => 0,
						"rating_count" => 0,
						"plugins" => ''
					);
				}
			}
            else if($this->config["data_source"] == 'redshop_categories' || 
	          $this->config["data_source"] == 'redshop_products' ||
	          $this->config["data_source"] == 'all_redshop_products') {
	            if($this->config['redshop_categories'] != -1  && file_exists(JPATH_ROOT.DS.'components'.DS.'com_redshop')) {
					$rs_mode = true;

                    $producthelper = new producthelper ();
       	            $redhelper = new redhelper();
				}else{ // exception when RedSHOP is not installed
					$this->content = array(
						"ID" => array(),
            			"CID" => array(),
            			"title" => array(),
            			"text" => array(),
            			"date" => array(),
            			"date_publish" => array(),
            			"price" => array(),
                        "discount_price" => array(),
                        "discount_start" => array(),
            			"discount_end" => array(),
            			"tax" => array(),
                        "cat_name" => array(),
            			"manufacturer" => array(),
            			"manufacturer_id" => array(),
            			"product_image" => array(),
            			"news_amount" => array()
					);
				}
			} elseif($this->config["data_source"] == 'vm_categories' || 
	                $this->config["data_source"] == 'vm_products') {
	            
	            if($this->config['vm_categories'] != -1){
					$vm_mode = true;
				}else{ // exception when VirtueMart is not installed
					$this->content = array(
						"ID" => array(),
						"CID" => array(),
						"title" => array(),
						"text" => array(),
						"date" => array(),
						"date_publish" => array(),
						"price" => array(),
						"price_currency" => array(),
						"discount_amount" => array(),
						"discount_is_percent" => array(),
						"discount_start" => array(),
						"discount_end" => array(),
						"tax" => array(),
	                    "cat_name" => array(),
						"manufacturer" => array(),
						"manufacturer_id" => array(),
						"product_image" => array(),
						"news_amount" => 0
					);
				}
			}
			// tables which will be used in generated content
			$news_list_tab = array();
			$news_html_tab = array();
			// Generating content 
			$uri =& JURI::getInstance();
			$li_counter = 0;
			$news_k2_store = '';
			$news_header = '';
			$news_image = '';
			$news_readmore = '';
			$news_textt = '';
			$news_infoo = '';
			$news_infoo2 = '';
			//
			for($i = 0; $i < count($this->content["ID"]); $i++) {	
				if($i < ($this->config['news_column'] * $this->config['news_rows'] * $this->config['news_full_pages'])) {
					// GENERATING NEWS CONTENT
	                if($k2_mode == FALSE && $rs_mode == FALSE && $vm_mode == FALSE) {
	    				// GENERATING HEADER
	    				if($this->config['news_header_enabled'] == 1) {
	    					$news_header = $renderer->header($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['title'][$i]);
	    				}
	    				// GENERATING IMAGE
	    				if($this->config['news_image_enabled'] == 1) {
	    					$news_image = $renderer->image($this->config, $uri, $this->content['ID'][$i], $this->content['IID'][$i], $this->content['CID'][$i], $this->content['text'][$i], $this->content['title'][$i], $this->content['images'][$i]);
	    				}
	    				// GENERATING READMORE
	    				if($this->config['news_readmore_enabled'] == 1) {
	    					$news_readmore = $renderer->readMore($this->config, $this->content['ID'][$i], $this->content['CID'][$i]);
	    				}
	    				// GENERATING TEXT
	    				if($this->config['news_text_enabled'] == 1) {
	    					$news_textt = $renderer->text($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['text'][$i], $news_readmore);	
	    				}
	    				// GENERATE NEWS INFO
	    				if($this->config['news_info_enabled'] == 1) {
	    					$news_infoo = $renderer->info($this->config, $this->content['catname'][$i], $this->content['CID'][$i], $this->content['author'][$i], $this->content['email'][$i], ($this->config['date_publish'] == TRUE) ? $this->content['date_publish'][$i] : $this->content['date'][$i], $this->content['hits'][$i], $this->content['ID'][$i], $this->content['rating_count'][$i], $this->content['rating_sum'][$i]);
	    				}
	    				// GENERATE NEWS INFO2
	    				if($this->config['news_info2_enabled'] == 1) {
	    					$news_infoo2 = $renderer->info($this->config, $this->content['catname'][$i], $this->content['CID'][$i], $this->content['author'][$i], $this->content['email'][$i], ($this->config['date_publish'] == TRUE) ? $this->content['date_publish'][$i] : $this->content['date'][$i], $this->content['hits'][$i], $this->content['ID'][$i], $this->content['rating_count'][$i], $this->content['rating_sum'][$i], 2);	
	    				}	
	                } else if($rs_mode == FALSE && $vm_mode == FALSE && $k2_mode == TRUE) {
	                   // GENERATING HEADER
					    if($this->config['news_header_enabled'] == 1) {
					    	$news_header = $renderer->header_k2($this->config, $this->content['ID'][$i], $this->content['alias'][$i], $this->content['CID'][$i], $this->content['cat_alias'][$i], $this->content['title'][$i]);
					    }
					    // GENERATING IMAGE
					    if($this->config['news_image_enabled'] == 1) {
					    	$news_image = $renderer->image_k2($this->config, $uri, $this->content['ID'][$i], $this->content['alias'][$i], $this->content['CID'][$i], $this->content['cat_alias'][$i], $this->content['text'][$i], $this->content['title'][$i]);
					    }
						// GENERATING READMORE
						if($this->config['news_readmore_enabled'] == 1) {
							$news_readmore = $renderer->readMore_k2($this->config, $this->content['ID'][$i], $this->content['alias'][$i], $this->content['CID'][$i], $this->content['cat_alias'][$i]);
						}
						// GENERATING TEXT
						if($this->config['news_text_enabled'] == 1) {
							$news_textt = $renderer->text_k2($this->config, $this->content['ID'][$i], $this->content['alias'][$i], $this->content['CID'][$i], $this->content['cat_alias'][$i], $this->content['text'][$i], $news_readmore);
						}	
						// GENERATE NEWS INFO
						if($this->config['news_info_enabled'] == 1) {
							$news_infoo = $renderer->info_k2($this->config, $this->content['cat_name'][$i], $this->content['CID'][$i], $this->content['cat_alias'][$i], $this->content['author'][$i], $this->content['author_id'][$i], $this->content['email'][$i], ($this->config['date_publish'] == TRUE) ? $this->content['date_publish'][$i] : $this->content['date'][$i], $this->content['hits'][$i], $this->content['ID'][$i], $this->content['alias'][$i], $this->content['comments'], $this->content['rating_count'][$i], $this->content['rating_sum'][$i]);
						}
						// GENERATE NEWS INFO2
						if($this->config['news_info2_enabled'] == 1) {
							$news_infoo2 = $renderer->info_k2($this->config, $this->content['cat_name'][$i], $this->content['CID'][$i], $this->content['cat_alias'][$i], $this->content['author'][$i], $this->content['author_id'][$i], $this->content['email'][$i], ($this->config['date_publish'] == TRUE) ? $this->content['date_publish'][$i] : $this->content['date'][$i], $this->content['hits'][$i], $this->content['ID'][$i], $this->content['alias'][$i], $this->content['comments'], $this->content['rating_count'][$i], $this->content['rating_sum'][$i], 2);
						}
						// GENERATE STORE BLOCK
						$news_k2_store = $renderer->store_k2($this->config, $this->content['ID'][$i], $this->content['plugins'][$i], $this->k2store_params);
						
	                }
                    else if ($rs_mode == TRUE && $vm_mode== FALSE && $k2_mode == FALSE){
                        $ItemData = $producthelper->getMenuInformation(0,0,'','product&pid='.$this->content['ID'][$i]);
                        $id = $this->content['ID'][$i];
                        $cid = $producthelper->getCategoryProduct($this->content['ID'][$i]);
                        $Itemid = $redhelper->getItemid($this->content['ID'][$i]);
                        $product = $producthelper->getProductById($this->content['ID'][$i]);
                        // GENERATING HEADER
    					if($this->config['news_header_enabled'] == 1) {
    						$news_header = $renderer->header_rs($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['title'][$i], $Itemid);
    					}
    					// GENERATING IMAGE
    					if($this->config['news_image_enabled'] == 1) {
    						$news_image = $renderer->image_rs($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['product_image'][$i], $this->content['title'][$i], $Itemid);
    					}
    					// GENERATING READMORE
    					if($this->config['news_readmore_enabled'] == 1) {
    						$news_readmore = $renderer->readMore_rs($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $Itemid);
    					}
    					// GENERATING TEXT
    					if($this->config['news_text_enabled'] == 1) {
    						$news_textt = $renderer->text_rs($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['text'][$i], $news_readmore, $Itemid);	
    					}
    					// GENERATE NEWS INFO
    					if($this->config['news_info_enabled'] == 1) {
    						$news_infoo = $renderer->info_rs($this->config, $this->content['ID'][$i], $this->content['cat_name'][$i], $this->content['CID'][$i], $this->content['manufacturer'][$i], ($this->config['date_publish'] == TRUE) ? $this->content['date_publish'][$i] : $this->content['date'][$i], $Itemid, $this->content['manufacturer_id'][$i], 1);
    					}
    					// GENERATE NEWS INFO2
    					if($this->config['news_info2_enabled'] == 1) {
    						$news_infoo2 = $renderer->info_rs($this->config, $this->content['ID'][$i], $this->content['cat_name'][$i], $this->content['CID'][$i], $this->content['manufacturer'][$i], ($this->config['date_publish'] == TRUE) ? $this->content['date_publish'][$i] : $this->content['date'][$i], $Itemid, $this->content['manufacturer_id'][$i], 2);
    					}                    
                        // COMPUTE PRICE DEPENDS OF USER ID
                        $user = &JFactory::getUser();
                        $price = $producthelper->getProductPrice($this->content['ID'][$i], $this->config['rs_price_with_vat'], $user->id);
                        $price = $producthelper->getProductFormattedPrice ($price, true);
                        if($this->config['rs_add_to_cart'] == 1) {
                           $addToCart = $producthelper->replaceCartTemplate($this->content['ID'][$i],0,0,0,"",false,array(),0,0,0);
                           $addToCart = str_replace('&', '&amp;', $addToCart);
                           if($this->config['rs_show_default_cart_button'] == 0){
                           $btnCode = '<a class=\'nspAddToCart\' onclick="if(displayAddtocartForm(\'addtocart_prd_'.$this->content['ID'][$i];
                           $btnCode.= '\',\''.$id.'\',\'0\',\'0\', \'user_fields_form\'))';
                           $btnCode.= '{checkAddtocartValidation(\'addtocart_prd_'.$id.'\',\''.$id.'\',\'0\',\'0\', \'user_fields_form\',\'0\',\'0\',\'0\');}"><span style=\'cursor: pointer;\' id=\'pdaddtocartprd'.$id.'\' title=\'\' class=\'\'>'.JText::_('MOD_NEWS_PRO_GK4_ADD_TO_CART').'</span></a>';
                           $addToCart = preg_replace('/\<img.*?\>/i', $btnCode, $addToCart);
                           }
                        } 
                        // GET THE CURRENCY
                        $bool = preg_match('/[^0-9]/u',$price, $currency);
                        $currency = $currency[0];
                        $bool = preg_match('/[0-9]+/u',$price,  $price);
                        $price = $price[0];
                        // GENERATE RedSHOP STORE INFO
                        $news_rs_store = $renderer->store_rs($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $price , $this->content['discount_start'][$i], $this->content['discount_end'][$i], $this->content['tax'][$i], $this->content['discount_price'][$i], $currency, $Itemid, $addToCart);
                       
                    } else if ($vm_mode == TRUE) {
            
                        // GENERATING HEADER
						if($this->config['news_header_enabled'] == 1) {
							$news_header = $renderer->header_vm($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['title'][$i]);
						}
						// GENERATING IMAGE
						if($this->config['news_image_enabled'] == 1) {
							$news_image = $renderer->image_vm($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['product_image'][$i], $this->content['title'][$i]);
						}
						// GENERATING READMORE
						if($this->config['news_readmore_enabled'] == 1) {
							$news_readmore = $renderer->readMore_vm($this->config, $this->content['ID'][$i], $this->content['CID'][$i]);
						}
						// GENERATING TEXT
						if($this->config['news_text_enabled'] == 1) {
							$news_textt = $renderer->text_vm($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['text'][$i], $news_readmore);
						}	
						// GENERATE NEWS INFO
						if($this->config['news_info_enabled'] == 1) {
							$news_infoo = $renderer->info_vm($this->config, $this->content['ID'][$i], $this->content['cat_name'][$i], $this->content['CID'][$i], $this->content['manufacturer'][$i], ($this->config['date_publish'] == TRUE) ? $this->content['date_publish'][$i] : $this->content['date'][$i], $this->content['comments']);
						}
						// GENERATE NEWS INFO2
						if($this->config['news_info2_enabled'] == 1) {
							$news_infoo2 = $renderer->info_vm($this->config, $this->content['ID'][$i], $this->content['cat_name'][$i], $this->content['CID'][$i], $this->content['manufacturer'][$i], ($this->config['date_publish'] == TRUE) ? $this->content['date_publish'][$i] : $this->content['date'][$i], $this->content['comments'], 2);
						}
						$news_vm_store = $renderer->store_vm($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['price'][$i], $this->content['price_currency'][$i], $this->content['discount_amount'][$i], true, $this->content['discount_start'][$i], $this->content['discount_end'][$i], $this->content['tax'][$i], $this->content['manufacturer_id'][$i]);
                    }
					// PARSING PLUGINS
					if($this->config['parse_plugins'] == TRUE) {
						$news_textt = JHtml::_('content.prepare', $news_textt);
					}	
					// CLEANING PLUGINS
					if($this->config['clean_plugins'] == TRUE) {
						$news_textt = preg_replace("/(\{.+?\}.+?\{.+?})|(\{.+?\})/", "", $news_textt);
					} 		
					// GENERATE CONTENT FOR TAB	
					$news_generated_content = ''; // initialize variable
					//
					for($j = 1;$j < 7;$j++) {
						if(
							$this->config['wrapContent'] == 1 && 
							$this->config['news_image_order'] == 1							
						) {
							if($j == 2) {
								$news_generated_content .= '<div class="gkArtContentWrap">';
							}
						}
						
						if($this->config['news_header_order'] == $j) $news_generated_content .= $news_header;
						if($this->config['news_image_order'] == $j) $news_generated_content .= $news_image;
						if($this->config['news_text_order'] == $j) $news_generated_content .= $news_textt;
						if($this->config['news_info_order'] == $j) $news_generated_content .= $news_infoo;
						if($this->config['news_info2_order'] == $j) $news_generated_content .= $news_infoo2;
                        if($this->config['news_rs_store_enabled'] != 'disabled') {
                        if($rs_mode != FALSE && $this->config['news_rs_store_order'] == $j) $news_generated_content .= $news_rs_store;
                        if($vm_mode != FALSE && $this->config['news_rs_store_order'] == $j) $news_generated_content .= $news_vm_store;
						if($k2_mode != FALSE && $this->config['news_rs_store_order'] == $j) $news_generated_content .= $news_k2_store;
                        }
					}		
					//
					if($this->config['news_content_readmore_pos'] != 'after') {
						$news_generated_content .= $news_readmore;
					}
					
					if(
						$this->config['wrapContent'] == 1 && 
						$this->config['news_image_order'] == 1							
					) {
						$news_generated_content .= '</div>';
					}
					// creating table with news content
					array_push($news_html_tab, $news_generated_content);
				} else { 
	                if($k2_mode == FALSE && $vm_mode == FALSE) {
						array_push($news_list_tab, $renderer->lists($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['title'][$i], $this->content['text'][$i], $li_counter % 2, $li_counter));
	                } elseif($k2_mode == TRUE) {
	                	array_push($news_list_tab, $renderer->lists_k2($this->config, $this->content['ID'][$i], $this->content['alias'][$i], $this->content['CID'][$i], $this->content['cat_alias'][$i], $this->content['title'][$i], $this->content['text'][$i], $li_counter % 2, $li_counter));
	                }
                    else {
                        	array_push($news_list_tab, $renderer->lists_vm($this->config, $this->content['ID'][$i], $this->content['CID'][$i], $this->content['title'][$i], $this->content['text'][$i], $li_counter % 2, $li_counter));
                    }
					//
					$li_counter++;
				}                    
			}
			/** GENERATING FINAL XHTML CODE START **/
			
			// create instances of basic Joomla! classes
			$document = JFactory::getDocument();
			$uri = JURI::getInstance();
			// add stylesheets to document header
			if($this->config["useCSS"] == 1) {
				$document->addStyleSheet( $uri->root().'modules/mod_news_pro_gk4/interface/css/style.css', 'text/css' );
			}
			// add script to the document header
			if($this->config['useScript'] == 1) {
				$document->addScript($uri->root().'modules/mod_news_pro_gk4/interface/scripts/engine.js');
			}
			// init $headData variable
			$headData = false;
			// add scripts with automatic mode to document header
			if($this->config['useScript'] == 2) {
				// getting module head section datas
				unset($headData);
				$headData = $document->getHeadData();
				// generate keys of script section
				$headData_keys = array_keys($headData["scripts"]);
				// set variable for false
				$engine_founded = false;
				// searching phrase mootools in scripts paths
				if(array_search($uri->root().'modules/mod_news_pro_gk4/interface/scripts/engine.js', $headData_keys) > 0) {
					$engine_founded = true;
				}
				// if engine doesn't exists in the head section
				if(!$engine_founded){ 
					// add new script tag connected with mootools from module
					$document->addScript($uri->root().'modules/mod_news_pro_gk4/interface/scripts/engine.js');
				}
			}
			//
			require(JModuleHelper::getLayoutPath('mod_news_pro_gk4', 'content'));
			require(JModuleHelper::getLayoutPath('mod_news_pro_gk4', 'default'));
		}
    }
    // RENDER PORTAL MODE LAYOUT
	function render_portal_mode($mode) {
		if(!class_exists('NSP_GK4_'.$mode)) {
			require_once (dirname(__FILE__).DS.'gk_classes'.DS.'portal_modes'.DS.'gk.'.strtolower($mode).'.php');
		}

		$class_name = 'NSP_GK4_'.$mode;
		$renderer = new $class_name();
		$renderer->init($this);
		$renderer->output();
	}
}

/* EOF */