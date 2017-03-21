<?php
error_reporting(0);
require_once('lib/ez_sql.php');
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2015','localhost');


function send_response($resp){
	if($_GET['jsonp_callback']){
		return $_GET['jsonp_callback'] . '(' . json_encode($resp) . ');';
	}else{
		return json_encode($resp);
	}
}

$events = $db->get_results("SELECT * FROM events WHERE event_name != '2013 Rodman Ride Template_template' ORDER BY event_name",ARRAY_A);
$c = 0;
foreach($events as $e){
	$events[$c]['event_name'] = trim(str_replace('2015','',$e['event_name']));
	$events[$c]['event_link'] = '';
	$c++;
}

// Add hardcoded events
array_push($events, array('id'=>count($events)+1,'event_name'=>'Big Brothers Big Sisters of Central Mass/Framingham','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6314464') );

array_push($events, array('id'=>count($events)+1,'event_name'=>'
Big Brothers Big Sisters Mass Bay','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6314757') );

array_push($events, array('id'=>count($events)+1,'event_name'=>'Big Sisters of Greater Boston','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6314394') );

array_push($events, array('id'=>count($events)+1,'event_name'=>'Jewish Big Brothers Big Sisters of Greater Boston','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6314475') );

array_push($events, array('id'=>count($events)+1,'event_name'=>'Mazie Foundation','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6314483') );



$resp_array = array('events'=>$events,'count'=>count($events));

echo send_response($resp_array);
exit;
?>