<?php

/**
 * @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Accordeon CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

class ModaccordeonckHelper {

	static function GetItems(&$params, $module) {
		// Initialise variables.
		$list = array();
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$menuID = $params->get('tag_id', 'accordeonck' . $module->id);

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();
		$active_id = isset($active) ? $active->id : $menu->getDefault()->id;

		$path = isset($active) ? $active->tree : array();
		$start = (int) $params->get('startLevel');
		$end = (int) $params->get('endLevel');
		$showAll = 1;
		$maxdepth = $params->get('maxdepth');
		$items = $menu->getItems('menutype', $params->get('menutype'));

		$lastitem = 0;

		if ($items) {
			// load the list of all published modules
			$modulesList = ModaccordeonckHelper::CreateModulesList();

			foreach ($items as $i => $item) {

				if (($start && $start > $item->level) || ($end && $item->level > $end) || (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))
						//|| ($maxdepth && $item->level > $maxdepth)
						|| ($start > 1 && !in_array($item->tree[$start - 2], $path))
				) {
					unset($items[$i]);
					continue;
				}

				$item->deeper = false;
				$item->shallower = false;
				$item->level_diff = 0;
				$item->isactive = false;

				if (isset($items[$lastitem])) {
					$items[$lastitem]->deeper = ($item->level > $items[$lastitem]->level);
					$items[$lastitem]->shallower = ($item->level < $items[$lastitem]->level);
					$items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
				}

				$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);

				$lastitem = $i;
				$item->active = false;
				$item->flink = $item->link;

				switch ($item->type) {
					case 'separator':
						// No further action needed.
						continue;

					case 'url':
						if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
							// If this is an internal Joomla link, ensure the Itemid is set.
							$item->flink = $item->link . '&Itemid=' . $item->id;
						}
						break;

					case 'alias':
						// If this is an alias use the item id stored in the parameters to make the link.
						$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
						break;

					default:
						$router = JSite::getRouter();
						if ($router->getMode() == JROUTER_MODE_SEF) {
							$item->flink = 'index.php?Itemid=' . $item->id;
						} else {
							$item->flink .= '&Itemid=' . $item->id;
						}
						break;
				}

				if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
					$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
				} else {
					$item->flink = JRoute::_($item->flink);
				}

				$item->ftitle = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
				$item->anchor_css = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
				$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
				$item->menu_image = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';

				// manage plugin parameters, need the plugin maximenu_ck_params to be installed and active
				//$item->description = $item->params->get('accordeonck_desc', '');
				$item->insertmodule = $item->params->get('accordeonckparams_insertmodule', 0);
				$item->module = $item->params->get('accordeonckparams_module', '');
				$item->content = '';

				// manage description
				$titreCK = explode("||", $item->ftitle);
				if (isset($titreCK[1])) {
					$item->desc = $titreCK[1];
				} else {
					$item->desc = '';
				}
				$item->ftitle = $titreCK[0];
				$item->desc = $item->params->get('accordeonckparams_desc', '') ? $item->params->get('accordeonckparams_desc', '') : $item->desc;
				if ($item->desc) {
					$item->desc = '<span class="accordeonckdesc">' . $item->desc . '</span>';
				}

				// manage rel attribute
				$item->rel = '';
				if ($rel = $item->params->get('accordeonckparams_relattr', '')) {
					$item->rel = ' rel="' . $rel . '"';
				} elseif (preg_match('/\[rel=([a-z]+)\]/i', $item->ftitle, $resultat)) {
					$item->ftitle = preg_replace('/\[rel=[a-z]+\]/i', '', $item->ftitle);
					$item->rel = ' rel="' . $resultat[1] . '"';
				}

				// manage module
				if ($item->insertmodule AND $item->module) {
					$item->content = '<div class="accordeonckmod">' . ModaccordeonckHelper::GenModuleById($item->module, $params, $modulesList) . '<div style="clear:both;"></div></div>';
				} else if (stristr($item->ftitle, '[modid=')) {
					preg_match('/\[modid=([0-9]+)\]/', $item->ftitle, $resultat);
					$item->ftitle = preg_replace('/\[modid=[0-9]+\]/', '', $item->ftitle);
					$item->content = '<div class="accordeonckmod">' . ModaccordeonckHelper::GenModuleById($resultat[1], $params, $modulesList) . '<div style="clear:both;"></div></div>';
				}

				// manage item class
				$item->classe = 'item-' . $item->id;
				if ($item->id == $active_id) {
					$item->classe .= ' current';
				}

				if (in_array($item->id, $path)) {
					$item->classe .= ' active';
					$item->isactive = true;
				} elseif ($item->type == 'alias') {
					$aliasToId = $item->params->get('aliasoptions');
					if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
						$item->classe .= ' active';
						$item->isactive = true;
					} elseif (in_array($aliasToId, $path)) {
						$item->classe .= ' alias-parent-active active';
						$item->isactive = true;
					}
				}

				// css management for the item following the plugin params
				self::injectItemCss($item, $menuID);
				// get plugin parameters that are used directly in the layout
				$item->liclass = $item->params->get('accordeonckparams_liclass', '');
			}

			if (isset($items[$lastitem])) {
				$items[$lastitem]->deeper = (($start ? $start : 1) > $items[$lastitem]->level);
				$items[$lastitem]->shallower = (($start ? $start : 1) < $items[$lastitem]->level);
				$items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ? $start : 1));
			}
		}

		return $items;
	}

	static function GenModuleById($title, &$params, &$modulesList) {
		$attribs['style'] = 'none';

		if (!isset($modulesList[$title]))
			return "<p>No module found !</p>";
		$modtitle = $modulesList[$title]->title;
		$modname = $modulesList[$title]->module;
		//$modname = preg_replace('/mod_/', '', $modname);
		// load the module
		if (JModuleHelper::isEnabled($modname)) {
			$module = JModuleHelper::getModule($modname, $modtitle);
			if ($module) {
				return JModuleHelper::renderModule($module, $attribs);
			}
		}

		return "<p>No module found !</p>";
	}

	static function CreateModulesList() {
		$db = JFactory::getDBO();
		$query = "
			SELECT *
			FROM #__modules
			WHERE published=1
			ORDER BY id
			;";
		$db->setQuery($query);
		$modulesList = $db->loadObjectList('id');
		return $modulesList;
	}

	static function createCss($params, $prefix = 'menu') {

		$css = Array();
		$css['padding'] = ($params->get($prefix . 'padding') AND $params->get($prefix . 'usemargin')) ? 'padding: ' . $params->get($prefix . 'padding', '0') . 'px;' : '';
		$css['margin'] = ($params->get($prefix . 'margin') AND $params->get($prefix . 'usemargin')) ? 'margin: ' . $params->get($prefix . 'margin', '0') . 'px;' : '';
		$css['background'] = ($params->get($prefix . 'bgcolor1') AND $params->get($prefix . 'usebackground')) ? 'background: ' . $params->get($prefix . 'bgcolor1') . ';' : '';
		$css['gradient'] = ($css['background'] AND $params->get($prefix . 'bgcolor2') AND $params->get($prefix . 'usegradient')) ?
				"background: -moz-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%, " . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%);"
				. "background: -webkit-gradient(linear, left top, left bottom, color-stop(0%," . $params->get($prefix . 'bgcolor1', '#f0f0f0') . "), color-stop(100%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . ")); "
				. "background: -webkit-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%);"
				. "background: -o-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%);"
				. "background: -ms-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%);"
				. "background: linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%); "
				. "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='" . $params->get($prefix . 'bgcolor1', '#f0f0f0') . "', endColorstr='" . $params->get($prefix . 'bgcolor2', '#e3e3e3') . "',GradientType=0 );" : '';
		$css['borderradius'] = ($params->get($prefix . 'useroundedcorners')) ?
				'-moz-border-radius: ' . $params->get($prefix . 'roundedcornerstl', '0') . 'px ' . $params->get($prefix . 'roundedcornerstr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbl', '0') . 'px;'
				. '-webkit-border-radius: ' . $params->get($prefix . 'roundedcornerstl', '0') . 'px ' . $params->get($prefix . 'roundedcornerstr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbl', '0') . 'px;'
				. 'border-radius: ' . $params->get($prefix . 'roundedcornerstl', '0') . 'px ' . $params->get($prefix . 'roundedcornerstr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbl', '0') . 'px;' : '';
		$shadowinset = $params->get($prefix . 'shadowinset', 0) ? 'inset ' : '';
		$css['shadow'] = ($params->get($prefix . 'shadowcolor') AND $params->get($prefix . 'shadowblur') AND $params->get($prefix . 'useshadow')) ?
				'-moz-box-shadow: ' . $shadowinset . $params->get($prefix . 'shadowoffsetx', '0') . 'px ' . $params->get($prefix . 'shadowoffsety', '0') . 'px ' . $params->get($prefix . 'shadowblur', '') . 'px ' . $params->get($prefix . 'shadowspread', '0') . 'px ' . $params->get($prefix . 'shadowcolor', '') . ';'
				. '-webkit-box-shadow: ' . $shadowinset . $params->get($prefix . 'shadowoffsetx', '0') . 'px ' . $params->get($prefix . 'shadowoffsety', '0') . 'px ' . $params->get($prefix . 'shadowblur', '') . 'px ' . $params->get($prefix . 'shadowspread', '0') . 'px ' . $params->get($prefix . 'shadowcolor', '') . ';'
				. 'box-shadow: ' . $shadowinset . $params->get($prefix . 'shadowoffsetx', '0') . 'px ' . $params->get($prefix . 'shadowoffsety', '0') . 'px ' . $params->get($prefix . 'shadowblur', '') . 'px ' . $params->get($prefix . 'shadowspread', '0') . 'px ' . $params->get($prefix . 'shadowcolor', '') . ';' : '';
		$css['border'] = ($params->get($prefix . 'bordercolor') AND $params->get($prefix . 'borderwidth') AND $params->get($prefix . 'useborders')) ?
				'border: ' . $params->get($prefix . 'bordercolor', '#efefef') . ' ' . $params->get($prefix . 'borderwidth', '1') . 'px solid;' : '';
		$css['fontsize'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontsize')) ?
				'font-size: ' . $params->get($prefix . 'fontsize') . ';' : '';
		$css['fontcolor'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontcolor')) ?
				'color: ' . $params->get($prefix . 'fontcolor') . ';' : '';
		$css['fontcolorhover'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontcolorhover')) ?
				'color: ' . $params->get($prefix . 'fontcolorhover') . ';' : '';
		$css['descfontsize'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'descfontsize')) ?
				'font-size: ' . $params->get($prefix . 'descfontsize') . ';' : '';
		$css['descfontcolor'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'descfontcolor')) ?
				'color: ' . $params->get($prefix . 'descfontcolor') . ';' : '';
		return $css;
	}

	/*
	 * Method to inject the CSS for a specific Itemid with the plugin params
	 */

	static function injectItemCss($item, $menuID) {
		$itemcss = self::createCss($item->params, 'accordeonckparams_link');
		$itemcsshover = self::createCss($item->params, 'accordeonckparams_linkhover');
		$itemcssactive = self::createCss($item->params, 'accordeonckparams_linkactive');
		$css = '';

		// normal state
		if ($itemcss['padding'] || $itemcss['margin'] || $itemcss['background'] || $itemcss['borderradius'] || $itemcss['shadow'] || $itemcss['border'])
			$css .= "#" . $menuID . " li#item-" . $item->id . " { " . $itemcss['padding'] . $itemcss['margin'] . $itemcss['background'] . $itemcss['gradient'] . $itemcss['borderradius'] . $itemcss['shadow'] . $itemcss['border'] . " }\n";
		if ($itemcss['fontcolor'] || $itemcss['fontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . " > a { " . $itemcss['fontcolor'] . $itemcss['fontsize'] . " }\n";
		if ($itemcss['descfontcolor'] || $itemcss['descfontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . " > a span.accordeonckdesc { " . $itemcss['descfontcolor'] . $itemcss['descfontsize'] . " }\n";

		// hover state
		if ($itemcsshover['padding'] || $itemcsshover['margin'] || $itemcsshover['background'] || $itemcsshover['borderradius'] || $itemcsshover['shadow'] || $itemcsshover['border'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ":hover { " . $itemcsshover['padding'] . $itemcsshover['margin'] . $itemcsshover['background'] . $itemcsshover['gradient'] . $itemcsshover['borderradius'] . $itemcsshover['shadow'] . $itemcsshover['border'] . " }\n";
		if ($itemcsshover['fontcolor'] || $itemcsshover['fontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ":hover > a { " . $itemcsshover['fontcolor'] . $itemcsshover['fontsize'] . " }\n";
		if ($itemcsshover['descfontcolor'] || $itemcsshover['descfontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ":hover > a span.accordeonckdesc { " . $itemcsshover['descfontcolor'] . $itemcsshover['descfontsize'] . " }\n";

		// active state
		if ($itemcssactive['padding'] || $itemcssactive['margin'] || $itemcssactive['background'] || $itemcssactive['borderradius'] || $itemcssactive['shadow'] || $itemcssactive['border'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ".active { " . $itemcssactive['padding'] . $itemcssactive['margin'] . $itemcssactive['background'] . $itemcssactive['gradient'] . $itemcssactive['borderradius'] . $itemcssactive['shadow'] . $itemcssactive['border'] . " }\n";
		if ($itemcssactive['fontcolor'] || $itemcssactive['fontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ".active > a { " . $itemcssactive['fontcolor'] . $itemcssactive['fontsize'] . " }\n";
		if ($itemcssactive['descfontcolor'] || $itemcssactive['descfontsize'])
			$css .= "#" . $menuID . " li#item-" . $item->id . ".active > a span.accordeonckdesc { " . $itemcssactive['descfontcolor'] . $itemcssactive['descfontsize'] . " }\n";

		// inject the css in the page
		if ($css) {
			$document = JFactory::getDocument();
			$document->addStyleDeclaration($css);
		}
	}

}

?>