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

class filterClass extends acymailingClass{

	var $tables = array('filter');
	var $pkey = 'filid';
	var $report = array();
	var $subid;
	var $onlynew = false;

	function trigger($triggerName){
		if(!acymailing_level(3)) return;

		$config = acymailing_config();
		if(!$config->get('triggerfilter_'.$triggerName)) return;

		$this->database->setQuery("SELECT * FROM `#__acymailing_filter` WHERE `trigger` LIKE '%".acymailing_getEscaped($triggerName,true)."%' ORDER BY filid ASC");
		$filters = $this->database->loadObjectList();

		if(empty($filters)){
			$newconfig = new stdClass();
			$name = 'triggerfilter_'.$triggerName;
			$newconfig->$name = 0;
			$config->save($newconfig);
			return;
		}
		foreach($filters as $oneFilter){
			if(empty($oneFilter->published)) continue;
			if(!empty($oneFilter->filter)) $oneFilter->filter = unserialize($oneFilter->filter);
			if(!empty($oneFilter->action)) $oneFilter->action = unserialize($oneFilter->action);
			$this->execute($oneFilter->filter,$oneFilter->action);
		}
	}


	function displayFilters($filters){
		$resultFilters = array();
		if(empty($filters['type'])) return $resultFilters;
		JPluginHelper::importPlugin('acymailing');
		$dispatcher = JDispatcher::getInstance();
		foreach($filters['type'] as $num => $oneType){
			if(empty($oneType)) continue;
			$resultFilters = array_merge($resultFilters,$dispatcher->trigger('onAcyDisplayFilter_'.$oneType,array($filters[$num][$oneType])));
		}
		return $resultFilters;
	}

	function execute($filters,$actions){
		JPluginHelper::importPlugin('acymailing');
		$this->dispatcher = JDispatcher::getInstance();
		$query = new acyQuery();

		if(!empty($this->subid)){
			$subArray = explode(',',trim($this->subid,','));
			JArrayHelper::toInteger($subArray);
			$query->where[] = 'sub.subid IN ('.implode(',',$subArray).')';
		}

		if(!empty($filters['type'])){
			foreach($filters['type'] as $num => $oneType){
				if(empty($oneType)) continue;
				$oldObject = count($query->where) + count($query->leftjoin) + count($query->join);
				$res = $this->dispatcher->trigger('onAcyProcessFilter_'.$oneType,array(&$query,$filters[$num][$oneType],$num));
				$newObject = count($query->where) + count($query->leftjoin) + count($query->join);
				if(count($res) == 0 && $newObject == $oldObject){
					$query->where[] = '0 = 1';
					$this->report[] = 'Function onAcyProcessFilter_'.$oneType.' did not add a condition, filter blocked. Maybe a plugin is missing ?';
				}
			}
		}

		if(!empty($actions['type'])){
			foreach($actions['type'] as $num => $oneType){
				if(empty($oneType)) continue;
				$this->report = array_merge($this->report,$this->dispatcher->trigger('onAcyProcessAction_'.$oneType,array(&$query,$actions[$num][$oneType],$num)));
			}
		}
	}


	function saveForm(){
		$filter = new stdClass();
		$filter->filid = acymailing_getCID('filid');

		$formData = JRequest::getVar( 'data', array(), '', 'array' );

		foreach($formData['filter'] as $column => $value){
			acymailing_secureField($column);
			$filter->$column = strip_tags($value);
		}
		$config = acymailing_config();
		$alltriggers = array_keys((array)JRequest::getVar('trigger'));
		$filter->trigger = implode(',',$alltriggers);
		$newConfig = new stdClass();
		foreach($alltriggers as $oneTrigger){
			$name = 'triggerfilter_'.$oneTrigger;
			if($config->get($name)) continue;
			$newConfig->$name = 1;
		}

		if(in_array('daycron', $alltriggers)){
			$oldTime = $config->get('cron_plugins_next', 0);
			$newHours = JRequest::getVar('triggerhours');
			$newMinutes = JRequest::getVar('triggerminutes');
			$newTime = acymailing_getTime(date('Y').'-'.date('m').'-'.date('d').' '.$newHours.':'.$newMinutes);
			if($newTime < $oldTime || $newTime < time()){
				$newTime += 86400;
			}
			$newConfig->cron_plugins_next = $newTime;
		}

		if(!empty($newConfig)) $config->save($newConfig);

		$data = array('action','filter');
		foreach($data as $oneData){
			$filter->$oneData = array();
			$formData = JRequest::getVar($oneData);
			foreach($formData['type'] as $num => $oneType){
				if(empty($oneType)) continue;
				$filter->{$oneData}['type'][$num] = $oneType;
				$filter->{$oneData}[$num][$oneType] = $formData[$num][$oneType];
			}
			$filter->$oneData = serialize($filter->$oneData);
		}

			$filid = $this->save($filter);
			if(!$filid) return false;

			JRequest::setVar( 'filid', $filid);
			return true;
	}

	function get($filid,$default = null){
		$query = 'SELECT a.* FROM #__acymailing_filter as a WHERE a.`filid` = '.intval($filid).' LIMIT 1';
		$this->database->setQuery($query);

		$filter = $this->database->loadObject();
		if(!empty($filter->filter)){
			$filter->filter = unserialize($filter->filter);
		}

		if(!empty($filter->action)){
			$filter->action = unserialize($filter->action);
		}

		if(!empty($filter->trigger)){
			$filter->trigger = array_flip(explode(',',$filter->trigger));
		}

		return $filter;
	}

	function countReceivers($listids,$filters,$mailid = 0){

		if(empty($listids)) return 0;

		JPluginHelper::importPlugin('acymailing');
		$this->dispatcher = JDispatcher::getInstance();
		$query = new acyQuery();

		JArrayHelper::toInteger($listids);
		$query->from = '#__acymailing_listsub as listsub';
		$query->join[] = '#__acymailing_subscriber as sub ON sub.subid = listsub.subid';
		$query->where[] = 'listsub.listid IN ('.implode(',',$listids).') AND listsub.status=1';
		$config = acymailing_config();
		if($config->get('require_confirmation')){
			$query->where[] = 'sub.confirmed = 1';
		}
		$query->where[] = 'sub.enabled = 1 AND sub.accept = 1';

		if($this->onlynew && !empty($mailid)){
			$query->leftjoin[] = '#__acymailing_userstats as userstats ON sub.subid = userstats.subid AND userstats.mailid = '.intval($mailid);
			$query->where[] = 'userstats.subid IS NULL';
		}

		if(!empty($filters['type'])){
			foreach($filters['type'] as $num => $oneType){
				if(empty($oneType)) continue;
				$this->dispatcher->trigger('onAcyProcessFilter_'.$oneType,array(&$query,$filters[$num][$oneType],$num));
			}
		}

		return $query->count();
	}

	function addJSFilterFunctions(){
		$js = " var numFilters = 0;
				function addAcyFilter(){
					var newdiv = document.createElement('div');
					newdiv.id = 'filter'+numFilters;
					newdiv.className = 'plugarea';
					newdiv.innerHTML = '';
					if(numFilters > 0) newdiv.innerHTML += '".JText::_('FILTER_AND')."';
					newdiv.innerHTML += document.getElementById('filters_original').innerHTML.replace(/__num__/g, numFilters);
					if(document.getElementById('allfilters')){
						document.getElementById('allfilters').appendChild(newdiv); updateFilter(numFilters); numFilters++;
					}
				}
				function countresults(num){ ";
					$app = JFactory::getApplication();
					if(!$app->isAdmin()) $js .= " return; ";
					$js .= "
					document.getElementById('countresult_'+num).innerHTML = '<span class=\"onload\"></span>';
					var form = $('adminForm');
					var data = form.toQueryString();
					data += '&task=countresults&ctrl=filter';
					try{
						new Ajax('index.php?option=com_acymailing&tmpl=component&ctrl=filter&task=countresults&num='+num,{
							method: 'post',
							data: data,
							update: document.getElementById('countresult_'+num)
						}).request();
					}catch(err){
						new Request({
							method: 'post',
							data: data,
							url: 'index.php?option=com_acymailing&tmpl=component&ctrl=filter&task=countresults&num='+num,
							onSuccess: function(responseText, responseXML) {
								document.getElementById('countresult_'+num).innerHTML = responseText;
							}
						}).send();
					}
				}

				function updateFilter(filterNum){
					currentFilterType =window.document.getElementById('filtertype'+filterNum).value;
					if(!currentFilterType){
						window.document.getElementById('filterarea_'+filterNum).innerHTML = '';
						document.getElementById('countresult_'+filterNum).innerHTML = '';
						return;
					}
					filterArea = 'filter__num__'+currentFilterType;
					window.document.getElementById('filterarea_'+filterNum).innerHTML = window.document.getElementById(filterArea).innerHTML.replace(/__num__/g,filterNum);
				}

				function displayCondFilter(fct, element, num, extra){";
					$ctrl = 'filter';
					if(!$app->isAdmin()) $ctrl = 'frontfilter';
					$js .= "
					try{
						var ajaxCall = new Ajax('index.php?option=com_acymailing&tmpl=component&ctrl=".$ctrl."&task=displayCondFilter&fct='+fct+'&num='+num+'&'+extra,{
							method: 'get',
							update: document.getElementById(element)
						}).request();
						ajaxCall.addEvent('onSuccess', function(){countresults(num);});
					}catch(err){
						new Request({
							url:'index.php?option=com_acymailing&tmpl=component&ctrl=".$ctrl."&task=displayCondFilter&fct='+fct+'&num='+num+'&'+extra,
							method: 'get',
							onSuccess: function(responseText, responseXML) {
								document.getElementById(element).innerHTML = responseText;
								countresults(num);
							}
						}).send();
					}
				}";

		if(ACYMAILING_J30) {
			$js .= ' function removeChosen(){
				jQuery("#acybase_filters .chzn-container").remove();
				jQuery("#acybase_filters .chzn-done").removeClass("chzn-done").show();
			}
			window.addEvent("domready", function(){removeChosen();
				setTimeout(function(){
					removeChosen();
			}, 100);});';
		}

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
	}
}

class acyQuery{
	var $leftjoin = array();
	var $join = array();
	var $where = array();
	var $from = '#__acymailing_subscriber as sub';

	function acyQuery(){
		$this->db = JFactory::getDBO();
	}

	function count(){
		$myquery = $this->getQuery(array('COUNT(DISTINCT sub.subid)'));
		$this->db->setQuery($myquery);
		return $this->db->loadResult();
	}

	function getQuery($select = array()){
		$query = '';
		if(!empty($select)) $query .= ' SELECT DISTINCT '.implode(',',$select);
		if(!empty($this->from)) $query .= ' FROM '.$this->from;
		if(!empty($this->join)) $query .= ' JOIN '.implode(' JOIN ',$this->join);
		if(!empty($this->leftjoin)) $query .= ' LEFT JOIN '.implode(' LEFT JOIN ',$this->leftjoin);
		if(!empty($this->where)) $query .= ' WHERE ('.implode(') AND (',$this->where).')';

		return $query;
	}

	function convertQuery($as,$column,$operator,$value){

		$operator = str_replace(array('&lt;','&gt;'),array('<','>'),$operator);

		if($operator == 'CONTAINS'){
			$operator = 'LIKE';
			$value = '%'.$value.'%';
		}elseif($operator == 'BEGINS'){
			$operator = 'LIKE';
			$value = $value.'%';
		}elseif($operator == 'END'){
			$operator = 'LIKE';
			$value = '%'.$value;
		}elseif($operator == 'NOTCONTAINS'){
			$operator = 'NOT LIKE';
			$value = '%'.$value.'%';
		}elseif(!in_array($operator,array('REGEXP','NOT REGEXP','IS NULL','IS NOT NULL','NOT LIKE','LIKE','=','!=','>','<','>=','<='))){
			die('Operator not safe : '.$operator);
		}

		 if(strpos($value,'{time}') !== false){
		 	$value = acymailing_replaceDate($value);
		 	$value = strftime('%Y-%m-%d %H:%M:%S',$value);
		 }

		 $replace = array('{year}','{month}','{day}');
		 $replaceBy = array(date('Y'),date('m'),date('d'));
		 $value = str_replace($replace,$replaceBy,$value);	 

		if(!is_numeric($value) OR in_array($operator,array('REGEXP','NOT REGEXP','NOT LIKE','LIKE'))){
			$value = $this->db->Quote($value);
		}

		if(in_array($operator,array('IS NULL','IS NOT NULL'))){
			$value = '';
		}

		return $as.'.`'.acymailing_secureField($column).'` '.$operator.' '.$value;
	}
}
