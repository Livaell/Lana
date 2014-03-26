<?php
//
class NSP_GK4_Portal_Mode_7 {
	//	
	var $parent;
	//
	function init($parent_obj) {
		$this->parent = $parent_obj;
	}
	//
	function output() {
		$renderer = new NSP_GK4_Layout_Parts();
		// detecting mode - com_content or K2
		$k2_mode = false;
		$vm_mode = false;
		//check the source
		if( $this->parent->config["data_source"] == 'k2_categories' ||
            $this->parent->config["data_source"] == 'k2_articles' || 
            $this->parent->config["data_source"] == 'all_k2_articles' || 
		    $this->parent->config["data_source"] == 'k2_tags') {
			
		    if($this->parent->config['k2_categories'] != -1){
				$k2_mode = true;
			}else{ // exception when K2 is not installed
				$this->parent->content = array(
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
		} elseif($this->parent->config["data_source"] == 'vm_categories' || 
		        $this->parent->config["data_source"] == 'vm_products') {
		    
		    if($this->parent->config['vm_categories'] != -1){
				$vm_mode = true;
			}else{ // exception when VirtueMart is not installed
				$this->parent->content = array(
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
		$news_image_tab = array();
		$news_title_tab = array();
		$news_text_tab = array();
		// Generating content 
		$uri = JURI::getInstance();
		//
		for($i = 0; $i < count($this->parent->content["ID"]); $i++) {	
			// GENERATING NEWS CONTENT
			if($k2_mode == FALSE && $vm_mode == FALSE){
                $news_header = $renderer->header($this->parent->config, $this->parent->content['ID'][$i], $this->parent->content['CID'][$i], $this->parent->content['title'][$i]);
				// GENERATING IMAGE
				$news_image = $renderer->image($this->parent->config, $uri, $this->parent->content['ID'][$i], $this->parent->content['IID'][$i], $this->parent->content['CID'][$i], $this->parent->content['text'][$i], $this->parent->content['title'][$i], $this->parent->content['images'][$i]);
				// GENERATING TEXT
				$news_textt = $renderer->text($this->parent->config, $this->parent->content['ID'][$i], $this->parent->content['CID'][$i], $this->parent->content['text'][$i], $news_readmore);	
			}else if($vm_mode == FALSE){
				// GENERATING HEADER
				$news_header = $renderer->header_k2($this->parent->config, $this->parent->content['ID'][$i], $this->parent->content['alias'][$i], $this->parent->content['CID'][$i], $this->parent->content['cat_alias'][$i], $this->parent->content['title'][$i]);
				// GENERATING IMAGE
				$news_image = $renderer->image_k2($this->parent->config, $uri, $this->parent->content['ID'][$i], $this->parent->content['alias'][$i], $this->parent->content['CID'][$i], $this->parent->content['cat_alias'][$i], $this->parent->content['text'][$i], $this->parent->content['title'][$i]);
				// GENERATING TEXT
				$news_textt = $renderer->text_k2($this->parent->config, $this->parent->content['ID'][$i], $this->parent->content['alias'][$i], $this->parent->content['CID'][$i], $this->parent->content['cat_alias'][$i], $this->parent->content['text'][$i], $news_readmore);
			} else {
	            // GENERATING HEADER
				$news_header = $renderer->header_vm($this->parent->config, $this->parent->content['ID'][$i], $this->parent->content['CID'][$i], $this->parent->content['title'][$i]);
				// GENERATING IMAGE
				$news_image = $renderer->image_vm($this->parent->config, $this->parent->content['ID'][$i], $this->parent->content['CID'][$i], $this->parent->content['product_image'][$i], $this->parent->content['title'][$i]);
				// GENERATING TEXT
				$news_textt = $renderer->text_vm($this->config, $this->parent->content['ID'][$i], $this->parent->content['CID'][$i], $this->parent->content['text'][$i], $news_readmore);
			}			
			// PARSING PLUGINS
			if($this->parent->config['parse_plugins'] == TRUE) {
				$news_textt = JHTML::_('content.prepare', $news_textt);
			}	
			// CLEANING PLUGINS
			if($this->parent->config['clean_plugins'] == TRUE) {
				$news_textt = preg_replace("/\{.+?\}/", "", $news_textt);	
			}			
			// GENERATE CONTENT FOR TAB	
			$news_image_content = $news_image;
			$news_generated_content = $news_header;	
			// creating table with news content
			if($news_image !== '') {
				array_push($news_image_tab, $news_image_content);
				array_push($news_title_tab, $news_generated_content);
				array_push($news_text_tab, $news_textt);
			}
		}
		
		/** GENERATING FINAL XHTML CODE START **/
		// create instances of basic Joomla! classes
		$document = JFactory::getDocument();
		$uri = JURI::getInstance();
		// add stylesheets to document header
		if($this->parent->config["useCSS"] == 1) {
			$document->addStyleSheet( $uri->root().'modules/mod_news_pro_gk4/interface/css/style.portal.mode.7.css', 'text/css' );
		}
		// init $headData variable
		$headData = false;
		// add scripts with automatic mode to document header
		if($this->parent->config['useScript'] == 2) {
			// getting module head section datas
			unset($headData);
			$headData = $document->getHeadData();
			// generate keys of script section
			$headData_keys = array_keys($headData["scripts"]);
			// set variable for false
			$engine_founded = false;
			// searching phrase mootools in scripts paths
			if(array_search($uri->root().'modules/mod_news_pro_gk4/interface/scripts/engine.portal_mode_7.js', $headData_keys) > 0) {
				$engine_founded = true;
			}
			// if mootools file doesn't exists in document head section
			if(!$engine_founded){ 
				// add new script tag connected with mootools from module
				$document->addScript($uri->root().'modules/mod_news_pro_gk4/interface/scripts/engine.portal.mode.7.js');
			}
		}
		//
		require(JModuleHelper::getLayoutPath('mod_news_pro_gk4', 'content.portal.mode.7'));
		require(JModuleHelper::getLayoutPath('mod_news_pro_gk4', 'default.portal.mode.7'));
	}
}
//
function Portal_Mode_7_getData($parent) {
	$db =& JFactory::getDBO();
	
	$output = array();
	
	if( $parent->config["data_source"] == "com_categories" ||
	    $parent->config["data_source"] == "com_articles" || 
	    $parent->config["data_source"] == "com_all_articles"){	
		// getting instance of Joomla! com_content source class
		$newsClass = new NSP_GK4_Joomla_Source();
		// Getting list of categories
		$categories = ($parent->config["data_source"] != "com_all_articles") ? $newsClass->getSources($parent->config) : false;
		// getting content
		$amountOfArts = $parent->config['news_portal_mode_amount'];
		$output = $newsClass->getArticles($categories, $parent->config, $amountOfArts);		   	
	} else if( $parent->config["data_source"] == "k2_categories" ||
	    $parent->config["data_source"] == "k2_tags" ||
	    $parent->config["data_source"] == "k2_articles" ||
	    $parent->config["data_source"] == "all_k2_articles") {
     
		// getting insance of K2 source class
	    $newsClass = new NSP_GK4_K2_Source();
		// Getting list of categories
		$categories = ($parent->config["data_source"] != "all_k2_articles") ? $newsClass->getSources($parent->config) : false;
       
		// getting content
		$amountOfArts = $parent->config['news_portal_mode_amount'];
		$output = $newsClass->getArticles($categories, $parent->config, $amountOfArts);	
    } else {
		// getting insance of VM source class
	    $newsClass = new NSP_GK4_VM_Source();
		// Getting list of categories
		$categories = $newsClass->getSources($parent->config);
		// getting content
		$amountOfProducts = $parent->config['news_portal_mode_amount']; 
		$output = $newsClass->getProducts($categories, $parent->config, $amountOfProducts);
	}
	
	return $output;
}

/* EOF */