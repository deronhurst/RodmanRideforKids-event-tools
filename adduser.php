<?php
/* Setup Database */
require_once('lib/ez_sql.php');
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2013','localhost');

function send_response($resp){
	return $_GET['jsonp_callback'] . '(' . json_encode($resp) . ');';
}

if($_GET['action']=="add"){
	$sid = $_GET['sid'];
	$eid = $_GET['eid'];
	$now = time();
	$db->query("INSERT INTO registered_users (supporter_id,event_id,date_created) VALUES ('".$sid."','".$eid."','".$now."')");
	$response_array = array('success'=>'1','url'=>$eid.$sid);
}

echo send_response($response_array);
exit;
?>