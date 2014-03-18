<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><div id="<?php echo $id; ?>" class="<?php if($data->get('noscript', 1)) {?>noscript <?php } ?><?php echo $data->get('moduleclass_sfx', ''); ?>">
  <div class="nextend-accordion-menu-inner <?php echo $data->get('class_sfx', 0); ?>">
    <?php if($data->get('moduleshowtitle', 0) ): ?>
    <div class="title">
      <h3><?php echo $this->getTitle(); ?></h3>
    </div>
    <?php endif; ?>
    <div class="nextend-accordion-menu-inner-container">
    <?php $menu->render(dirname(__FILE__).DIRECTORY_SEPARATOR.'item.php'); ?>
    </dl>
    </div>
  </div>
</div>