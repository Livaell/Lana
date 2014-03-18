<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
if ($item->fib) {
    $this->stack[] = $item->parent;
    $this->level = count($this->stack);
}
if ($this->up) {
    while ($this->level > $item->level) {
?>
</dl></dd>
<?php
        array_pop($this->stack);
        $this->level = count($this->stack);
    }
    $this->up = false;
}
$classes = array(
    'level' . $this->level,
    $this->_classPrefix . $item->id,
    ($item->p ? "parent" : "notparent") ,
    ($item->opened ? "opened" : "") ,
    ($item->active ? "active" : "")
);
if (isset($this->openedlevels[$this->level]) && $item->p) $classes[] = 'opened forceopened';
if ($item->fib) $classes[] = 'first';
if ($item->lib) $classes[] = 'last';
$classes = implode(' ', $classes);
if ($item->fib):
?>
<dl class="level<?php echo $this->level." ".$item->classes ?>">
<?php
endif; ?>
  <dt class="<?php echo $classes ?>">
    <span class="outer">
      <span class="inner">
        <?php echo $item->nname; ?>
      </span>
    </span>
  </dt>
  <dd class="<?php echo $classes ?>">
    <?php echo $content; ?>
    <?php if ($item->p):
    $this->renderItem();
else: ?>
  </dd>
  <?php
endif; ?>
<?php
if ($item->lib):
    $this->up = true;
?>
<?php
endif; ?>