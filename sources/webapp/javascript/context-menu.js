/*
 *	Global variables
 *
 */
var objects = new Array();	// Selected objects
var modalObjList = new Array();	// Modal objects
var actions = new Array();	// Actions
var messages = new Array();	// Messages

/*	
 *	Is the object an image
 *
 *	@param	object	the object to test
 *
 */
function isObjectImage(obj)
{
	var flag = false;
	try	{flag = (obj instanceof HTMLImageElement);}
	catch (e)	{flag = (obj.tagName == 'IMG');}
	return flag;
}

/*	
 *	Is the object an image
 *
 *	@param	object	the object to test
 *
 */
function isObjectInput(obj)
{
	var flag = false;
	try	{flag = (obj instanceof HTMLInputElement);}
	catch (e)	{flag = (obj.tagName == 'INPUT');}
	return flag;
}

/*	
 *	Is the object an image
 *
 *	@param	object	the object to test
 *
 */
function isObjectSelect(obj)
{
	var flag = false;
	try	{flag = (obj instanceof HTMLSelectElement);}
	catch (e)	{flag = (obj.tagName == 'SELECT');}
	return flag;
}

// Jump to anchor
function anchor()	{}

/*	
 *	Display the contect menu
 *
 *	@param	String	source	the object on which this function is called
 *
 */
function showContextMenu(event)
{
	// Positionning
	var x = event.pageX + 2;
	var y = event.pageY + 2;
	var menuWidth = $('#contextmenu').width();
	var menuHeight = $('#contextmenu').height();
	if (y + menuHeight > $(window).height())	{y -= menuHeight;}
	if (x + menuWidth > $(window).width())	{x -= menuWidth;}

	// Display the context menu
	$('#contextmenu').css('top', y + 'px');
	$('#contextmenu').css('left', x + 'px');
	$('#contextmenu').css('position', 'absolute');
	$('#contextmenu').css('z-index', '9999');
	$('#contextmenu').css('display', '');

	// Handle a single click to the document to hide the menu
	$(document).one('click',null,function()	{$('#contextmenu').hide();});
}

/*	
 *	Calculate the possible actions on object(s)
 *
 */
function objCalculateActions()
{
	// if ( !$('#phpinfo').length ) {$('body').append('<div id="phpinfo" class="phpinfo drag"></div>');}
	// $('#phpinfo').html('objCalculateActions' + '<br>' + $('#phpinfo').html());
	// Create an empty menu and action
	var strMenu = '', strActions = '';
	var strActions = '';
	var isSeparatorVisible = 0;
	var maxWidth = $('.toolbar-actions').width() - 124;
	var innerWidth = 0;
	var bOverflow = false;
	$('.toolbar-actions').html('');

	// Fill the context menu
	for (var i = 0; i < actions.length; i++)
	{
		var actionObj = actions[i];
//		$('#phpinfo').html($('#phpinfo').html() + '<br>' + actionObj.name);
		var listIds = actionObj.listIds;
		var icon = $(location).attr('pathname') + 'webapp/themes/default/images/icons/' + actionObj.icon + '.png'
		var strObjectIds = (objects.toString() != '') ? "'objectId=" + objects.toString() + "'" : 'null';
		var target = "postServerEvent('objectlist', '" + actionObj.type + "', '" + actionObj.target + "', null, " + strObjectIds + ");";
		if (actionObj.type == 'open')	{target = actionObj.target + "('" + objects.toString() + "')";}
		var objectIds = objects.toString();
		var j = 0, isActionVisible = false, flag = true;
		switch (actionObj.isMulti)
		{
			case 0:	// No object selected
				if (objects.length == 0)	{isActionVisible = true;}
				break;
			case 1:	// No more than one object selected
				if (objects.length == 1 && listIds.indexOf(objectIds) >= 0)	{isActionVisible = true;}
				break;
			case 2:	// One or more object selected
				while (j < objects.length && flag == true)	{	if (listIds.indexOf(objects[j++]) == -1)	{flag = false;}	}
				if (flag && objects.length > 0)	{isActionVisible = true;}
				break;
			default: // Separator
				if (isSeparatorVisible == 0 && strMenu != '')	{isSeparatorVisible = 1;}
				break;
		}
		if (isSeparatorVisible == 1)	{strMenu += '<div class="separator"><span><!-- --></span></div>';isSeparatorVisible = -1;}
		else if (isActionVisible)
		{
			strMenu += '<a href="javascript:' + target + '" class="contextitem">';
			strMenu += '<div class="container">';
			strMenu += '<div><img src="' + icon + '"></div>';
			strMenu += '<span>' + actionObj.name + '</span>';
			strMenu += '</div>';
			strMenu += '</a>';
			isSeparatorVisible = 0;
			strActions = '<a id="action_' + i + '" href="javascript:' + target + '">';
			strActions += '	<img src="' + icon + '">';
			strActions += '	<span>' + actionObj.name + '</span>';
			strActions += '</a>';
			if (innerWidth > maxWidth)	{strActions = '	<span>...</span>';}
			if (!bOverflow)	{$('.toolbar-actions').html($('.toolbar-actions').html() + strActions);}
			if (strActions == '	<span>...</span>')	{bOverflow = true;}
			innerWidth += $('#action_' + i).width() + 28;
		}
	}
	if (strMenu == '')
	{
		var icon = $(location).attr('pathname') + 'webapp/themes/default/images/icons/noaction_16.png'
		strMenu += '<a href="#" class="contextitem">';
		strMenu += '	<div class="container">';
		strMenu += '		<div><img src="' + icon + '"></div>';
		strMenu += '		<span>' + messages['noaction'] + '</span>';
		strMenu += '	</div>';
		strMenu += '</a>';
		strActions += '<a href="#">';
		strActions += '	<img src="' + icon + '">';
		strActions += '	<span>' + messages['noaction'] + '</span>';
		strActions += '</a>';
		$('.toolbar-actions').html(strActions);
	}
	else if (isSeparatorVisible == -1 && strMenu != '')	{strMenu = strMenu.substring(0, strMenu.length - 32);}
	$('#contextmenu').html(strMenu);

}

/*	
 *	Highlight an object on click event
 *
 *	@param	String	source	the object on which this function is called
 *
 */
function objMouseClick(src, event)
{
	// if ( !$('#phpinfo').length ) {$('body').append('<div id="phpinfo" class="phpinfo drag"></div>');}
	// $('#phpinfo').html('objMouseClick' + '<br>' + $('#phpinfo').html());
	var lastobjectid = (objects.length > 0) ? objects[objects.length - 1] : '';
	var objectid = $(src).attr("id");
	var bClick = ((event.target.href == undefined && !isObjectImage(event.target)) ? false : true);
	// // Third case : the context menu has been called
	// if (src == null)
	// {
		// // Remove all objects
		// $('.selected').each(function()	{objRemoveObject(this);});
	// }
	// First case : the ctrl key is pressed
	if (event.ctrlKey)
	{
		// Add/remove the current object
		objManageCurrentObject(src, event);
		// No click possible
		bClick = false;
		// Calculate the actions
		objCalculateActions();
	}
	// Second case : the SHIFT key is pressed AND it is not the first object selected
	else if (event.shiftKey && lastobjectid != '')
	{
		var flag = false;
		$('.perobj').each(function()	{
			// Reset the flag if the object Id is equal to the last object Id or the current object Id
			if ($(this).attr("id") == objectid || $(this).attr("id") == lastobjectid)	{flag = !flag;}
			// If flag is true, highlight the object
			if (flag || $(this).attr("id") == lastobjectid)	{objAddObject(this, false);}
			// Highlight the current object (selected/hover)
			else if ($(this).attr("id") == objectid)	{objAddObject(this, true);}
			// Otherwise un-highlight it
			else	{objRemoveObject(this);}
		});
		// No click possible
		bClick = false;
		// Calculate the actions
		objCalculateActions();
	}
	// Fourth case : no key is pressed OR it is the first object selected (if SHIFT is pressed)
	else
	{
		// Remove all other objects
		$('.selected').each(function()	{
			if ($(this).attr("id") != objectid)	{objRemoveObject(this);}
		});
		// And add/remove the current one
		objManageCurrentObject(src, event);
		// Calculate the actions
		if (!bClick)	objCalculateActions();
	}
	// Show objects
//	var strObjectList = objects.toString();
//	var reg = new RegExp("(,)", "g");
//	strObjectList = strObjectList.replace(reg, "<br>")
//	$('#phpinfo').html($('#phpinfo').html() + '<br><br><span style="font-weight: bold; text-decoration: underline;">IDs :</span><br>' + strObjectList);
	return bClick;
}

/*	
 *	Highlight an object on click event
 *
 *	@param	String	source	the object on which this function is called
 *
 */
function objModalMouseClick(src, event)
{
	var objectid = $(src).attr("id");
	// Remove all other objects
	modalObjList.length = 0;
	$('.selected').each(function()	{
		if ($(this).attr("id") != objectid && $(this).attr("class").indexOf("modalperobj") >= 0)
		{
			$(this).removeClass('selected');
		}
	});
	// And add the current one
	// Add object
	if ($(src).attr("class").indexOf("modalperobj") >= 0)
	{
		var objectid = $(src).attr("id");
		$(src).addClass('selected');
		if ($.inArray(objectid, modalObjList) == -1)	{modalObjList.push(objectid);}
	}
	if ( !$('#modalObjList').length ) {$('.modal-header').append('<input type="hidden" id="modalObjList" name="modalObjList" value="' + modalObjList.toString() + '" />');}
	else	{$('#modalObjList').attr("value", modalObjList.toString())}
	// Return
	return false;
}

/*	
 *	Context menu
 *
 *	@param	String	source	the object on which this function is called
 *
 */
function objMouseRightClick(src, event)
{
	if ($(src).attr("class").indexOf("perobj") > 0 && $(src).attr("class").indexOf("selected") == -1)	{if (objMouseClick(src, event))	objCalculateActions();}
}

/*	
 *	Highlight the current object on click event
 *
 *	@param	String	source	the object on which this function is called
 *
 */
function objManageCurrentObject(src, event)
{
	// Remove object
	if (event.target.href == undefined && !isObjectImage(event.target) && $(src).attr("class").indexOf("selected") >= 0)	{objRemoveObject(src);}
	// Add object
	else	{objAddObject(src, true);}
}

/*	
 *	Add an object
 *
 *	@param	String	source	the object on which this function is called
 *
 */
function objAddObject(src, flag)
{
	var objectid = $(src).attr("id");
	$(src).addClass('selected');
	if ($.inArray(objectid, objects) == -1)	{objects.push(objectid);}
}

/*	
 *	Remove all objects
 *
 */
function objRemoveAllObjects()
{
	// if ( !$('#phpinfo').length ) {$('body').append('<div id="phpinfo" class="phpinfo drag"></div>');}
	// $('#phpinfo').html('objRemoveAllObjects' + '<br>' + $('#phpinfo').html());
	$('.selected').each(function()	{
		$(this).removeClass('selected');
	});
	objects.length = 0;
	objCalculateActions();
}

/*	
 *	Remove an object
 *
 *	@param	String	source	the object on which this function is called
 *
 */
function objRemoveObject(src)
{
	var objectid = $(src).attr("id");
	$(src).removeClass('selected');
	if ($.inArray(objectid, objects) >= 0)	{objects.splice($.inArray(objectid, objects), 1);}
}
