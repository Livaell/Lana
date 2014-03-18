<?php
/**
 * @copyright	Copyright (C) 2012 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<!-- debut maximenu CK, par cedric keiflin -->
<select name="maximenuckdropselect" style="width: auto" onchange="top.location.href=this.options[this.selectedIndex].value" class="<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php
	foreach ($items as $i => &$item) {
		$selected = ($item->current) ? ' selected="selected"' : '';
		echo "<option " . $selected . "value=\"" . $item->flink . "\">" . str_repeat("- ", $item->level - 1) . $item->ftitle . "</option>";
	}
	?>
</select>
<!-- fin maximenuCK -->
