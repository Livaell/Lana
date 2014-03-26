<?php
//
class NSP_GK4_Portal_Mode_6 {
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
		}
		// tables which will be used in generated content
		$news_image_tab = array();
		$news_title_tab = array();
		// Generating content 
		$uri =& JURI::getInstance();
		//
		$config = $this->parent->config;
		//
		$config['news_header_enabled'] = 1;
		$config['news_image_enabled'] = 1;
		$config['news_content_header_pos'] = '';
		$config['news_content_image_pos'] = '';
		$config['create_thumbs'] = 1;
		$config['img_width'] = 272;
		$config['img_height'] = 272;
		$config['img_link'] = 1;
		$config['img_keep_aspect_ratio'] = 0;
		//
		for($i = 0; $i < count($this->parent->content["ID"]); $i++) {	
			//
			$news_image = '';
			$news_header = '';
			// GENERATING NEWS CONTENT
			if($k2_mode == FALSE && $vm_mode == FALSE){
                $news_header = $renderer->header($config, $this->parent->content['ID'][$i], $this->parent->content['CID'][$i], $this->parent->content['title'][$i]);
				// GENERATING IMAGE
				$news_image = $renderer->image($config, $uri, $this->parent->content['ID'][$i], $this->parent->content['IID'][$i], $this->parent->content['CID'][$i], $this->parent->content['text'][$i], $this->parent->content['title'][$i], $this->parent->content['images'][$i]);
			}else if($vm_mode == FALSE){
				// GENERATING HEADER
				$news_header = $renderer->header_k2($config, $this->parent->content['ID'][$i], $this->parent->content['alias'][$i], $this->parent->content['CID'][$i], $this->parent->content['cat_alias'][$i], $this->parent->content['title'][$i]);
				// GENERATING IMAGE
				$news_image = $renderer->image_k2($config, $uri, $this->parent->content['ID'][$i], $this->parent->content['alias'][$i], $this->parent->content['CID'][$i], $this->parent->content['cat_alias'][$i], $this->parent->content['text'][$i], $this->parent->content['title'][$i]);
			}				
			// GENERATE CONTENT
			if($news_image !== '') {
				array_push($news_image_tab, $news_image);
				array_push($news_title_tab, $news_header);
			}
		}
		
		/** GENERATING FINAL XHTML CODE START **/
		// create instances of basic Joomla! classes
		$document = JFactory::getDocument();
		$uri = JURI::getInstance();
		// add stylesheets to document header
		if($this->parent->config["useCSS"] == 1) {
			$document->addStyleSheet( $uri->root().'modules/mod_news_pro_gk4/interface/css/style.portal.mode.6.css', 'text/css' );
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
			if(array_search($uri->root().'modules/mod_news_pro_gk4/interface/scripts/engine.portal_mode_6.js', $headData_keys) > 0) {
				$engine_founded = true;
			}
			// if mootools file doesn't exists in document head section
			if(!$engine_founded){ 
				// add new script tag connected with mootools from module
				$document->addScript($uri->root().'modules/mod_news_pro_gk4/interface/scripts/engine.portal.mode.6.js');
			}
		}
		//
		require(JModuleHelper::getLayoutPath('mod_news_pro_gk4', 'content.portal.mode.6'));
		require(JModuleHelper::getLayoutPath('mod_news_pro_gk4', 'default.portal.mode.6'));
	}
}
//
function Portal_Mode_6_getData($parent) {
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
		$amountOfArts = 50;
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
		$amountOfArts = 50;
		$output = $newsClass->getArticles($categories, $parent->config, $amountOfArts);	
    }
	
	return $output;
}

/* EOF */