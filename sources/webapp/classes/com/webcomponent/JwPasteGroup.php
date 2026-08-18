<?php
/**
 * JwPasteGroup webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwPasteGroup extends JwModalList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'object_name', 'description', 'paste');

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
		// Get the objects from the clipboard
		$clipboard = new JcClipBoard();
		$clipboardIds = $clipboard->getObjectIds();
		$strClipboardIds = implode("', '", $clipboardIds);

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT s.r_object_id, 'jm_group' AS r_object_type, 'Group' AS description, s.group_name AS object_name, s.r_modify_date,
					COUNT(r.r_object_id) AS _group_count_
				FROM jm_group_s s, jm_group_r r
				WHERE s.r_object_id IN ('".$strClipboardIds."')
					AND r.groups_ids = s.r_object_id
				GROUP BY s.r_object_id
				UNION SELECT DISTINCT s.r_object_id, 'jm_user' AS r_object_type, 'User' AS description, s.user_name AS object_name, s.r_modify_date,
					COUNT(r.r_object_id) AS _group_count_
				FROM jm_user_s s, jm_group_r r
				WHERE s.r_object_id IN ('".$strClipboardIds."')
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
		$strObjectsToRemove = ''; $strObjectsToCopy = ''; $strObjectsToLink = ''; $strObjectsToMove = '';
		foreach ($action as $key => $value)
		{
			switch ($value)
			{
				case 1:		// Remove object from clipboard
					$strObjectsToRemove .= "', '".$key;
					break;
				case 2:		// Copy
					$strObjectsToCopy .= "', '".$key;
					break;
				case 3:		// Link
					$strObjectsToLink .= "', '".$key;
					break;
				case 4:		// Move
					$strObjectsToMove .= "', '".$key;
					break;
				default:
					break;
			}
		}
		if (strlen($strObjectsToRemove) > 0)	{$this->removeObjectFromClipBoard(substr($strObjectsToRemove, 4));}
		if (strlen($strObjectsToCopy) > 0)	{$this->copyObjects(substr($strObjectsToCopy, 4), $containerId);}
		if (strlen($strObjectsToLink) > 0)	{$this->moveObjects(false, substr($strObjectsToLink, 4), $containerId);}
		if (strlen($strObjectsToMove) > 0)	{$this->moveObjects(true, substr($strObjectsToMove, 4), $containerId);}
		return '';
	}

	/**
	 * Remove an object.
	 *
	 * @access	private
	 */
	private function removeObjectFromClipBoard($strObjectIds)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$objectIds = explode("', '", $strObjectIds);
		// Get the clipboard
		$clipboard = new JcClipBoard();
		foreach ($objectIds as $key => $strObjectId)
		{
			$clipboardObject = new JcClipBoardObject();
			$clipboardObject->setObjectId($strObjectId);
			$clipboard->removeObject($clipboardObject);
		}
		// Save the clipboard
		$clipboard->save();
	}

	/**
	 * Copy an object to the current folder.
	 *
	 * @access	private
	 */
	private function copyObjects($strObjectIds, $containerId)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// JcLogger::info('strObjectIds : '.$strObjectIds);
			$objectIds = explode("', '", $strObjectIds);
			// Get the clipboard
			$clipboard = new JcClipBoard();
			// Use of JfCopyOperation
			$copyOperation = new JfCopyOperation();
			foreach ($objectIds as $key => $strObjectId)
			{
				$copyOperation->setFolderId($containerId);
				$session = $this->session;
				$copyObject = $session->getObject(new JfId($strObjectId));
				$copyOperation->add($copyObject);
				// And remove the object from the clipboard
				$clipboardObject = new JcClipBoardObject();
				$clipboardObject->setObjectId($strObjectId);
				$clipboard->removeObject($clipboardObject);
			}
			// Run the move operation
			$copyOperation->execute();
			// Save the clipboard
			$clipboard->save();
		}
		catch (JfException $exception)
		{
			JcLogger::error('exception : '.$exception->getMessage());
			throw new JfException('COPY_OPERATION_NOT_ALLOWED');
		}
	}

	/**
	 * Link an object to the current folder.
	 *
	 * @access	private
	 */
	private function moveObjects($bMove, $strObjectIds, $containerId)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// JcLogger::info('strObjectIds : '.$strObjectIds);
			$objectIds = explode("', '", $strObjectIds);
			// Get the clipboard
			$clipboard = new JcClipBoard();
			// Get the current session
			$session = $this->session;
			// Get the group object corresponding to the current containerId
			$groupObj = $session->getGroup(new JfId($containerId));
			// Initialize the source group object list
			$sourceList = array();
			// For each user or group in the clipboard
			foreach ($objectIds as $key => $strObjectId)
			{
				// Get the old group Id :
				$clipboardObject = $clipboard->getObject($strObjectId);
				JcLogger::info('strObjectId : '.$strObjectId);
				JcLogger::info('clipboardObject->getContainerId() : '.$clipboardObject->getContainerId());
				// Add the user or group to the new group
				if (substr($strObjectId, 0, 2) == '11')	{$groupObj->addUser($strObjectId);}
				else if (substr($strObjectId, 0, 2) == '12')	{$groupObj->addGroup($strObjectId);}
				// And remove it from the old group if bMove is true
				if ($bMove)
				{
					$sourceObj = $session->getGroup(new JfId($clipboardObject->getContainerId()));
					JcLogger::info('Type : '.substr($strObjectId, 0, 2));
					if (substr($strObjectId, 0, 2) == '11')	{$sourceObj->removeUser($strObjectId);}
					else if (substr($strObjectId, 0, 2) == '12')	{$sourceObj->removeGroup($strObjectId);}
					// Add the group to the list
					$sourceList[] = $sourceObj;
				}
				// And remove the object from the clipboard
				$clipboardObject = new JcClipBoardObject();
				$clipboardObject->setObjectId($strObjectId);
				$clipboard->removeObject($clipboardObject);
			}
			// Save the target group (Add/Link)
			$groupObj->save();
			// Save the source group (Remove)
			foreach ($sourceList as $key => $sourceObj)
			{
				JcLogger::info('sourceId : '.$sourceObj->getValue('r_object_id'));
				// Save the group
				$sourceObj->save();
			}
			// Save the clipboard
			$clipboard->save();
		}
		catch (JfException $exception)
		{
			JcLogger::error('exception : '.$exception->getMessage());
			$strErrorPrefix = 'MOVE';
			if (!$bMove)	{$strErrorPrefix = 'LINK';}
			throw new JfException($strErrorPrefix.'_OPERATION_NOT_ALLOWED');
		}
	}
}
?>