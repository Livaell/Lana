<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class iNewsViewiNews extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{

		global $mainframe;
		require_once(JPATH_SITE . "/components/com_inews/news_inc_joomla_d.php");

		# ------ Параметры новостей  ------------ #

		$Num_st_news = "10"; // количество новостей на странице

		// Технические настройки
		$charset = "UTF-8"; // указать кодировку:  WINDOWS-1251 или UTF-8

		# ------------------ конец ---------------------------- #

		$imid = JRequest::getInt('Itemid');

		$URL_modul_inews = 'http://' . $_SERVER['HTTP_HOST'] . '/index.php?option=com_inews&Itemid=' . $imid;
        $ip_serv = $_SERVER['REMOTE_ADDR'];
	    $ip_uz = $_SERVER['HTTP_USER_AGENT'];
		$myDiamond = new nuk_CatalogNews_joomla_n('' . $URL_modul_inews . '', '' . $Num_st_news . '',  '' . $ip_serv . '',  '' . $ip_uz . '', './cache/', 'wap.mplaza.ru');
		$Catalog_title_news = $myDiamond->getTitleCatalog_joomla_n();
		$CatalogNews = $myDiamond->getNewsCatalog_joomla_n();

		if ($charset != 'UTF-8') {

				$pagetitle = iconv("UTF-8", $charset, $Catalog_title_news);
				$CatalogNews = iconv("UTF-8", $charset, $CatalogNews);
				$js_cal_in = $js_cal_win;

		} else {
			$pagetitle = $Catalog_title_news;
			$CatalogNews = $CatalogNews;
			$js_cal_in = $js_cal;
		}

		$document =& JFactory::getDocument();
		$document->setTitle($pagetitle . ' / ' . $document->getTitle());
		$document->setMetaData("keywords", "$pagetitle");
		$document->setDescription("Новости");

		echo "$pagetitle<br><br>";
		echo "$js_cal_in $CatalogNews";

		// Display the view
		parent::display($tpl);
	}
}
