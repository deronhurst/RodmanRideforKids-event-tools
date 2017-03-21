<?php
function jsonpResponse($resp){
	return $_GET['jsonp_callback'] . '(' . json_encode($resp) . ');';
}
require_once("lib/ez_sql.php");
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2015','localhost');

$start = $_GET['s'];
$perpage = 50;
$fname = mysql_real_escape_string($_GET['fname']);
$lname = mysql_real_escape_string($_GET['lname']);
$orderby = mysql_real_escape_string($_GET['orderby']);
$order = mysql_real_escape_string($_GET['order']);

if(!$start) $start = 0;

if(!empty($fname) && empty($lname)){
	$where = "fname LIKE '%{$fname}%'";
}elseif(empty($fname)&&!empty($lname)){
	$where = "lname LIKE '%{$lname}%'";
}elseif(!empty($fname)&&!empty($lname)){
	$where = "fname LIKE '%{$fname}%' AND lname LIKE '%{$lname}%'";
}else{
	$where = "event_id!=''";
}

$total_participants = $db->get_var("SELECT COUNT(1) FROM members WHERE {$where}");
$sql = "SELECT * FROM members WHERE {$where} ORDER BY lname LIMIT $start,$perpage";


$participants = $db->get_results($sql,ARRAY_A);

$one_day_ago = strtotime("-1 day");
if(count($participants)==0){
	$participants = array();
}else{
	$c=0;
	foreach($participants as $w){
		// strip year from event name for display
		$event_name = $w['event_name'];
		$pattern = '/\s(19|20)[0-9][0-9]/';
		$participants[$c]['event_name'] = preg_replace($pattern, '', $event_name);

		// If the walker was modified over a day ago, get their new total
		if($w['last_modified']<$one_day_ago){
			$totals = file_get_contents("http://www.kintera.org/gadgets/data/thermometer.aspx?sid=".$w['supporter_id']."&eid=".$w['event_id']);
			$pairs = explode("&",$totals);
			foreach ($pairs as $pair) {
				list($key, $val) = explode("=",$pair);
				$out[$key] = str_replace(",","",$val);
			}
			$db->query("UPDATE members SET amount_raised = '".$out['moneyraised']."', last_modified = '".time()."' WHERE supporter_id = '".$w['supporter_id']."'");
			$participants[$c]['amount_raised'] = $out['moneyraised'];
		}
		$c++;
	}
}

$pages = ceil($total_participants/$perpage);
$current_page = ($start+$perpage) / $perpage;
$return_array = array("start"=>$start,"perpage"=>$perpage,"pages"=>$pages,"current_page"=>$current_page,"total_results"=>$total_participants,"participants"=>$participants);
echo jsonpResponse($return_array);
exit;
?>