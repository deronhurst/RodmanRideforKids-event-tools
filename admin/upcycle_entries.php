<?php
session_start();
//error_reporting(E_ALL);
require_once('config.php');

if (!$_SESSION['user'] || !$_SESSION['pass']) {
	header('Location: login.php');
	die();

}else{

	if ($_SESSION['user']!=ADMIN_USER || $_SESSION['pass']!=ADMIN_PASS) {
		// If the credentials didn't match,
		// redirect the user to the login screen.
		header('Location: login.php');
		die();
	}
}

if($_POST['submit_modify']){
	$entry_id = mysql_real_escape_string($_POST['entry_id']);
	$supporter_id = mysql_real_escape_string($_POST['supporter_id']);
	$event_id = mysql_real_escape_string($_POST['event_id']);
	$donation_amount = mysql_real_escape_string($_POST['donation_amount']);

	$db->query("UPDATE upcycle_donations SET 
		supporter_id = '{$supporter_id}',
		event_id = '{$event_id}',
		donation_amount = '{$donation_amount}'
	WHERE id = '{$entry_id}'");
	$updated = 1;
}

if($_POST['delete_all_selected']){
	$db->query("DELETE FROM upcycle_donations WHERE id IN(".$_POST['selected_entries'].")");
}

if($_GET['action']=="delete" && is_numeric($_GET['id'])){
	$db->query("DELETE FROM upcycle_donations WHERE id = '".$_GET['id']."'");
}

?>

<html>
<head>
<title>Rodman Ride for Kids - Admin</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link rel="stylesheet" type="text/css" href="admin.css"/>
<script src="jquery-1.4.2.min.js" type="text/javascript"></script>
</head>
<body>

<div id="container">
	<div id="inner">

		<div id="header">Administration</div>
		<div id="top_nav">
			<ul>
				<li><a href="index.php">Main Page</a></li>
				<li><a href="upcycle_entries.php" class="active">Upcycle Entries</a></li>
			</ul>
		</div>
		
		<div id="content">

			<?php
			if($_GET['action']=="edit" && is_numeric($_GET['id'])){
				$entry = $db->get_row("SELECT * FROM upcycle_donations WHERE id = '".$_GET['id']."'");
				?>
				<h1>Edit entry</h1>
				<?php
				if($updated=="1"){
					echo '<div class="success">This entry has been updated successfully. <a href="upcycle_entries.php">Return to Entries list</a></div>';
				}
				?>
				<form action="upcycle_entries.php?action=edit&id=<?php echo $_GET['id'];?>" method="post">
					<input type="hidden" name="entry_id" value="<?php echo $entry->id;?>"/>
					<strong>Supporter ID</strong><br/>
					<input type="text" name="supporter_id" value="<?php echo stripslashes($entry->supporter_id);?>"/>
					<br/><br/>
					<strong>Event ID</strong><br/>
					<input type="text" name="event_id" value="<?php echo stripslashes($entry->event_id);?>"/>
					<br/><br/>
					<strong>Donation Amount</strong><br/>
					$<input type="text" name="donation_amount" value="<?php echo stripslashes($entry->donation_amount);?>"/>
					<br/><br/>
					<input type="submit" name="submit_modify" value="  Modify Entry  "/>&nbsp;&nbsp;&nbsp;<input type="button" value="Cancel" onClick="javascript:window.location='upcycle_entries.php';"/>

				</form>
			<?php
			}else{
			?>			
			
				<h1>Upcycle Entries</h1>

				<form method="post" action="upcycle_entries.php" onSubmit="return submit_multi_form();">
					<input type="submit" name="delete_all_selected" value="Delete Selected" style="margin-bottom:5px;"/>
					<input type="hidden" name="selected_entries" value=""/>
				</form>

				<table class="approved_list" width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<th style="width:22px;"><input type="checkbox" name="multi_all" value=""/></th>
						<th>Supporter ID</th>
						<th>Event ID</th>
						<th>Donation Amount</th>
						<th>Date</th>
						<th class="actions_header">Actions</th>
					</tr>

					<?php
					$entries = $db->get_results("SELECT * FROM upcycle_donations");
					$total = $db->num_rows;
					if($total > 0){
						foreach($entries as $e){
							echo '<tr><td><input type="checkbox" name="multi" value="'.$e->id.'"/></td><td>'.$e->supporter_id.'</td><td>'.$e->event_id.'</td><td>$'.$e->donation_amount.'</td><td nowrap="nowrap">'.date("F d, Y",$e->date_created).'</td><td class="actions_cell"><a href="upcycle_entries.php?action=edit&id='.$e->id.'" class="icon_edit"><img alt="Edit this entry" title="Edit this entry" src="icon_edit.png" border="0"/></a>&nbsp;<a href="javascript:void(0);" class="icon_delete" rel="'.$e->id.'"><img alt="Delete this entry" title="Delete this entry" src="icon_reject.png" border="0"/></a></td></tr>';
						}
					}
					?>
				</table>

			<?php
			}
			?>
		</div>

	</div>
</div>


<script type="text/javascript">
$("a.icon_delete").click(function(){
	var id = $(this).attr('rel');
	var phrase = $(this).parent().prev().text();
	var answer = confirm('Are you sure you want to delete this entry?')
	if(answer){
		window.location = "upcycle_entries.php?action=delete&id="+id;
	}
});

$("input[name='multi_all']").click(function(){
	var checked = $(this).attr('checked');
	if(checked==true){
		$("input[name='multi']").attr('checked',true);
	}else{
		$("input[name='multi']").attr('checked',false);
	}
});

function submit_multi_form(){
	var $se = $("input[name='multi']:checked");
	if($se.size()==0){
		alert('Select an entry to delete');
		return false;
	}else{
		var sel_array = [];
		$se.each(function(){
			sel_array.push( $(this).val() );
		});
		$("input[name='selected_entries']").val(sel_array.join(','));
		var answer = confirm('Are you sure you want to delete these '+$se.size()+' entries?')
		if(answer){
			return true;
		}else{
			return false;
		}
	}

}

</script>

</body>
</html>


