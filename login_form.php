<?php
error_reporting(0);
require_once('lib/ez_sql.php');

function sort_eventname($a, $b){
    return strnatcmp($a['event_name'], $b['event_name']);
}

$eventOptions = array();
$events = array();
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2015','localhost');
//$events = $db->get_results("SELECT * FROM events WHERE event_name != 'Rodman Ride for Kids 2016' ORDER BY event_name",ARRAY_A);
$events = $db->get_results("SELECT * FROM events ORDER BY event_name",ARRAY_A);



// Add hardcoded events
/*
array_push($events, array('id'=>count($events)+1,'event_name'=>'Big Brothers Big Sisters of Central Mass/Metrowest','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6314464') );

array_push($events, array('id'=>count($events)+1,'event_name'=>'
Big Brothers Big Sisters Mass Bay','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6314757') );

array_push($events, array('id'=>count($events)+1,'event_name'=>'Big Sister of Greater Boston','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6314394') );

array_push($events, array('id'=>count($events)+1,'event_name'=>'Jewish Big Brothers Big Sisters of Greater Boston','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6314475') );

array_push($events, array('id'=>count($events)+1,'event_name'=>'Mazie Foundation','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6314483') );

array_push($events, array('id'=>count($events)+1,'event_name'=>'Big Brothers Big Sisters of Hampden County','event_id'=>'1134200','amount_raised'=>'0.00','last_updated'=>'','no_json'=>'','event_link'=>'http://bbbsmass.kintera.org/faf/teams/groupTeamList.asp?ievent=1134200&lis=1&tlteams=6409029') );
*/

usort($events,"sort_eventname");

$idx = 0;
foreach ($events as $event){
	$eventOptions[$idx] = "<option value='".$event['event_id']."'>".str_replace('2017','',$event['event_name'])."</option>";
	$idx++;
}
?>

<html style="margin: 0; height: 240px;">
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="jquery.form.min.js"></script>
	</head>
	<body style="margin: 0; height: 207px;">
		<div style="width: 237px; text-align: center; margin: 0 auto;">
			<h4 style="width: 226px; margin: 20px auto; font-family: Arial, sans-serif; font-weight: normal; text-align: center;">Select your charity and enter your login credentials to continue.</h4>
			<select id="thons" style="max-width: 157px; margin: 0 0 15px; outline: none;">
				<option value="">Select your charity...</option>
				<?php foreach($eventOptions as $option) { echo $option; } ?>
			</select>
			<form target="_blank" id="FAFLoginFormHeader" name="registrationForm2" action="" method="POST" style="margin: 0 auto 5px; width: 157px">
				<input style="width: 100%;" type="text" name="username" value="Username" maxlength="100" id="Text1" onfocus="this.value=''" onblur="if(this.value == '') this.value='Username'">
				<input style="width: 100%;" type="password" name="password" value="Password" maxlength="20" id="Password1" onfocus="this.value=''" onblur="if(this.value == '') this.value='Password'">
				<input type="hidden" name="passwordRem" value="" id="Hidden1">
				<input type="hidden" name="ssl" value="" id="Hidden2">
				<span class="rememberMe" style="display:none">
					<input type="checkbox" name="faf_rememberme" value="y" checked="" id="Checkbox1"> 
					Remember Me
				</span>
				<span id="forgotUsername" style="display:none">
					<a href="http://operation.kintera.org/faf/login/loginFindPassword.asp?ievent=1156986" class="faf-forgotusername">
						Forgot Username?
					</a>
				</span>
			</form>
			<button style="margin: 10px 0;" border="0" name="imageField2" id="loginSubmit" value="Log In">Log In</button>
			<p id="msg-box" style="color: red; font-size: 12px; font-family: Arial, sans-serif; margin-top: 0;"></p>
		</div>
	</body>
</html>
<script type="text/javascript">
	$(document).ready(function(){
		$('select#thons').change(function(){
			$(this).css('border', '1px solid darkgray');
			$('#msg-box').text('');
			//get selected thon event id
			var eventID = $(this).find('option:selected').attr('value');
			//update the action of the form so it attempts to log in to the selected thon
			if (eventID != "")
				$('#FAFLoginFormHeader').attr('action', "http://www.kintera.org/faf/login/checkPartLogin.asp?ievent=" + eventID);
			else
				$('#FAFLoginFormHeader').attr('action', "" + eventID);
		});
		
		//check for pushing enter key while using textboxes
		$('#FAFLoginFormHeader input').keydown(function(e){
			var code = (e.keyCode ? e.keyCode : e.which);
			if(code == 13)
				$('#FAFLoginFormHeader').submit();
		});
		
		$('#loginSubmit').click(function(){
			$('#FAFLoginFormHeader').submit();
		});
		
		$('#FAFLoginFormHeader').submit(function(){
			$('select#thons').css('border', '1px solid darkgray');
			$('#msg-box').text('');
			if ($(this).attr('action') == ""){
				$('select#thons').css('border', '1px solid red');
				$('#msg-box').text('Please select an event.');
				return false;
			}
		});
		
		// $('#FAFLoginFormHeader').ajaxForm(function(){
			// console.log('nice');
		// });
	});
</script>