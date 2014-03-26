<?php
//
class NSP_GK4_Portal_Mode_2 {
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
			} else { // exception when K2 is not installed
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
			} else { // exception when VirtueMart is not installed
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
		// Generating content 
		$uri = JURI::getInstance();
		//
		for($i = 0; $i < count($this->parent->content["ID"]); $i++) {	
			// GENERATING NEWS CONTENT
			if($k2_mode == FALSE && $vm_mode == FALSE){
                $news_header = $renderer->header($this->parent->config, $this->parent->content['ID'][$i], $this->parent->content['CID'][$i], $this->parent->content['title'][$i]);
				// GENERATING IMAGE
				$news_image = $renderer->image($this->parent->config, $uri, $this->parent->content['ID'][$i], $this->parent->content['IID'][$i], $this->parent->content['CID'][$i], $this->parent->content['text'][$i], $this->parent->content['title'][$i], $this->parent->content['images'][$i]);
                // GENERATE NEWS INFO
				$news_infoo = JHTML::_('date', ($this->parent->config['date_publish'] == TRUE) ? $this->parent->content['date_publish'][$i] : $this->parent->content['date'][$i], $this->parent->config['date_format']);
			} else if ($vm_mode == FALSE) {
				// GENERATING HEADER
				$news_header = $renderer->header_k2($this->parent->config, $this->parent->content['ID'][$i], $this->parent->content['alias'][$i], $this->parent->content['CID'][$i], $this->parent->content['cat_alias'][$i], $this->parent->content['title'][$i]);
				// GENERATING IMAGE
				$news_image = $renderer->image_k2($this->parent->config, $uri, $this->parent->content['ID'][$i], $this->parent->content['alias'][$i], $this->parent->content['CID'][$i], $this->parent->content['cat_alias'][$i], $this->parent->content['text'][$i], $this->parent->content['title'][$i]);
				// GENERATE NEWS INFO
				$news_infoo = JHTML::_('date', ($this->parent->config['date_publish'] == TRUE) ? $this->parent->content['date_publish'][$i] : $this->parent->content['date'][$i], $this->parent->config['date_format']);
			} else {
	            // GENERATING HEADER
				$news_header = $renderer->header_vm($this->parent->config, $this->parent->content['ID'][$i], $this->parent->content['CID'][$i], $this->parent->content['title'][$i]);
				// GENERATING IMAGE
				$news_image = $renderer->image_vm($this->parent->config, $this->parent->content['ID'][$i], $this->parent->content['CID'][$i], $this->parent->content['product_image'][$i], $this->parent->content['title'][$i]);
				// GENERATE NEWS INFO
				$news_infoo = JHTML::_('date', ($this->parent->config['date_publish'] == TRUE) ? $this->parent->content['date_publish'][$i] : $this->parent->content['date'][$i], $this->parent->config['date_format']);
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
			$news_image_content = '<div><div>' . $news_image . '</div></div>';
			$news_generated_content = '<div class="nspInfo">' . $news_infoo . '</div><div class="nspHeadline">' . $news_header . '</div>';	
			// creating table with news content
			if($news_image !== '') {
				array_push($news_image_tab, $news_image_content);
				array_push($news_title_tab, $news_generated_content);
			}
		}
		/** GENERATING FINAL XHTML CODE START **/
		// create instances of basic Joomla! classes
		$document = JFactory::getDocument();
		$uri = JURI::getInstance();
		// add stylesheets to document header
		if($this->parent->config["useCSS"] == 1) $document->addStyleSheet( $uri->root().'modules/mod_news_pro_gk4/interface/css/style.portal.mode.2.css', 'text/css' );
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
			if(array_search($uri->root().'modules/mod_news_pro_gk4/interface/scripts/engine.portal_mode_2.js', $headData_keys) > 0) $engine_founded = true;
			// if mootools file doesn't exists in document head section
			if(!$engine_founded){ 
				// add new script tag connected with mootools from module
				$headData["scripts"][$uri->root().'modules/mod_news_pro_gk4/interface/scripts/engine.portal.mode.2.js'] = "text/javascript";
				$document->setHeadData($headData);
			}
		}
		/*
		if($this->parent->config['k2store_support'] == 1) {
			// getting module head section datas
			$headData = $document->getHeadData();
			$headData_keys = array_keys($headData["scripts"]);
			$k2store_founded = false;
			// searching phrase mootools in scripts paths
			if(array_search($uri->root().'components/com_k2store/js/k2store.js', $headData_keys) > 0) $k2store_founded = true;
			// if mootools file doesn't exists in document head section
			if(!$k2store_founded){ 
				// add new script tag connected with mootools from module
				$headData["scripts"][$uri->root().'components/com_k2store/js/k2store.js'] = "text/javascript";
				$document->setHeadData($headData);
			}
		}
        */
		//
		require(JModuleHelper::getLayoutPath('mod_news_pro_gk4', 'content.portal.mode.2'));
		require(JModuleHelper::getLayoutPath('mod_news_pro_gk4', 'default.portal.mode.2'));
	}
}
//
function Portal_Mode_2_getData($parent) {
	$db = JFactory::getDBO();
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
    } 
    /*else {
		// getting insance of K2 source class
	    $newsClass = new NSP_GK4_VM_Source();
		// Getting list of categories
		$categories = $newsClass->getSources($parent->config);
		// getting content
		$amountOfProducts = $parent->config['news_portal_mode_amount']; 
		$output = $newsClass->getProducts($categories, $parent->config, $amountOfProducts);					  
	}*/
	return $output;
}
/* EOF */