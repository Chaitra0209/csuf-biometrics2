<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.6.0
 * @author	acyba.com
 * @copyright	(C) 2009-2014 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
<div id="iframedoc"></div>
<form action="<?php echo JRoute::_('index.php?option=com_acymailing&ctrl='.JRequest::getCmd('ctrl')); ?>" method="post" name="adminForm" id="adminForm" >
	<table>
		<tr>
			<td width="100%">
				<?php acymailing_listingsearch($this->escape($this->pageInfo->search)); ?>
			</td>
			<td nowrap="nowrap">
				<?php echo $this->filters->list; ?>
				<?php if($this->app->isAdmin()){ echo $this->filters->creator; }?>
			</td>
		</tr>
	</table>

	<table class="adminlist table table-striped table-hover" cellpadding="1">
		<thead>
			<tr>
				<th class="title titlenum">
					<?php echo JText::_( 'ACY_NUM' );?>
				</th>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="acymailing_js.checkAll(this);" />
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('JOOMEXT_SUBJECT'), 'a.subject', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value ); ?>
				</th>
				<th class="title titledate">
					<?php echo JHTML::_('grid.sort', JText::_('SEND_DATE'), 'a.senddate', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value ); ?>
				</th>
				<th class="title titlesender">
					<?php echo JHTML::_('grid.sort', JText::_('SENDER_INFORMATIONS'), 'a.fromname', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value ); ?>
				</th>
				<th class="title titlesender">
					<?php echo JHTML::_('grid.sort', JText::_('CREATOR'), 'b.name', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value ); ?>
				</th>
				<?php if($this->app->isAdmin()){?>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('JOOMEXT_VISIBLE'), 'a.visible', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value ); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort',   JText::_('ACY_PUBLISHED'), 'a.published', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value ); ?>
				</th>
				<?php } ?>
				<th class="title titleid">
					<?php echo JHTML::_('grid.sort',   JText::_( 'ACY_ID' ), 'a.mailid', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter();
					echo $this->pagination->getResultsCounter();
					if(ACYMAILING_J30) echo '<br/>'.$this->pagination->getLimitBox(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
				$k = 0;

				for($i = 0,$a = count($this->rows);$i<$a;$i++){
					$row =& $this->rows[$i];

					$publishedid = 'published_'.$row->mailid;
					$visibleid = 'visible_'.$row->mailid;
			?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
					<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td align="center">
						<?php echo JHTML::_('grid.id', $i, $row->mailid ); ?>
					</td>
					<td>
						<?php if(acymailing_level(2)){
						 if(acymailing_isAllowed($this->config->get('acl_statistics_manage','all')) && !empty($row->senddate)){
							if($this->app->isAdmin())
								$urlStat = acymailing_completeLink('diagram&task=mailing&mailid='.$row->mailid,true);
							else
								$urlStat = acymailing_completeLink('frontnewsletter&task=stats&mailid='.$row->mailid,true); ?>
							<span class="acystatsbutton"><a class="modal" rel="{handler: 'iframe', size: {x: 800, y: 590}}" href="<?php echo $urlStat; ?>"><img src="<?php echo ACYMAILING_IMAGES; ?>icons/icon-16-stats.png" alt="<?php echo JText::_('STATISTICS',true) ?>" /></a></span>
						<?php } else{
							echo '<span class="acystatsbutton" style="margin-right:12px;">&nbsp;</span>';
						 }
						}

						$subjectLine =  str_replace('<ADV>',$this->escape('<ADV>'),$row->subject);
						echo acymailing_tooltip('<b>'.JText::_('JOOMEXT_ALIAS').' : </b>'.$row->alias, ' ', '', $subjectLine,acymailing_completeLink(($this->app->isAdmin() ? '' : 'front').'newsletter&task=edit&mailid='.$row->mailid));
						?>
					</td>
					<td align="center">
						<?php echo acymailing_getDate($row->senddate); ?>
					</td>
					<td align="center">
						<?php
							if(empty($row->fromname)) $row->fromname = $this->config->get('from_name');
							if(empty($row->fromemail)) $row->fromemail = $this->config->get('from_email');
							if(empty($row->replyname)) $row->replyname = $this->config->get('reply_name');
							if(empty($row->replyemail)) $row->replyemail = $this->config->get('reply_email');
							if(!empty($row->fromname)){
								$text = '<b>'.JText::_('FROM_NAME').' : </b>'.$row->fromname;
								$text .= '<br/><b>'.JText::_('FROM_ADDRESS').' : </b>'.$row->fromemail;
								$text .= '<br/><br/><b>'.JText::_('REPLYTO_NAME').' : </b>'.$row->replyname;
								$text .= '<br/><b>'.JText::_('REPLYTO_ADDRESS').' : </b>'.$row->replyemail;
								echo acymailing_tooltip($text, ' ', '', $row->fromname);
							}
						?>
					</td>
					<td align="center">
						<?php
						if(!empty($row->name)){
							$text = '<b>'.JText::_('JOOMEXT_NAME').' : </b>'.$row->name;
							$text .= '<br/><b>'.JText::_('ACY_USERNAME').' : </b>'.$row->username;
							$text .= '<br/><b>'.JText::_('JOOMEXT_EMAIL').' : </b>'.$row->email;
							$text .= '<br/><b>'.JText::_('ACY_ID').' : </b>'.$row->userid;
							echo acymailing_tooltip($text, $row->name, '', $row->name,'index.php?option=com_users&task=edit&cid[]='.$row->userid);
						}
						?>
					</td>
					<?php if($this->app->isAdmin()){ ?>
					<td align="center">
						<span id="<?php echo $visibleid ?>" class="loading"><?php echo $this->toggleClass->toggle($visibleid,(int) $row->visible,'mail') ?></span>
					</td>
					<td align="center">
						<span id="<?php echo $publishedid ?>" class="loading"><?php echo $this->toggleClass->toggle($publishedid,(int) $row->published,'mail') ?></span>
					</td>
					<?php } ?>
					<td width="1%" align="center">
						<?php echo $row->mailid; ?>
					</td>
				</tr>
			<?php
					$k = 1-$k;
				}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
