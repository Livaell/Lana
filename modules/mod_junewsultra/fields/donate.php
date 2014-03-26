<?php
/**
* @package Joomla! 2.5
* @version 4.x
* @author 2008-2012 (c)  Denys Nosov (aka Dutch)
* @author web-site: www.joomla-ua.org
* @copyright This module is licensed under a Creative Commons Attribution-Noncommercial-No Derivative Works 3.0 License.
**/

define( '_JEXEC', 1 );
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..");
define ("MAX_SIZE","500");

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'factory.php' );

$mainframe  = JFactory::getApplication('administrator');
$joomlaUser = JFactory::getUser();
$lang       = JFactory::getLanguage();

$mainframe->initialise();
$lang->load('mod_junewsultra', JPATH_SITE);

$language   = mb_strtolower($lang->getTag());

$version    = new JVersion;
$joomla     = substr($version->getShortVersion(), 0, 3);

if($joomla >= '3.0') {
    $csslink = '<link href="../../../../../administrator/templates/isis/css/template.css" rel="stylesheet" type="text/css" />';
} else {
    $csslink = '<link href="../../../../../media/mod_junewsultra/js/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />';
}

function alert($text, $joomla, $error)
{
    if($error == 'message') {
        $error = 'alert-info';
    }
    if($error == 'notice') {
        $error = 'alert-error';
    }

    return '<div class="alert '. $error .' v'.$joomla.'">'. $text .'</div>';
}

if ($joomlaUser->get('id') < 1) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language; ?>" lang="<?php echo $language; ?>">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?php echo $csslink; ?>
</head>
<body>
    <?php echo alert(JText::_('MOD_JUNEWS_LOGIN'), $joomla, 'notice'); ?>
</body>
</html>
<?php
    return;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language; ?>" lang="<?php echo $language; ?>">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?php echo $csslink; ?>
</head>
<body>
<fieldset class="adminform">
    <legend><?php echo JText::_('MOD_JUNEWS_DONATE'); ?></legend>
    <h3 style="margin-top:0;">PayPal</h3>
    <div class="well well-small">
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="margin:0;">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="3EDSBT4BL6KD4">
        <input class="border" type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
        <img alt="" border="0" src="https://www.paypalobjects.com/ru_RU/i/scr/pixel.gif" width="1" height="1">
    </form>
    </div>
    <h3>Interkassa</h3>
    <div class="well well-small">
    <form accept-charset="cp1251" action="https://interkassa.com/lib/payment.php" enctype="application/x-www-form-urlencoded" method="post" name="payment" target="_blank" style="margin:0;">
	    <input name="ik_shop_id" type="hidden" value="6B90164E-0507-DF3F-0AD2-1D4E8B0B4CDD" />
        USD <input name="ik_payment_amount" value="10.00"  style="width:150px;" />
        <input name="ik_payment_id" type="hidden" value="1" />
        <input name="ik_payment_desc" type="hidden" value="Donate" />
        <input name="process" type="submit" value="Donate" class="btn btn-primary" />
    </form>
    </div>
    <h3>WebMoney</h3>
    <div class="well well-small">
    <ul>
    	<li>Z162084860012</li>
    	<li>R371967759323</li>
    </ul>
    </div>
</fieldset>
</body>
</html>