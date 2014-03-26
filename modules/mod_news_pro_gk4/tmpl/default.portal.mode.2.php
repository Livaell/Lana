<?php
/**
* Script template
* @package News Show Pro GK4
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 4.3.0.0 $
**/
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php if($this->parent->config['useScript'] != 0) : ?>
<script type="text/javascript" src="<?php echo $uri->root(); ?>modules/mod_news_pro_gk4/interface/scripts/engine.portal.mode.2.js"></script>
<?php endif; ?>
<script type="text/javascript">
//<![CDATA[
try {$Gavick;}catch(e){$Gavick = {};};
$Gavick["nsp-<?php echo $this->parent->config['module_id'];?>"] = {
	"animation_speed": <?php echo $this->parent->config['animation_speed']; ?>,
	"animation_interval": <?php echo $this->parent->config['animation_interval']; ?>
};
//]]>
</script>	