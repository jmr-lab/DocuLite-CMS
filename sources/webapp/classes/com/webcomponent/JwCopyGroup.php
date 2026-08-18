<?php
/**
 * JwCopyGroup webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwCopyGroup extends JwModalList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'object_name', 'description', 'r_modify_date');

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
			$link['open'] = '<input type="hidden" name="objectId[]" value="'.$object->getValue('r_object_id').'"><span style="font-size: 12px; font-weight: bold; color: #5F5F5F;">';
			$link['close'] = '</span>';
			// Set the link
			$object->setValue('_link_name_', $link);
			$this->objects[$index] = $object;
		}
	}

	/**
	 * Get a short version of a string :
	 *
	 * 'Microsoft Office Word Document 8.0-2003 (Windows)' will become 'Microsoft Office Wor...'
	 *
	 * @access	protected
	 * @param	String	The message to truncate
	 * @return	String	The message
	 */
	protected function getShortString($message)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		if (strlen($message) > 20)	{$message = substr($message, 0, 20).'...';}
		return $message;
	}

	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function init()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$this->objectclass = 'modalperobj';
		// Get the user login info
		$user = $this->user;
		// Get the object info
		$request = new JcHttpServletRequest();
		$objectList = $request->getParameter('objectId');
		$objectList = str_replace(",", "','", $objectList);

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT s.r_object_id, 'jm_group' AS r_object_type, 'Group' AS description, s.group_name AS object_name, s.r_modify_date,
					COUNT(r.r_object_id) AS _group_count_
				FROM jm_group_s s, jm_group_r r
				WHERE s.r_object_id IN ('".$objectList."')
					AND r.groups_ids = s.r_object_id
				GROUP BY s.r_object_id
				UNION SELECT DISTINCT s.r_object_id, 'jm_user' AS r_object_type, 'User' AS description, s.user_name AS object_name, s.r_modify_date,
					COUNT(r.r_object_id) AS _group_count_
				FROM jm_user_s s, jm_group_r r
				WHERE s.r_object_id IN ('".$objectList."')
					AND r.users_ids = s.r_object_id
				GROUP BY s.r_object_id
				ORDER BY r_object_type, object_name";

		$queryObj = new JcQuery($sql);
		$this->setSQL($queryObj);

		// Force the 'details' view to be used
		$this->view = 'details';
		$this->objectgridcontent = 'nestedobjectgridcontent';
		parent::init();
	}

	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function onOk()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the request and session objects
		$request = new JcHttpServletRequest();
		$httpSession = $request->getSession();
		// Get the data sent to the server
		$objectIds = $request->getParameter('objectId');
		// JcLogger::info("objectIds : '".$objectIds."'");
		// $strObjectIds = implode($objectIds, "', '");
//		JcLogger::info("strObjectIds : '".$strObjectIds."'");
		// Get current folder ID
		$pathSO = $httpSession->getAttribute('path');
		$containerId = (sizeof($pathSO) > 0 ? $pathSO[sizeof($pathSO) - 1] :'0000000000000000');
		// JcLogger::info('containerId : '.$containerId);
		// Get the clipboard
		$clipboard = new JcClipBoard();
		foreach ($objectIds as $key => $strObjectId)
		{
			// JcLogger::info("strObjectId : '".$strObjectId."'");
			$clipboardObject = new JcClipBoardObject();
			$clipboardObject->setContainerId($containerId);
			$clipboardObject->setObjectId($strObjectId);
			$clipboardObject->setOperation('copygroup');
			$clipboard->addObject($clipboardObject);
		}
		// Save the clipboard
		$clipboard->save();
		// Get the user login info
		$user = $this->user;
		// Create a copy event for each object
		$session = $this->session;
		$auditTrailMgr = $session->getAuditTrailManager();
		$stringArgs = array(	'userName' => $user->getValue('user_name'),
								'userIP' => getenv("REMOTE_ADDR")	);
		foreach ($objectIds as $key => $strObjectId)
		{
			$auditTrailMgr->createAudit($strObjectId, 'copy', $stringArgs, null);
		}
		return '';
	}
}
?>