<?php
/**
 * @version		$Id: mod_coolfeed.php 48 2011-06-25 08:22:19Z trung3388@gmail.com $
 * @copyright	JoomAvatar.com
 * @author		Nguyen Quang Trung
 * @link		http://joomavatar.com
 * @license		License GNU General Public License version 2 or later
 */
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
$list = modAvatarArticlesNewsHelper::getList($params);
if (count($list) < 1) return; 
$moduleclassSfx = htmlspecialchars($params->get('moduleclass_sfx'));
$duration = (int) $params->get('duration');

require JModuleHelper::getLayoutPath('mod_avatar_articles_news');