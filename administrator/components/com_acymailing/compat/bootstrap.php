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


JHtml::_('bootstrap.framework');

class JHtmlAcyselect extends JHTMLSelect {
	static $event = false;

	public static function booleanlist($name, $attribs = null, $selected = null, $yes = 'JYES', $no = 'JNO', $id = false){
		$arr = array(JHtml::_('select.option', '0', JText::_($no)),JHtml::_('acyselect.option', '1', JText::_($yes)));
		$arr[0]->class = 'btn-danger';
		$arr[1]->class = 'btn-success';
		return JHtml::_('acyselect.radiolist', $arr, $name, $attribs, 'value', 'text', (int) $selected, $id);
	}

	public static function radiolist( $data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false, $vertical = false ){
		reset($data);
		$app = JFactory::getApplication();
		$backend = $app->isAdmin();

		if(!self::$event) {
			self::$event = true;
			$doc = JFactory::getDocument();
			if($backend) {
				$doc->addScriptDeclaration('
(function($){
	$.propHooks.checked = {
		set: function(elem, value, name) {
			var ret = (elem[ name ] = value);
			$(elem).trigger("change");
			return ret;
		}
	};
})(jQuery);');
			} else {
				$doc->addScriptDeclaration('
(function($){
if(!window.acyLocal)
	window.acyLocal = {};
window.acyLocal.radioEvent = function(el) {
	var id = $(el).attr("id"), c = $(el).attr("class"), lbl = $("label[for=\"" + id + "\"]");
	if(c !== undefined && c.length > 0)
		lbl.addClass(c);
	lbl.addClass("active");
	$("input[name=\"" + $(el).attr("name") + "\"]").each(function() {
		if($(this).attr("id") != id) {
			c = $(this).attr("class");
			lbl = $("label[for=\"" + $(this).attr("id") + "\"]");
			if(c !== undefined && c.length > 0)
				lbl.removeClass(c);
			lbl.removeClass("active");
		}
	});
}
$(document).ready(function() {
	setTimeout(function() { $(".acyradios .btn-group label").off("click"); }, 200 );
});

})(jQuery);');
			}
		}

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		$id_text = str_replace(array('[',']'),array('_',''),$idtag ? $idtag : $name);
		$htmlLabels = '';

		if($backend) {
			$html = '<div class="controls"><fieldset id="'.$id_text.'" class="radio btn-group'. ($vertical?' btn-group-vertical':'').'">';
		} else {
			$html = '<div class="acyradios" id="'.$id_text.'">';
		}

		foreach ($data as $obj){
			if(is_string($obj)) {
				$html .= $obj;
				continue;
			}
			$k = $obj->$optKey;
			$t = $translate ? JText::_($obj->$optText) : $obj->$optText;
			$id = (isset($obj->id) ? $obj->id : null);

			$sel = false;
			$extra = $id ? ' id="' . $obj->id . '"' : '';
			$currId = $id_text . $k;
			if(isset($obj->id))
				$currId = $obj->id;

			if (is_array($selected)) {
				foreach ($selected as $val){
					$k2 = is_object($val) ? $val->$optKey : $val;
					if ($k == $k2){
						$extra .= ' selected="selected"';
						$sel = true;
						break;
					}
				}
			}elseif((string) $k == (string) $selected) {
				$extra .= ' checked="checked"';
				$sel = true;
			}

			if(!empty($obj->class)) $extra .= ' class="'.$obj->class.'"';

			if($backend) {
				$html .= "\n\t\n\t".'<input type="radio" name="'.$name.'" id="'.$id_text.$k.'" value="'.$k.'" '.$extra.' '.$attribs.'/>';
				$html .= "\n\t".'<label for="'.$id_text.$k.'">'.$t.'</label>';
			} else {
				$extra = ' '.$extra;
				if(strpos($extra, ' style="') !== false) {
					$extra = str_replace(' style="', ' style="display:none;', $extra);
				} elseif(strpos($extra, 'style=\'') !== false) {
					$extra = str_replace(' style=\'', ' style=\'display:none;', $extra);
				} else {
					$extra .= ' style="display:none;"';
				}
				if(strpos($extra, ' onchange="') !== false) {
					$extra = str_replace(' onchange="', ' onchange="window.acyLocal.radioEvent(this);', $extra);
				} elseif(strpos($extra, 'onchange=\'') !== false) {
					$extra = str_replace(' onchange=\'', ' onchange=\'window.acyLocal.radioEvent(this);', $extra);
				} else {
					$extra .= ' onchange="window.acyLocal.radioEvent(this);"';
				}
				$html .= "\n\t" . '<input type="radio" name="' . $name . '"' . ' id="' . $currId . '" value="' . $k . '"' . ' ' . $extra . ' ' . $attribs . '/>';
				$htmlLabels .= "\n\t"."\n\t" . '<label for="' . $currId . '"' . ' class="btn'. ($sel ? ' active'.(empty($obj->class) ? '' : ' '.$obj->class) : '') .'">' . $t . '</label>';
			}
		}

		if($backend) {
			$html .= '</fieldset></div>';
		} else {
			$html .= "\n" . '<div class="btn-group'. ($vertical?' btn-group-vertical':'').'" data-toggle="buttons-radio">' . $htmlLabels . "\n" . '</div>';
			$html .= "\n" . '</div>';
		}
		$html .= "\n";
		return $html;
	}

}
