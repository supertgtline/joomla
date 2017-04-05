<?php 
/**
 * Joomla! component sexypolling
 *
 * @version $Id: default.php 2012-04-05 14:30:25 svn $
 * @author 2GLux.com
 * @package Sexy Polling
 * @subpackage com_sexypolling
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restircted access');

$document = JFactory::getDocument();

$jsFile = JURI::base(true).'/components/com_sexypolling/assets/js/jquery-1.7.2.min.js';
$document->addScript($jsFile);
$jsFile = JURI::base(true).'/components/com_sexypolling/assets/js/highstock.js';
$document->addScript($jsFile);
$jsFile = JURI::base(true).'/components/com_sexypolling/assets/js/exporting.js';
$document->addScript($jsFile);

$document->addScriptDeclaration ( 'jQuery.noConflict();' );


//function to return dates array
function get_dates_array($date1,$date2) {
	$a = strtotime($date1);
	$dates = array();
	while ($a <= strtotime($date2)) {
		$dates[] = date('Y-m-d', $a);
		$a += (60 * 60 * 24);
	}

	return $dates;
}

function show_buy_pro_link() {
	echo 
		'
			<div style="color: rgb(235, 9, 9);font-size: 16px;font-weight: bold;">'.JText::_("COM_SEXYPOLLING_PLEASE_UPGRADE_TO_SEE_STATISTICS").'</div>
			<div id="cpanel" style="float: left;">
			<div class="icon" style="float: right;">
			<a href="'.JText::_("COM_SEXYPOLLING_SUBMENU_BUY_PRO_VERSION_LINK").'" target="_blank" title="'.JText::_("COM_SEXYPOLLING_SUBMENU_BUY_PRO_VERSION_DESCRIPTION").'">
			<table style="width: 100%;height: 100%;text-decoration: none;">
			<tr>
			<td align="center" valign="middle">
			<img src="components/com_sexypolling/assets/images/shopping_cart.png" /><br />
									'.JText::_("COM_SEXYPOLLING_SUBMENU_BUY_PRO_VERSION").'
								</td>
							</tr>
						</table>
					</a>
				</div>
			</div>
		';
}

$db = JFactory::getDBO();
$poll_id_original = (int)$_GET['id'];
$poll_id = 1;
$query = "
			SELECT 
				sp.name,
				sp.question,
				count(sv.ip) votes,
				sv.date
			FROM 
				#__sexy_polls sp 
			LEFT JOIN #__sexy_answers sa ON sa.id_poll = sp.id
			LEFT JOIN #__sexy_votes sv ON sv.id_answer = sa.id
			WHERE sp.id = '$poll_id_original' 
			GROUP BY 
				sv.date
			ORDER BY 
				sv.date
		";
$db->setQuery($query);
$statdata = $db->loadAssocList();

$poll_name = $statdata[0]['name'];
$poll_question = $statdata[0]['question'];
$min_date = $statdata[0]['date'];
$size = sizeof($statdata) - 1;
$max_date = $statdata[$size]['date'];


$stat_array = array();
foreach($statdata as $val) {
	$stat_array["$val[date]"] = $val['votes'];
}


//get all range of dates
$dates_array = get_dates_array($min_date,$max_date);
//get final array wuth all dates
$stat_array_final = array();
foreach($dates_array as $k => $date_val) {
	$cur_votes = (in_array($date_val,array_keys($stat_array))) ? $stat_array[$date_val] : 0;
	
	$date_data = explode('-',$date_val);
		
	$stat_array_final[$k]["votes"] = $cur_votes;
	$stat_array_final[$k]["y"] = $date_data[0];
	$stat_array_final[$k]["m"] = $date_data[1];
	$stat_array_final[$k]["d"] = $date_data[2];
}

//country stat
$query = "
			SELECT
				count(sv.ip) votes,
				sv.country
			FROM
				#__sexy_votes sv
			LEFT JOIN #__sexy_answers sa ON sa.id_poll = '$poll_id'
			WHERE sv.id_answer = sa.id
			GROUP BY
			sv.country
			ORDER BY
			sv.country
			";
$db->setQuery($query);
$statcountrydata = $db->loadAssocList();

$max = 0;
$max_country_name = @$statcountrydata[0]['country'];
foreach($statcountrydata as $val) {
	if($val['votes'] >= $max) {
		$max = $val['votes'];
		$max_country_name = $val['country'];
	}
}


//answers stat
$query = "
			SELECT
				count(sv.ip) votes,
				sa.id,
				sa.name
			FROM
				#__sexy_votes sv
			JOIN #__sexy_answers sa ON sa.id_poll = '$poll_id'
			WHERE sv.id_answer = sa.id
			GROUP BY
			sv.id_answer
			ORDER BY votes DESC
			";
$db->setQuery($query);
$statanswersdata = $db->loadAssocList();
$max = 0;
$max_ans_id = @$statanswersdata[0]['id'];
foreach($statanswersdata as $val) {
	if($val['votes'] >= $max) {
		$max = $val['votes'];
		$max_ans_id = $val['id'];
	}
}

$query = "
			SELECT
			count(sv.ip) votes
			FROM
			#__sexy_votes sv
			JOIN #__sexy_answers sa ON sa.id_poll = '$poll_id'
			WHERE sv.id_answer = sa.id
			";
$db->setQuery($query);
$totalvotes = $db->loadResult();

//FREE VERSION CHECK
if($poll_question == 'Do You like Sexy Polling by 2GLux?') {
	$free_version_title = JText::_('COM_SEXYPOLLING_DEMO');
}
else {
	$free_version_title = JText::_('COM_SEXYPOLLING_PLEASE_UPGRADE_TO_SEE_STATISTICS');
}

JToolBarHelper::title(   JText::_( 'Statistics' ).' - ('.$poll_name.') : '.$free_version_title,'manage.png' );


//get demo question name
$query = "
SELECT
sp.question
FROM
#__sexy_polls sp
WHERE sp.id = '$poll_id'
";
$db->setQuery($query);
$demo_question = $db->loadResult();

if($totalvotes > 0 && $demo_question == 'Do You like Sexy Polling by 2GLux?') {
?>
<script type="text/javascript">
(function($) {
	$(document).ready(function() {


		// Create the chart
		window.chart = new Highcharts.StockChart({
			chart : {
				renderTo : 'graph_container'
			},

			rangeSelector : {
				selected : 1
			},

			title : {
				text : '<?php echo JText::_("Votes Statistics")." - ($poll_name)";?>'
			},

			scrollbar: {
				barBackgroundColor: '#bbb',
				barBorderRadius: 7,
				barBorderWidth: 0,
				buttonBackgroundColor: '#999',
				buttonBorderWidth: 0,
				buttonBorderRadius: 7,
				trackBackgroundColor: 'none',
				trackBorderWidth: 1,
				trackBorderRadius: 8,
				trackBorderColor: '#fff'
		    },
			
			series : [
			<?php 
				$c_data = array();
				foreach($stat_array_final as $row) {
					$m = $row['m'] - 1;
					$c_data[] = '[Date.UTC('.$row["y"].','.$m.','.$row["d"].',0,0,0),'.$row["votes"].']';
				}
				
				//print series javacript
				echo '{';
				echo "name : 'Votes',\n";
				echo "type: 'areaspline',\n";
				//echo "type: 'column',\n";
				//echo "type: 'spline',\n";
				echo 'data : [';
					foreach ($c_data as $r => $val) {
						echo $val;
						if($r != sizeof($c_data) - 1)
							echo ',';
						echo "\n";
					}
				echo '],';
				echo 'marker : {
							enabled : false,
							radius : 3
						},
						shadow : true,
						tooltip : {
							valueDecimals : 0
						},
						fillColor : {
							linearGradient : {
								x1: 0, 
								y1: 0, 
								x2: 0, 
								y2: 1
							},
							stops : [[0, Highcharts.getOptions().colors[0]], [1, "rgba(0,0,0,0)"]]
						},
						threshold: null
						';
				echo '}';
			?>
			]
		});


		/*pie charts*/
		 var chart;
		    $(document).ready(function() {
		        chart = new Highcharts.Chart({
		            chart: {
		                renderTo: 'container1',
		                plotBackgroundColor: null,
		                plotBorderWidth: null,
		                plotShadow: false
		            },
		            title: {
		                text: '<?php echo JText::_("Country Statistics");?>'
		            },
		            tooltip: {
		                formatter: function() {
		                    return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
		                }
		            },
		            plotOptions: {
		                pie: {
		                    allowPointSelect: true,
		                    cursor: 'pointer',
		                    dataLabels: {
		                        enabled: true,
		                        color: '#444444',
		                        connectorColor: '#555555',
		                        formatter: function() {
		                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
		                        }
		                    },
		                    showInLegend: true
		                }
		            },
		            series: [{
		                type: 'pie',
		                name: '',
		                data: [
				            <?php 
				            	foreach($statcountrydata as $k => $val) {
					            	$perc = sprintf ("%.2f", ((100 * $val['votes']) / $totalvotes));
					            	
				            		if($max_country_name == $val['country']) {
					            		echo "{
						                        name: '".$val['country']."',
						                        y: $perc,
						                        sliced: true,
						                        selected: true
						                    }";
				            		}
				            		else {
					            		echo "['".$val['country']."',".$perc."]";
				            		}
			            			if($k != sizeof($statcountrydata) - 1)
			            				echo ',';	
				            	}
				            ?>
		                ]
		            }]
		        });
		    });
		    
		/*pie charts*/
		 var chart;
		    $(document).ready(function() {
		        chart = new Highcharts.Chart({
		            chart: {
		                renderTo: 'container2',
		                plotBackgroundColor: null,
		                plotBorderWidth: null,
		                plotShadow: false
		            },
		            title: {
		                text: '<?php echo JText::_("Answers Statistics");?>'
		            },
		            tooltip: {
		                formatter: function() {
		                    return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
		                }
		            },
		            plotOptions: {
		                pie: {
		                    allowPointSelect: true,
		                    cursor: 'pointer',
		                    dataLabels: {
		                        enabled: true,
		                        color: '#444444',
		                        connectorColor: '#555555',
		                        formatter: function() {
		                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
		                        }
		                    },
		                    showInLegend: true
		                }
		            },
		            series: [{
		                type: 'pie',
		                name: '',
		                data: [
				            <?php 
				            	foreach($statanswersdata as $k => $val) {
					            	$perc = sprintf ("%.2f", ((100 * $val['votes']) / $totalvotes));
					            	
				            		if($val['id'] == $max_ans_id) {
					            		echo "{
						                        name: '".htmlspecialchars_decode($val['name'])."',
						                        y: $perc,
						                        sliced: true,
						                        selected: true
						                    }";
				            		}
				            		else {
					            		echo "['".str_replace(array('\'','"'),"",htmlspecialchars_decode($val['name']))."',".$perc."]";
				            		}
			            			if($k != sizeof($statanswersdata) - 1)
			            				echo ',';	
				            	}
				            ?>
		                ]
		            }]
		        });
		    });


		
})
})(jQuery);
</script>
			
<?php show_buy_pro_link();?>
<div style="color: rgb(21, 90, 177);font-size: 20px;font-weight: bold;clear: both;text-align: center;margin: 5px 0 5px 0;"><?php echo JText::_('COM_SEXYPOLLING_STATISTICS_DEMO')?></div>


<div style="position: relative;float: left; width: 48%;padding: 8px;border: 1px solid #ccc;border-radius: 6px;box-shadow: inset 0 0 28px -3px #bbb;margin: 15px 0;">
	<div id="container2" style=""></div>
	<div style="position: absolute;z-index: 100000;color: red;height: 13px;width: 87px;bottom: 10px;right: 10px;background-color: #fff;"></div>
</div>
<div style="position: relative;float: right; width: 48%;padding: 8px;border: 1px solid #ccc;border-radius: 6px;box-shadow: inset 0 0 28px -3px #bbb;margin: 15px 0;">
	<div id="container1" style=""></div>
	<div style="position: absolute;z-index: 100000;color: red;height: 13px;width: 87px;bottom: 10px;right: 10px;background-color: #fff;"></div>
</div>

<div style="position: relative;padding: 8px;border: 1px solid #ccc;border-radius: 6px;box-shadow: inset 0 0 28px -3px #bbb;margin: 15px 0;clear: both;">
	<div id="graph_container" style="width: 98%;margin:0 auto;"></div>
	<div style="position: absolute;z-index: 100000;color: red;height: 13px;width: 200px;bottom: 10px;right: 10px;background-color: #fff;"></div>
</div>
<?php }
else {
	echo 'No Data';
}?>



<form action="index.php" method="post" name="adminForm" id="adminForm"> 
<input type="hidden" name="option" value="com_sexypolling" />
<input type="hidden" name="task" value="cancel" />
<input type="hidden" name="controller" value="statistics" />
</form>
<table class="adminlist" style="width: 100%;margin-top: 12px;clear: both;"><tr><td align="center" valign="middle" id="twoglux_ext_td" style="position: relative;">
	<div id="twoglux_bottom_link"><a href="<?php echo JText::_( 'COM_SEXYPOLLING_SUBMENU_PROJECT_HOMEPAGE_LINK' ); ?>" target="_blank"><?php echo JText::_( 'COM_SEXYPOLLING' ); ?></a> <?php echo JText::_( 'COM_SEXYPOLLING_DEVELOPED_BY' ); ?> <a href="http://2glux.com" target="_blank">2GLux.com</a></div>
	<div style="position: absolute;right: 2px;top: 7px;">
		<a href="<?php echo JText::_( 'COM_SEXYPOLLING_SUBMENU_RATE_US_LINK' ); ?>" target="_blank" id="twoglux_ext_rate" class="twoglux_ext_bottom_icon" title="<?php echo JText::_( 'COM_SEXYPOLLING_SUBMENU_RATE_US_DESCRIPTION' ); ?>">&nbsp;</a>
		<a href="<?php echo JText::_( 'COM_SEXYPOLLING_SUBMENU_PROJECT_HOMEPAGE_LINK' ); ?>" target="_blank" id="twoglux_ext_homepage" style="margin: 0 2px 0 0px;" class="twoglux_ext_bottom_icon" title="<?php echo JText::_( 'COM_SEXYPOLLING_SUBMENU_PROJECT_HOMEPAGE_DESCRIPTION' ); ?>">&nbsp;</a>
		<a href="<?php echo JText::_( 'COM_SEXYPOLLING_SUBMENU_SUPPORT_FORUM_LINK' ); ?>" target="_blank" id="twoglux_ext_support" class="twoglux_ext_bottom_icon" title="<?php echo JText::_( 'COM_SEXYPOLLING_SUBMENU_SUPPORT_FORUM_DESCRIPTION' ); ?>">&nbsp;</a>
		<a href="<?php echo JText::_( 'COM_SEXYPOLLING_SUBMENU_BUY_PRO_VERSION_LINK' ); ?>" target="_blank" id="twoglux_ext_buy" class="twoglux_ext_bottom_icon" title="<?php echo JText::_( 'COM_SEXYPOLLING_SUBMENU_BUY_PRO_VERSION_DESCRIPTION' ); ?>">&nbsp;</a>
	</div>
</td></tr></table>