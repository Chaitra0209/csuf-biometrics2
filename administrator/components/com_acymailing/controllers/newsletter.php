<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.6.0
 * @author	acyba.com
 * @copyright	(C) 2009-2014 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class NewsletterController extends acymailingController{

	var $aclCat = 'newsletters';

	function replacetags(){
		if(!$this->isAllowed($this->aclCat,'manage')) return;
		$this->store();
		return $this->edit();

	}

	function copy(){
		if(!$this->isAllowed($this->aclCat,'manage')) return;
		JRequest::checkToken() or JSession::checkToken( 'get' ) or die( 'Invalid Token' );

		$cids = JRequest::getVar( 'cid', array(), '', 'array' );
		$db = JFactory::getDBO();
		$time = time();

		$my = JFactory::getUser();
		$creatorId = intval($my->id);

		$addSendDate = '';
		if(!empty($this->copySendDate)) $addSendDate = ', `senddate`';

		foreach($cids as $oneMailid){
			$query = 'INSERT INTO `#__acymailing_mail` (`subject`, `body`, `altbody`, `published`'. $addSendDate . ', `created`, `fromname`, `fromemail`, `replyname`, `replyemail`, `type`, `visible`, `userid`, `alias`, `attach`, `html`, `tempid`, `key`, `frequency`, `params`,`filter`,`metakey`,`metadesc`)';
			$query .= " SELECT CONCAT('copy_',`subject`), `body`, `altbody`, `published`". $addSendDate .", '.$time.', `fromname`, `fromemail`, `replyname`, `replyemail`, `type`, `visible`, '.$creatorId.', `alias`, `attach`, `html`, `tempid`, ".$db->Quote(md5(rand(1000,999999))).', `frequency`, `params`,`filter`,`metakey`,`metadesc` FROM `#__acymailing_mail` WHERE `mailid` = '.(int) $oneMailid;
			$db->setQuery($query);
			$db->query();
			$newMailid = $db->insertid();
			$db->setQuery('INSERT IGNORE INTO `#__acymailing_listmail` (`listid`,`mailid`) SELECT `listid`,'.$newMailid.' FROM `#__acymailing_listmail` WHERE `mailid` = '.(int) $oneMailid);
			$db->query();
		}

		return $this->listing();
	}

	function store(){
		if(!$this->isAllowed($this->aclCat,'manage')) return;
		JRequest::checkToken() or die( 'Invalid Token' );

		$app = JFactory::getApplication();

		$mailClass = acymailing_get('class.mail');
		$status = $mailClass->saveForm();
		if($status){
			$app->enqueueMessage(JText::_( 'JOOMEXT_SUCC_SAVED' ), 'message');
		}else{
			$app->enqueueMessage(JText::_( 'ERROR_SAVING' ), 'error');
			if(!empty($mailClass->errors)){
				foreach($mailClass->errors as $oneError){
					$app->enqueueMessage($oneError, 'error');
				}
			}
		}
	}

	function unschedule(){
		if(!$this->isAllowed($this->aclCat,'schedule')) return;
		$mailid = acymailing_getCID('mailid');

		(JRequest::checkToken() && !empty($mailid)) or die( 'Invalid Token' );
		$mail = new stdClass();
		$mail->mailid = $mailid;
		$mail->senddate = 0;
		$mail->published = 0;

		$mailClass = acymailing_get('class.mail');
		$mailClass->save($mail);

		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('SUCC_UNSCHED'));

		return $this->preview();
	}

	function remove(){
		if(!$this->isAllowed($this->aclCat,'delete')) return;
		JRequest::checkToken() or die( 'Invalid Token' );

		$cids = JRequest::getVar( 'cid', array(), '', 'array' );

		$class = acymailing_get('class.mail');
		$num = $class->delete($cids);

		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS',$num), 'message');

		return $this->listing();
	}

	function savepreview(){
		$this->store();
		return $this->preview();
	}

	function preview(){
		JRequest::setVar( 'layout', 'preview'  );
		JRequest::setVar('hidemainmenu',1);
		return parent::display();
	}

	function sendtest(){
		$this->_sendtest();
		return $this->preview();
	}

	function _sendtest(){
		JRequest::checkToken() or die( 'Invalid Token' );

		$mailid = acymailing_getCID('mailid');

		$receiver_type = JRequest::getVar('receiver_type','','','string');

		if(empty($mailid) OR empty($receiver_type)) return false;

		$app = JFactory::getApplication();
		$mailer = acymailing_get('helper.mailer');
		$mailer->forceVersion = JRequest::getVar('test_html',1,'','int');
		$mailer->autoAddUser = true;
		if($app->isAdmin()) $mailer->SMTPDebug = 1;
		$mailer->checkConfirmField = false;
		$comment = JRequest::getString('commentTest', '');
		if(!empty($comment))
			$mailer->introtext = '<div align="center" style="width:600px;margin:auto;margin-top:10px;margin-bottom:10px;padding:10px;border:1px solid #cccccc;background-color:#f6f6f6;color:#333333;">'.nl2br($comment).'</div>';

		$receivers = array();
		if($receiver_type == 'user'){
			$user = JFactory::getUser();
			$receivers[] = $user->email;
		}elseif($receiver_type == 'other'){
			$receiverEntry = JRequest::getVar('test_email','','','string');
			if(substr_count($receiverEntry,'@')>1){
				$receivers = explode(' ',trim(preg_replace('# +#',' ',str_replace(array(';',','),' ',$receiverEntry))));
			}else{
				$receivers[] = trim($receiverEntry);
			}
		}else{
			$gid = substr($receiver_type,strpos($receiver_type,'_')+1);
			if(empty($gid)) return false;
			$db = JFactory::getDBO();
			$db->setQuery('SELECT email from '.acymailing_table('users',false).' WHERE gid = '.intval($gid));
			$receivers = acymailing_loadResultArray($db);
		}

		if(empty($receivers)){
			$app->enqueueMessage(JText::_('NO_SUBSCRIBER'), 'notice');
			return false;
		}

		$result = true;
		foreach($receivers as $receiver){
			$result = $mailer->sendOne($mailid,$receiver) && $result;
		}

		return $result;
	}

	function upload(){
		if(!$this->isAllowed($this->aclCat,'manage')) return;
		JRequest::setVar( 'layout', 'upload'  );
		return parent::display();
	}

	function douploadnewsletter(){
		if(!$this->isAllowed($this->aclCat,'manage')) return;
		JRequest::checkToken() or die( 'Invalid Token' );

		$templateClass = acymailing_get('class.template');
		$templateClass->checkAreas = false;
		$statusUpload = $templateClass->doupload();

		if($statusUpload){
			$mailClass = acymailing_get('class.mail');
			$mail = new stdClass();
			$newTemplate = $templateClass->get($templateClass->templateId);
			$mail->subject = $newTemplate->name;
			$mail->body = $newTemplate->body;
			$mail->tempid = $templateClass->templateId;

			$idMailCreated = $mailClass->save($mail);
			if($idMailCreated){
				acymailing_display(JText::_('NEWSLETTER_INSTALLED'),'success');
				$js = "setTimeout('redirect()',2000); function redirect(){window.top.location.href = 'index.php?option=com_acymailing&ctrl=newsletter&task=edit&mailid=" .$idMailCreated . "'; }";
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration( $js );
				return;
			} else{
				acymailing_display(JText::_('ERROR_SAVING'),'error');
				return $this->upload();
			}
		} else{
			return $this->upload();
		}
	}
}
