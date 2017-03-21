<?php
set_time_limit(900);
error_reporting(E_ALL);
require_once('lib/ez_sql.php');
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2013','localhost');

$csv = 'Event Name,Event ID,Upcycle URL'."\n";
$all_events = $db->get_results("SELECT * FROM events");
foreach($all_events as $e){
	$csv .= '"'.$e->event_name.'",'.$e->event_id.',"http://www.causesinternational.com/upcycleforcharity/'.$e->event_id.'RRK"'."\n";

}

echo $csv;
echo 'Sync Done';
exit;
?>