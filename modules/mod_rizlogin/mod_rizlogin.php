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

JHTML::_( 'behavior.framework' );

$document = &JFactory::getDocument();

$document->addScript(JURI::base().'modules/mod_rizlogin/js/jquery.min.js');

$document->addScript(JURI::base().'modules/mod_rizlogin/js/jquery-ui.min.js');

$document->addScript(JURI::base().'modules/mod_rizlogin/js/side-bar.js');

// Include the syndicate functions only once

require_once __DIR__ . '/helper.php';

$params->def('greeting', 1);

$type	= modRizLoginHelper::getType();
$return	= modRizLoginHelper::getReturnURL($params, $type);
$user	= JFactory::getUser();

require JModuleHelper::getLayoutPath('mod_rizlogin', $params->get('layout', 'default'));