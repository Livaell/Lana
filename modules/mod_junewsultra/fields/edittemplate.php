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
    $csslink = '<link href="../../../../../media/mod_junewsultra/js/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="../../../../../media/mod_junewsultra/js/jquery-1.8.3.min.js"></script>';
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

$app         = JFactory::getApplication('site');
$current_tpl = explode(":", $_GET["file"]);

if($current_tpl[0] == '_') {
    $jtpl   = $app->getTemplate();
} else {
    $jtpl   = $current_tpl[0];
}

$css = '0';

if (is_file(JPATH_SITE . '/modules/mod_junewsultra/tmpl/'. str_replace('.php', '', $current_tpl[1]) .'/css/style.css')) {
    $css_filename = JPATH_BASE .'/modules/mod_junewsultra/tmpl/'. str_replace('.php', '', $current_tpl[1]) .'/css/style.css';
    $css = '1';
}
if (is_file(JPATH_SITE . '/templates/'. $jtpl .'/html/mod_junewsultra/'. str_replace('.php', '', $current_tpl[1]) .'/css/style.css')) {
  	$css_filename = JPATH_BASE .'/templates/'. $jtpl .'/html/mod_junewsultra/'. str_replace('.php', '', $current_tpl[1]) .'/css/style.css';
    $css = '1';
}

if(isset($_GET["css"])){
    $filename = $css_filename;
} else {
    if (is_file(JPATH_SITE . '/modules/mod_junewsultra/tmpl/'. $current_tpl[1])) {
        $filename = JPATH_BASE .'/modules/mod_junewsultra/tmpl/'. $current_tpl[1];
    }
    if (is_file(JPATH_SITE . '/templates/'. $jtpl .'/html/mod_junewsultra/'. $current_tpl[1])) {
    	$filename = JPATH_BASE .'/templates/'. $jtpl .'/html/mod_junewsultra/'. $current_tpl[1];
    }
}

if (isset($_POST['newd'])) $newdata = $_POST['newd'];

if (isset($newdata) != '') {
    $fw = fopen($filename, 'w') or die('Could not open file!');
    $fb = fwrite($fw,stripslashes($newdata)) or die('Could not write to file');
    fclose($fw);
    chmod($filename, 0777);
}

$fh = fopen($filename, "r") or die("Could not open file!");
$data = fread($fh, filesize($filename)) or die("Could not read file!");

fclose($fh);
chmod($filename, 0777);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language; ?>" lang="<?php echo $language; ?>">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?php echo $csslink; ?>
<link rel="stylesheet" href="../assets/css/codemirror.css" />
<style type="text/css">
/*<![CDATA[*/
body{ background: transparent; font-size: 102%; margin: 0 20px 0 20px;}
.left {
  float: left;
}
.right {
  float: right;
}

.wells {
   position: fixed;
   z-index: 100;
   top: 0;
   left: 0;
   overflow: hiden;
   width: 100%;
   padding: 9px;
   background: #fff;
   border-bottom: 1px solid #ccc;
}

.CodeMirror-scroll {
    margin-top: 50px;
    height: auto!important;
    overflow-y: hidden!important;
    overflow-x: auto!important;
}
/*]]>*/
</style>
</head>
<body>
<form method="post">
    <div class="wells">
        <div class="btn-group left" style="margin-left: 10px;">
        <?php if($css == 1): ?>

            <?php if(isset($_GET["css"])): ?>
                <?php echo '<a href="'. JURI::base().'edittemplate.php?file='.$_GET['file'] .'" class="btn btn-success">Edit template: '. $_GET['file'] .'</a>'; ?>
                <?php echo '<span class="btn disabled">style.css</span>'; ?>
            <?php else: ?>
            <?php echo '<span class="btn disabled">'. $current_tpl[1] .'</span>'; ?>
                <?php echo '<a href="'. JURI::base().'edittemplate.php?file='.$_GET['file'].'&css=1" class="btn btn-success">Edit CSS: style.css</a>'; ?>
            <?php endif; /*?>

        <b>Edit CSS:</b> <?php echo (isset($_GET["css"]) ? '<span style="color: green;">style.css</span>' : '<a href="'. JURI::base().'edittemplate.php?file='.$_GET['file'].'&css=1">style.css</a>'); */?>
        <?php else : ?>
            <?php echo '<span class="btn disabled">'. $current_tpl[1] .'</span>'; ?>
        <?php endif; ?>
        </div>
        <button type="submit" class="btn right" style="margin-right: 20px;">Save template</button>
    </div>
    <div style="clear: both;"></div>
    <textarea name="newd" style="width: 100%; height: 585px; clear: both;" id="newd"><?php echo $data; ?></textarea>
</form>

    <script src="../assets/js/codemirror.js"></script>
    <script src="../assets/js/xml.js"></script>
    <script src="../assets/js/javascript.js"></script>
    <script src="../assets/js/css.js"></script>
    <script src="../assets/js/clike.js"></script>
    <script src="../assets/js/php.js"></script>
    <link rel="stylesheet" href="../assets/css/default.css">
    <link rel="stylesheet" href="../assets/css/elegant.css">
<script type="text/javascript">
      var editor = CodeMirror.fromTextArea(document.getElementById("newd"), {
        lineNumbers: true,
        matchBrackets: true,
        <?php if(isset($_GET["css"])): ?>
        mode: "text/css",
        <?php else: ?>
        mode: "application/x-httpd-php",
        <?php endif; ?>
        indentUnit: 8,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift"
      });
</script>
</body>
</html>