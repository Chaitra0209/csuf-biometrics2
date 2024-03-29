<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$sitename = $this->params->get('sitename') ? $this->params->get('sitename') : JFactory::getConfig()->get('sitename');
$slogan = $this->params->get('slogan');
$logotype = $this->params->get('logotype', 'text');
$logoimage = $logotype == 'image' ? $this->params->get('logoimage', '') : '';
if ($logoimage) {
  $logoimage = ' style="background-image:url('.JURI::base(true).'/'.$logoimage.');"';
}
?>

<!-- HEADER -->
<header id="t3-header" class="container t3-header">
  <div class="row">

    <!-- LOGO -->
    <div class="span4 logo">
      <div class="logo-<?php echo $logotype ?>">
        <a href="<?php echo JURI::base(true) ?>" title="<?php echo strip_tags($sitename) ?>"<?php echo $logoimage ?>>
          <span><?php echo $sitename ?></span>
        </a>
        <small class="site-slogan hidden-phone"><?php echo $slogan ?></small>
      </div>
    </div>
    <!-- //LOGO -->
     <!-- HEAD SEARCH -->
      <div class="head-search<?php $this->_c('head-search')?>">     
        <jdoc:include type="modules" name="<?php $this->_p('head-search') ?>" style="raw" />
      </div>
     <!-- //HEAD SEARCH -->
    <?php if ($this->countModules('languageswitcherload')) : ?>
    	<?php if($this->countModules('head-search or languageswitcherload')): ?>
    		<div class="span4 clearfix">  
      		<?php if ($this->countModules('head-search')) : ?>
    		</div>
    		 <?php endif ?>
      <!-- LANGUAGE SWITCHER -->
      <div class="languageswitcherload">
          <jdoc:include type="modules" name="<?php $this->_p('languageswitcherload') ?>" style="raw" />
      <!-- //LANGUAGE SWITCHER -->
      <?php endif ?>
    </div>
    <?php endif ?>

  </div>
</header>
<!-- //HEADER -->
