<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die;

class modMaximenuckHelper {

	/**
	 * Get a list of the menu items.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 */
	static function getItems(&$params) {
		$app = JFactory::getApplication();
		$menu = $app->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

//		$user = JFactory::getUser();
//		$levels = $user->getAuthorisedViewLevels();
//		asort($levels);
//		$key = 'menu_items' . $params . implode(',', $levels) . '.' . $active->id;
//		$cache = JFactory::getCache('mod_maximenuck', '');
//		if (!($items = $cache->get($key)) || (int) $params->get('cache') == '0') {
			// Initialise variables.
			$list = array();
			$modules = array();
			$db = JFactory::getDbo();
			$document = JFactory::getDocument();

			// load the libraries
			jimport('joomla.application.module.helper');

			$path = isset($active) ? $active->tree : array();
			$start = (int) $params->get('startLevel');
			$end = (int) $params->get('endLevel');
			$items = $menu->getItems('menutype', $params->get('menutype'));

			// if no items in the menu then exit
			if (!$items)
				return false;

			$lastitem = 0;
			// list all modules
			$modulesList = modmaximenuckHelper::CreateModulesList();

			foreach ($items as $i => $item) {
				$isdependant = $params->get('dependantitems', false) ? ($start > 1 && !in_array($item->tree[$start - 2], $path)) : false;
				if (($start && $start > $item->level) || ($end && $item->level > $end) || $isdependant
				) {
					unset($items[$i]);
					continue;
				}

				$item->deeper = false;
				$item->shallower = false;
				$item->level_diff = 0;

				if (isset($items[$lastitem])) {
					$items[$lastitem]->deeper = ($item->level > $items[$lastitem]->level);
					$items[$lastitem]->shallower = ($item->level < $items[$lastitem]->level);
					$items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
				}

				// Test if this is the last item
				$item->is_end = !isset($items[$i + 1]);

				$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);
				$item->active = false;
				$item->current = false;
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
						$item->flink = JFilterOutput::ampReplace(htmlspecialchars($item->flink));
						break;

					case 'alias':
						// If this is an alias use the item id stored in the parameters to make the link.
						$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
						break;

					default:
						$router = $app::getRouter();
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

				//$item->title = htmlspecialchars($item->title);
				$item->anchor_css = htmlspecialchars($item->params->get('menu-anchor_css', ''));
				$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''));
				$item->menu_image = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', '')) : '';



				//  ---------------- begin the maximenu work on items --------------------

				$item->ftitle = htmlspecialchars($item->title);
				$item->ftitle = JFilterOutput::ampReplace($item->ftitle);
				$parentItem = modMaximenuckHelper::getParentItem($item->parent_id, $items);

				// ---- add some classes ----
				// add itemid class
				$item->classe = ' item' . $item->id;
				// add current class
				if (isset($active) && $active->id == $item->id) {
					$item->classe .= ' current';
					$item->current = true;
				}
				// add active class
				if (is_array($path) &&
						( ($item->type == 'alias' && in_array($item->params->get('aliasoptions'), $path)) || in_array($item->id, $path))) {
					$item->classe .= ' active';
					$item->active = true;
				}
				// add the parent class
				if ($item->deeper) {
					$item->classe .= ' deeper';
				}

				if ($item->parent && ($end == 0 || (int)$item->level < (int)$end)) {
					if ($params->get('layout', 'default') != '_:flatlist')
						$item->classe .= ' parent';
				}

				// add last and first class
				$item->classe .= $item->is_end ? ' last' : '';
				$item->classe .=!isset($items[$i - 1]) ? ' first' : '';

				if (isset($items[$lastitem])) {
					$items[$lastitem]->classe .= $items[$lastitem]->shallower ? ' last' : '';
					$item->classe .= $items[$lastitem]->deeper ? ' first' : '';
					if (isset($items[$i + 1]) AND $item->level - $items[$i + 1]->level > 1 AND $parentItem) {
						$parentItem->classe .= ' last';
					}
				}


				// ---- manage params ----
				// -- manage column --
				$item->colwidth = $item->params->get('maximenu_colwidth', '180');
				$item->createnewrow = $item->params->get('maximenu_createnewrow', 0) || stristr($item->ftitle, '[newrow]');
				// check if there is a width for the subcontainer
				preg_match('/\[subwidth=([0-9]+)\]/', $item->ftitle, $subwidth);
				$subwidth = isset($subwidth[1]) ? $subwidth[1] : '';
				if ($subwidth)
					$item->ftitle = preg_replace('/\[subwidth=[0-9]+\]/', '', $item->ftitle);
				$item->submenucontainerwidth = $item->params->get('maximenu_submenucontainerwidth', '') ? $item->params->get('maximenu_submenucontainerwidth', '') : $subwidth;

				if ($item->params->get('maximenu_createcolumn', 0)) {
					$item->colonne = true;
					// add the value to give the total parent container width
					if (isset($parentItem->submenuswidth)) {
						$parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($item->colwidth);
					} else {
						$parentItem->submenuswidth = strval($item->colwidth);
					}
					// if specified by user with the plugin, then give the width to the parent container
					if (isset($items[$lastitem]) && $items[$lastitem]->deeper) {
						$items[$lastitem]->nextcolumnwidth = $item->colwidth;
					}
					$item->columnwidth = $item->colwidth;
				} elseif (preg_match('/\[col=([0-9]+)\]/', $item->ftitle, $resultat)) {
					$item->ftitle = str_replace('[newrow]', '', $item->ftitle);
					$item->ftitle = preg_replace('/\[col=[0-9]+\]/', '', $item->ftitle);
					$item->colonne = true;
					if (isset($parentItem->submenuswidth)) {
						$parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($resultat[1]);
					} else {
						if (isset($parentItem)) $parentItem->submenuswidth = strval($resultat[1]);
					}
					if (isset($items[$lastitem]) && $items[$lastitem]->deeper) {
						$items[$lastitem]->nextcolumnwidth = $resultat[1];
					}
					$item->columnwidth = $resultat[1];
				}
				if (isset($parentItem->submenucontainerwidth) AND $parentItem->submenucontainerwidth)
					$parentItem->submenuswidth = $parentItem->submenucontainerwidth;

				// -- manage module --
				$moduleid = $item->params->get('maximenu_module', '');
				$style = $item->params->get('maximenu_forcemoduletitle', 0) ? 'xhtml' : '';
				if ($item->params->get('maximenu_insertmodule', 0)) {
					if (!isset($modules[$moduleid]))
						$modules[$moduleid] = modmaximenuckHelper::GenModuleById($moduleid, $params, $modulesList, $style);
					$item->content = '<div class="maximenuck_mod">' . $modules[$moduleid] . '<div class="clr"></div></div>';
				} elseif (preg_match('/\[modid=([0-9]+)\]/', $item->ftitle, $resultat)) {
					$item->ftitle = preg_replace('/\[modid=[0-9]+\]/', '', $item->ftitle);
					$item->content = '<div class="maximenuck_mod">' . modmaximenuckHelper::GenModuleById($resultat[1], $params, $modulesList, $style) . '<div class="clr"></div></div>';
				}

				// -- manage rel attribute --
				$item->rel = '';
				if ($rel = $item->params->get('maximenu_relattr', '')) {
					$item->rel = ' rel="' . $rel . '"';
				} elseif (preg_match('/\[rel=([a-z]+)\]/i', $item->ftitle, $resultat)) {
					$item->ftitle = preg_replace('/\[rel=[a-z]+\]/i', '', $item->ftitle);
					$item->rel = ' rel="' . $resultat[1] . '"';
				}

				// -- manage link description --
				$item->description = $item->params->get('maximenu_desc', '');
				if ($item->description) {
					$item->desc = $item->description;
				} else {
					$resultat = explode("||", $item->ftitle);
					if (isset($resultat[1])) {
						$item->desc = $resultat[1];
					} else {
						$item->desc = '';
					}
					$item->ftitle = $resultat[0];
				}

				// add the anchor tag
				$item->flink .= $item->params->get('maximenu_anchor', '') ? '#' . $item->params->get('maximenu_anchor', '') : '';

				// add styles to the page for customization
				$menuID = $params->get('menuid', 'maximenuck');
				$itemstyles = "";
				if ($item->titlecolor = $item->params->get('maximenu_titlecolor', ''))
					$itemstyles .= "div#" . $menuID . " ul.maximenuck li.item" . $item->id . " > a span.titreck {color:" . $item->titlecolor . " !important;} div#" . $menuID . " ul.maximenuck li.item" . $item->id . " > span.separator span.titreck {color:" . $item->titlecolor . " !important;}";
				if ($item->desccolor = $item->params->get('maximenu_desccolor', ''))
					$itemstyles .= "div#" . $menuID . " ul.maximenuck li.item" . $item->id . " > a span.descck {color:" . $item->desccolor . " !important;} div#" . $menuID . " ul.maximenuck li.item" . $item->id . " > span.separator span.descck {color:" . $item->desccolor . " !important;}";
				if ($item->titlehovercolor = $item->params->get('maximenu_titlehovercolor', ''))
					$itemstyles .= "div#" . $menuID . " ul.maximenuck li.item" . $item->id . " > a:hover span.titreck {color:" . $item->titlehovercolor . " !important;} div#" . $menuID . " ul.maximenuck li.item" . $item->id . " > span.separator:hover span.titreck {color:" . $item->titlehovercolor . " !important;}";
				if ($item->deschovercolor = $item->params->get('maximenu_deschovercolor', ''))
					$itemstyles .= "div#" . $menuID . " ul.maximenuck li.item" . $item->id . " > a:hover span.descck {color:" . $item->deschovercolor . " !important;} div#" . $menuID . " ul.maximenuck li.item" . $item->id . " > span.separator:hover span.descck {color:" . $item->deschovercolor . " !important;}";
				if ($item->titleactivecolor = $item->params->get('maximenu_titleactivecolor', ''))
					$itemstyles .= "div#" . $menuID . " ul.maximenuck li.active.item" . $item->id . " > a span.titreck {color:" . $item->titleactivecolor . " !important;} div#" . $menuID . " ul.maximenuck li.active.item" . $item->id . " > span.separator span.titreck {color:" . $item->titleactivecolor . " !important;}";
				if ($item->descactivecolor = $item->params->get('maximenu_descactivecolor', ''))
					$itemstyles .= "div#" . $menuID . " ul.maximenuck li.active.item" . $item->id . " > a span.descck {color:" . $item->descactivecolor . " !important;} div#" . $menuID . " ul.maximenuck li.active.item" . $item->id . " > span.separator span.descck {color:" . $item->descactivecolor . " !important;}";
				if ($item->libgcolor = $item->params->get('maximenu_libgcolor', ''))
					$itemstyles .= "div#" . $menuID . " ul.maximenuck li.item" . $item->id . " {background:" . $item->libgcolor . " !important;}";
				if ($item->lihoverbgcolor = $item->params->get('maximenu_lihoverbgcolor', ''))
					$itemstyles .= "div#" . $menuID . " ul.maximenuck li.item" . $item->id . ":hover {background:" . $item->lihoverbgcolor . " !important;}";
				if ($itemstyles)
					$document->addStyleDeclaration($itemstyles);

				// get plugin parameters that are used directly in the layout
				$item->leftmargin = $item->params->get('maximenu_leftmargin', '');
				$item->topmargin = $item->params->get('maximenu_topmargin', '');
				$item->liclass = $item->params->get('maximenu_liclass', '');
				$item->colbgcolor = $item->params->get('maximenu_colbgcolor', '');
				$item->tagcoltitle = $item->params->get('maximenu_tagcoltitle', 'none');
				$item->submenucontainerheight = $item->params->get('maximenu_submenucontainerheight', '');

				// set the item styles if the plugin is enabled
				if (JPluginHelper::isEnabled('system', 'maximenuckparams')) {
					$itemcss = self::injectItemCss($item, $menuID, $params);
					if ($itemcss)
						$document->addStyleDeclaration($itemcss);
				}

				$lastitem = $i;
			} // end of boucle for each items
			// give the correct deep infos for the last item
			if (isset($items[$lastitem])) {
				$items[$lastitem]->deeper = (($start ? $start : 1) > $items[$lastitem]->level);
				$items[$lastitem]->shallower = (($start ? $start : 1) < $items[$lastitem]->level);
				$items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ? $start : 1));
			}

//			$cache->store($items, $key);
//		}
		return $items;
	}

	/**
	 * Get a the parent item object
	 *
	 * @param Object $id The current item
	 * @param Array $items The list of all items
	 *
	 * @return object
	 */
	static function getParentItem($id, $items) {
		foreach ($items as $item) {
			if ($item->id == $id)
				return $item;
		}
		return false;
	}

	/**
	 * Render the module
	 *
	 * @param Int $moduleid The module ID to load
	 * @param JRegistry $params
	 * @param Array $modulesList The list of all module objects published
	 *
	 * @return string with HTML
	 */
	static function GenModuleById($moduleid, $params, $modulesList, $style) {


		$attribs['style'] = $style;
		// get the title of the module to load
		$modtitle = $modulesList[$moduleid]->title;
		$modname = $modulesList[$moduleid]->module;
		//$modname = preg_replace('/mod_/', '', $modname);
		// load the module
		if (JModuleHelper::isEnabled($modname)) {
			$module = JModuleHelper::getModule($modname, $modtitle);
			return JModuleHelper::renderModule($module, $attribs);
		}
		return 'Module ID=' . $moduleid . ' not found !';
	}

	/**
	 * Create the list of all modules published as Object
	 *
	 * @return Array of Objects
	 */
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

	/**
	 * Create the css properties
	 * @param JRegistry $params
	 * @param string $prefix the xml field prefix
	 *
	 * @return Array
	 */
	static function createCss($menuID, $params, $prefix = 'menu', $important = false, $itemid = '') {
// var_dump($params->get($prefix . 'paddingtop'));
		$css = Array();
		$important = ($important == true ) ? ' !important' : '';
		$csspaddingtop = ($params->get($prefix . 'paddingtop') != '' AND $params->get($prefix . 'usemargin')) ? 'padding-top: ' . self::testUnit($params->get($prefix . 'paddingtop', '0')) . $important . ';' : '';
		$csspaddingright = ($params->get($prefix . 'paddingright') != '' AND $params->get($prefix . 'usemargin')) ? 'padding-right: ' . self::testUnit($params->get($prefix . 'paddingright', '0')) . $important . ';' : '';
		$csspaddingbottom = ($params->get($prefix . 'paddingbottom') != '' AND $params->get($prefix . 'usemargin') ) ? 'padding-bottom: ' . self::testUnit($params->get($prefix . 'paddingbottom', '0')) . $important . ';' : '';
		$csspaddingleft = ($params->get($prefix . 'paddingleft') != '' AND $params->get($prefix . 'usemargin')) ? 'padding-left: ' . self::testUnit($params->get($prefix . 'paddingleft', '0')) . $important . ';' : '';
		$css['padding'] = $csspaddingtop . $csspaddingright . $csspaddingbottom . $csspaddingleft;
		$cssmargintop = ($params->get($prefix . 'margintop') != '' AND $params->get($prefix . 'usemargin')) ? 'margin-top: ' . self::testUnit($params->get($prefix . 'margintop', '0')) . $important . ';' : '';
		$cssmarginright = ($params->get($prefix . 'marginright') != '' AND $params->get($prefix . 'usemargin')) ? 'margin-right: ' . self::testUnit($params->get($prefix . 'marginright', '0')) . $important . ';' : '';
		$cssmarginbottom = ($params->get($prefix . 'marginbottom') != '' AND $params->get($prefix . 'usemargin')) ? 'margin-bottom: ' . self::testUnit($params->get($prefix . 'marginbottom', '0')) . $important . ';' : '';
		$cssmarginleft = ($params->get($prefix . 'marginleft') != '' AND $params->get($prefix . 'usemargin')) ? 'margin-left: ' . self::testUnit($params->get($prefix . 'marginleft', '0')) . $important . ';' : '';
		$css['margin'] = $cssmargintop . $cssmarginright . $cssmarginbottom . $cssmarginleft;
		$bgcolor1 = ($params->get($prefix . 'bgcolor1') && $params->get($prefix . 'bgopacity')) ? self::hex2RGB($params->get($prefix . 'bgcolor1'), $params->get($prefix . 'bgopacity')) : $params->get($prefix . 'bgcolor1');
		$css['background'] = ($params->get($prefix . 'bgcolor1') AND $params->get($prefix . 'usebackground')) ? 'background: ' . $bgcolor1 . $important . ';' : '';
		$css['background'] .= ( $params->get($prefix . 'bgimage') AND $params->get($prefix . 'usebackground')) ? 'background-image: url("' . JURI::ROOT() . $params->get($prefix . 'bgimage') . '")' . $important . ';' : '';
		$css['background'] .= ( $params->get($prefix . 'bgimage') AND $params->get($prefix . 'usebackground')) ? 'background-repeat: ' . $params->get($prefix . 'bgimagerepeat') . $important . ';' : '';
		$css['background'] .= ( $params->get($prefix . 'bgimage') AND $params->get($prefix . 'usebackground')) ? 'background-position: ' . $params->get($prefix . 'bgpositionx') . ' ' . $params->get($prefix . 'bgpositiony') . $important . ';' : '';

		// manage gradient svg for ie9
		$svggradientfile = '';
		if ($css['background'] AND $params->get($prefix . 'bgcolor2') AND $params->get($prefix . 'usegradient')) {
			$svggradientfile = self::createSvgGradient($menuID, $prefix . $itemid, $params->get($prefix . 'bgcolor1', '#f0f0f0'), $params->get($prefix . 'bgcolor2', '#e3e3e3'));
		}
		$svggradient = $svggradientfile ? "background-image: url(\"" . $svggradientfile . "\")" . $important . ";" : "";
		$css['gradient'] = ($css['background'] AND $params->get($prefix . 'bgcolor2') AND $params->get($prefix . 'usegradient')) ?
				$svggradient
				. "background: -moz-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%, " . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%)" . $important . ";"
				. "background: -webkit-gradient(linear, left top, left bottom, color-stop(0%," . $params->get($prefix . 'bgcolor1', '#f0f0f0') . "), color-stop(100%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . "))" . $important . "; "
				. "background: -webkit-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%)" . $important . ";"
				. "background: -o-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%)" . $important . ";"
				. "background: -ms-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%)" . $important . ";"
				. "background: linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%)" . $important . "; " : '';
//                . "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='" . $params->get($prefix . 'bgcolor1', '#f0f0f0') . "', endColorstr='" . $params->get($prefix . 'bgcolor2', '#e3e3e3') . "',GradientType=0 );" : '';
		$css['borderradius'] = ($params->get($prefix . 'useroundedcorners')) ?
				'-moz-border-radius: ' . self::testUnit($params->get($prefix . 'roundedcornerstl', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornerstr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbl', '0')) . $important . ';'
				. '-webkit-border-radius: ' . self::testUnit($params->get($prefix . 'roundedcornerstl', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornerstr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbl', '0')) . $important . ';'
				. 'border-radius: ' . self::testUnit($params->get($prefix . 'roundedcornerstl', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornerstr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbr', '0')) . ' ' . self::testUnit($params->get($prefix . 'roundedcornersbl', '0')) . $important . ';' : '';
		$shadowinset = $params->get($prefix . 'shadowinset', 0) ? 'inset ' : '';
		$css['shadow'] = ($params->get($prefix . 'shadowcolor') AND $params->get($prefix . 'shadowblur') AND $params->get($prefix . 'useshadow')) ?
				'-moz-box-shadow: ' . $shadowinset . self::testUnit($params->get($prefix . 'shadowoffsetx', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowoffsety', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowblur', '')) . ' ' . self::testUnit($params->get($prefix . 'shadowspread', '0')) . ' ' . $params->get($prefix . 'shadowcolor', '') . $important . ';'
				. '-webkit-box-shadow: ' . $shadowinset . self::testUnit($params->get($prefix . 'shadowoffsetx', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowoffsety', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowblur', '')) . ' ' . self::testUnit($params->get($prefix . 'shadowspread', '0')) . ' ' . $params->get($prefix . 'shadowcolor', '') . $important . ';'
				. 'box-shadow: ' . $shadowinset . self::testUnit($params->get($prefix . 'shadowoffsetx', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowoffsety', '0')) . ' ' . self::testUnit($params->get($prefix . 'shadowblur', '')) . ' ' . self::testUnit($params->get($prefix . 'shadowspread', '0')) . ' ' . $params->get($prefix . 'shadowcolor', '') . $important . ';' :
				(($params->get($prefix . 'useshadow') && $params->get($prefix . 'shadowblur') == '0') ? '-moz-box-shadow: none' . $important . ';'
						. '-webkit-box-shadow: none' . $important . ';'
						. 'box-shadow: none' . $important . ';' : '');
		$css['border'] = ($params->get($prefix . 'useborders')) ?
				(($params->get($prefix . 'bordertopwidth') == '0') ? 'border-top: none' . $important . ';' : (($params->get($prefix . 'bordertopwidth') != '' AND $params->get($prefix . 'bordercolor')) ? 'border-top: ' . $params->get($prefix . 'bordercolor', '#efefef') . ' ' . self::testUnit($params->get($prefix . 'bordertopwidth', '1')) . ' solid ' . $important . ';' : '') )
				. (($params->get($prefix . 'borderrightwidth') == '0') ? 'border-right: none' . $important . ';' : (($params->get($prefix . 'borderrightwidth') != '' AND $params->get($prefix . 'bordercolor')) ? 'border-right: ' . $params->get($prefix . 'bordercolor', '#efefef') . ' ' . self::testUnit($params->get($prefix . 'borderrightwidth', '1')) . ' solid ' . $important . ';' : '') )
				. (($params->get($prefix . 'borderbottomwidth') == '0') ? 'border-bottom: none' . $important . ';' : (($params->get($prefix . 'borderbottomwidth') != '' AND $params->get($prefix . 'bordercolor')) ? 'border-bottom: ' . $params->get($prefix . 'bordercolor', '#efefef') . ' ' . self::testUnit($params->get($prefix . 'borderbottomwidth', '1')) . ' solid ' . $important . ';' : '') )
				. (($params->get($prefix . 'borderleftwidth') == '0') ? 'border-left: none' . $important . ';' : (($params->get($prefix . 'borderleftwidth') != '' AND $params->get($prefix . 'bordercolor')) ? 'border-left: ' . $params->get($prefix . 'bordercolor', '#efefef') . ' ' . self::testUnit($params->get($prefix . 'borderleftwidth', '1')) . ' solid ' . $important . ';' : '') ) :
				(($params->get($prefix . 'useborders') && $params->get($prefix . 'borderwidth') == '0') ? 'border: none' . $important . ';' : '');
		$css['fontsize'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontsize')) ?
				'font-size: ' . self::testUnit($params->get($prefix . 'fontsize')) . $important . ';' : '';
		$css['fontcolor'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontcolor')) ?
				'color: ' . $params->get($prefix . 'fontcolor') . $important . ';' : '';
		$css['fontweight'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontweight')) ?
				'font-weight: ' . $params->get($prefix . 'fontweight') . $important . ';' : '';
		/* $css['fontcolorhover'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontcolorhover')) ?
		  'color: ' . $params->get($prefix . 'fontcolorhover') . ';' : ''; */
		$css['descfontsize'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'descfontsize')) ?
				'font-size: ' . $params->get($prefix . 'descfontsize') . $important . ';' : '';
		$css['descfontcolor'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'descfontcolor')) ?
				'color: ' . $params->get($prefix . 'descfontcolor') . $important . ';' : '';
		$textshadowoffsetx = ($params->get($prefix . 'textshadowoffsetx', '0') == '') ? '0px' : self::testUnit($params->get($prefix . 'textshadowoffsetx', '0'));
		$textshadowoffsety = ($params->get($prefix . 'textshadowoffsety', '0') == '') ? '0px' : self::testUnit($params->get($prefix . 'textshadowoffsety', '0'));
		$css['textshadow'] = ($params->get($prefix . 'textshadowcolor') AND $params->get($prefix . 'textshadowblur') AND $params->get($prefix . 'usetextshadow')) ?
				'text-shadow: ' . $textshadowoffsetx . ' ' . $textshadowoffsety . ' ' . self::testUnit($params->get($prefix . 'textshadowblur', '')) . ' ' . $params->get($prefix . 'textshadowcolor', '') . $important . ';' :
				(($params->get($prefix . 'textshadowblur') == '0' AND $params->get($prefix . 'usetextshadow')) ? 'text-shadow: none' . $important . ';' : '');

		return $css;
	}

	/**
	 * Create the svg gradient for IE9
	 * @param string $prefix
	 *
	 * @return void
	 */
	static function createSvgGradient($menuID, $prefix, $color1, $color2) {
		// create the file svg for IE9 and Opera gradient compatibility
		$path = JPATH_ROOT . '/modules/mod_maximenuck/assets/svggradient/';
		$svgie9cssdest = $path . $menuID . $prefix . '-gradient.svg';

		$svgie9csstext = '<?xml version="1.0" ?>
            <svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" version="1.0" width="100%"
            height="100%"
            xmlns:xlink="http://www.w3.org/1999/xlink">

            <defs>
            <linearGradient id="' . $menuID . $prefix . '"
            x1="0%" y1="0%"
            x2="0%" y2="100%"
            spreadMethod="pad">
            <stop offset="0%"   stop-color="' . $color1 . '" stop-opacity="1"/>
            <stop offset="100%" stop-color="' . $color2 . '" stop-opacity="1"/>
            </linearGradient>
            </defs>

            <rect width="100%" height="100%"
            style="fill:url(#' . $menuID . $prefix . ');" />
            </svg>
            ';

		if (!JFile::write($svgie9cssdest, $svgie9csstext))
			return '';

		return JURI::root() . 'modules/mod_maximenuck/assets/svggradient/' . $menuID . $prefix . '-gradient.svg';
	}

	/**
	 * Create the css properties
	 *
	 * @return Array
	 */
	static function injectItemCss($item, $menuID, $params) {
		$start = (int) $params->get('startLevel');
		$itemlevel = ($start > 1) ? $item->level - $start + 1 : $item->level;
		$itemcss = '';
		$cssitemnormal = self::createCss($menuID, $item->params, 'itemnormalstyles', true, $item->id);
		$cssitemhover = self::createCss($menuID, $item->params, 'itemhoverstyles', true, $item->id);
		$cssitemactive = self::createCss($menuID, $item->params, 'itemactivestyles', true, $item->id);
		$csssubmenu = self::createCss($menuID, $item->params, 'submenustyles', true, $item->id);
		//$cssheading = self::createCss($menuID, $item->params, 'headingstyles');

		$separator = ($item->type == 'separator') ? ' > span.separator' : '';
		$document = JFactory::getDocument();
		
		$itemparentimage = $item->params->get('itemnormalstylesparentitemimage', '');
		$itemparentpaddingtop = $item->params->get('itemnormalstylesparentitempaddingtop', '');
		$itemparentpaddingright = $item->params->get('itemnormalstylesparentitempaddingright', '');
		$itemparentpaddingbottom = $item->params->get('itemnormalstylesparentitempaddingbottom', '');
		$itemparentpaddingleft = $item->params->get('itemnormalstylesparentitempaddingleft', '');
		$itemparentbackground = ( $item->params->get('itemnormalstylesparentitemimage')) ? 'background-image: url("' . JURI::ROOT() . $item->params->get('itemnormalstylesparentitemimage') . '") !important;' : '';
		$itemparentbackground .= ( $item->params->get('itemnormalstylesparentitemimage')) ? 'background-repeat: ' . $item->params->get('itemnormalstylesparentitemimagerepeat') . ' !important;' : '';
		$itemparentbackground .= ( $item->params->get('itemnormalstylesparentitemimage')) ? 'background-position: ' . $item->params->get('itemnormalstylesparentitemimagepositionx') . ' ' . $item->params->get('itemnormalstylesparentitemimagepositiony') . ' !important;' : '';
		if ($item->params->get('itemnormalstylesuseparentitem') && ($itemparentimage || $itemparentpaddingtop || $itemparentpaddingright || $itemparentpaddingbottom || $itemparentpaddingleft))
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . " > a, div#" . $menuID . " ul.maximenuck li.maximenuck.parent.item" . $item->id . " > span.separator { " . $itemparentbackground . " padding-top: " . self::testUnit($itemparentpaddingtop) . " !important; padding-right: " . self::testUnit($itemparentpaddingright) . " !important; padding-bottom: " . self::testUnit($itemparentpaddingbottom) . " !important; padding-left: " . self::testUnit($itemparentpaddingleft) . " !important; } ";

		// normal item styles
		if (isset($cssitemnormal)) {
			if ($cssitemnormal['padding'] || $cssitemnormal['margin'] || $cssitemnormal['background'] || $cssitemnormal['gradient'] || $cssitemnormal['borderradius'] || $cssitemnormal['shadow'] || $cssitemnormal['border']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . $separator . ",
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . $separator . "{ " . $cssitemnormal['padding'] . $cssitemnormal['margin'] . $cssitemnormal['background'] . $cssitemnormal['gradient'] . $cssitemnormal['borderradius'] . $cssitemnormal['shadow'] . $cssitemnormal['border'] . " } ";
			}
			if ($cssitemnormal['fontcolor'] || $cssitemnormal['fontsize'] || $cssitemnormal['fontweight']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > a.maximenuck span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > span.separator span.titreck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > a.maximenuck span.titreck, div#" . $menuID . " li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > span.separator span.titreck { " . $cssitemnormal['fontcolor'] . $cssitemnormal['fontsize'] . $cssitemnormal['fontweight'] . " } ";
			}
			if ($cssitemnormal['descfontcolor'] || $cssitemnormal['descfontsize']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > a.maximenuck span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $item->level . " > span.separator span.descck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > a.maximenuck span.descck, div#" . $menuID . " li.maximenuck.item" . $item->id . ".level" . $itemlevel . " > span.separator span.descck { " . $cssitemnormal['descfontcolor'] . $cssitemnormal['descfontsize'] . " } ";
			}
		}

		// hover item styles
		if (isset($cssitemhover)) {
			if ($cssitemhover['padding'] || $cssitemhover['margin'] || $cssitemhover['background'] || $cssitemhover['gradient'] || $cssitemhover['borderradius'] || $cssitemhover['shadow'] || $cssitemhover['border']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . $separator . ":hover,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . $separator . ":hover { " . $cssitemhover['padding'] . $cssitemhover['margin'] . $cssitemhover['background'] . $cssitemhover['gradient'] . $cssitemhover['borderradius'] . $cssitemhover['shadow'] . $cssitemhover['border'] . " } ";
			}
			if ($cssitemhover['fontcolor'] || $cssitemhover['fontsize'] || $cssitemhover['fontweight']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > a.maximenuck span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > span.separator span.titreck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > a.maximenuck span.titreck, div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > span.separator span.titreck { " . $cssitemhover['fontcolor'] . $cssitemhover['fontsize'] . $cssitemhover['fontweight'] . " } ";
			}
			if ($cssitemhover['descfontcolor'] || $cssitemhover['descfontsize']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > a.maximenuck span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > span.separator span.descck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > a.maximenuck span.descck, div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ":hover > span.separator span.descck { " . $cssitemhover['descfontcolor'] . $cssitemhover['descfontsize'] . " } ";
			}
		}

		// active item styles
		if (isset($cssitemactive)) {
			if ($cssitemactive['padding'] || $cssitemactive['margin'] || $cssitemactive['background'] || $cssitemactive['gradient'] || $cssitemactive['borderradius'] || $cssitemactive['shadow'] || $cssitemactive['border']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active" . $separator . ",
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active" . $separator . " { " . $cssitemactive['padding'] . $cssitemactive['margin'] . $cssitemactive['background'] . $cssitemactive['gradient'] . $cssitemactive['borderradius'] . $cssitemactive['shadow'] . $cssitemactive['border'] . " } ";
			}
			if ($cssitemactive['fontcolor'] || $cssitemactive['fontsize'] || $cssitemactive['fontweight']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > a.maximenuck span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > span.separator span.titreck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > a.maximenuck span.titreck, div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > span.separator span.titreck { " . $cssitemactive['fontcolor'] . $cssitemactive['fontsize'] . $cssitemactive['fontweight'] . " } ";
			}
			if ($cssitemactive['descfontcolor'] || $cssitemactive['descfontsize']
			) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > a.maximenuck span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > span.separator span.descck,
div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > a.maximenuck span.descck, div#" . $menuID . " ul.maximenuck2 li.maximenuck.item" . $item->id . ".level" . $itemlevel . ".active > span.separator span.descck { " . $cssitemactive['descfontcolor'] . $cssitemactive['descfontsize'] . " } ";
			}
		}

		// submenu item styles
		if (isset($csssubmenu)) {
			if ($csssubmenu['padding'] || $csssubmenu['margin'] || $csssubmenu['background'] || $csssubmenu['gradient'] || $csssubmenu['borderradius'] || $csssubmenu['shadow'] || $csssubmenu['border']) {
				$itemcss .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $item->level . " > div.floatck,
div#" . $menuID . " .maxipushdownck div.floatck.submenuck" . $item->id . " { " . $csssubmenu['padding'] . $csssubmenu['margin'] . $csssubmenu['background'] . $csssubmenu['gradient'] . $csssubmenu['borderradius'] . $csssubmenu['shadow'] . $csssubmenu['border'] . " } ";
				// $document->addStyleDeclaration("div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $item->level . ".active > a.maximenuck span.titreck { " . $csssubmenu['fontcolor'] . $csssubmenu['fontsize'] . $csssubmenu['fontweight'] . " } ");
				// $document->addStyleDeclaration("div#" . $menuID . " ul.maximenuck li.maximenuck.item" . $item->id . ".level" . $item->level . ".active > a.maximenuck span.descck { " . $csssubmenu['descfontcolor'] . $csssubmenu['descfontsize'] ." } ");
			}
		}

		return $itemcss;
	}

	/**
	 * load the css properties for the module
	 * @param JRegistry $params
	 * @param string $menuID the module ID
	 *
	 * @return void
	 */
	static function injectModuleCss($params, $menuID) {

		$document = JFactory::getDocument();
		// set the prefixes for all xml fieldset
		$prefixes = array('menustyles',
			'level1itemnormalstyles',
			'level1itemhoverstyles',
			'level1itemactivestyles',
			'level2menustyles',
			'level2itemnormalstyles',
			'level2itemhoverstyles',
			'level2itemactivestyles',
			'headingstyles');

		$css = new stdClass();
		$csstoinject = '';
		$important = false;
		foreach ($prefixes as $prefix) {
			$param = $params->get($prefix, '[]');
			$objs = json_decode(str_replace("|qq|", "\"", $param));
			//var_dump($objs);
			if (!$objs)
				continue;

			$fields = new CkCssParams();
			foreach ($objs as $obj) {
				$fieldid = str_replace($prefix . "_", "", $obj->id);
				$fields->$fieldid = isset($obj->value) ? $obj->value : null;
			}
			if ($prefix == 'headingstyles')
				$important = true;
			$css->$prefix = modMaximenuckHelper::createCss($menuID, $fields, $prefix, $important);

			// global options
			if ($prefix == 'menustyles') {

				// load the google font
				$gfont = $fields->get('menustylestextgfont', 'Droid Sans');
				if ($gfont != '0' && $fields->get('menustylesusefont')) {
					$gfonturl = str_replace(" ", "+", $gfont);
					$document->addStylesheet('https://fonts.googleapis.com/css?family=' . $gfonturl);
					$document->addStyleDeclaration("div#" . $menuID . " li > a, div#" . $menuID . " li > span { font-family: " . $gfont . ";}");
				}

				// set the styles for the global menu
				$submenuwidth = $fields->get('menustylessubmenuwidth', '');
				$submenuheight = $fields->get('menustylessubmenuheight', '');
				$submenu1marginleft = $fields->get('menustylessubmenu1marginleft', '');
				$submenu1margintop = $fields->get('menustylessubmenu1margintop', '');
				$submenu2marginleft = $fields->get('menustylessubmenu2marginleft', '');
				$submenu2margintop = $fields->get('menustylessubmenu2margintop', '');
				$csstoinject = '';
				if ($submenuwidth)
					$csstoinject .= "\ndiv#" . $menuID . " div.maximenuck2 { width: " . self::testUnit($submenuwidth) . "; } ";
				if ($submenuheight)
					$csstoinject .= "\ndiv#" . $menuID . " div.floatck, div#" . $menuID . " ul.maximenuck li.maximenuck div.floatck { height: " . self::testUnit($submenuheight) . "; } ";
				if ($submenu1marginleft)
					$csstoinject .= "\ndiv#" . $menuID . " div.floatck, div#" . $menuID . " ul.maximenuck li.maximenuck div.floatck { margin-left: " . self::testUnit($submenu1marginleft) . "; } ";
				if ($submenu1margintop)
					$csstoinject .= "\ndiv#" . $menuID . " div.floatck, div#" . $menuID . " ul.maximenuck li.maximenuck div.floatck { margin-top: " . self::testUnit($submenu1margintop) . "; } ";
				if ($submenu2marginleft)
					$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck div.floatck div.floatck { margin-left: " . self::testUnit($submenu2marginleft) . "; } ";
				if ($submenu2margintop)
					$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck div.floatck div.floatck { margin-top: " . self::testUnit($submenu2margintop) . "; } ";
			}

			if ($prefix == 'level1itemnormalstyles') {
				$itemparentlevel1image = $fields->get('level1itemnormalstylesparentitemimage', '');
				$itemparentlevel1paddingtop = $fields->get('level1itemnormalstylesparentitempaddingtop', '');
				$itemparentlevel1paddingright = $fields->get('level1itemnormalstylesparentitempaddingright', '');
				$itemparentlevel1paddingbottom = $fields->get('level1itemnormalstylesparentitempaddingbottom', '');
				$itemparentlevel1paddingleft = $fields->get('level1itemnormalstylesparentitempaddingleft', '');
				$itemparentlevel1background = ( $fields->get('level1itemnormalstylesparentitemimage')) ? 'background-image: url("' . JURI::ROOT() . $fields->get('level1itemnormalstylesparentitemimage') . '") !important;' : '';
				$itemparentlevel1background .= ( $fields->get('level1itemnormalstylesparentitemimage')) ? 'background-repeat: ' . $fields->get('level1itemnormalstylesparentitemimagerepeat') . ' !important;' : '';
				$itemparentlevel1background .= ( $fields->get('level1itemnormalstylesparentitemimage')) ? 'background-position: ' . $fields->get('level1itemnormalstylesparentitemimagepositionx') . ' ' . $fields->get('level1itemnormalstylesparentitemimagepositiony') . ' !important;' : '';

				if ($fields->get('level1itemnormalstylesuseparentitem') && ($itemparentlevel1image || $itemparentlevel1paddingtop || $itemparentlevel1paddingright || $itemparentlevel1paddingbottom || $itemparentlevel1paddingleft))
					$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1.parent > a, div#" . $menuID . " ul.maximenuck li.maximenuck.level1.parent > span.separator { " . $itemparentlevel1background . " padding-top: " . self::testUnit($itemparentlevel1paddingtop) . " !important; padding-right: " . self::testUnit($itemparentlevel1paddingright) . " !important; padding-bottom: " . self::testUnit($itemparentlevel1paddingbottom) . " !important; padding-left: " . self::testUnit($itemparentlevel1paddingleft) . " !important; } ";
			}

			if ($prefix == 'level2itemnormalstyles') {
				$itemparentlevel2image = $fields->get('level2itemnormalstylesparentitemimage', '');
				$itemparentlevel2paddingtop = $fields->get('level2itemnormalstylesparentitempaddingtop', '');
				$itemparentlevel2paddingright = $fields->get('level2itemnormalstylesparentitempaddingright', '');
				$itemparentlevel2paddingbottom = $fields->get('level2itemnormalstylesparentitempaddingbottom', '');
				$itemparentlevel2paddingleft = $fields->get('level2itemnormalstylesparentitempaddingleft', '');
				$itemparentlevel2background = ( $fields->get('level2itemnormalstylesparentitemimage')) ? 'background-image: url("' . JURI::ROOT() . $fields->get('level2itemnormalstylesparentitemimage') . '") !important;' : '';
				$itemparentlevel2background .= ( $fields->get('level2itemnormalstylesparentitemimage')) ? 'background-repeat: ' . $fields->get('level2itemnormalstylesparentitemimagerepeat') . ' !important;' : '';
				$itemparentlevel2background .= ( $fields->get('level2itemnormalstylesparentitemimage')) ? 'background-position: ' . $fields->get('level2itemnormalstylesparentitemimagepositionx') . ' ' . $fields->get('level2itemnormalstylesparentitemimagepositiony') . ' !important;' : '';

				if ($fields->get('level2itemnormalstylesuseparentitem') && ($itemparentlevel2image || $itemparentlevel2paddingtop || $itemparentlevel2paddingright || $itemparentlevel2paddingbottom || $itemparentlevel2paddingleft))
					$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck.parent > a, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck.parent > span.separator, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck.parent:hover > a, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck.parent:hover > span.separator,
div#" . $menuID . " .maxipushdownck li.maximenuck.parent > a, div#" . $menuID . " .maxipushdownck li.maximenuck.parent > span.separator, div#" . $menuID . " .maxipushdownck li.maximenuck.parent:hover > a, div#" . $menuID . " .maxipushdownck li.maximenuck.parent:hover > span.separator { " . $itemparentlevel2background . " padding-top: " . self::testUnit($itemparentlevel2paddingtop) . " !important; padding-right: " . self::testUnit($itemparentlevel2paddingright) . " !important; padding-bottom: " . self::testUnit($itemparentlevel2paddingbottom) . " !important; padding-left: " . self::testUnit($itemparentlevel2paddingleft) . " !important; } ";
			}
		}

		// root styles
		if (isset($css->menustyles)) {
			if ($css->menustyles['padding'] || $css->menustyles['margin'] || $css->menustyles['background'] || $css->menustyles['gradient'] || $css->menustyles['borderradius'] || $css->menustyles['shadow'] || $css->menustyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck { " . $css->menustyles['padding'] . $css->menustyles['margin'] . $css->menustyles['background'] . $css->menustyles['gradient'] . $css->menustyles['borderradius'] . $css->menustyles['shadow'] . $css->menustyles['border'] . " } ";
			}
			if ($css->menustyles['fontcolor'] || $css->menustyles['fontsize'] || $css->menustyles['textshadow']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck > a span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck > span.separator span.titreck,
div#" . $menuID . " .maxipushdownck li.maximenuck > a span.titreck, div#" . $menuID . " .maxipushdownck li.maximenuck > span.separator span.titreck { " . $css->menustyles['fontcolor'] . $css->menustyles['fontsize'] . $css->menustyles['textshadow'] . " } ";
			}
			if ($css->menustyles['descfontcolor'] || $css->menustyles['descfontsize']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck > a span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck > span.separator span.descck,
div#" . $menuID . " .maxipushdownck li.maximenuck > a span.descck, div#" . $menuID . " .maxipushdownck li.maximenuck > span.separator span.descck { " . $css->menustyles['descfontcolor'] . $css->menustyles['descfontsize'] . " } ";
			}
		}

		// level1 normal items styles
		if (isset($css->level1itemnormalstyles)) {
			if ($css->level1itemnormalstyles['padding'] || $css->level1itemnormalstyles['margin'] || $css->level1itemnormalstyles['background'] || $css->level1itemnormalstyles['gradient'] || $css->level1itemnormalstyles['borderradius'] || $css->level1itemnormalstyles['shadow'] || $css->level1itemnormalstyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1, div#" . $menuID . " ul.maximenuck li.maximenuck.level1.parent { " . $css->level1itemnormalstyles['padding'] . $css->level1itemnormalstyles['margin'] . $css->level1itemnormalstyles['background'] . $css->level1itemnormalstyles['gradient'] . $css->level1itemnormalstyles['borderradius'] . $css->level1itemnormalstyles['shadow'] . $css->level1itemnormalstyles['border'] . " } ";
			}
			if ($css->level1itemnormalstyles['fontcolor'] || $css->level1itemnormalstyles['fontsize'] || $css->level1itemnormalstyles['textshadow']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 > a span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 > span.separator span.titreck { " . $css->level1itemnormalstyles['fontcolor'] . $css->level1itemnormalstyles['fontsize'] . $css->level1itemnormalstyles['fontweight'] . $css->level1itemnormalstyles['textshadow'] . " } ";
			}
			if ($css->level1itemnormalstyles['descfontcolor'] || $css->level1itemnormalstyles['descfontsize']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 > a span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 > span.separator span.descck { " . $css->level1itemnormalstyles['descfontcolor'] . $css->level1itemnormalstyles['descfontsize'] . " } ";
			}
		}

		// level1 hover items styles
		if (isset($css->level1itemhoverstyles)) {
			if ($css->level1itemhoverstyles['padding'] || $css->level1itemhoverstyles['margin'] || $css->level1itemhoverstyles['background'] || $css->level1itemhoverstyles['gradient'] || $css->level1itemhoverstyles['borderradius'] || $css->level1itemhoverstyles['shadow'] || $css->level1itemhoverstyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1:hover, div#" . $menuID . " ul.maximenuck li.maximenuck.level1.parent:hover { " . $css->level1itemhoverstyles['padding'] . $css->level1itemhoverstyles['margin'] . $css->level1itemhoverstyles['background'] . $css->level1itemhoverstyles['gradient'] . $css->level1itemhoverstyles['borderradius'] . $css->level1itemhoverstyles['shadow'] . $css->level1itemhoverstyles['border'] . " } ";
			}
			if ($css->level1itemhoverstyles['fontcolor'] || $css->level1itemhoverstyles['fontsize'] || $css->level1itemhoverstyles['textshadow']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1:hover > a span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1:hover > span.separator span.titreck { " . $css->level1itemhoverstyles['fontcolor'] . $css->level1itemhoverstyles['fontsize'] . $css->level1itemhoverstyles['fontweight'] . $css->level1itemhoverstyles['textshadow'] . " } ";
			}
			if ($css->level1itemhoverstyles['descfontcolor'] || $css->level1itemhoverstyles['descfontsize']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1:hover > a span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1:hover > span.separator span.descck { " . $css->level1itemhoverstyles['descfontcolor'] . $css->level1itemhoverstyles['descfontsize'] . " } ";
			}
		}

		// level1 active items styles
		if (isset($css->level1itemactivestyles)) {
			if ($css->level1itemactivestyles['padding'] || $css->level1itemactivestyles['margin'] || $css->level1itemactivestyles['background'] || $css->level1itemactivestyles['gradient'] || $css->level1itemactivestyles['borderradius'] || $css->level1itemactivestyles['shadow'] || $css->level1itemactivestyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1.active { " . $css->level1itemactivestyles['padding'] . $css->level1itemactivestyles['margin'] . $css->level1itemactivestyles['background'] . $css->level1itemactivestyles['gradient'] . $css->level1itemactivestyles['borderradius'] . $css->level1itemactivestyles['shadow'] . $css->level1itemactivestyles['border'] . " } ";
			}
			if ($css->level1itemactivestyles['fontcolor'] || $css->level1itemactivestyles['fontsize'] || $css->level1itemactivestyles['textshadow']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1.active > a span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1.active > span.separator span.titreck { " . $css->level1itemactivestyles['fontcolor'] . $css->level1itemactivestyles['fontsize'] . $css->level1itemactivestyles['fontweight'] . $css->level1itemactivestyles['textshadow'] . " } ";
			}
			if ($css->level1itemactivestyles['descfontcolor'] || $css->level1itemactivestyles['descfontsize']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1.active > a span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1.active > span.separator span.descck { " . $css->level1itemactivestyles['descfontcolor'] . $css->level1itemactivestyles['descfontsize'] . " } ";
			}
		}

		// submenu styles
		if (isset($css->level2menustyles)) {
			if ($css->level2menustyles['padding'] || $css->level2menustyles['margin'] || $css->level2menustyles['background'] || $css->level2menustyles['gradient'] || $css->level2menustyles['borderradius'] || $css->level2menustyles['shadow'] || $css->level2menustyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck div.floatck,
div#" . $menuID . " .maxipushdownck div.floatck { " . $css->level2menustyles['padding'] . $css->level2menustyles['margin'] . $css->level2menustyles['background'] . $css->level2menustyles['gradient'] . $css->level2menustyles['borderradius'] . $css->level2menustyles['shadow'] . $css->level2menustyles['border'] . " } ";
			}
		}

		// level2 normal items styles
		if (isset($css->level2itemnormalstyles)) {
			if ($css->level2itemnormalstyles['padding'] || $css->level2itemnormalstyles['margin'] || $css->level2itemnormalstyles['background'] || $css->level2itemnormalstyles['gradient'] || $css->level2itemnormalstyles['borderradius'] || $css->level2itemnormalstyles['shadow'] || $css->level2itemnormalstyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck,
div#" . $menuID . " .maxipushdownck li.maximenuck { " . $css->level2itemnormalstyles['padding'] . $css->level2itemnormalstyles['margin'] . $css->level2itemnormalstyles['background'] . $css->level2itemnormalstyles['gradient'] . $css->level2itemnormalstyles['borderradius'] . $css->level2itemnormalstyles['shadow'] . $css->level2itemnormalstyles['border'] . " } ";
			}
			if ($css->level2itemnormalstyles['fontcolor'] || $css->level2itemnormalstyles['fontsize'] || $css->level2itemnormalstyles['textshadow']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck > a span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck > span.separator span.titreck,
div#" . $menuID . " .maxipushdownck li.maximenuck > a span.titreck, div#" . $menuID . " .maxipushdownck li.maximenuck > span.separator span.titreck { " . $css->level2itemnormalstyles['fontcolor'] . $css->level2itemnormalstyles['fontsize'] . $css->level2itemnormalstyles['fontweight'] . $css->level2itemnormalstyles['textshadow'] . " } ";
			}
			if ($css->level2itemnormalstyles['descfontcolor'] || $css->level2itemnormalstyles['descfontsize']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck > a span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck > span.separator span.descck,
div#" . $menuID . " .maxipushdownck li.maximenuck > a span.descck, div#" . $menuID . " .maxipushdownck li.maximenuck > span.separator span.descck { " . $css->level2itemnormalstyles['descfontcolor'] . $css->level2itemnormalstyles['descfontsize'] . " } ";
			}
		}

		// level2 hover items styles
		if (isset($css->level2itemhoverstyles)) {
			if ($css->level2itemhoverstyles['padding'] || $css->level2itemhoverstyles['margin'] || $css->level2itemhoverstyles['background'] || $css->level2itemhoverstyles['gradient'] || $css->level2itemhoverstyles['borderradius'] || $css->level2itemhoverstyles['shadow'] || $css->level2itemhoverstyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck:hover,
div#" . $menuID . " .maxipushdownck li.maximenuck:hover { " . $css->level2itemhoverstyles['padding'] . $css->level2itemhoverstyles['margin'] . $css->level2itemhoverstyles['background'] . $css->level2itemhoverstyles['gradient'] . $css->level2itemhoverstyles['borderradius'] . $css->level2itemhoverstyles['shadow'] . $css->level2itemhoverstyles['border'] . " } ";
			}
			if ($css->level2itemhoverstyles['fontcolor'] || $css->level2itemhoverstyles['fontsize'] || $css->level2itemhoverstyles['textshadow']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck:hover > a span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck:hover > span.separator span.titreck,
div#" . $menuID . " .maxipushdownck li.maximenuck:hover > a span.titreck, div#" . $menuID . " .maxipushdownck li.maximenuck:hover > span.separator span.titreck { " . $css->level2itemhoverstyles['fontcolor'] . $css->level2itemhoverstyles['fontsize'] . $css->level2itemhoverstyles['fontweight'] . $css->level2itemhoverstyles['textshadow'] . " } ";
			}
			if ($css->level2itemhoverstyles['descfontcolor'] || $css->level2itemhoverstyles['descfontsize']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck:hover > a span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck:hover > span.separator span.descck,
div#" . $menuID . " .maxipushdownck li.maximenuck:hover > a span.descck, div#" . $menuID . " .maxipushdownck li.maximenuck:hover > span.separator span.descck { " . $css->level2itemhoverstyles['descfontcolor'] . $css->level2itemhoverstyles['descfontsize'] . " } ";
			}
		}

		// level2 active items styles
		if (isset($css->level2itemactivestyles)) {
			if ($css->level2itemactivestyles['padding'] || $css->level2itemactivestyles['margin'] || $css->level2itemactivestyles['background'] || $css->level2itemactivestyles['gradient'] || $css->level2itemactivestyles['borderradius'] || $css->level2itemactivestyles['shadow'] || $css->level2itemactivestyles['border']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck.active,
div#" . $menuID . " .maxipushdownck li.maximenuck.active { " . $css->level2itemactivestyles['padding'] . $css->level2itemactivestyles['margin'] . $css->level2itemactivestyles['background'] . $css->level2itemactivestyles['gradient'] . $css->level2itemactivestyles['borderradius'] . $css->level2itemactivestyles['shadow'] . $css->level2itemactivestyles['border'] . " } ";
			}
			if ($css->level2itemactivestyles['fontcolor'] || $css->level2itemactivestyles['fontsize'] || $css->level2itemactivestyles['textshadow']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck.active > a span.titreck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck.active > span.separator span.titreck,
div#" . $menuID . " .maxipushdownck li.maximenuck.active > a span.titreck, div#" . $menuID . " .maxipushdownck li.maximenuck.active > span.separator span.titreck { " . $css->level2itemactivestyles['fontcolor'] . $css->level2itemactivestyles['fontsize'] . $css->level2itemactivestyles['fontweight'] . $css->level2itemactivestyles['textshadow'] . " } ";
			}
			if ($css->level2itemactivestyles['descfontcolor'] || $css->level2itemactivestyles['descfontsize']
			) {
				$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck.active > a span.descck, div#" . $menuID . " ul.maximenuck li.maximenuck.level1 li.maximenuck.active > span.separator span.descck,
div#" . $menuID . " .maxipushdownck li.maximenuck.active > a span.descck, div#" . $menuID . " .maxipushdownck li.maximenuck.active > span.separator span.descck { " . $css->level2itemactivestyles['descfontcolor'] . $css->level2itemactivestyles['descfontsize'] . " } ";
			}
		}

		// level1 normal items styles
		if (isset($css->headingstyles)) {
			$headingclass = '.separator';

			$padding = $css->headingstyles['padding'] ? trim($css->headingstyles['padding'], ";") . ";" : '';
			$margin = $css->headingstyles['margin'] ? trim($css->headingstyles['margin'], ";") . ";" : '';
			$background = $css->headingstyles['background'] ? trim($css->headingstyles['background'], ";") . ";" : '';
			$gradient = $css->headingstyles['gradient'] ? trim($css->headingstyles['gradient'], ";") . ";" : '';
			$borderradius = $css->headingstyles['borderradius'] ? trim($css->headingstyles['borderradius'], ";") . ";" : '';
			$shadow = $css->headingstyles['shadow'] ? trim($css->headingstyles['shadow'], ";") . ";" : '';
			$border = $css->headingstyles['border'] ? trim($css->headingstyles['border'], ";") . ";" : '';
			$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck > li.maximenuck.level1.parent ul.maximenuck2 li.maximenuck > " . $headingclass . ",
div#" . $menuID . " .maxipushdownck ul.maximenuck2 li.maximenuck > " . $headingclass . " { " . $padding . $margin . $background . $gradient . $borderradius . $shadow . $border . " } ";
			$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck > li.maximenuck.level1.parent ul.maximenuck2 li.maximenuck > " . $headingclass . " span.titreck,
div#" . $menuID . " .maxipushdownck ul.maximenuck2 li.maximenuck > " . $headingclass . " span.titreck { " . $css->headingstyles['fontcolor'] . $css->headingstyles['fontsize'] . $css->headingstyles['fontweight'] . $css->headingstyles['textshadow'] . " } ";
			$csstoinject .= "\ndiv#" . $menuID . " ul.maximenuck > li.maximenuck.level1.parent ul.maximenuck2 li.maximenuck > " . $headingclass . " span.descck,
div#" . $menuID . " .maxipushdownck ul.maximenuck2 li.maximenuck > " . $headingclass . " span.descck{ " . $css->headingstyles['descfontcolor'] . $css->headingstyles['descfontsize'] . " } ";
		}

		if ($csstoinject)
			$document->addStyleDeclaration($csstoinject);
	}

	/**
	 * Test if there is already a unit, else add the px
	 *
	 * @param string $value
	 * @return string
	 */
	static function testUnit($value) {

		if ((stristr($value, 'px')) OR (stristr($value, 'em')) OR (stristr($value, '%')))
			return $value;

		return $value . 'px';
	}
	/**
	 * Convert a hexa decimal color code to its RGB equivalent
	 *
	 * @param string $hexStr (hexadecimal color value)
	 * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
	 * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
	 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
	 */
	static function hex2RGB($hexStr, $opacity) {
		if (!stristr($opacity, '.')) $opacity = $opacity/100;
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
			return false; //Invalid hex color code
		}
		$rgbacolor = "rgba(" . $rgbArray['red'] . "," . $rgbArray['green'] . "," . $rgbArray['blue'] . "," . $opacity . ")";

		return $rgbacolor;
	}
	

}

// create a new class to manage objects
if (!class_exists('CkCssParams')) {

	class CkCssParams extends stdClass {

		function get($key) {
			return isset($this->$key) ? $this->$key : null;
		}

	}

}
