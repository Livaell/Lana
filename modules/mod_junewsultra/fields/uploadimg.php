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
define('JPATH_BASE', dirname(__FILE__).DS."..".DS."..".DS."..");
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
    $csslink = '
    <link href="../../../../../administrator/templates/isis/css/template.css" rel="stylesheet" type="text/css" />
    <link href="../../../../../media/jui/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <script src="../../../../../media/jui/js/jquery.min.js" type="text/javascript"></script>';
} else {
    $csslink = '<link href="../../../../../media/mod_junewsultra/js/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="../../../../../media/mod_junewsultra/js/jquery-1.8.3.min.js"></script>';
}

$csslink .= '
<script src="../../../../../modules/mod_junewsultra/assets/js/jquery.custom-input-file.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery.noConflict();
    (function($) {
        $(function() {
        $("#lefile").customInputFile({
            filename: "#juCover",
            replacementClass       : "customInputFile",
            replacementClassHover  : "customInputFileHover",
            replacementClassActive : "customInputFileActive",
            filenameClass          : "customInputFileName",
            wrapperClass           : "customInputFileWrapper",
            replacement : $(\'<button />\', {
                "text" : "Select",
                "class": "btn"
            })
        });
        });
    })(jQuery);
</script>
';

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
?>
<?php if ($joomlaUser->get('id') < 1) : ?>
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
endif;

$path               = str_replace('modules'.DS.'mod_junewsultra'.DS.'fields'.DS.'..'.DS.'..'.DS.'..', 'media/mod_junewsultra', JPATH_BASE);
$max_image_width	= 800;
$max_image_height	= 800;
$max_image_size		= 1024 * 1024;
$valid_types 		= array("gif", "jpg", "png", "jpeg");

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
            <legend><?php echo JText::_('MOD_JUNEWS_UPLOAD_MODULE'); ?></legend>
        	<form enctype="multipart/form-data" method="post">
            <span class="input-append">
               <input id="juCover" class="input-mini disabled" style="width:110px!important;" value="" type="text">
               <input id="lefile" name="userfile" type="file" autocomplete="off">
               <button type="submit" class="btn btn-primary"><?php echo JText::_('MOD_JUNEWS_UPLOAD'); ?></button>
            </span>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_image_size; ?>">
        	</form>
        </fieldset>
        <?php
        if (isset($_FILES["userfile"]))
        {
        	if (is_uploaded_file($_FILES['userfile']['tmp_name']))
            {
        		$filename   = $_FILES['userfile']['tmp_name'];
        		$ext        = substr($_FILES['userfile']['name'], 1 + strrpos($_FILES['userfile']['name'], "."));

        		if (filesize($filename) > $max_image_size) {
        		    echo alert(JText::_('MOD_JUNEWS_ERROR1') . $max_image_size.' KB', $joomla, 'notice');
        		} elseif (!in_array($ext, $valid_types)) {
        		    echo alert(JText::_('MOD_JUNEWS_ERROR2'), $joomla, 'notice');
        		} else {
         			$size = GetImageSize($filename);
         			if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height))
                    {
        				if (@move_uploaded_file($filename, $path.'/jn_'.$_FILES['userfile']['name'])) {
        				    echo alert(JText::_('MOD_JUNEWS_NOTICE8'), $joomla, 'message');
        				} else {
        				    echo alert(JText::_('MOD_JUNEWS_ERROR3'), $joomla, 'notice');
        				}
        			} else {
        			    echo alert(JText::_('MOD_JUNEWS_ERROR4'), $joomla, 'notice');
        			}
        		}
        	} else {
        	    echo alert(JText::_('MOD_JUNEWS_ERROR3'), $joomla, 'notice');
        	}
        }
        ?>
    </body>
</html>