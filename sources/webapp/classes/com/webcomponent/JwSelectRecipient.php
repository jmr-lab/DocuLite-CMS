<?php
/**
 * JwSelectRecipient webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwSelectRecipient extends JwModalList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'object_name', 'description', 'r_modify_date');

	/**
	 * Empty message
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $empty_message = 'NO_USER';

	/**
	 * List of recipients ('TO', 'CC', 'BCC')
	 *
	 * @access	private
	 * @var		array
	 */
	private $recipients = array();

	/**
	 * Get a link to the target
	 *
	 * @access	public
	 * @return	String	the link
	 */
	protected function setLinks()
	{
		if (!isset($this->objects) || sizeof($this->objects) == 0)	{return;}
		foreach ($this->objects as $index => $object)
		{
			// Init the link
			$link = array('open' => '<span class="recipient" style="font-size: 12px; font-weight: bold; color: #5F5F5F; text-decoration: none;">', 'close' => '</span>');
			// Set the link
			$object->setValue('_link_name_', $link);
			$this->objects[$index] = $object;
		}
	}

	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function init()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the user login info
		$user = $this->user;

		$sql = "SELECT r_object_id, group_display_name AS object_name, '3' AS r_accessor_permit, owner_name,
				'jm_group' AS r_object_type, 'Group' AS description, group_address AS address, r_modify_date
				FROM jm_group_s
				UNION SELECT r_object_id, user_name AS object_name, '3' AS r_accessor_permit, user_name AS owner_name,
				'jm_user' AS r_object_type, 'User' AS description, user_address AS address, r_modify_date
				FROM jm_user_s
				WHERE (r_object_id = '".$user->getValue('r_object_id')."' OR r_object_id IN
					(SELECT child_id FROM jm_relation_s WHERE parent_id = '".$user->getValue('r_object_id')."'
					UNION SELECT parent_id FROM jm_relation_s WHERE child_id = '".$user->getValue('r_object_id')."'))
				ORDER BY r_object_type, object_name";

		$queryObj = new JcQuery($sql);
		$this->setSQL($queryObj);
		// Force the 'details' view to be used
		$this->view = 'details';
		$this->objectgridcontent = 'nestedobjectgridcontent';
		parent::init();
		// Print Javascript code
		$this->printJavaScript();
		// Get the selected users
		$request = new JcHttpServletRequest();
		// Get last component
		$httpsession = $request->getSession();
		$arrComponent = $httpsession->getAttribute('component');
		$currentIndex = sizeof($arrComponent)-1;
		$lastIndex = sizeof($arrComponent)-2;
		JcLogger::debug('Component['.$currentIndex.'] : '.$arrComponent[$currentIndex]);
		JcLogger::debug('Component['.$lastIndex.'] : '.$arrComponent[$lastIndex]);
		$event = $request->getParameter('event');
		JcLogger::debug('Event : '.$event);
		$component = $this->component;
		if ($event == 'nest')	{$component = 'writemessage';}
		$this->recipients['TO'] = $request->getParameter($component.'_TO');
		$this->recipients['CC'] = $request->getParameter($component.'_CC');
		$this->recipients['BCC'] = $request->getParameter($component.'_BCC');
	}

	/**
	 * Print Javascript code
	 *
	 * @access	private
	 */
	private function printJavaScript()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		echo "<script>";
		echo "function doAddUser(src)	{";
		echo "	var strUserId = $('#modalObjList').attr('value');";
		echo "	var strUserName = $('#' + strUserId).children().children('li[name=recipient]').children().html();";
		echo "	if (strUserName == null || strUserName == '')	return;";
		echo "	var strUserIcon = $('#' + strUserId).children().children('li[name=icon]').html();";
		echo "	var strIcon = 'jm_group_16.png';";
		echo "	if (strUserIcon.indexOf('jm_user_16.png') >= 0)	{strIcon = 'jm_user_16.png';}";
		echo "	var strUserList = $(src).parent().parent().children('.maillinediv').html();";
		echo "	var strNewUser = '<jm:actionbutton target=\"TARGET\" cssclass=\"user\" id=\"OBJID\" nlsid=\"NLSID\" src=\"ICON\" delete=\"true\"/>';";
		echo "	var regex = /id=\"(\w+)\"/gi;";
		echo "	match = regex.exec(strNewUser);";
		echo "	var tagId = match[0].substring(4, match[0].length - 1);";
		echo "	var newTagId = Math.random().toString(36).substring(7);";
		echo "	strNewUser = strNewUser.replace(new RegExp(tagId, 'g'), newTagId);";
		echo "	strNewUser = strNewUser.replace('Nlsid', strUserName);";
		echo "	strNewUser = strNewUser.replace('NLSID', strUserName);";
		echo "	strNewUser = strNewUser.replace('OBJID', strUserId);";
		echo "	strNewUser = strNewUser.replace('ICON', strIcon);";
		echo "	strNewUser = strNewUser.replace('TARGET', $(src).parent().parent().attr('name'));";
		echo "	if (strUserList.indexOf(strUserId) >= 0)	{return;}";
		echo "	strUserList += strNewUser;";
		echo "	$(src).parent().parent().children('.maillinediv').html(strUserList);";
		echo "}";
		echo "</script>";
	}

	/**
	 * Method called when the current component is closed.
	 *
	 * @access	public
	 */
	public function onClose()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Add date to the string
		$milli = floor(1000 * microtime());
		while (strlen($milli) < 3)	{$milli = '0'.$milli;}
		echo "	<script>
				var returnflag = '".date("Y-m-d H:i:s").",".$milli."';
				_callback(returnflag);
				var _callback = function(value)	{};
				</script>";
		return false;
	}

	/**
	 * Method called when the current component is closed.
	 *
	 * @access	public
	 */
	public function onOk()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the selected users
		$request = new JcHttpServletRequest();
		$component = 'selectrecipient';
		$this->recipients['TO'] = $request->getParameter($component.'_TO');
		$this->recipients['CC'] = $request->getParameter($component.'_CC');
		$this->recipients['BCC'] = $request->getParameter($component.'_BCC');
		// Fill the recipient fields
		echo "	<script>
					function postProcessingEvent()	{
						var source = new RegExp('name=\"[a-zA-Z]*_', 'gi');
						var strTO = '".$this->getRecipients('TO')."';
						var strCC = '".$this->getRecipients('CC')."';
						var strBCC = '".$this->getRecipients('BCC')."';
						strTO = strTO.replace(source, 'name=\"writemessage_');
						strCC = strCC.replace(source, 'name=\"writemessage_');
						strBCC = strBCC.replace(source, 'name=\"writemessage_');
						var tabId = $('.current').attr('id');
						var divId = tabId.replace('tab_', 'div_');
						$('#' + divId).find('.mailline[name=TO]').children('.maillinediv').html(strTO);
						$('#' + divId).find('.mailline[name=CC]').children('.maillinediv').html(strCC);
						$('#' + divId).find('.mailline[name=BCC]').children('.maillinediv').html(strBCC);
					}
				</script>";
		return false;
	}

	/**
	 * Print a list of recipients
	 *
	 * @access	public
	 */
	public function getRecipients($type)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$strRecipients = '';
		if (!is_array($this->recipients[$type]))	return '';
		foreach ($this->recipients[$type] as $key => $value)
		{
			JcLogger::debug('Recipient['.$key.'] : '.$value);
			$icon = 'jm_group_16.png';
			if (substr($key, 0, 2) == '11')	$icon = 'jm_user_16.png';
			$strRecipients .= '<jm:actionbutton target="'.$type.'" cssclass="user" id="'.$key.'" nlsid="'.$value.'" src="'.$icon.'" delete="true"/>';
		}
		return $strRecipients;
	}
}
?>