<?php
/*------------------------------------------------------------------------
# mod_rizvn_support - Module RizVN Support
# ------------------------------------------------------------------------
# Author: Phuoc Nguyen
# copyright Copyright (C) 2011 RizVN.Net. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.rizvn.net
# Technical Support: Mail - rizvn93@gmail.com
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die;
$color = ($params->get('color'));
$top = ($params->get('top')); 
JHtml::_('behavior.keepalive');
?>
<link rel="stylesheet" href="<?php echo JURI::base().'modules/mod_rizlogin/'; ?>color/<?php echo $color; ?>/css.css" type="text/css" />
<div id="rizsideBar2" style="top:<?php echo $top; ?>px">
<?php if ($type == 'logout') : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
<a id="sideBarTab2" onclick="rizslide();"><img src="<?php echo JURI::base().'modules/mod_rizlogin/'; ?>color/<?php echo $color; ?>/slide-button-o.gif" alt="RizVN Login" title="RizVN Login" /></a>
	
	<div id="sideBarContents" style="width:0px;">
		<div id="sideBarContentsInner">
	<div style="color:#fff; padding-right: 30px; text-align: center;"><br><br>
		<span style="font-weight: bold;">
		<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('name'));
	} else : {
		echo JText::sprintf('MOD_LOGIN_HINAME', $user->get('username'));
	} endif; ?></span> <br><br><br>
	
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
	</div>

	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.logout" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>	
</form>
<?php else : ?>

	
	<a id="sideBarTab2" onclick="rizslide();"><img src="<?php echo JURI::base().'modules/mod_rizlogin/'; ?>color/<?php echo $color; ?>/slide-button.gif" alt="RizVN Login" title="RizVN Login" /></a>
	
	<div id="sideBarContents" style="width:0px;">
		<div id="sideBarContentsInner">
<div style="color:#fff; padding: 10px; text-align: left;">	

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-inline">
	<?php if ($params->get('pretext')): ?>
		<div class="pretext">
		<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>
	<div class="userdata">
		<div id="form-login-username" class="control-group">
			<div class="controls">
				<div class="input-prepend input-append">
					<span class="add-on"><i class="icon-user tip" title="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>"></i><label for="modlgn-username" class="element-invisible"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME'); ?></label></span><input id="modlgn-username" type="text" name="username" class="input-small" tabindex="1" size="18" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" /><a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>" class="btn hasTooltip" title="<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?>"><i class="icon-question-sign"></i></a>
				</div>
			</div>
		</div>
		<div id="form-login-password" class="control-group">
			<div class="controls">
				<div class="input-prepend input-append">
					<span class="add-on"><i class="icon-lock tip" title="<?php echo JText::_('JGLOBAL_PASSWORD') ?>"></i><label for="modlgn-passwd" class="element-invisible"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label></span><input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="2" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" /><a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>" class="btn hasTooltip" title="<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?>"><i class="icon-question-sign"></i></a>
				</div>
			</div>
		</div>
		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<div id="form-login-remember" class="control-group checkbox">
			<label for="modlgn-remember" class="control-label"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label> <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
		</div>
		<?php endif; ?>
		<div id="form-login-submit" class="control-group">
			<div class="controls">
				<button type="submit" tabindex="3" name="Submit" class="btn btn-primary btn"><?php echo JText::_('JLOGIN') ?></button>
			</div>
		</div>
		<?php
			$usersConfig = JComponentHelper::getParams('com_users');
			if ($usersConfig->get('allowUserRegistration')) : ?>
			<ul class="unstyled">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
					<?php echo JText::_('MOD_LOGIN_REGISTER'); ?> <i class="icon-arrow-right"></i></a>
				</li>

			</ul>
		<?php endif; ?>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<?php if ($params->get('posttext')): ?>
		<div class="posttext">
		<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
</form>
</div>
<?php endif; ?>
</div></div></div>
<script>
$.noConflict();
</script>