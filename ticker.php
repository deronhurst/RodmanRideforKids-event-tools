<?php
set_time_limit(900);
error_reporting(0);
require_once('lib/ez_sql.php');
$db = new ezSQL_mysql('zurigrou_webapp','astr0dog','zurigrou_rodman2015','localhost');


$total_raised = $db->get_var("SELECT SUM(amount_raised) FROM events");
$upcycle_total = $db->get_var("SELECT SUM(donation_amount) FROM upcycle_donations");
if(!$upcycle_total){
	$upcycle_total = 0;
}
$total_raised = (int)$total_raised + (int)$upcycle_total;
echo "document.write('$".number_format($total_raised,2)."');";
exit;
?>