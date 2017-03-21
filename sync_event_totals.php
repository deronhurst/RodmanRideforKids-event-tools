<?php
set_time_limit(900);
error_reporting(E_ALL);
require_once('lib/ez_sql.php');
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2015','localhost');

$all_events = $db->get_results("SELECT * FROM events");
foreach($all_events as $e){
	$json = json_decode(file_get_contents("http://www.kintera.org/faf/json/event.asp?ievent=".$e->event_id));

	if(empty($json)){
		$db->query("UPDATE events SET no_json = '1', last_updated = '".time()."' WHERE event_id = '".$e->event_id."'");
	}else{
		$total_raised = $json->event->raised;
		$db->query("UPDATE events SET event_name = '".mysql_real_escape_string($json->event->name)."', amount_raised = '".$total_raised."', last_updated = '".time()."', no_json = '0' WHERE event_id = '".$e->event_id."'");
	}

}

echo 'Sync Done';

// Code Snippet for Zurigroup Cron Job Monitoring
$id = "98"; //Script ID from the database;
$url = "http://www.zuriscripts.com/bubba/UpdateCron.php";
$fields = array("id"=>urlencode($id));
$fields_string = '';
foreach($fields as $key=>$value) { $fields_string .= $key."=".$value."&"; }
rtrim($fields_string,"&");
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
$result = curl_exec($ch);
curl_close($ch);

exit;
?>