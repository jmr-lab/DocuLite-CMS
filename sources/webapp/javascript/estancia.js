/*
 *	Global variables
 *
 */
var nbcomponent = 0;

$(function()	{disableSelection();});
// Bind events to the 'perobj' objects and to the 'a' anchors
$('.perobj').live('click', function(event) {$('#contextmenu').hide();return objMouseClick(this, event);	});
$('.perobj').live('contextmenu', function(event) {objMouseRightClick(this, event);showContextMenu(event);return false;});
// Bind an event to the click event on 'objectlist' object
$('.objectlist').live('click', function(event) {$('#contextmenu').hide();return ((event.target.href == undefined && !isObjectImage(event.target)) ? objRemoveAllObjects() : true);});
$('.objectlist').live('contextmenu', function(event) {objRemoveAllObjects();showContextMenu(event);return false;});
$('.modalperobj').live('click', function(event) {return ((event.target.href == undefined && !isObjectImage(event.target) && !isObjectInput(event.target) && !isObjectSelect(event.target)) ? objModalMouseClick(this, event) : true);	});
// Bind an event to the double click event on 'mail' objects
$('.read').live('dblclick', function(event) {objRemoveAllObjects(); objMouseClick(this, event); viewmessage($(this).attr("id"));});
$('.unread').live('dblclick', function(event) {objRemoveAllObjects(); objMouseClick(this, event); viewmessage($(this).attr("id"));});

/*	
 *	Disable the selection on object items
 *
 */
function disableSelection()
{
	$('.perobj').each(function()	{$(this).disableSelection();});
	$('.modalperobj').each(function()	{$(this).disableSelection();});
}

/*	
 *	Display the Login window
 *
 */
function displayLogin()
{
	$('#overlay').css('display', 'inline');
	$('#overlay').css({'zIndex': 4});
	wheight = $(window).height();
	sheight = $('#sheet_2').height();
	swidth = $('#sheet_2').width();
	sleft = ($(window).width() - swidth)/2;
	if (sheight > 0.7 * $(window).height())	{sheight = 0.7 * $(window).height();}
	ptop = (wheight - sheight)/2 - 20;
	$('#sheet_2').css({
		position: 'absolute',
		width: swidth + 'px',
		left: sleft + 'px',
		top: ptop + 'px',
		display: 'inline',
		'zIndex': 5
	});
	$('#user_name').focus();
	// Be sure all modal windows are draggable
	$('.sheet').draggable({ scroll: false, handle: '.drag' });
}

/*	
 *	Open another component, either by jumping (event = jump), nesting (event= nest)
 *	or returning from the current component (event = return)
 *	If the function_name argument is set, it will cause the associated function
 *	to be executed from the server (php script with the same name)
 *	postServerEvent('objectlist', 'jump', 'doclist', null, 'folderId=0000000000000000')
 *
 *	@param	String	source			the source object which called this event (optional)
 *	@param	String	event			the kind of event (jump, nest or return)
 *	@param	String	component		the component called
 *	@param	String	function_name	the function called in the component (optional)
 *	@param	String	arguments		arguments (optional)
 *
 */
function postServerEvent(source, event, component, function_name, arguments)
{
//	if (event == 'return')	{alert('NbComponent : ' + nbcomponent);}
	// Reset the actions and objects selected
	if (event == 'jump' && !$('#sheet_2').length)	{objects.length = 0;actions.length = 0;}
	// Display the 'Please wait' message
	$('#please_wait').css('display', 'inline');
	// Send the request to the server (standard Ajax call)
	$('#ajax').load(
		$(location).attr('pathname') + 'webapp/interfaces/servereventmgr.php',
		getPostValues(source, event, component, function_name, arguments),
		function(responseText, status, xhr){
			$('#ajax').ready(function(){
				// Hide the 'Please wait' message
				$('#please_wait').css('display', 'none');
				// Disable the selection on object items
				disableSelection();
				// Run post processing event function (if defined)
				if(typeof postProcessingEvent == 'function') {postProcessingEvent(); postProcessingEvent = null;}
			});
		}
	);
}

/*	
 *	Parse the content of the form to get all values to be sent by POST method
 *	The data will be retrieved from 'select', 'textarea' and 'input' boxes
 *
 *	@param	String	event			the kind of event (jump, nest or return)
 *	@param	String	component		the component called
 *	@param	String	function_name	the function called in the component (optional)
 *	@param	String	arguments		arguments (optional)
 *
 */
function getPostValues(source, event, component, function_name, arguments)
{
	// if ( !$('#phpinfo').length ) {$('body').append('<div id="phpinfo" class="phpinfo drag"></div>');}
	// $('#phpinfo').html('Message : <br>' + $('#phpinfo').html());
	obj = new Object;
	obj.source = source;
	obj.event = event;
	obj.component = component;
	obj.function_name = function_name;
	obj.arguments = arguments;

	var tmpObj = new Object;
	$('select').each(function(i, elt){ 
		if ($(elt).parent().is(':visible'))	{
			if ($(elt).attr("name").substr($(elt).attr("name").length - 2) == '[]')
			{
				if (tmpObj[$(elt).attr("name")] == undefined)	{tmpObj[$(elt).attr("name")] = '';}
				tmpObj[$(elt).attr("name")] += ',' + $(elt).val();
			}
			else	{obj[$(elt).attr("name")] = $(elt).val();}
		}
	});
	$('textarea').each(function(i, elt){ 
		if ($(elt).parent().is(':visible'))	{
			if ($(elt).attr("name").substr($(elt).attr("name").length - 2) == '[]')
			{
				if (tmpObj[$(elt).attr("name")] == undefined)	{tmpObj[$(elt).attr("name")] = '';}
				tmpObj[$(elt).attr("name")] += ',' + $(elt).val();
			}
			else	{obj[$(elt).attr("name")] = $(elt).val();}
		}
	});
	$('input').each(function(i, elt){
		if ($(elt).parent().is(':visible'))	{
			if ($(elt).attr("name").substr($(elt).attr("name").length - 2) == '[]')
			{
				if (tmpObj[$(elt).attr("name")] == undefined)	{tmpObj[$(elt).attr("name")] = '';}
				tmpObj[$(elt).attr("name")] += ',' + $(elt).val();
			}
			else if ($(elt).attr("type") == 'checkbox' && $(elt).attr("name") != '')	{obj[$(elt).attr("name")] = elt.checked;}
			else if ($(elt).attr("name") != '')		{obj[$(elt).attr("name")] = $(elt).val();}
		}
	});
	$.each(tmpObj, function(index, key){
		obj[index] = key.substr(1).split(',');
	});

	return obj;
}

/*	
 *	Upload a file to a temporary folder
 *
 *	@param	String	component		the component called
 *
 */
function ajaxFileUpload(component)
{
	// Display the 'Please wait' message
	$('#please_wait').css('display', 'inline');

	$.ajaxFileUpload
	(
		{
			url: $(location).attr('pathname') + 'webapp/interfaces/fileimportmgr.php',
			secureuri:false,
			fileElementId:'file',
			dataType: 'json',
			success: function (data, status)
			{
				if(typeof(data.status) != 'undefined')
				{
					// if(data.error != '')	{$('#please_wait').css('display', 'none');$('#message').html(data.error);}
					// else	{postServerEvent(component, 'return', null, 'onOk', null);}
					// postServerEvent(component, 'return', null, 'onOk', null);
					if(data.status == '3')	{$('#please_wait').css('display', 'none');$('#message').html(data.error);}
					else	{postServerEvent(component, 'return', null, 'onOk', null);}
				}
			},
			error: function (data, status, e)
			{
				// Hide the 'Please wait' message
				$('#please_wait').css('display', 'none');
			}
		}
	)
}

/*	
 *	Open another component, either by jumping (event = jump), nesting (event= nest)
 *	or returning from the current component (event = return)
 *	If the function_name argument is set, it will cause the associated function
 *	to be executed from the server (php script with the same name)
 *	Thuis function is to be ran asynchroneously
 *	postClientEvent('objectlist', 'jump', 'doclist', null, 'folderId=0000000000000000')
 *
 *	@param	String	source			the source object which called this event (optional)
 *	@param	String	event			the kind of event (jump, nest or return)
 *	@param	String	component		the component called
 *	@param	String	function_name	the function called in the component (optional)
 *	@param	String	arguments		arguments (optional)
 *
 */
function postClientEvent(source, event, component, function_name, arguments)
{
	if (event == 'return')	{event = 'null';}
	else					{return;}
	// Reset the actions and objects selected
	if (event == 'jump' && !$('#sheet_2').length)	{objects.length = 0;actions.length = 0;}
	// Display the 'Please wait' message
	$('#please_wait').css('display', 'inline');
	// Close the window
	closeWindow(nbcomponent);
	// Hide the 'Please wait' message for faster response
	$('#please_wait').css('display', 'none');
	// Send the request to the server (standard Ajax call)
	$('#fakeajax').load(
		$(location).attr('pathname') + 'webapp/interfaces/servereventmgr.php',
		getPostValues(source, event, component, function_name, arguments),
		function(responseText, status, xhr){
			$('#fakeajax').ready(function(){
				// Disable the selection on object items
				disableSelection();
				// Run post processing event function (if defined)
				if(typeof postProcessingEvent == 'function') {postProcessingEvent(); postProcessingEvent = null;}
			});
		}
	);
}

function view(objectId)
{
	postServerEvent('objectlist', 'nest', 'view', null, 'objectId=' + objectId);
}

function open(objectId)
{
	postServerEvent('objectlist', 'jump', 'doclist', null, 'path=./' + objectId);
}

function properties(objectId)
{
	postServerEvent('objectlist', 'nest', 'properties', null, 'objectId=' + objectId);
}

// Preview the message
function getMessage(mailId)
{
	$('#please_wait').css('display', 'inline');
	var message = $(location).attr('pathname') + "webapp/interfaces/getmessage.php?arguments=messageId=" + mailId;
	$('#mail').html('<iframe frameborder="no" id="preview" class="mailframe" src="' + message + '"></iframe>');
	$("#preview").load(function() {
			bheight = $("#preview").height() - 69;
			$('#preview').contents().find('#mailbody').css({
				'position': 'absolute',
				top: '68px',
				bottom: '0px',
				left: '0px',
				right: '0px',
				height: bheight + 'px',
				display: ''
			});


			$('#please_wait').css('display', 'none');
		});
	objCalculateActions();
}

function markAsRead(mailId)
{
	$('#' + mailId).addClass('read');
	$('#' + mailId).removeClass('unread');
}
	
function markAsUnRead(mailId)
{
	$('#' + mailId).addClass('unread');
	$('#' + mailId).removeClass('read');
}

// Open a message in a new tab
function selectTab(src)
{
	if ($(src).attr("class").indexOf("current") == -1)
	{
		var tabId = $(src).attr("id");
		tabId = tabId.substring(4, tabId.length);
		tabName = 'div_' + tabId;
		if (tabId == 'estancia')	{tabName = 'objectlist';}
		$("#tabs").children().removeClass('current');
		$(src).addClass('current');
		$('#mainpanel').children().css({display: 'none'});
		$("#tabs").css({display: ''});
		$("#" + tabName).css({display: ''});
	}
}

// Close a tab
function closeTab(src, event)
{
	var mailId = $(src).attr("id");
	// If the tab to close is selected (current)
	if ($("#tab_" + mailId).attr("class").indexOf("current") >= 0)
	{
		$("#tab_" + mailId).prev().addClass('current');
		if ($("#tab_" + mailId).prev().attr("id") == 'tab_estancia')	{	$('#objectlist').css({display: 'inline'});	}
		else
		{
			var tabId = $("#tab_" + mailId).prev().attr("id");
			tabId = tabId.substring(4, tabId.length);
			tabName = 'div_' + tabId;
			$("#" + tabName).css({display: ''});
		}
	}
	$("#div_" + mailId).remove();
	$("#tab_" + mailId).remove();
	return false;
}

// Open a message in a new tab
$('.tabtitle').live('click', function(event) {selectTab(this);});
$('.tabclose').live('click', function(event) {return closeTab(this, event);});
function viewmessage(mailId)
{
	if ($('#tab_' + mailId).length)	{return selectTab($('#tab_' + mailId));}
	// Display the  'Please wait' message'
	$('#please_wait').css('display', 'inline');
	// Change the class of the tabs
	$("#tabs").children().removeClass('current');
	// Add a new tab
	var newtab = '<span id="tab_' + mailId + '" class="current tabtitle">Message<a class="tabclose" id="' + mailId + '">x</a></span>';
	$('#tabs').append(newtab);
	var newdiv = '<div id="div_' + mailId + '"></div>';
	$('#mainpanel').prepend(newdiv);
	// Display the message
	var message = $(location).attr('pathname') + "webapp/interfaces/getmessage.php?arguments=messageId=" + mailId;
	$('#div_' + mailId).html('<iframe frameborder="no" id="frame_' + mailId + '" class="mailframe" src="' + message + '"></iframe>');
	$('#objectlist').css({display: 'none'});
	$('#frame_' + mailId).load(function() {
			bheight = $('#frame_' + mailId).height() - 97;
			$('#frame_' + mailId).contents().find('#mailbody').css({
				'position': 'absolute',
				top: '68px',
				bottom: '28px',
				left: '0px',
				right: '0px',
				height: bheight + 'px',
				display: ''
			});

			var tabName = $('#frame_' + mailId).contents().find('#subject').html() + '<a class="tabclose" id="' + mailId + '">x</a>';
			$('#tab_' + mailId).html(tabName); 
			$('#please_wait').css('display', 'none');
		});
}

var txtsearch = '';
$('#txtsearch').live('click', function() {	$('#txtsearch').select();	});

function search()
{
	if ($.trim($('#txtsearch').val()) == txtsearch)	{postServerEvent('objectlist', 'nest', 'search', null, null);}
	else if ($.trim($('#txtsearch').val()) != '')	{postServerEvent('objectlist', 'jump', 'searchresults', null, 'search=' + $('#txtsearch').attr("value"));}
}

/*	
 *	This method is called when a jump event is fired on the server, if the current page is the page 1
 *
 */
function jump1()
{
	// Grid Height :
	sheight = $('#sheet_' + nbcomponent).height();
	if (sheight > 0.8 * $(window).height())
	{
		gheight = 0.8 * $(window).height() + $('#nestedobjectgridcontent').height() - sheight;
		$('#nestedobjectgridcontent').css({
			height: gheight + 'px',
			'overflow-y' : 'scroll'
		});
	}
	else
	{
		$('#nestedobjectgridcontent').css({
			'overflow-y' : 'hidden'
		});
	}
	shtop = ($(window).height() - $('#sheet_' + nbcomponent).height()) / 2;
	$('#sheet_' + nbcomponent).css({
		top: shtop + 'px'
	});
	if (nbcomponent == 1)	{selectTab($('#tab_estancia'));}
}

/*	
 *	This method is called when a jump event is fired on the server, if the current page is more than 1
 *
 */
function jump2()
{
	// Grid Height :
	sheight = $('#sheet_' + nbcomponent).height();
	if (sheight > 0.8 * $(window).height())
	{
		gheight = 0.8 * $(window).height() + $('#nestedobjectgridcontent').height() - sheight;
		$('#nestedobjectgridcontent').css({
			height: gheight + 'px',
			'overflow-y' : 'scroll'
		});
	}
	else
	{
		gheight = 0.8 * $(window).height() + $('#nestedobjectgridcontent').height() - sheight;
		$('#nestedobjectgridcontent').css({
			height: gheight + 'px',
			'overflow-y' : 'scroll'
		});
	}
	shtop = ($(window).height() - $('#sheet_' + nbcomponent).height()) / 2;
	$('#sheet_' + nbcomponent).css({
		top: shtop + 'px'
	});
}

/*	
 *	This method is called when a nest event is fired on the server
 *
 */
function nest()
{
	$('#overlay').css('display', 'inline');
	$('#overlay').css({'zIndex': (2 * nbcomponent)});
	var newdiv = '<div id=\"sheet_' + nbcomponent + '\" class=\"sheet\" style=\"display: none;\" />';
	$('body').append(newdiv);
	$('#sheet_' + nbcomponent).html($('#ajax').html());
	$('#ajax').html('');
	$('#sheet_' + nbcomponent).css({
		'zIndex': (2 * nbcomponent + 1)
	});
	wheight = $(window).height();
	sheight = $('#sheet_' + nbcomponent).height();
	swidth = $('#sheet_' + nbcomponent).width();
	sleft = ($(window).width() - swidth)/2;
	if (sheight > 0.7 * $(window).height())	{sheight = 0.7 * $(window).height();}
	ptop = (wheight - sheight)/2 - 64;
	$('#sheet_' + nbcomponent).css({
		position: 'absolute',
		width: swidth + 'px',
		left: sleft + 'px',
		top: ptop + 'px',
		display: 'inline'
	});	
	// Be sure all modal windows are draggable
	$('#sheet_' + nbcomponent).draggable({ scroll: false, handle: '.drag' });
	// Content Height :
	sheight = $('#content').height();
	if (sheight > 0.8 * $(window).height())
	{
		sheight = 0.8 * $(window).height();
		$('#content').css({
			height: sheight + 'px',
			'overflow-y' : 'scroll'
		});
		$('.modal').css({
			height: sheight + 'px'
		});
	}
	else
	{
		$('#content').css({
			'overflow-y' : 'hidden'
		});
	}
	// Grid Height :
	sheight = $('#sheet_' + nbcomponent).height();
	if (sheight > 0.8 * $(window).height())
	{
		gheight = 0.8 * $(window).height() + $('#nestedobjectgridcontent').height() - sheight;
		$('#nestedobjectgridcontent').css({
			height: gheight + 'px',
			'overflow-y' : 'scroll'
		});
	}
	else
	{
		$('#nestedobjectgridcontent').css({
			'overflow-y' : 'hidden'
		});
	}
	shtop = ($(window).height() - $('#sheet_' + nbcomponent).height()) / 2;
	$('#sheet_' + nbcomponent).css({
		top: shtop + 'px'
	});
	errtop = ($(window).height() - $('#error').height())/2;
	errtop = 0;
	$('#error').css({
		position: 'absolute',
		top: errtop + 'px'
	});
}
	
function closeWindow(level)
{
	if (level == null)	{level = 1;}
	$('#overlay').css('display', 'none');
	$('#sheet_' + level).remove();
}