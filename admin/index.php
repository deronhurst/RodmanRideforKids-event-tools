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

$imported = false;
if($_POST['submit']=="Import"){

	$csv = array();

	// check there are no errors
	if($_FILES['upycle_csv']['error'] == 0){
		$name = $_FILES['upycle_csv']['name'];
		$ext = strtolower(end(explode('.', $_FILES['upycle_csv']['name'])));
		$type = $_FILES['upycle_csv']['type'];
		$tmpName = $_FILES['upycle_csv']['tmp_name'];

		// check the file is a csv
		if($ext === 'csv'){
			if(($handle = fopen($tmpName, 'r')) !== FALSE) {
				// necessary if a large csv file
				set_time_limit(0);

				$row = 0;

				while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
					// number of fields in the csv
					$num = count($data);

					// get the values from the csv
					$csv[$row]['supporter_id'] = $data[0];
					$csv[$row]['event_id'] = $data[1];
					$csv[$row]['amount'] = $data[2];

					// inc the row
					$row++;
				}
				fclose($handle);
			}
		}
	}

	/* Loop through CSV array and add to DB */
	$now = time();
	foreach($csv as $c){
		$db->query("INSERT INTO upcycle_donations (supporter_id,event_id,donation_amount,date_created) VALUES ('".$c['supporter_id']."','".$c['event_id']."','".$c['amount']."','".$now."')");
	}

	$imported = true;


}

?>

<html>
<head>
<title>Rodman Ride for Kids - Admin</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link rel="stylesheet" type="text/css" href="admin.css"/>
</head>
<body>

<div id="container">
	<div id="inner">

		<div id="header">Administration</div>
		<div id="top_nav">
			<ul>
				<li><a href="index.php" class="active">Main Page</a></li>
				<li><a href="upcycle_entries.php">Upcycle Entries</a></li>
			</ul>
		</div>
		
		<div id="content">
			<h1>Rodman Ride for Kids - Admin</h1>

			<?php
			if($imported==true){
				echo '<div class="success">CSV file imported successfully</div>';
			}
			?>
			<strong>Import UpCycle CSV file:</strong><br/>
			<form method="post" action="index.php" enctype="multipart/form-data" style="margin-top:10px;">
				<input type="file" name="upycle_csv" value=""/><br/>
				<input type="submit" value="Import" name="submit" style="margin-top:10px;"/>
			</form>

		</div>

	</div>
</div>

</body>
</html>


