<?php
/**
 * Joomla! component sexypolling
 *
 * @version $Id: addanswer.php 2012-04-05 14:30:25 svn $
 * @author 2GLux.com
 * @package Sexy Polling
 * @subpackage com_sexypolling
 * @license GNU/GPL
 *
 */

// no direct access
define('_JEXEC',true);
defined('_JEXEC') or die('Restircted access');

/*
 * This is external PHP file and used on AJAX calls, so it has not "defined('_JEXEC') or die;" part.
*/

error_reporting(0);
header('Content-type: application/json');
include '../../configuration.php';

$config = new JConfig;

//conects to datababse
mysql_connect($config->host, $config->user, $config->password);
mysql_select_db($config->db);
mysql_query("SET NAMES utf8");

//get ip address
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
	$ip=$_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
	$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
}
else {
	$ip=$_SERVER['REMOTE_ADDR'];
}

//get post data
$polling_id = (int)$_POST['polling_id'];
$autopublish = (int)$_POST['autopublish'];
$writeinto = (int)$_POST['writeinto'];
$answer = mysql_real_escape_string(strip_tags($_POST['answer']));

$countryname = (!isset($_POST['country_name']) || $_POST['country_name'] == '' || $_POST['country_name'] == '-' ) ? 'Unknown' : mysql_real_escape_string($_POST['country_name']);
$cityname = (!isset($_POST['city_name']) || $_POST['city_name'] == '' || $_POST['city_name'] == '-' ) ? 'Unknown' : mysql_real_escape_string($_POST['city_name']);
$regionname = (!isset($_POST['region_name']) || $_POST['region_name'] == '' || $_POST['region_name'] == '-' ) ? 'Unknown' : mysql_real_escape_string($_POST['region_name']);
$countrycode = (!isset($_POST['country_code']) || $_POST['country_code'] == '' || $_POST['country_code'] == '-' ) ? 'Unknown' : mysql_real_escape_string($_POST['country_code']);

if($writeinto == 1 || $autopublish == 0) {
	$published = $autopublish == 1 ? 1 : 0;
	mysql_query("INSERT INTO `".$config->dbprefix."sexy_answers` (`id_poll`,`name`,`published`,`created`) VALUES ('$polling_id','$answer','$published',NOW())");
	$insert_id = mysql_insert_id();
	
	mysql_query("INSERT INTO `".$config->dbprefix."sexy_votes` (`id_answer`,`ip`,`date`,`country`,`city`,`region`,`countrycode`) VALUES ('$insert_id','$ip',NOW(),'$countryname','$cityname','$regionname','$countrycode')");
	//set the cookie
	$expire = time()+(60*60*24*365);//one year
	setcookie("sexy_poll_$polling_id", 1, $expire, '/');
}
else {
	$insert_id = 0;
}

$ans = str_replace('\\','',htmlspecialchars(stripslashes($answer),ENT_QUOTES));
echo '[{"answer": "'.$ans.'", "id" : "'.$insert_id.'"}]';
//echo $answer;

?>