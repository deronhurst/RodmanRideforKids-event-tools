<!doctype html>
<html>
<head>
</head>
<body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$.ajax({
		dataType: 'jsonp',
		data: '',
		jsonp: 'jsonp_callback',
		url: 'http://zurigroup2.com/RodmanRideforKids/get_events.php',
		success: function (data) {

			if(data.count>0){
				$("#events_list").append('<table id="events_list_table"></table>');
				for(i=0;i<data.events.length;i++){
					var e = data.events[i];
					$("#events_list_table").append('<tr><td><a href="https://www.kintera.org/faf/donorReg/donorPledge.asp?supId=0&ievent='+e.event_id+'">'+e.event_name+'</a></td></tr>');
				}
			}
		}
	});
});
</script>

<div id="events_list"></div>
</body>
</html>