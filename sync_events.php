<?php
ob_start();
set_time_limit(900);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ALL);
include_once("lib/config.php");
include_once("lib/kennect.php");
include_once('lib/webrequest.php');
include("/home/zurigrou/public_html/databaseLib/atlasConnection.php");

function wlog($msg){
	echo '[<strong>'.date("h:i:s").'</strong>] - '.$msg.'<br/>';
	ob_flush();
	flush();
}

if(file_exists('lib/api_update1.txt')){
	unlink('lib/api_update1.txt');
}

echo '<div style="font-family:Arial;font-size:12px;">';
wlog('Starting connection process...');
$access = new atlasConnection("QqcvaIMQAHrUkKQsMss5VeCnGd4IaLvDqHznit3z"); // was 13


$api = new kennect( array(
				'LoginName' => $access->username,
           		'Password' => $access->password,
				'UserID'=>"290085"
			));




$req = new WebRequest("api_update1","");
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2015','localhost');



function getProfile($cid){
	global $api;
	$profile = $api->query("SELECT * FROM ContactProfile WHERE ContactID = '".$cid."'");
	return $profile['Records']['Record'];
}
function getCustomProfile($cid){
	global $api;
	$custom_profile = $api->query("SELECT * FROM ContactCustomProfileField WHERE ContactID = '".$cid."'");
	return $custom_profile['Records']['Record'];
}
function getCustomProfileValue($field,$haystack){
	foreach($haystack as $k=>$v){
		if($v['FieldID']==$field){
			return $v['Value'];
		}
	}
}
function search_array($needle,$haystack){
	foreach($haystack as $k=>$v){
		if(is_object($v) || is_array($v)){
			return search_array($needle,$v);
		}else if($k===$needle){
			return $v;
		}
	}
}

wlog('Connected to API');
wlog('Beginning event group scraping process ('.Config::$Report.')');


$base_url = "http://maps.google.com/maps/geo?output=xml&key=ABQIAAAAv40caD4G8S35A4Q7bZMQxRTWpRixivecSDlGMWKhhmUQ";
// 2012 = 503317
$response = $req->Post("http://www.kintera.com/spherelite/public/asp/eventcentral.asp?grpid=505433","submit_type=&search_name=&search_city=&search_state=any&start_date=&end_date=&__ren_submit_type=&__pagenum=1&__export_or_printable=&__persist=&exporttitle=&__listname=%2Fspherelite%2Fpublic%2Fasp%2Feventcentral.asp&__orderby=event_name&__isasc=asc&__deforderby=event_name&__selcolumns=event_name%13city%13state%13begin_date&__allcolumns=Event+Name%13City%13State%13Event+Date&__allfields=event_name%13city%13state%13begin_date&__allflags=1%131%131%131&__allcats=%13%13%13&__newcolumns=&__renderer_serial=True%2C1%2C20%2Cevent_name%2Cevent_name%2Cevent_name%2Ccity%2Cstate%2Cbegin_date%2C%40%23eop%23%40&__pagesize=500&__checked_recIds=");

//echo $response;


$dom = str_get_html($response);

$tables = $dom->find("#form1 table");
$num = count($tables);
$results_table = $tables[$num-1];

$trs = $results_table->find("tr");
$c = 0;

$r=0;
$result_info = array();

wlog('Scraped event group... emptying DB and processing');

$db->query("TRUNCATE TABLE events");
$db->query("TRUNCATE TABLE map_events");
foreach($trs as $row){
	
	if($c>2){
		$tds = $row->find('td');
		$cellnum=0;
		foreach($tds as $cell){
			if($cellnum==0){
				$as = $cell->find('a');
				$result_info[$r]['link'] = $as[0]->href;
				$result_info[$r]['name'] = $as[0]->innertext;
				wlog('Found event "'.$as[0]->innertext.'" with a URL of '.$as[0]->href);
			}elseif($cellnum==1){
				$result_info[$r]['city'] = $cell->innertext;
			}elseif($cellnum==2){
				$result_info[$r]['state'] = $cell->innertext;
			}elseif($cellnum==3){
				$result_info[$r]['event_date'] = $cell->innertext;
			}
			$cellnum++;
		}
		
		
		$as = $row->find('a');
		$link = $as[0]->href;

	$r++;
	}
	$c++;
}


wlog('Scraped event group results... get event IDs');

// Got all the events... now lets figure out their event ID's

// Grab what we have stored in the DB
$existing_event_ids = $db->get_results("SELECT event_url,event_id FROM event_ids");
$existing = array();
foreach($existing_event_ids as $eeid){
	$existing[$eeid->event_url] = $eeid->event_id;
}

$c=0;
foreach($result_info as $event){
	$event_link = $event['link'];

	//check if we already know this event's id
	if($existing[$event_link]!=""){
		$result_info[$c]['event_id'] = $existing[$event_link];
		wlog('Got event ID ('.$result_info[$c]['event_id'].') from database for '.$event_link);
	}else{
		if(strpos($event_link,'ievent=') ){
			$params_e = parse_url($event_link);
			$qs_e = explode('&',$params_e['query']);
			foreach($qs_e as $q_e){
				$q2_e = explode('=',$q_e);
				if($q2_e[0]=="ievent"){
					$this_event_id = $q2_e[1];
					$result_info[$c]['event_id'] = $this_event_id;
					$db->query("INSERT INTO event_ids (event_url,event_id) VALUES ('".$event_link."','".$this_event_id."')");
					break;
				}
			}
			wlog('Got event ID from existing URL for '.$event_link.' ('.$this_event_id.')');
		}else{
			$dom->clear();
			unset($dom);
			$response = $req->Get($event_link);
			$dom = str_get_html($response);
			$logo = $dom->find('li.menuItem a');
			$logo_link = $logo[0]->href;
			$params = parse_url($logo_link);
			$qs = explode('&',$params['query']);
			foreach($qs as $q){
				$q2 = explode('=',$q);
				if($q2[0]=="ievent"){
					$this_event_id = $q2[1];
					$result_info[$c]['event_id'] = $this_event_id;
					$db->query("INSERT INTO event_ids (event_url,event_id) VALUES ('".$event_link."','".$this_event_id."')");
					break;
				}
			}
			wlog('Got event ID for '.$event_link.' ('.$this_event_id.')');
		}
	}
	$c++;
}

//$base_url = "http://maps.googleapis.com/maps/api/geocode/xml?sensor=false";
$base_url = "http://maps.google.com/maps/geo?output=xml&sensor=false&key=ABQIAAAAv40caD4G8S35A4Q7bZMQxRTWpRixivecSDlGMWKhhmUQ_JES7RR_pRexN2T9egtIDqZh1g0oHsHI8Q";



wlog('Got event IDs. Get Lat/Lng, totals and update DB');


foreach($result_info as $result){

	$event_city = $result['city'];
	$end_date = '';
	$eid = $result['event_id'];
	$raised = '';
	$goal = '';
	$event_zip = '';
	$event_name = mysql_real_escape_string($result['name']);
	$begin_date = strtotime($result['event_date']);
	$event_state = strtoupper($result['state']);
	$end_date = strtotime($result['event_date']);


	// Make changes to specific cities for typos
	if($event_city=='Foxboro'){
		$event_city = 'Foxborough';
	}elseif($event_city=='Porltland'){
		$event_city = 'Portland';
	}elseif($event_city=='Panama City Beach City'){
		$event_city = 'Panama City';
	}elseif($event_city=='Pensacla'){
		$event_city = 'Pensacola';
	}elseif($event_city=='Kettering' && $event_state=='OH'){
		$event_city = 'Dayton';
	}

	$lat = 0;
	$lng = 0;
	$latlng = $db->get_row("SELECT lat, lng, zip FROM locations WHERE city = '".$event_city."' && state = '".$event_state."' LIMIT 1");
	$lat = $latlng->lat;
	$lng = $latlng->lng;
	$event_zip = $latlng->zip;

			$dtotal = 0;
		$total_goal = 0;
		$total_raised = 0;
		$totals = '';
		$totals = file_get_contents("https://www.kintera.org/faf/home/default.asp?ievent=".$eid);
		$totsplit = explode('var dTotal = "',$totals);
		if( count($totsplit)<=1){
			$totals = file_get_contents("https://www.kintera.org/gadgets/data/thermometer.aspx?eid=".$eid);
			$pairs = explode("&",$totals);

			$data = array();
			foreach ($pairs as $pair) {
				list($key, $val) = explode("=",$pair);
				$data[$key] = $data[$key] + str_replace(",","",$val);
			}
			
			// This is always going to be 0, so let's just remove it
			unset($data['err_msg']);
			
			$total_raised = $data['moneyraised'];
			$total_goal = $data['mygoal'];
		}else{
			$totals = file_get_contents("https://www.kintera.org/gadgets/data/thermometer.aspx?eid=".$eid);
			$pairs = explode("&",$totals);
	
			$data = array();
			foreach ($pairs as $pair) {
				list($key, $val) = explode("=",$pair);
				$data[$key] = $data[$key] + str_replace(",","",$val);
			}
			
			// This is always going to be 0, so let's just remove it
			unset($data['err_msg']);
			$total_goal = $data['mygoal'];
			$dtotal = explode('"',$totsplit[1]);
			$dtotal = $dtotal[0];
			$total_raised = $dtotal;
		}

//		wlog($total_goal);
		$raised = $total_raised;
		$goal = $total_goal;
//		$raised = $out['moneyraised'];
//		$goal = $out['mygoal'];
//		$check = $db->get_row("SELECT walk_name FROM map_events WHERE event_id = '$eid'");
//		if($db->num_rows==0){

			$now = time();
			$db->query("INSERT INTO events (event_id,event_name,amount_raised,last_updated) VALUES ('$eid','$event_name','$raised','$now')");

			$db->query("INSERT INTO map_events (event_id,walk_name,event_city,event_state,event_zip,event_start_date,event_end_date,total_raised,event_goal,lat,lng) VALUES ('$eid','$event_name','$event_city','$event_state','$event_zip','$begin_date','$end_date','$raised','$goal','$lat','$lng')");

			if($lat=='0' || $lat==''){
				wlog('<span style="color:red;font-weight:bold;">DID NOT UPDATE '.$event_name.' - '.$request_url.' - ('.$event_city.', '.$event_state.') '.$lat.'|'.$lng.'</span>');
			}else{
				wlog('Updated '.$event_name.' - '.$request_url.' - ('.$event_city.', '.$event_state.') '.$lat.'|'.$lng);
			}

//		}else{
			/*$db->query("UPDATE map_events SET
					walk_name = '{$walk_name}',
					event_city = '{$event_city}',
					event_state = '{$event_state}',
					event_zip = '{$event_zip}',
					event_start_date = '{$begin_date}',
					event_end_date = '{$end_date}',
					lat = '{$lat}',
					lng = '{$lng}',
					event_goal = '{$goal}',
					total_raised = '{$raised}',
					last_updated = '{$now}'
				WHERE event_id = '{$eid}'
			");*/
//		}
		//wlog('Updated '.$walk_name);
}



wlog('<strong>Done with event group portion');





function flip_state($state){
	switch($state){
		case "Alaska": return "AK"; break;
		case "Arizona": return "AZ"; break;
		case "Arkansas": return "AR"; break;
		case "Alabama": return "AL"; break;
		case "California": return "CA"; break;
		case "Colorado": return "CO"; break;
		case "Connecticut": return "CT"; break;
		case "Delaware": return "DE"; break;
		case "DistrictofColumbia": return "DC"; break;
		case "Florida": return "FL"; break;
		case "Georgia": return "GA"; break;
		case "Hawaii": return "HI"; break;
		case "Iowa": return "IA"; break;
		case "Idaho": return "ID"; break;
		case "Illinois": return "IL"; break;
		case "Indiana": return "IN"; break;
		case "Kansas": return "KS"; break;
		case "Kentucky": return "KY"; break;
		case "Louisiana": return "LA"; break;
		case "Massachusetts": return "MA"; break;
		case "Maryland": return "MD"; break;
		case "Maine": return "ME"; break;
		case "Michigan": return "MI"; break;
		case "Minnesota": return "MN"; break;
		case "Mississippi": return "MS"; break;
		case "Missouri": return "MO"; break;
		case "Montana": return "MT"; break;
		case "NorthCarolina": return "NC"; break;
		case "NorthDakota": return "ND"; break;
		case "Nebraska": return "NE"; break;
		case "NewHampshire": return "NH"; break;
		case "NewJersey": return "NJ"; break;
		case "NewMexico": return "NM"; break;
		case "Nevada": return "NV"; break;
		case "NewYork": return "NY"; break;
		case "Ohio": return "OH"; break;
		case "Oklahoma": return "OK"; break;
		case "Oregon": return "OR"; break;
		case "Pennsylvania": return "PA"; break;
		case "PuertoRico": return "PR"; break;
		case "RhodeIsland": return "RI"; break;
		case "SouthCarolina": return "SC"; break;
		case "SouthDakota": return "SD"; break;
		case "Tennessee": return "TN"; break;
		case "Texas": return "TX"; break;
		case "Utah": return "UT"; break;
		case "Virginia": return "VA"; break;
		case "Vermont": return "VT"; break;
		case "VirginIslands": return "VI"; break;
		case "Washington": return "WA"; break;
		case "WestVirginia": return "WV"; break;
		case "Wisconsin": return "WI"; break;
		case "Wyoming": return "WY"; break;
	}
}


wlog('<strong>All Done</strong>');
echo '</div>';



// Code Snippet for Zurigroup Cron Job Monitoring
$id = "168";    //Script ID from the database;
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



ob_end_clean();
exit;
?>
