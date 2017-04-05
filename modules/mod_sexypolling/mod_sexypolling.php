<?php
/**
 * Joomla! component sexypolling
 *
 * @version $Id: mod_sexypolling.php 2012-04-05 14:30:25 svn $
 * @author 2GLux.com
 * @package Sexy Polling
 * @subpackage com_sexypolling
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restircted access');

//get ip
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
	$sexyip=$_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
	$sexyip=$_SERVER['HTTP_X_FORWARDED_FOR'];
}
else {
	$sexyip=$_SERVER['REMOTE_ADDR'];
}

$userRegistered = (JFactory::getUser()->id == 0) ? false : true;

// get a parameter from the module's configuration
$module_id = $module->id;
$id_poll = $params->get('poll_id',1);
$poll_type = $params->get('poll_type',0);
$category_id = $params->get('category_id');
$global_template = $use_com_params = $params->get('use_com_params',1);
$template_id = $params->get('template_id');

if($use_com_params == 1) {
	$comparams = JComponentHelper::getParams( 'com_sexypolling' );
	$answerPermission = $comparams->get( 'answerPermission',1 );
	$autoPublish = $comparams->get( 'autoPublish',1 );
	$autoOpenTimeline = $comparams->get( 'autoOpenTimeline',1);
	$loadJquery = $comparams->get( 'loadJquery',1 );
	$loadJqueryUi = $comparams->get( 'loadJqueryUi',1 );
	$dateFormat = $comparams->get( 'dateFormat',1 );
	$checkIp = $comparams->get( 'checkIp',1 );
	$checkCookie = $comparams->get( 'checkCookie',1 );
	$autoAnimate = $comparams->get( 'autoAnimate',1 );
}
else {
	$answerPermission = $params->get( 'answerPermission',1 );
	$autoPublish = $params->get( 'autoPublish',1 );
	$autoOpenTimeline = $params->get( 'autoOpenTimeline',1);
	$loadJquery = $params->get( 'loadJquery',1 );
	$loadJqueryUi = $params->get( 'loadJqueryUi',1 );
	$dateFormat = $params->get( 'dateFormat',1 );
	$checkIp = $params->get( 'checkIp',1 );
	$checkCookie = $params->get( 'checkCookie',1 );
	$autoAnimate = $params->get( 'autoAnimate',1 );
	$multiple_answers_from_module = $params->get( 'multiple_answers',1 );
}
$multiple_answers_from_module;

$document = JFactory::getDocument();

if(JRequest::getString("option") != 'com_sexypolling') {
	$cssFile = JURI::base(true).'/components/com_sexypolling/assets/css/main.css';
	$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	$cssFile = JURI::base(true).'/components/com_sexypolling/assets/css/ui.slider.extras.css';
	$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	$cssFile = JURI::base(true).'/components/com_sexypolling/assets/css/jquery-ui-1.7.1.custom.css';
	$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	if($loadJquery == 1) {
		$jsFile = JURI::base(true).'/components/com_sexypolling/assets/js/jquery-1.7.2.min.js';
		$document->addScript($jsFile);
		//$document->addScriptDeclaration ( 'jQuery.noConflict();' );
	}
	
	if($loadJqueryUi == 1) {
		$jsFile = JURI::base(true).'/components/com_sexypolling/assets/js/jquery-ui-1.8.19.custom.min.js';
		$document->addScript($jsFile);
	}
	
	$jsFile = JURI::base(true).'/components/com_sexypolling/assets/js/selectToUISlider.jQuery.js';
	$document->addScript($jsFile);
	
	$jsFile = JURI::base(true).'/components/com_sexypolling/assets/js/color.js';
	$document->addScript($jsFile);
	
	$jsFile = JURI::base(true).'/components/com_sexypolling/assets/js/sexypolling.js';
	$document->addScript($jsFile);
}


if($poll_type == 0)
	$cssFile = JURI::base(true).'/components/com_sexypolling/generate.css.php?id_poll='.$id_poll.'&id_module='.$module_id.'&id_template='.$template_id.'&global_template='.$global_template;
else
	$cssFile = JURI::base(true).'/components/com_sexypolling/generate.css.php?id_category='.$category_id.'&id_module='.$module_id.'&id_template='.$template_id.'&global_template='.$global_template;
$document->addStyleSheet($cssFile, 'text/css', null, array());

$sexyAnimationTypeBar = $params->get( 'barAnimationType' );
$sexyAnimationTypeContainer = $params->get( 'colorAnimationType' );
$sexyAnimationTypeContainerMove = $params->get( 'reorderingAnimationType' );

$db = JFactory::getDBO();

$query = 'SELECT '.
			'sp.id polling_id, '.
			'sp.id_template id_template, '.
			'sp.date_start date_start, '.
			'sp.date_end date_end, '.
			'sp.multiple_answers multiple_answers, '.
			'st.styles styles, '.
			'sp.name polling_name, '.
			'sp.question polling_question, '.
			'sa.id answer_id, '.
			'sa.name answer_name '.
			'FROM '.
			'`#__sexy_polls` sp '.
			'JOIN '.
			'`#__sexy_answers` sa ON sa.id_poll = sp.id '.
			'AND sa.published = \'1\' '.
			'LEFT JOIN ';
if($global_template == 1)
	$query .= '`#__sexy_templates` st ON st.id = sp.id_template ';
else
	$query .= '`#__sexy_templates` st ON st.id = '.$template_id.' ';

$query .= 
			'WHERE sp.published = \'1\' ';
if($poll_type == 0) {
	$query .= 'AND sp.id = '.$id_poll.' ';
}
else {
	$query .= 'AND sp.id_category = '.$category_id.' ';
}
	$query .=
				'ORDER BY sp.ordering,sp.name,sa.ordering DESC,sa.name';

$db->setQuery($query);
$polls = $db->loadObjectList();

$polling_words = array(JText::_("MOD_SEXYPOLLING_WORD_1"),JText::_("MOD_SEXYPOLLING_WORD_2"),JText::_("MOD_SEXYPOLLING_WORD_3"),JText::_("MOD_SEXYPOLLING_WORD_4"),JText::_("MOD_SEXYPOLLING_WORD_5"),JText::_("MOD_SEXYPOLLING_WORD_6"),JText::_("MOD_SEXYPOLLING_WORD_7"),JText::_("MOD_SEXYPOLLING_WORD_8"),JText::_("MOD_SEXYPOLLING_WORD_9"),JText::_("MOD_SEXYPOLLING_WORD_10"),JText::_("MOD_SEXYPOLLING_WORD_11"),JText::_("MOD_SEXYPOLLING_WORD_12"),JText::_("MOD_SEXYPOLLING_WORD_13"),JText::_("MOD_SEXYPOLLING_WORD_14"),JText::_("MOD_SEXYPOLLING_WORD_15"),JText::_("MOD_SEXYPOLLING_WORD_16"),JText::_("MOD_SEXYPOLLING_WORD_17"),JText::_("MOD_SEXYPOLLING_WORD_18"),JText::_("MOD_SEXYPOLLING_WORD_19"),JText::_("MOD_SEXYPOLLING_WORD_20"));

for ($i=0, $n=count( $polls ); $i < $n; $i++) {
	$pollings[$polls[$i]->polling_id][] = $polls[$i];
}

$polling_select_id = array();
$custom_styles = array();
$voted_ids = array();
$start_disabled_ids = array();
$end_disabled_ids = array();
$multiple_answers_info_array = array();
$date_format = $dateFormat == 1 ? 'str' : 'digits';
$date_now = strtotime("now");


if(sizeof($pollings) > 0)
foreach ($pollings as $poll_index => $polling_array) {

	//check start,end dates
	if($polling_array[0]->date_start != '0000-00-00' &&  $date_now < strtotime($polling_array[0]->date_start)) {
		$start_disabled_ids[] = array($poll_index,$polling_words[17] . date('F j, Y',strtotime($polling_array[0]->date_start)));
	}
	if($polling_array[0]->date_end != '0000-00-00' &&  $date_now > strtotime($polling_array[0]->date_end)) {
		$end_disabled_ids[] = array($poll_index,$polling_words[18] . date('F j, Y',strtotime($polling_array[0]->date_end)));
	}

	//check cookie
	if ($checkCookie == 1)
		if (isset($_COOKIE["sexy_poll_$poll_index"]))
			if(!in_array($poll_index,$voted_ids))
				$voted_ids[] = $poll_index;
	//check ip
	if ($checkIp == 1) {
		$query = "SELECT ip FROM #__sexy_votes sv JOIN #__sexy_answers sa ON sa.id_poll = '$poll_index' WHERE sv.id_answer = sa.id AND sv.ip = '$sexyip'";
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();
		if($num_rows > 0) {
			if(!in_array($poll_index,$voted_ids))
				$voted_ids[] = $poll_index;
		}
	}

	//set styles
	$custom_styles[$poll_index] = $polling_array[0]->styles;
	echo '<div class="polling_container_wrapper" id="mod_'.$module_id.'_'.$poll_index.'" roll="'.$module_id.'"><div class="polling_container" id="poll_'.$poll_index.'">';
	echo '<div class="polling_name">'.$polling_array[0]->polling_question.'</div>';

	$multiple_answers = $polling_array[0]->multiple_answers;
	if($use_com_params == 0)
		$multiple_answers = $multiple_answers_from_module;
	$multiple_answers_info_array[$poll_index] = $multiple_answers;
	
	$colors_array = array("black","blue","red","litegreen","yellow","liteblue","green","crimson","litecrimson");
	echo '<ul class="polling_ul">';
	foreach ($polling_array as $k => $poll_data) {
		$color_index = $k % 20 + 1;
		$data_color_index = $k % 9;
		echo '<li id="answer_'.$poll_data->answer_id.'" class="polling_li"><div class="animation_block"></div>';
		echo '<div class="answer_name"><label uniq_index="'.$module_id.'_'.$poll_data->answer_id.'" class="twoglux_label">'.$poll_data->answer_name.'</label></div>';
		echo '<div class="answer_input">';
		
		if($multiple_answers == 0)
			echo '<input  id="'.$module_id.'_'.$poll_data->answer_id.'" type="radio" class="poll_answer '.$poll_data->answer_id.' twoglux_styled" value="'.$poll_data->answer_id.'" name="'.$poll_data->polling_id.'" data-color="'.$colors_array[$data_color_index].'" />';
		else
			echo '<input  id="'.$module_id.'_'.$poll_data->answer_id.'" type="checkbox" class="poll_answer '.$poll_data->answer_id.' twoglux_styled" value="'.$poll_data->answer_id.'" name="'.$poll_data->polling_id.'" data-color="'.$colors_array[$data_color_index].'" />';
		
		echo '</div><div class="sexy_clear"></div>';
		echo '<div class="answer_result">
		<div class="answer_navigation polling_bar_'.$color_index.'" id="answer_navigation_'.$poll_data->answer_id.'"><div class="grad"></div></div>
		<div class="answer_votes_data" id="answer_votes_data_'.$poll_data->answer_id.'">'.$polling_words[0].': <span id="answer_votes_data_count_'.$poll_data->answer_id.'"></span><span id="answer_votes_data_count_val_'.$poll_data->answer_id.'" style="display:none"></span> (<span id="answer_votes_data_percent_'.$poll_data->answer_id.'">0</span><span style="display:none" id="answer_votes_data_percent_val_'.$poll_data->answer_id.'"></span>%)</div>
		<div class="sexy_clear"></div>
		</div>';
		echo '</li>';
	}
	echo '</ul>';
	
	//check perrmision, to show add answer option
	if($answerPermission == 1 || ($answerPermission == 0 && $userRegistered)) {
		if(!in_array($poll_index,$voted_ids)) {
			echo '<div class="answer_wrapper opened" ><div style="padding:6px">';
			echo '<div class="add_answer"><input name="answer_name" class="add_ans_name" value="'.$polling_words[11].'" />
			<input type="button" value="'.$polling_words[12].'" class="add_ans_submit" /><input type="hidden" value="'.$poll_index.'" class="poll_id" /><img class="loading_small" src="'.JURI::base(true).'/components/com_sexypolling/assets/images/loading_small.gif" /></div>';
			echo '</div></div>';
		}
	}

	$new_answer_bar_index = ($k + 1) % 20 + 1;


	echo '<span class="polling_bottom_wrapper1"><img src="components/com_sexypolling/assets/images/loading_polling.gif" class="polling_loading" />';
	echo '<input type="button" value="'.$polling_words[6].'" class="polling_submit" id="poll_'.$module_id.'_'.$poll_index.'" />';
	echo '<input type="button" value="'.$polling_words[7].'" class="polling_result" id="res_'.$module_id.'_'.$poll_index.'" /></span>';
	echo '<div class="polling_info"><table cellpadding="0" cellspacing="0" border="0"><tr><td class="left_col">'.$polling_words[1].':<span class="total_votes_val" style="display:none"></span> </td><td class="total_votes right_col"></td></tr><tr><td class="left_col">'.$polling_words[2].': </td><td class="first_vote right_col"></td></tr><tr><td class="left_col">'.$polling_words[3].': </td><td class="last_vote right_col"></td></tr></table></div>';


	//timeline
	$polling_select_id[$poll_index]['select1'] = 'polling_select_'.$module_id.'_'.$poll_index.'_1';
	$polling_select_id[$poll_index]['select2'] = 'polling_select_'.$module_id.'_'.$poll_index.'_2';

	//get count of total votes, min and max dates of voting
	$query = "SELECT COUNT(sv.`id_answer`) total_count, MAX(sv.`date`) max_date,MIN(sv.`date`) min_date FROM `#__sexy_votes` sv JOIN `#__sexy_answers` sa ON sa.id_poll = '$poll_index' WHERE sv.id_answer = sa.id";
	$db->setQuery($query);
	$row_total = $db->loadAssoc();
	$count_total_votes = $row_total['total_count'];
	$min_date = strtotime($row_total['min_date']);
	$max_date = strtotime($row_total['max_date']);
	//if no votes, set time to current
	if((int)$min_date == 0) {
		$min_date = $max_date = strtotime("now");
	}


	$timeline_array = array();


	for($current = $min_date; $current <= $max_date; $current += 86400) {
		$timeline_array[] = $current;
	}

	//check, if max date is not included in timeline array, then add it.
	if(date('F j, Y', $max_date) !== date('F j, Y', $timeline_array[sizeof($timeline_array) - 1]))
		$timeline_array[] = $max_date;


	echo '<div class="timeline_wrapper">';
	echo '<div class="timeline_icon" title="'.$polling_words[4].'"></div>';
	echo '<div class="sexyback_icon" title="'.$polling_words[19].'"></div>';
	if($answerPermission == 1 || ($answerPermission == 0 && $userRegistered)) {
		if(!in_array($poll_index,$voted_ids)) {
			$add_ans_txt = $polling_words[10];
			$o_class = 'opened';			
		}
		else {
			$add_ans_txt = $polling_words[9];
			$o_class = 'voted_button';			
		}
		echo '<div class="add_answer_icon '.$o_class.'" title="'.$add_ans_txt.'"></div>';
	}

	echo '<div class="scale_icon" title="'.$polling_words[14].'"></div>';
	
	echo '<div class="timeline_select_wrapper" >';
	echo '<div style="padding:5px 6px"><select class="polling_select1" id="polling_select_'.$module_id.'_'.$poll_index.'_1" name="polling_select_'.$module_id.'_'.$poll_index.'_1">';

	$optionGroups = array();
	foreach ($timeline_array as $k => $curr_time) {
		if(!in_array(date('M Y', $curr_time),$optionGroups)) {
				
			if (sizeof($optionGroups) != 0)
				echo '</optgroup>';
				
			$optionGroups[] = date('M Y', $curr_time);
			echo '<optgroup label="'.date('M Y', $curr_time).'">';
		}
		$first_label = (intval((sizeof($timeline_array) * 0.4)) - 1) == -1 ? 0 : (intval((sizeof($timeline_array) * 0.4)) - 1);
		$first_label = 0;
		$selected = $k == $first_label ? 'selected="selected"' : '';

		$date_item = $date_format == 'str' ? date('F j, Y', $curr_time) : date('d/m/Y', $curr_time);

		echo '<option '.$selected.' value="'.date('Y-m-d', $curr_time).'">'.$date_item.'</option>';
	}
	echo '</select>';
	echo '<select class="polling_select2" id="polling_select_'.$module_id.'_'.$poll_index.'_2" name="polling_select_'.$module_id.'_'.$poll_index.'_2">';
	$optionGroups = array();
	foreach ($timeline_array as $k => $curr_time) {

		if(!in_array(date('M Y', $curr_time),$optionGroups)) {

			if (sizeof($optionGroups) != 0)
				echo '</optgroup>';

			$optionGroups[] = date('M Y', $curr_time);
			echo '<optgroup label="'.date('M Y', $curr_time).'">';
		}
		$selected = $k == sizeof($timeline_array) - 1 ? 'selected="selected"' : '';

		$date_item = $date_format == 'str' ? date('F j, Y', $curr_time) : date('d/m/Y', $curr_time);

		echo '<option '.$selected.' value="'.date('Y-m-d', $curr_time).'">'.$date_item.'</option>';
	}
	echo '</select></div>';
	echo '</div>';
	echo '</div>';
	$t_id = $global_template == 0 ? $template_id : $polling_array[0] -> id_template;
	echo '<div class="sexy_clear">&nbsp;</div><div class="powered_by powered_by_'.$t_id.'">'.JText::_("MOD_SEXYPOLLING_POWERED_BY").' <a href="http://2glux.com/projects/sexypolling" target="_blank">Sexy Polling</a></div><div class="sexy_clear">&nbsp;</div>';
	echo '</div></div>';
}

if(sizeof($custom_styles) > 0)
foreach ($custom_styles as $poll_id => $styles_list) {
	$styles_array = explode('|',$styles_list);
	foreach ($styles_array as $val) {
		$arr = explode('~',$val);
		$styles_[$poll_id][$arr[0]] = $arr[1];
	}
}

//create javascript animation styles array
$jsInclude = 'if (typeof animation_styles === \'undefined\') { var animation_styles = new Array();};';

if(sizeof($styles_) > 0)
foreach ($styles_ as $poll_id => $styles) {
	$s1 = $styles[12];//backround-color
	$s2 = $styles[73];//border-color
	$s3 = $styles[68].' '.$styles[69].'px '.$styles[70].'px '.$styles[71].'px '.$styles[72].'px '.$styles[11];//box-shadow
	$s4 = $styles[74].'px';//border-top-left-radius
	$s5 = $styles[75].'px';//border-top-right-radius
	$s6 = $styles[76].'px';//border-bottom-left-radius
	$s7 = $styles[77].'px';//border-bottom-right-radius
	$s8 = $styles[0];//static color
	$s9 = $styles[68];//shadow type
	$s9 = $styles[68];//shadow type
	$s10 = $styles[90];//navigation bar height
	$s11 = $styles[251];//Answer Color Inactive
	$s12 = $styles[270];//Answer Color Active
	$jsInclude .= 'animation_styles["'.$module_id.'_'.$poll_id.'"] = new Array("'.$s1.'", "'.$s2.'", "'.$s3.'", "'.$s4.'", "'.$s5.'", "'.$s6.'", "'.$s7.'","'.$s8.'","'.$s9.'","'.$s10.'","'.$s11.'","'.$s12.'");';
}
$jsInclude .= 'if (typeof sexyPolling_words === \'undefined\') { var sexyPolling_words = new Array();};';
foreach ($polling_words as $k => $val) {
	$jsInclude .= 'sexyPolling_words["'.$k.'"] = "'.$val.'";';
}
$jsInclude .= 'if (typeof multipleAnswersInfoArray === \'undefined\') { var multipleAnswersInfoArray = new Array();};';
foreach ($multiple_answers_info_array as $k => $val) {
	$jsInclude .= 'multipleAnswersInfoArray["'.$k.'"] = "'.$val.'";';
}
$jsInclude .= 'var sexyIp = "'.$sexyip.'";';
$jsInclude .= 'var newAnswerBarIndex = "'.$new_answer_bar_index.'";';
$jsInclude .= 'var autoOpenTimeline = "'.$autoOpenTimeline.'";';
$jsInclude .= 'var autoAnimate = "'.$autoAnimate.'";';
$jsInclude .= 'var sexyAutoPublish = "'.$autoPublish.'";';
$jsInclude .= 'var dateFormat = "'.$date_format.'";';
$jsInclude .= 'var sexyPath = "'.JURI::base(true).'/";';

$jsInclude .= 'if (typeof sexyPollingIds === \'undefined\') { var sexyPollingIds = new Array();};';
$k = 0;
foreach ($polling_select_id as $poll_id) {
	$jsInclude .= 'sexyPollingIds.push(Array("'.$poll_id["select1"].'","'.$poll_id["select2"].'"));';
	$k ++;
}
$jsInclude .= 'if (typeof votedIds === \'undefined\') { var votedIds = new Array();};';
foreach ($voted_ids as $voted_id) {
	$jsInclude .= 'votedIds.push(Array("'.$voted_id.'","'.$module_id.'"));';
}
$jsInclude .= 'if (typeof startDisabledIds === \'undefined\') { var startDisabledIds = new Array();};';
foreach ($start_disabled_ids as $start_disabled_data) {
	$jsInclude .= 'startDisabledIds.push(Array("'.$start_disabled_data[0].'","'.$start_disabled_data[1].'","'.$module_id.'"));';
}
$jsInclude .= 'if (typeof endDisabledIds === \'undefined\') { var endDisabledIds = new Array();};';
foreach ($end_disabled_ids as $end_disabled_data) {
	$jsInclude .= 'endDisabledIds.push(Array("'.$end_disabled_data[0].'","'.$end_disabled_data[1].'","'.$module_id.'"));';
}
$jsInclude .= 'if (typeof sexyAnimationTypeBar === \'undefined\') { var sexyAnimationTypeBar = new Array();};';
$jsInclude .= 'sexyAnimationTypeBar["'.$module_id.'"] = "'.$sexyAnimationTypeBar.'";';
$jsInclude .= 'if (typeof sexyAnimationTypeContainer === \'undefined\') { var sexyAnimationTypeContainer = new Array();};';
$jsInclude .= 'sexyAnimationTypeContainer["'.$module_id.'"] = "'.$sexyAnimationTypeContainer.'";';
$jsInclude .= 'if (typeof sexyAnimationTypeContainerMove === \'undefined\') { var sexyAnimationTypeContainerMove = new Array();};';
$jsInclude .= 'sexyAnimationTypeContainerMove["'.$module_id.'"] = "'.$sexyAnimationTypeContainerMove.'";';
$document->addScriptDeclaration ( $jsInclude );
?>