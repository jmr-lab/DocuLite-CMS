<?php
/**
 * JwDeleteGroup webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDeleteGroup extends JwModalList
{
	/**
	* REMOVE_OBJECT
	*
	* Remove the current object specified by its ID.
	*
	* @access	private
	* @var		int
	*/
	private static $REMOVE_OBJECT = 0;

	/**
	* UNLINK
	*
	* Remove current object from group.
	*
	* @access	private
	* @var		int
	*/
	private static $UNLINK = 2;

	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'object_name', 'description', 'deletegroup');

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
		$action = $request->getParameter('action');
		// Get current folder ID
		$pathSO = $httpSession->getAttribute('path');
		$containerId = (sizeof($pathSO) > 0 ? $pathSO[sizeof($pathSO) - 1] :'0000000000000000');
		// Show the parameters
//		JcLogger::info('containerId : '.$containerId);
//		if (sizeof($action) == 0) {JcLogger::info('action is empty');}
		if (sizeof($action) == 0) return '';
//		foreach ($action as $key => $value)	{JcLogger::info('action['.$key.'] : '.$value);}
		$strObjectsToRemove = ''; $strObjectsToUnlink = '';
		foreach ($action as $key => $value)
		{
			switch ($value)
			{
				case 0:		// REMOVE_OBJECT
					$strObjectsToRemove .= "', '".$key;
					break;
				case 2:		// UNLINK
					$strObjectsToUnlink .= "', '".$key;
					break;
				default:
					break;
			}
		}
		// Check that the user has enough permit (DELETE - 7) on the objects
		$strObjectIds = $strObjectsToRemove.$strObjectsToUnlink;
		if (strlen($strObjectIds) > 0)	{$strObjectIds = substr($strObjectIds, 4);}
		$this->checkAccess(explode("', '", $strObjectIds), 7);
		// Delete/Unlink objects
		if (strlen($strObjectsToRemove) > 0)	{$this->removeObject(substr($strObjectsToRemove, 4), $containerId);}
		if (strlen($strObjectsToUnlink) > 0)	{$this->removeObjectUnlink(substr($strObjectsToUnlink, 4), $containerId);}
		return '';
	}

	/**
	 * Remove an object.
	 *
	 * @access	private
	 */
	private function removeObject($strObjectIds, $containerId)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Prepare a fetch event for each object
		$session = $this->session;
		$arrObjectIds = explode("', '", $strObjectIds);
		// Get the current group
		$group = $session->getGroup(new JfId($containerId));
		foreach ($arrObjectIds as $key => $strObjectId)
		{
			// Remove group/user from all containing groups first
			if (substr($strObjectId, 0, 2) == '11')	$group->removeUser($strObjectId);
			else if (substr($strObjectId, 0, 2) == '12')	$group->removeGroup($strObjectId);
			// Delete each object
			$perObj = $session->getObject(new JfId($strObjectId));
			$perObj->destroy();
		}
		$group->save();
	}

	/**
	 * Remove an object from current folder.
	 *
	 * @access	private
	 */
	private function removeObjectUnlink($strObjectIds, $containerId)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Prepare a fetch event for each object
		$session = $this->session;
		$arrObjectIds = explode("', '", $strObjectIds);
		// Get the current group
		$group = $session->getGroup(new JfId($containerId));
		foreach ($arrObjectIds as $key => $strObjectId)
		{
			// Remove group/user from all containing groups first
			if (substr($strObjectId, 0, 2) == '11')	$group->removeUser($strObjectId);
			else if (substr($strObjectId, 0, 2) == '12')	$group->removeGroup($strObjectId);
		}
		$group->save();
	}
}
?>