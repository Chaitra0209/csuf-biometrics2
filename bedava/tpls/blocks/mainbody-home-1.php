<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<div class="home">

  <?php if ($this->countModules('home-1')) : ?>
  <!-- HOME SL 1 -->
  <section class="wrap t3-sl t3-sl-1<?php $this->_c('home-1')?>">
    <jdoc:include type="modules" name="<?php $this->_p('home-1') ?>" style="raw" />
  </section>
  <!-- //HOME SL 1 -->
  <?php endif ?>

  <?php if ($this->countModules('home-2')) : ?>
  <!-- HOME SL 2 -->
  <section class="container t3-sl t3-sl-2<?php $this->_c('home-2')?>">
    <jdoc:include type="modules" name="<?php $this->_p('home-2') ?>" style="raw" />
  </section>
  <!-- //HOME SL 2 -->
  <?php endif ?>

  <?php if ($this->countModules('home-3')) : ?>
  <!-- HOME SL 3 -->
  <section class="container t3-sl t3-sl-3<?php $this->_c('home-3')?>">
    <jdoc:include type="modules" name="<?php $this->_p('home-3') ?>" style="raw" />
  </section>
  <!-- //HOME SL 3 -->
  <?php endif ?>

  <?php if ($this->countModules('home-4')) : ?>
  <!-- HOME SL 4 -->
  <section class="container t3-sl t3-sl-4<?php $this->_c('home-4')?>">
      <jdoc:include type="modules" name="<?php $this->_p('home-4') ?>" style="raw" />
  </section>
  <!-- //HOME SL 4 -->
  <?php endif ?>

  <?php if ($this->countModules('home-5')) : ?>
  <!-- HOME SL 5 -->
  <section class="wrap t3-sl t3-sl-5<?php $this->_c('home-5')?>">
    <div class="container">
      <jdoc:include type="modules" name="<?php $this->_p('home-5') ?>" style="raw" />
    </div>
  </section>
  <!-- //HOME SL 5 -->
  <?php endif ?>

</div>
