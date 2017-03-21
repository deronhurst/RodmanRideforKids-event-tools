<html>
<head>
<style>
/*PARTICIPANT SEARCH*/

#PARTICIPANTSEARCH {
	padding: 40px 20px 60px 20px;
}
#PARTICIPANTSEARCH label, #PARTICIPANTSEARCH input {
	display: block;
	float: left;
}
#PARTICIPANTSEARCH label {
	width: 100px;
}
#PARTICIPANTSEARCH input[type=submit] {
	background-image: linear-gradient(bottom, rgb(74,125,30) 0%, rgb(134,198,82) 100%);
	background-image: -o-linear-gradient(bottom, rgb(74,125,30) 0%, rgb(134,198,82) 100%);
	background-image: -moz-linear-gradient(bottom, rgb(74,125,30) 0%, rgb(134,198,82) 100%);
	background-image: -webkit-linear-gradient(bottom, rgb(74,125,30) 0%, rgb(134,198,82) 100%);
	background-image: -ms-linear-gradient(bottom, rgb(74,125,30) 0%, rgb(134,198,82) 100%);
	background-image: -webkit-gradient(  linear,  left bottom,  left top,  color-stop(0, rgb(74,125,30)),  color-stop(1, rgb(134,198,82))  );
	background-color: #86C652;
	border: 0px;
	border-radius: 5px 5px 5px 5px;
	color: #FFFFFF;
	padding: 3px 10px;
	font-size: 15px;
}
#PARTICIPANTSEARCH .form_row {
	overflow: hidden;
}
#PARTICIPANTSEARCH .form_submit_row {
	overflow: hidden;
	padding-top: 10px;
}
#ajax_search_results {
	margin-top: 20px;
	border-top: 5px solid #86C652;
	overflow: hidden;
}
#ajax_pagination {
	padding: 10px 0px;
}
#ajax_results {
	padding-top: 10px;
	border-top: 1px solid #cfcfcf;
	width: 100%;
}
#ajax_results td, #ajax_results th {
	padding: 5px 0px;
	text-align: left;
}
.prev_btn, .next_btn {
	padding: 5px 10px 5px 10px;
	float: left;
	margin-right: 5px;
}
.pages {
	margin-top: 5px;
}
.prev_btn, .next_btn {
	background-color: #86C652;
	border-radius: 5px 5px 5px 5px;
	color: #FFFFFF;
	text-align: center;
}
.prev_btn a, .next_btn a {
	color: #FFFFFF;
	display: block;
}
</style>
</head>
<body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">
function change_page(el){
	$("#ali").show();
    var thisrel = $(el).attr('rel').split('|');
    var thistype = thisrel[0];
    var fname = thisrel[1];
    var lname = thisrel[2];
    var ename = thisrel[3];
    var tname = thisrel[4];
    var start = parseInt($("option:selected",el).val())*10;
    if(thistype=='participant'){
        get_participants(start,fname,lname,ename,tname);
    }else if(thistype=='team'){
		var tname = thisrel[1];
		//get_teams(start,tname);
	}
}

function submit_participant_search(){
    var fname = $.trim($("input[id='first_name']").val());
    var lname = $.trim($("input[id='last_name']").val());
	var ename = $.trim($("input[id='event_name']").val());
	var tname = $.trim($("input[id='team_name']").val());
    if(fname=="" && lname=="" && ename=="" && tname==""){
        alert('Please enter a first, last, charity or team name to search for');
    }else{
       get_participants(0,fname,lname,ename,tname);
    }
	return false;
}

function get_participants(start,fname,lname,ename,tname){
  $("#ali").show();
  if(typeof start=="undefined") start = 0;
  $.ajax({
    dataType: 'jsonp',
    data: 's='+start+'&fname='+fname+'&lname='+lname+'&ename='+ename+'&tname='+tname,
    jsonp: 'jsonp_callback',
    url: 'http://zurigroup2.com/RodmanRideforKids/participant_search.php',
    success: function (data) {
	    $("#search_container").hide();
		$("#ajax_search_results").remove();
        if(data.participants.length>0){
            start = parseInt(start);
            var total = parseInt(data.total_results);
            var perpage = parseInt(data.perpage);
            var next = start+perpage;
            var prev = start-perpage;
            if(prev<0) prev = 0;
            $("#search_container").before('<div id="ajax_search_results"></div>');
            $("#ajax_search_results").html('<table id="ajax_results" cellspacing="0"><tr><th>Participant Name</th><th>Event Name</th><th>City, State</th><th>Amount Raised</th></tr></table>');
            for(i=0;i<data.participants.length;i++){
                var w = data.participants[i];
                $("#ajax_results tr:last").after('<tr><td class="name"><a href="http://www.kintera.org/faf/donorReg/donorPledge.asp?ievent='+w.event_id+'&supId='+w.id+'">'+w.fname+' '+w.lname+'</a></td><td class="eventname"><a href="http://www.kintera.org/faf/home/default.asp?ievent='+w.event_id+'">'+w.event_name+'</a></td><td class="citystate">'+w.city+', '+w.state+'</td><td class="amount">$'+w.amount_raised+'</td></tr>');
            }
            $("#ajax_results tr:first").after('<tr><td colspan="4" class="pagination_cell"><div id="ajax_pagination"></div></td></tr>');
            if(start!=0) $("#ajax_pagination").prepend('<div class="prev_btn"><a href="javascript:void(0);" onclick="get_participants(\''+prev+'\',\''+fname+'\',\''+lname+'\',\''+ename+'\',\''+tname+'\')">Previous Page</a></div>');
            if(next<total) $("#ajax_pagination").append('<div class="next_btn"><a href="javascript:void(0);" onclick="get_participants(\''+next+'\',\''+fname+'\',\''+lname+'\',\''+ename+'\',\''+tname+'\')">Next Page</a></div>');

            $("#ajax_pagination").append('<div class="pages"><select name="page_choice" rel="participant|'+fname+'|'+lname+'|'+ename+'|'+tname+'" onchange="change_page(this)"></select></div>');
            for(p=0;p<parseInt(data.pages);p++){
                var p2 = p+1;
                var sel = '';
                if(p2==data.current_page) sel = 'selected="selected"';
                $("select[name='page_choice']").append('<option value="'+p+'" '+sel+'>Page '+p2+'</option>');
            }
			$("#ajax_search_results").append('<div class="ajax_load_icon"><span id="ali"><img src="/atf/cf/{6d1defe8-1b83-4f4b-95bd-7af653fa2098}/icon_ajaxloader.gif"/></span></div>');
        }else{
            $("#search_container").before('<div id="ajax_search_results" class="none"><p>No participants found</p></div>');
        }
		$("#ali").hide();
    }
  });
}

</script>
<div id="PARTICIPANTSEARCH">
<strong>Participant Search:</strong><br/><br/>
 <div class="form_row">
    <label>First Name:</label>
    <input type="text" id="first_name">
  </div>
  <div class="form_row">
    <label>Last Name:</label>
    <input type="text" id="last_name">
  </div>
  <div class="form_row">
    <label>Charity Name:</label>
    <input type="text" id="event_name">
  </div>
  <div class="form_row">
    <label>Team Name:</label>
    <input type="text" id="team_name">
  </div>
  <div class="form_submit_row">
    <label>&nbsp;</label>
    <input type="submit" onclick="submit_participant_search();" value="Search" name="submit">
  </div>
</div>
<br/><br/><hr/><br/><br/>
<div id="search_container"></div>
</body>
</html>