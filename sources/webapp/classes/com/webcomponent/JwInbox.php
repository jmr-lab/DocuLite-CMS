<?php
/**
 * The Inbox webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwInbox extends JwMailbox
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('flag', 'icon', 'event', 'sent_by', 'date_sent');

	/**
	 * Returns the title name (My Documents).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleImage()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return 'inbox.png';
	}

	/**
	 * Returns the title name (My Documents).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleName()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return $this->getString('INBOX');
	}

	/**
	 * Returns the title path.
	 *
	 * @access	public
	 * @return	String	the current path
	 */
	public function getTitlePath()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Init the path to display the refresh link
		$link = "javascript:postServerEvent('objectlist', 'jump', '".$this->component."', null, null);";
		$strPath = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/refresh_16.png"><a href="'.$link.'">'.$this->getString('REFRESH').'</a>';
		// Add the 'New mail' link
		$link = "javascript:postServerEvent('objectlist', 'open', 'writemessage', null, null);";
		$strPath .= '&nbsp;<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/create_16.png"><a href="'.$link.'">'.$this->getString('WRITE').'</a>';
		// Return the path
		return $strPath;
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

		$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, event, item_name, date_sent, dequeued_by,
					'jm_mail' AS r_object_type, '' AS description, sent_by, '' AS message, priority
				FROM jmi_queue_item_s
				WHERE (	name = '".$user->getValue('user_name')."'
						OR name IN
							(	SELECT group_name
								FROM jm_group_s
								WHERE r_object_id IN
								(SELECT i_group_id FROM v_users_groups WHERE r_object_id = '".$user->getValue('r_object_id')."')
							)
						) AND delete_flag = 0";

		$queryObj = new JcQuery($sql);
		$order = array("priority" => "DESC");
		$queryObj->setOrderByClauses($order);
		$this->setSQL($queryObj);

		parent::init();
	}
}
?>