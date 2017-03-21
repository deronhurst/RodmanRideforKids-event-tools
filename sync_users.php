<?php
set_time_limit(900);
error_reporting(0);
include_once("lib/config.php");
//include_once("lib/DataContext.php");
//include_once("lib/Member.php");
include_once("lib/kennect.php");
include_once('lib/webrequest.php');
include_once('lib/simple_html_dom.php');
include("/home/zurigrou/public_html/databaseLib/atlasConnection.php");
$s = 0;
$u = 0;
$ts = 0;
$tu = 0;
$es = 0;
$eu = 0;

if(file_exists('lib/update.txt')){
	unlink('lib/update.txt');
}

	$access = new atlasConnection("QqcvaIMQAHrUkKQsMss5VeCnGd4IaLvDqHznit3z"); // was 13

	$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2015','localhost');
	$req = new WebRequest("update","");

	// Logging to CMS
	$response = $req->Post("https://www.kintera.com/Kintera_Sphere/login/asp/Login.aspx", "LoginName=" . $access->username . "&Password=" . $access->password . "&DomainCode=&x=74&y=16");


	$response = $req->Post("https://www.kintera.com/Kintera_Sphere/login/asp/LoginAccount.aspx", "LR1_CP_choosecolumns_flag=&SelUserID=290085&__EVENTARGUMENT=&__EVENTTARGET=&");

	/* Choose Virtual Account

	$response = $req->Post("https://www.kintera.com/kintera_sphere/admin/privilege/VirtualAccountSetup/ChooseVirtualAccount.aspx", "EveSrc=VirtualAccountSelected&EveArgs=0&BtnVAccount=Login&LR1_CP_choosecolumns_flag=&__EVENTARGUMENT=&__EVENTTARGET="); */

	// load report page
	$response = $req->Get(Config::$Report);

	/* Choose accounts */
	//$response = $req->Get('https://www.kintera.com/kintera_sphere/net_reports/ChooseAccounts.aspx?forasp=1');




	//  post cust. step 1

	$response = $req->Post("https://www.kintera.com/kintera_sphere/reports/asp/customize_a.asp?prerun=1", "submit_type=finish&strAccounts=&strAllSingle=&eveSrc=&strAll=&strOrgs=&saveName=&__rangename=%2Fkintera_sphere%2Freports%2Fasp%2Fcustomize_a.asp&__rangetype=TODATE&__begindate=&__enddate=&s1_custom_type=Group");


	// Choose accounts 
	$response = $req->Get('https://www.kintera.com/kintera_sphere/net_reports/ChooseAccounts.aspx?forasp=1');
	
	$dom = str_get_html($response);
	$sel = $dom->find("select[name='PresetList']",0);
	//$lastopt = $sel->last_child(); 
	//$lastoptval = $lastopt->value;
	$opts = $sel->find('option');
	foreach ($opts as $o) {
		if ($o->innertext == '2016_ALL') {
			$lastoptval = $o->value;
			break;
		}
	}
		
	$viewstate = $req->GetFieldVal($response,'input#__VIEWSTATE');
	$viewstate = urlencode($viewstate);
	$response = $req->Post("https://www.kintera.com/kintera_sphere/net_reports/ChooseAccounts.aspx?forasp=1","POSTDATA=ReportState=&__EVENTTARGET=PresetList&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=".$viewstate."&newPresetName=&PresetList=".$lastoptval."&OrgTree=");
	//echo $response;

	$viewstate = $req->GetFieldVal($response,'input#__VIEWSTATE');
	$viewstate = urlencode($viewstate);
	$response = $req->Post("https://www.kintera.com/kintera_sphere/net_reports/ChooseAccounts.aspx?forasp=1","POSTDATA=ReportState=&__EVENTTARGET=OK&__EVENTARGUMENT=Click&__LASTFOCUS=&__VIEWSTATE=".$viewstate."&newPresetName=&PresetList=".$lastoptval."&OrgTree=");
	//echo $response;

	// Go to report page

	$response = $req->Post("https://www.kintera.com/kintera_sphere/reports/asp/customize_prepare.asp","submit_type=finish&user_report_name=&user_email=stephanie%40zurigroup.com&user_message=&user_report_format=XLS&choose_offline=no");

	// submit form and goto report screen
	//$url = $req->GetActionUrl($response,0);
	$url ="https://www.kintera.com/kintera_sphere/reports/asp/general_report.asp"; 
	$data = "__allcolumns=&__allfields=&__allflags=&__checked_recIds=&__deforderby=last_name&__export_or_printable=3&__isasc=asc&__listname=/kintera_sphere/reports/asp/general_report.asp&__newcolumns=&__orderby=last_name&__pagenum=1&__pagesize=100&__persist=&__ren_submit_type=&eveSrc=&exporttitle=&graph_report_type=Registration&graph_where_clause=&mailing_save_name=&saveName=&strAccounts=&strAll=&strAllSingle=&strOrgs=&submit_type=";
	
	$response = $req->Post($url,$data);
		echo $response;




	// parse file
	$dom = str_get_html($response);

	$table = $dom->find("table td ",0);

	$headding = array();
	
	//get first row
	$header = $table->find("tr",0);
	//echo $header->innertext;
	foreach ($header->find("td") as $td){
		$headding[] = str_replace(array("&nbsp;"," "),array("","_"),strip_tags($td->innertext));	
	}
	
	$line = 0; 

	// Empty team totals
	$db->query("UPDATE teams SET amount_raised = '0'");
	foreach ($table->find("tr") as $row){
		
		if($line == 0){
			$line++;
			continue;	
		}
		
		$report = array();

		$cells = $row->find('td');

		if (count($cells) == count($headding)){
			
			$cellindex = 0;
			foreach ($cells as $cell) {
				
				if(empty($cell->innertext) && $cellindex == 0){
					// skip this line
					break;
				}
				
				if($headding[$cellindex] == 'Total_Amount_Raised')
					$report[$headding[$cellindex]] = str_replace(array("&nbsp;"," ",",","$"),array("","_","",""),strip_tags($cell->innertext));
				else
					$report[$headding[$cellindex]] = str_replace(array("&nbsp;"),array(" "),strip_tags(trim($cell->innertext)));
				
				$cellindex++;
			}

			if(isset($report['Supporter_ID'])) {

				if($report['Initiative_Name']=="2017 Rodman Ride_template" || $report['Event_ID']=="1133970"){
					continue;
				}

				// insert record into db
				$member = $db->get_row("SELECT * FROM members WHERE supporter_id = '".$report['Supporter_ID']."' && event_id = '".$report['Event_ID']."'");
				($report['Team_Leader_Yes/No']=='Yes') ? $tl = '1' : $tl = '0';
				if($db->num_rows==0){
					$db->query("INSERT INTO members (supporter_id,fname,lname,email_address,event_id,event_name,amount_raised,address_1,address_2,city,state,zip_postal,phone_number,team_name,team_leader,team_id,date_created,last_modified) VALUES (
						'".$report['Supporter_ID']."',
						'".mysql_real_escape_string($report['First_Name'])."',
						'".mysql_real_escape_string($report['Last_Name'])."',
						'".mysql_real_escape_string($report['Email'])."',
						'".$report['Event_ID']."',
						'".mysql_real_escape_string($report['Initiative_Name'])."',
						'".$report['Total_Amount_Raised']."',
						'".mysql_real_escape_string($report['Address_Line_1'])."',
						'".mysql_real_escape_string($report['Address_Line_2'])."',
						'".mysql_real_escape_string($report['City'])."',
						'".mysql_real_escape_string($report['State'])."',
						'".mysql_real_escape_string($report['ZIP/Postal_Code'])."',
						'".mysql_real_escape_string($report['Home_Phone'])."',
						'".mysql_real_escape_string($report['Team_Name'])."',
						'".$tl."',
						'".mysql_real_escape_string($report['Team_ID'])."',
						'".time()."',
						'".time()."'
					)");
					echo "SID: {$report['Supporter_ID']} Inserted<br/>";
					$s++;
				}else{
					$db->query("UPDATE members SET 
						fname = '".mysql_real_escape_string($report['First_Name'])."',
						lname = '".mysql_real_escape_string($report['Last_Name'])."',
						email_address = '".mysql_real_escape_string($report['Email'])."',
						event_id = '".$report['Event_ID']."',
						event_name = '".mysql_real_escape_string($report['Initiative_Name'])."',
						amount_raised = '".$report['Total_Amount_Raised']."',
						address_1 = '".mysql_real_escape_string($report['Address_Line_1'])."',
						address_2 = '".mysql_real_escape_string($report['Address_Line_2'])."',
						city = '".mysql_real_escape_string($report['City'])."',
						state = '".mysql_real_escape_string($report['State'])."',
						zip_postal = '".mysql_real_escape_string($report['ZIP/Postal_Code'])."',
						phone_number = '".mysql_real_escape_string($report['Home_Phone'])."',
						team_name = '".mysql_real_escape_string($report['Team_Name'])."',
						team_leader = '".$tl."',
						team_id = '".mysql_real_escape_string($report['Team_ID'])."',
						last_modified = '".time()."'
					WHERE supporter_id = '".$report['Supporter_ID']."' && event_id = '".$report['Event_ID']."'");
					echo "SID: {$report['Supporter_ID']} Already in DB<br/>";
					$u++;
				}

				/* Insert team if it doesn't exist */
				$team = $db->get_row("SELECT * FROM teams WHERE team_id = '".mysql_real_escape_string($report['Team_ID'])."' && event_id = '".mysql_real_escape_string($report['Event_ID'])."'");
				if($db->num_rows==0 && $report['Team_Name']!=""){
					$db->query("INSERT INTO teams (team_name,event_id,event_name,team_id) VALUES ('".mysql_real_escape_string($report['Team_Name'])."','".$report['Event_ID']."','".$report['Initiative_Name']."','".mysql_real_escape_string($report['Team_ID'])."')");
					echo "TEAM: {$report['Team_Name']} Inserted<br/>";
					$ts++;
				}else{
					echo "TEAM: {$report['Team_Name']} Already in DB<br/>";
					$tu++;
				} 
				

				// Update team total
				$db->query("UPDATE teams SET amount_raised = amount_raised + '".$report['Total_Amount_Raised']."' WHERE team_id = '".$report['Team_ID']."'");

				/* Insert event into events list if it doesn't exist
				$event = $db->get_row("SELECT * FROM events WHERE event_id = '".$report['Event_ID']."'");
				if($db->num_rows==0 && $report['Initiative_Name']!=""){
					$db->query("INSERT INTO events (event_name,event_id) VALUES ('".$report['Initiative_Name']."','".$report['Event_ID']."')");
					echo "EVENT: {$report['Initiative_Name']} Inserted<br/>";
					$es++;
				}else{
					echo "EVENT: {$report['Initiative_Name']} Already in DB<br/>";
					$eu++;
				} */


			}

		}
		
		$line++;		
	}

/* Generate CSV 
function promptToDownload($path, $browserFilename, $mimeType){
	if (!file_exists($path) || !is_readable($path)) {
		return null;
	}
	header("Content-Type: " . $mimeType);
	header("Content-Disposition: attachment; filename=\"$browserFilename\"");
	header('Expires: ' . gmdate('D, d M Y H:i:s', gmmktime() - 3600) . ' GMT');
	header("Content-Length: " . filesize($path));
	// If you wish you can add some code here to track or log the download

	// Special headers for IE 6
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	$fp = fopen($path, "r");
	fpassthru($fp);
}

	$now = time();
	$path = "./csvs/";
	$filename = "export_".$now.".csv";
	$fp = fopen($path.$filename, "w+");
//	fwrite($fp, "first_name,last_name,email_address,date_created\n");
	fwrite($fp, "Email Address,First Name,Last Name,Address Line 1,Address Line 2,City,State,Zip,Phone Number,Upcycle Personal URL,Event Name,Team Name\n");


$csv = '';
$all_members = $db->get_results("SELECT * FROM members WHERE sent_via_csv = '0'");
if($db->num_rows>0){
	foreach($all_members as $m){
		$csv .= '"'.$m->email_address.'",'
		.'"'.$m->fname.'",'
		.'"'.$m->lname.'",'
		.'"'.$m->address_1.'",'
		.'"'.$m->address_2.'",'
		.'"'.$m->city.'",'
		.'"'.$m->state.'",'
		.'"'.$m->zip_postal.'",'
		.'"'.$m->phone_number.'",'
		.'"http://www.causesinternational.com/iupcycle/'.$m->supporter_id.'-'.$m->event_id.'",'
		.'"'.$m->event_name.'",'
		.'"'.$m->team_name.'"'."\n";
		$db->query("UPDATE members SET sent_via_csv = '1' WHERE id = '".$m->id."'");
	}
}

fwrite($fp,$csv."\n");
fclose($fp);
*/



//promptToDownload($path.$filename,'email_export_'.$now.'.csv','text/csv');
//exit;
//echo $csv;



/* Send email with CSV attached 
//$to = 'julie.shane@causesinternational.com'; 

$to = 'julie.shane@causesinternational.com,kitty.dowd@causesinternational.com';

$subject = 'Rodman Ride for Kids - Registrants CSV file'; 
//create a boundary string. It must be unique, so we use the MD5 algorithm to generate a random hash 
$random_hash = md5(date('r', time())); 

$headers = "From: mike@zurigroup.com\r\nReply-To: mike@zurigroup.com";

//add boundary string and mime type specification 
$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 
//read the atachment file contents into a string, encode it with MIME base64 and split it into smaller chunks
$attachment = chunk_split(base64_encode(file_get_contents($path.$filename))); 

ob_start(); //Turn on output buffering 
?> 
--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>" 

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/plain; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

CSV file of all registrants attached. If there is no attachment, follow this link: http://www.zurigroup2.com/RodmanRideforKids/2012/csvs/<?php echo $filename;?>


--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/html; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

<p>CSV file of all registrants attached.</p>
<p>If there is no attachment, follow this link: <a href="http://www.zurigroup2.com/RodmanRideforKids/2012/csvs/<?php echo $filename;?>">http://www.zurigroup2.com/RodmanRideforKids/2012/csvs/<?php echo $filename;?></a></p>

--PHP-alt-<?php echo $random_hash; ?>-- 

--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: text/csv; name="<?php echo $filename;?>"  
Content-Transfer-Encoding: base64  
Content-Disposition: attachment  

<?php echo $attachment; ?> 
--PHP-mixed-<?php echo $random_hash; ?>-- 

<?php 
//copy current buffer contents into $message variable and delete current output buffer 
$message = ob_get_clean(); 
//send the email 
$mail_sent = mail( $to, $subject, $message, $headers ); 
//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
echo $mail_sent ? "Mail sent" : "Mail failed"; 
*/


// Code Snippet for Zurigroup Cron Job Monitoring
$id = "169";    //Script ID from the database;
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

echo 'done';
exit;
?>
