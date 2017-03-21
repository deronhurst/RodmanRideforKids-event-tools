<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once('lib/ez_sql.php');
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2015','localhost');

function send_response($resp){
	return $_GET['jsonp_callback'] . '(' . json_encode($resp) . ');';
}

$sid = $_GET['sid'];
if($sid){
	$events = $db->get_results("SELECT m.event_id, m.event_name FROM members m, events e WHERE m.event_id=e.event_id && m.supporter_id = '{$sid}'",ARRAY_A);
	$c=0;
	foreach($events as $e){
		$events[$c]['event_name'] = trim(str_replace('2015','',$e['event_name']));
		$c++;
	}

	$return_array = array("success"=>1,"events"=>$events);
	echo send_response($return_array);
}else{
	$return_array = array("success"=>0,"message"=>"No Supporter ID");
	echo send_response($return_array);
}

exit;

?>