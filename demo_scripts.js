function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}

/*
 * SimpleModal 1.4.4 - jQuery Plugin
 * http://simplemodal.com/
 * Copyright (c) 2013 Eric Martin
 * Licensed under MIT and GPL
 * Date: Sun, Jan 20 2013 15:58:56 -0800
 */
(function(b){"function"===typeof define&&define.amd?define(["jquery"],b):b(jQuery)})(function(b){var j=[],n=b(document),k=navigator.userAgent.toLowerCase(),l=b(window),g=[],o=null,p=/msie/.test(k)&&!/opera/.test(k),q=/opera/.test(k),m,r;m=p&&/msie 6./.test(k)&&"object"!==typeof window.XMLHttpRequest;r=p&&/msie 7.0/.test(k);b.modal=function(a,h){return b.modal.impl.init(a,h)};b.modal.close=function(){b.modal.impl.close()};b.modal.focus=function(a){b.modal.impl.focus(a)};b.modal.setContainerDimensions=
function(){b.modal.impl.setContainerDimensions()};b.modal.setPosition=function(){b.modal.impl.setPosition()};b.modal.update=function(a,h){b.modal.impl.update(a,h)};b.fn.modal=function(a){return b.modal.impl.init(this,a)};b.modal.defaults={appendTo:"body",focus:!0,opacity:50,overlayId:"simplemodal-overlay",overlayCss:{},containerId:"simplemodal-container",containerCss:{},dataId:"simplemodal-data",dataCss:{},minHeight:null,minWidth:null,maxHeight:null,maxWidth:null,autoResize:!1,autoPosition:!0,zIndex:1E3,
close:!0,closeHTML:'<a class="modalCloseImg" title="Close"></a>',closeClass:"simplemodal-close",escClose:!0,overlayClose:!1,fixed:!0,position:null,persist:!1,modal:!0,onOpen:null,onShow:null,onClose:null};b.modal.impl={d:{},init:function(a,h){if(this.d.data)return!1;o=p&&!b.support.boxModel;this.o=b.extend({},b.modal.defaults,h);this.zIndex=this.o.zIndex;this.occb=!1;if("object"===typeof a){if(a=a instanceof b?a:b(a),this.d.placeholder=!1,0<a.parent().parent().size()&&(a.before(b("<span></span>").attr("id",
"simplemodal-placeholder").css({display:"none"})),this.d.placeholder=!0,this.display=a.css("display"),!this.o.persist))this.d.orig=a.clone(!0)}else if("string"===typeof a||"number"===typeof a)a=b("<div></div>").html(a);else return alert("SimpleModal Error: Unsupported data type: "+typeof a),this;this.create(a);this.open();b.isFunction(this.o.onShow)&&this.o.onShow.apply(this,[this.d]);return this},create:function(a){this.getDimensions();if(this.o.modal&&m)this.d.iframe=b('<iframe src="javascript:false;"></iframe>').css(b.extend(this.o.iframeCss,
{display:"none",opacity:0,position:"fixed",height:g[0],width:g[1],zIndex:this.o.zIndex,top:0,left:0})).appendTo(this.o.appendTo);this.d.overlay=b("<div></div>").attr("id",this.o.overlayId).addClass("simplemodal-overlay").css(b.extend(this.o.overlayCss,{display:"none",opacity:this.o.opacity/100,height:this.o.modal?j[0]:0,width:this.o.modal?j[1]:0,position:"fixed",left:0,top:0,zIndex:this.o.zIndex+1})).appendTo(this.o.appendTo);this.d.container=b("<div></div>").attr("id",this.o.containerId).addClass("simplemodal-container").css(b.extend({position:this.o.fixed?
"fixed":"absolute"},this.o.containerCss,{display:"none",zIndex:this.o.zIndex+2})).append(this.o.close&&this.o.closeHTML?b(this.o.closeHTML).addClass(this.o.closeClass):"").appendTo(this.o.appendTo);this.d.wrap=b("<div></div>").attr("tabIndex",-1).addClass("simplemodal-wrap").css({height:"100%",outline:0,width:"100%"}).appendTo(this.d.container);this.d.data=a.attr("id",a.attr("id")||this.o.dataId).addClass("simplemodal-data").css(b.extend(this.o.dataCss,{display:"none"})).appendTo("body");this.setContainerDimensions();
this.d.data.appendTo(this.d.wrap);(m||o)&&this.fixIE()},bindEvents:function(){var a=this;b("."+a.o.closeClass).bind("click.simplemodal",function(b){b.preventDefault();a.close()});a.o.modal&&a.o.close&&a.o.overlayClose&&a.d.overlay.bind("click.simplemodal",function(b){b.preventDefault();a.close()});n.bind("keydown.simplemodal",function(b){a.o.modal&&9===b.keyCode?a.watchTab(b):a.o.close&&a.o.escClose&&27===b.keyCode&&(b.preventDefault(),a.close())});l.bind("resize.simplemodal orientationchange.simplemodal",
function(){a.getDimensions();a.o.autoResize?a.setContainerDimensions():a.o.autoPosition&&a.setPosition();m||o?a.fixIE():a.o.modal&&(a.d.iframe&&a.d.iframe.css({height:g[0],width:g[1]}),a.d.overlay.css({height:j[0],width:j[1]}))})},unbindEvents:function(){b("."+this.o.closeClass).unbind("click.simplemodal");n.unbind("keydown.simplemodal");l.unbind(".simplemodal");this.d.overlay.unbind("click.simplemodal")},fixIE:function(){var a=this.o.position;b.each([this.d.iframe||null,!this.o.modal?null:this.d.overlay,
"fixed"===this.d.container.css("position")?this.d.container:null],function(b,e){if(e){var f=e[0].style;f.position="absolute";if(2>b)f.removeExpression("height"),f.removeExpression("width"),f.setExpression("height",'document.body.scrollHeight > document.body.clientHeight ? document.body.scrollHeight : document.body.clientHeight + "px"'),f.setExpression("width",'document.body.scrollWidth > document.body.clientWidth ? document.body.scrollWidth : document.body.clientWidth + "px"');else{var c,d;a&&a.constructor===
Array?(c=a[0]?"number"===typeof a[0]?a[0].toString():a[0].replace(/px/,""):e.css("top").replace(/px/,""),c=-1===c.indexOf("%")?c+' + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"':parseInt(c.replace(/%/,""))+' * ((document.documentElement.clientHeight || document.body.clientHeight) / 100) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"',a[1]&&(d="number"===typeof a[1]?
a[1].toString():a[1].replace(/px/,""),d=-1===d.indexOf("%")?d+' + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"':parseInt(d.replace(/%/,""))+' * ((document.documentElement.clientWidth || document.body.clientWidth) / 100) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"')):(c='(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"',
d='(document.documentElement.clientWidth || document.body.clientWidth) / 2 - (this.offsetWidth / 2) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"');f.removeExpression("top");f.removeExpression("left");f.setExpression("top",c);f.setExpression("left",d)}}})},focus:function(a){var h=this,a=a&&-1!==b.inArray(a,["first","last"])?a:"first",e=b(":input:enabled:visible:"+a,h.d.wrap);setTimeout(function(){0<e.length?e.focus():h.d.wrap.focus()},
10)},getDimensions:function(){var a="undefined"===typeof window.innerHeight?l.height():window.innerHeight;j=[n.height(),n.width()];g=[a,l.width()]},getVal:function(a,b){return a?"number"===typeof a?a:"auto"===a?0:0<a.indexOf("%")?parseInt(a.replace(/%/,""))/100*("h"===b?g[0]:g[1]):parseInt(a.replace(/px/,"")):null},update:function(a,b){if(!this.d.data)return!1;this.d.origHeight=this.getVal(a,"h");this.d.origWidth=this.getVal(b,"w");this.d.data.hide();a&&this.d.container.css("height",a);b&&this.d.container.css("width",
b);this.setContainerDimensions();this.d.data.show();this.o.focus&&this.focus();this.unbindEvents();this.bindEvents()},setContainerDimensions:function(){var a=m||r,b=this.d.origHeight?this.d.origHeight:q?this.d.container.height():this.getVal(a?this.d.container[0].currentStyle.height:this.d.container.css("height"),"h"),a=this.d.origWidth?this.d.origWidth:q?this.d.container.width():this.getVal(a?this.d.container[0].currentStyle.width:this.d.container.css("width"),"w"),e=this.d.data.outerHeight(!0),f=
this.d.data.outerWidth(!0);this.d.origHeight=this.d.origHeight||b;this.d.origWidth=this.d.origWidth||a;var c=this.o.maxHeight?this.getVal(this.o.maxHeight,"h"):null,d=this.o.maxWidth?this.getVal(this.o.maxWidth,"w"):null,c=c&&c<g[0]?c:g[0],d=d&&d<g[1]?d:g[1],i=this.o.minHeight?this.getVal(this.o.minHeight,"h"):"auto",b=b?this.o.autoResize&&b>c?c:b<i?i:b:e?e>c?c:this.o.minHeight&&"auto"!==i&&e<i?i:e:i,c=this.o.minWidth?this.getVal(this.o.minWidth,"w"):"auto",a=a?this.o.autoResize&&a>d?d:a<c?c:a:f?
f>d?d:this.o.minWidth&&"auto"!==c&&f<c?c:f:c;this.d.container.css({height:b,width:a});this.d.wrap.css({overflow:e>b||f>a?"auto":"visible"});this.o.autoPosition&&this.setPosition()},setPosition:function(){var a,b;a=g[0]/2-this.d.container.outerHeight(!0)/2;b=g[1]/2-this.d.container.outerWidth(!0)/2;var e="fixed"!==this.d.container.css("position")?l.scrollTop():0;this.o.position&&"[object Array]"===Object.prototype.toString.call(this.o.position)?(a=e+(this.o.position[0]||a),b=this.o.position[1]||b):
a=e+a;this.d.container.css({left:b,top:a})},watchTab:function(a){if(0<b(a.target).parents(".simplemodal-container").length){if(this.inputs=b(":input:enabled:visible:first, :input:enabled:visible:last",this.d.data[0]),!a.shiftKey&&a.target===this.inputs[this.inputs.length-1]||a.shiftKey&&a.target===this.inputs[0]||0===this.inputs.length)a.preventDefault(),this.focus(a.shiftKey?"last":"first")}else a.preventDefault(),this.focus()},open:function(){this.d.iframe&&this.d.iframe.show();b.isFunction(this.o.onOpen)?
this.o.onOpen.apply(this,[this.d]):(this.d.overlay.show(),this.d.container.show(),this.d.data.show());this.o.focus&&this.focus();this.bindEvents()},close:function(){if(!this.d.data)return!1;this.unbindEvents();if(b.isFunction(this.o.onClose)&&!this.occb)this.occb=!0,this.o.onClose.apply(this,[this.d]);else{if(this.d.placeholder){var a=b("#simplemodal-placeholder");this.o.persist?a.replaceWith(this.d.data.removeClass("simplemodal-data").css("display",this.display)):(this.d.data.hide().remove(),a.replaceWith(this.d.orig))}else this.d.data.hide().remove();
this.d.container.hide().remove();this.d.overlay.hide();this.d.iframe&&this.d.iframe.hide().remove();this.d.overlay.remove();this.d={}}}}});


$(document).ready(function(){

// Register For Ride button click
$(".RideLanding a:contains('Register for Ride')").click(function(){
	$.ajax({
		type: "GET",
		dataType: 'jsonp',
		data: '',
		jsonp: 'jsonp_callback',
		url: '//zurigroup2.com/RodmanRideforKids/get_events.php',
		success: function (data) {
			$("#event_list_modal").remove();
			$("body").append('<div id="event_list_modal" style="display:none;"><h2>All Events</h2><div id="elm_content"></div></div>');
			for(i=0;i<data.events.length;i++){
				var e = data.events[i];
				$("#event_list_modal #elm_content").append('<div class="event_row"><a href="http://www.kintera.org/FAF/home/default.asp?ievent='+e.event_id+'">'+e.event_name+'</a></div>');
			}
			$("#event_list_modal").modal();
		}
	});
	return false;
});

// Register For Ride button click
$(".RideLanding a:contains('Donate to an Affiliated Charity')").click(function(){
	$.ajax({
		type: "GET",
		dataType: 'jsonp',
		data: '',
		jsonp: 'jsonp_callback',
		url: '//zurigroup2.com/RodmanRideforKids/get_events.php',
		success: function (data) {
			$("#event_list_modal").remove();
			$("body").append('<div id="event_list_modal" style="display:none;"><h2>All Events</h2><div id="elm_content"></div></div>');
			for(i=0;i<data.events.length;i++){
				var e = data.events[i];
				$("#event_list_modal #elm_content").append('<div class="event_row"><a href="http://www.kintera.org/faf/donorReg/donorPledge.asp?supId=0&ievent='+e.event_id+'&team=">'+e.event_name+'</a></div>');
			}
			$("#event_list_modal").modal();
		}
	});
	return false;
});


	// Event List
	if($("#event_list").size()>0){
		$.ajax({
			type: "GET",
			dataType: 'jsonp',
			data: '',
			jsonp: 'jsonp_callback',
			url: '//zurigroup2.com/RodmanRideforKids/get_events.php',
			success: function (data) {
				$("#event_list").html('');
				for(i=0;i<data.events.length;i++){
					var e = data.events[i];
					$("#event_list").append('<div class="event_row"><span class="event_name"><a href="http://www.kintera.org/FAF/home/default.asp?ievent='+e.event_id+'">'+e.event_name+'</a></span><span class="event_total">$'+number_format(e.amount_raised)+'</span></div>');
				}
			}
		});
	}

function submit_participant_search(){
    var fname = $.trim($("form[name='search_form'] input[name='first_name']").val());
    var lname = $.trim($("form[name='search_form'] input[name='last_name']").val());
    if(fname=="" && lname==""){
        alert('Please enter a valid first name or last name.');
    }else{
       get_participants(0,fname,lname);
    }
}
$("#submit_button").click(function(){
	submit_participant_search();
});

function get_participants(start,fname,lname){
  if(typeof start=="undefined") start = 0;
  $.ajax({
	dataType: 'jsonp',
	data: 's='+start+'&fname='+fname+'&lname='+lname,
	jsonp: 'jsonp_callback',
	url: '//zurigroup2.com/RodmanRideforKids/participant_search.php',
	success: function (data) {
		$("#search_results_table > tbody > tr:gt(0)").remove();

		//$("#map_container").before('<div id="ajax_search_results">Search Results</div>');
		//$("#ajax_search_results").html('<p style="margin-bottom:0;padding-bottom:0;text-align:left;">Click on the Bowler Name below to visit that Bowler\'s personal headquarters and show your support. Click on the Event Name to visit the Bowl for Kids\' Sake website for the event in which that bowler is registered. This database may not include all of the many Bowl for Kids\' Sake supporters that help us Start Something across the country. If you don\'t see a particular bowler listed in the results below, please reach out to the Big Brothers Big Sisters agency serving your community for more information about how you can show your support.</p>');
		//$("#ajax_search_results").append('<table id="ajax_results" cellspacing="0"><tr><th>Bowler Name</th><th>Event Name</th><th>City, State</th><th>Amount Raised</th></tr></table>');
		if(data.participants.length>0){
			start = parseInt(start);
			var total = parseInt(data.total_results);
			var perpage = parseInt(data.perpage);
			var next = start+perpage;
			var prev = start-perpage;
			if(prev<0) prev = 0;
			for(i=0;i<data.participants.length;i++){
				var p = data.participants[i];
				$("#search_results_table tr:last").after('<tr><td><a href="/faf/donorReg/donorPledge.asp?ievent='+p.event_id+'&supId='+p.supporter_id+'">'+p.fname+' '+p.lname+'</a></td><td>'+p.event_name+'</td><td>$'+number_format(p.amount_raised,2)+'</td></tr>');
			}
			if(data.total_results>50){
				$("#search_results_table tr:last").after('<tr><td colspan="4" class="pagination_cell"><div id="ajax_pagination"></div></td></tr>');
				if(start!=0) $("#ajax_pagination").prepend('<div class="prev_btn"><a href="javascript:void(0);" onclick="get_participants(\''+prev+'\',\''+fname+'\',\''+lname+'\')">&laquo;  Previous Page</a></div>');
				if(next<total) $("#ajax_pagination").append('<div class="next_btn"><a href="javascript:void(0);" onclick="get_participants(\''+next+'\',\''+fname+'\',\''+lname+'\')">Next Page &raquo; </a></div>');
				if(start!=0 && next<total) $(".prev_btn").after('<div class="pagination_sep">&nbsp;|&nbsp;</div>');
				$("#ajax_pagination").append('<div class="pages"><select name="page_choice" rel="walker|'+fname+'|'+lname+'" onchange="change_page(this)"></select></div>');
				for(p=0;p<parseInt(data.pages);p++){
					var p2 = p+1;
					var sel = '';
					if(p2==data.current_page) sel = 'selected="selected"';
					$("select[name='page_choice']").append('<option value="'+p+'" '+sel+'>Page '+p2+'</option>');
				}
			}

			//$("#search_results_table").append('<div class="ajax_load_icon"><span id="ali"><img src="/atf/cf/{6d1defe8-1b83-4f4b-95bd-7af653fa2098}/icon_ajaxloader.gif"/></span></div>');
		}else{
			$("#search_results_table tr:last").after('<tr><td colspan="4">No participants found.</td></tr>');
		}
	}
  });
}

function get_toplists(num){
	$.ajax({
		dataType: 'jsonp',
		data: 'num='+num,
		jsonp: 'jsonp_callback',
		url: '//zurigroup2.com/RodmanRideforKids/get_top_lists.php',
		success: function (data) {
			/* Participants */
			for(var i=0;i<data.participants.length;i++){
				var d = data.participants[i];
				var n = i+1;
				$("#top_participants_table").append('<tr><td class="num">'+n+'.</td><td class="name"><a href="http://www.kintera.org/faf/donorReg/donorPledge.asp?ievent='+d.event_id+'&supId='+d.supporter_id+'">'+d.fname+' '+d.lname+'</a></td><td class="amount">$'+number_format(d.amount_raised)+'</td></tr>');
			}

			// Teams/Companies
			
			for(var i=0;i<data.teams.length;i++){
				var d = data.teams[i];
				var n = i+1;
				$("#top_teams_table").append('<tr><td class="num">'+n+'.</td><td class="name"><a href="http://www.kintera.org/faf/teams/groupTeamList.asp?ievent='+d.event_id+'&tlteams='+d.team_company_id+'">'+d.team_company_name+'</a></td><td class="amount">$'+number_format(d.amount_raised)+'</td></tr>');
			}
			

			// Events
			for(var i=0;i<data.events.length;i++){
				var d = data.events[i];
				var n = i+1;
				$("#top_events_table").append('<tr><td class="num">'+n+'.</td><td class="name"><a href="http://www.kintera.org/faf/teams/groupTeamList.asp?ievent='+d.event_id+'">'+d.event_name+'</a></td><td class="amount">$'+number_format(d.amount_raised)+'</td></tr>');
			}

			$("#top_participants_table tr:even,#top_teams_table tr:even,#top_events_table tr:even").addClass('odd');
		}
	});
}
get_toplists('10');


});