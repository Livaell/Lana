<?php
###################################################################################################
#  Display News  1.6.03 - July -2009 by bkomraz1@gmail.com
#  http://joomlacode.org/gf/project/display_news/
#  Based on Display News - Latest 1-3 [07 June 2004] by Rey Gigataras [stingrey]   www.stingrey.biz  mambo@stingrey.biz
#  @ Released under GNU/GPL License : http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
###################################################################################################

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

require_once (dirname(__FILE__).'/helper.php');

global $modDisplayNewsHelperRuns;
if ( ! $modDisplayNewsHelperRuns ) {
	$modDisplayNewsHelperRuns++;
	$dn = new modDisplayNewsHelper();
	$dn->main($params, $module->id);
	$modDisplayNewsHelperRuns--;
}
?>
