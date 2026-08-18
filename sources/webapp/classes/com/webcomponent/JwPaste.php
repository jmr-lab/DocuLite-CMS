<?php
/**
 * JwPaste webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwPaste extends JwModalList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('checkout', 'icon', 'object_name', 'description', 'paste');

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

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT jm_sysobject_s.r_object_id, r_object_type, object_name, owner_name, r_creation_date, r_modify_date,
						r_accessor_permit, r_content_size, r_lock_owner, i_contents_id, a_content_type,
						CASE WHEN jm_format_s.dos_extension IS NULL THEN a_content_type ELSE jm_format_s.dos_extension END AS dos_extension,
						CASE WHEN jm_format_s.description IS NULL THEN CONCAT(UCASE(a_content_type), ' File') ELSE jm_format_s.description END AS description
				FROM	(jm_sysobject_s, jm_sysobject_r r1,
						(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
						FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit)
				LEFT JOIN jm_format_s ON a_content_type = jm_format_s.name
				WHERE	jm_sysobject_s.i_is_deleted = false
						AND jm_sysobject_s.r_object_id = r1.r_object_id
						AND r1.i_position = '-2'
						AND r1.r_version_label <> 'OLD'
						AND jm_sysobject_s.acl_id = table_permit.acl_id
						AND jm_sysobject_s.r_object_id IN ('".$strClipboardIds."')";

		$queryObj = new JcQuery($sql);
		$order = array("CASE WHEN jm_sysobject_s.r_object_type IN ('jm_cabinet', 'jm_folder') THEN NULL ELSE jm_sysobject_s.r_object_type END" => "ASC");
		$queryObj->setOrderByClauses($order);

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
			// Use of JfCheckinOperation
			$moveOperation = new JfMoveOperation($bMove);
			foreach ($objectIds as $key => $strObjectId)
			{
				// Get the old folder Id :
				$clipboardObject = $clipboard->getObject($strObjectId);
				$moveOperation->setOldFolderId($clipboardObject->getContainerId());
				$moveOperation->setNewFolderId($containerId);
				$session = $this->session;
				$moveObject = $session->getObject(new JfId($strObjectId));
				$moveOperation->add($moveObject);
				// select * from jm_sysobject_r where r_object_id = '09001e240ffe1c91'
				// And remove the object from the clipboard
				$clipboardObject = new JcClipBoardObject();
				$clipboardObject->setObjectId($strObjectId);
				$clipboard->removeObject($clipboardObject);
			}
			// Run the move operation
			$moveOperation->execute();
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