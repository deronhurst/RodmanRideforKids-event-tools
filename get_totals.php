<?php
set_time_limit(900);
error_reporting(0);
require_once('lib/ez_sql.php');
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2015','localhost');

$eid = $_GET['eid'];
$sid = $_GET['sid'];


function send_response($resp){
	if($_GET['jsonp_callback']){
		return $_GET['jsonp_callback'] . '(' . json_encode($resp) . ');';
	}else{
		return json_encode($resp);
	}
}

if($_GET['type']=="events"){

	$total_raised = $db->get_var("SELECT SUM(amount_raised) FROM events");
	$upcycle_total = $db->get_var("SELECT SUM(donation_amount) FROM upcycle_donations");
	if(!$upcycle_total){
		$upcycle_total = 0;
	}
	$total_raised = (int)$total_raised + (int)$upcycle_total;
	$resp_array = array("events_total"=>number_format($total_raised,2),"upcycle_total"=>number_format($upcycle_total,2));

}else{

	if( isset($eid) && !isset($sid) ){
		
		// Get total for event only
		$total = $db->get_var("SELECT SUM(donation_amount) FROM upcycle_donations WHERE event_id = '".$eid."'");
		$resp_array = array("total"=>$total);

	}elseif( !isset($eid) && isset($sid) ){

		// Get total for participant in specific event
		$total = $db->get_var("SELECT SUM(donation_amount) FROM upcycle_donations WHERE supporter_id = '".$sid."'");
		$resp_array = array("total"=>$total);

	}else if( isset($eid) && isset($sid) ){

		// Get total for participant in specific event
		$total = $db->get_var("SELECT SUM(donation_amount) FROM upcycle_donations WHERE supporter_id = '".$sid."' && event_id = '".$eid."'");
		$resp_array = array("total"=>$total);

	}elseif( !isset($eid) && !isset($sid) ){
		
		$total = $db->get_var("SELECT SUM(donation_amount) FROM upcycle_donations");
		$resp_array = array("total"=>$total);

	}

}

echo send_response($resp_array);
exit;
?>