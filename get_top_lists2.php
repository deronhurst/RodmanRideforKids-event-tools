<?php
require_once("lib/ez_sql.php");
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2015','localhost');

function send_response($resp){
	return $_GET['jsonp_callback'] . '(' . json_encode($resp) . ');';
}

$total_to_show = mysql_real_escape_string($_GET['num']);
if($total_to_show>10) $total_to_show = 10;

$top_participants = $db->get_results("SELECT * FROM members ORDER BY amount_raised DESC LIMIT $total_to_show");

$top_events = $db->get_results("SELECT * FROM events ORDER BY amount_raised DESC LIMIT $total_to_show");
$c = 0;
foreach($top_events as $e){
	$top_events[$c]->event_name = trim(str_replace('2015','',$e->event_name));
	$c++;
}

$top_teams = $db->get_results("SELECT team_name AS team_company_name, event_id, amount_raised, team_id AS team_company_id FROM teams ORDER BY amount_raised DESC LIMIT $total_to_show");
print_r($top_teams);
exit;
//$top_teams = $db->get_results("");

$return_array = array("success"=>1,"teams"=>$top_teams,"participants"=>$top_participants,"events"=>$top_events);
echo send_response($return_array);
exit;
?>