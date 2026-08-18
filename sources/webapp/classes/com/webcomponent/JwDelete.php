<?php
/**
 * JwDelete webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDelete extends JwModalList
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
	* REMOVE_ALL_VERSIONS
	*
	* Remove all versions of the current object.
	*
	* @access	private
	* @var		int
	*/
	private static $REMOVE_ALL_VERSIONS = 1;

	/**
	* UNLINK_FROM_CURRENT_FOLDER
	*
	* Unlink current object from folder.
	*
	* @access	private
	* @var		int
	*/
	private static $UNLINK_FROM_CURRENT_FOLDER = 2;

	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('checkout', 'icon', 'object_name', 'description', 'delete');

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

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT s1.r_object_id, s1.r_object_type, s1.object_name, COUNT(r2.i_folder_id) AS _folder_count, _version_count,
						s1.owner_name, s1.r_creation_date, s1.r_modify_date, s1.r_immutable_flag,
						r_accessor_permit, s1.r_content_size, s1.r_lock_owner, s1.i_contents_id, s1.a_content_type,
						CASE WHEN jm_format_s.dos_extension IS NULL THEN s1.a_content_type ELSE jm_format_s.dos_extension END AS dos_extension,
						CASE WHEN jm_format_s.description IS NULL THEN CONCAT(UCASE(s1.a_content_type), ' File') ELSE jm_format_s.description END AS description
				FROM	(jm_sysobject_s s1, jm_sysobject_r r1, jm_sysobject_r r2,
						(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
						FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit)
				LEFT JOIN jm_format_s ON s1.a_content_type = jm_format_s.name
				LEFT JOIN
				(
					SELECT s1.r_object_id, COUNT(s2.r_object_id) AS _version_count
					FROM jm_sysobject_s s1, jm_sysobject_s s2
					WHERE	s2.i_chronicle_id = s1.i_chronicle_id
							AND s2.i_is_deleted = false
					GROUP BY s1.r_object_id
				) AS _version_
				ON _version_.r_object_id = s1.r_object_id
				WHERE	s1.i_is_deleted = false
						AND s1.r_object_id = r1.r_object_id
						AND s1.r_object_id IN ('".$objectList."')
						AND r1.i_position = '-2'
						AND s1.acl_id = table_permit.acl_id
						AND (r_accessor_permit > 1 OR s1.owner_name = '".$user->getValue('user_name')."')
						AND s1.r_object_id = r2.r_object_id
						AND r2.i_folder_id <> ''
						AND r2.i_folder_id IS NOT NULL
				GROUP BY s1.r_object_id";

		$queryObj = new JcQuery($sql);
		$order = array("CASE WHEN s1.r_object_type IN ('jm_cabinet', 'jm_folder') THEN NULL ELSE s1.r_object_type END" => "ASC");
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
		$strObjectsToRemove = ''; $strObjectsToRemoveAll = ''; $strObjectsToUnlink = ''; $strObjectsClipBoard = ''; $strObjectsFavorites = '';
		foreach ($action as $key => $value)
		{
			switch ($value)
			{
				case 0:		// REMOVE_OBJECT
					$strObjectsToRemove .= "', '".$key;
					break;
				case 1:		// REMOVE_ALL_VERSIONS
					$strObjectsToRemoveAll .= "', '".$key;
					break;
				case 2:		// UNLINK_FROM_CURRENT_FOLDER
					$strObjectsToUnlink .= "', '".$key;
					break;
				case 3:		// REMOVE_FROM_CLIPBOARD
					$strObjectsClipBoard .= "', '".$key;
					break;
				case 4:		// REMOVE_FROM_FAVORITES
					$strObjectsFavorites .= "', '".$key;
					break;
				default:
					break;
			}
		}
		// Check that the user has enough permit (DELETE - 7) on the objects
		$strObjectIds = $strObjectsToRemove.$strObjectsToRemoveAll.$strObjectsToUnlink;
		if (strlen($strObjectIds) > 0)	{$strObjectIds = substr($strObjectIds, 4);}
		$this->checkAccess(explode("', '", $strObjectIds), 7);
		// Delete/Unlink objects
		if (strlen($strObjectsToRemove) > 0)	{$this->removeObject(substr($strObjectsToRemove, 4));}
		if (strlen($strObjectsToRemoveAll) > 0)	{$this->removeObjectAllVersions(substr($strObjectsToRemoveAll, 4));}
		if (strlen($strObjectsToUnlink) > 0)	{$this->removeObjectUnlink(substr($strObjectsToUnlink, 4), $containerId);}
		if (strlen($strObjectsClipBoard) > 0)	{$this->removeObjectFromClipBoard(substr($strObjectsClipBoard, 4));}
		if (strlen($strObjectsFavorites) > 0)	{$this->removeObjectFromFavorites(substr($strObjectsFavorites, 4));}
		return '';
	}

	/**
	 * Remove an object.
	 *
	 * @access	private
	 */
	private function removeObject($strObjectIds)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// JcLogger::info("removeObject : '".$strObjectIds."'");
		// Get the user login info
		$user = $this->user;
		// Check that the user has enough permit (DELETE - 7) on the objects
		// JcLogger::info('strObjectIds (before) : '.$strObjectIds);
		// $security = new JcSecurity($this->session);
		// foreach (explode("', '", $strObjectIds) as $key => $strObjectId)
		// {
			// $typedObject = new JcTypedObject();
			// $typedObject->setObjectId($strObjectId);
			// $security->addObject($typedObject);
		// }
		// $security->setThreshold(7);
		// $security->execute();
		// $security->removeObjects();
		// $strObjectIds = implode("', '", $security->getObjectIds());
		// JcLogger::info('strObjectIds (after) : '.$strObjectIds);
		// First get the most recent version (antecedent) to mark it as 'CURRENT' if there is another version of this object
		$query = new JfQuery();
		$sql = "SELECT r_version_label FROM jm_sysobject_rp WHERE i_position = -2 AND r_object_id IN ('".$strObjectIds."')";
		$query->setSQL($sql);
		$results = $query->execute($this->session);
		$isCurrent = false;
		while ($results->next())	{JcLogger::info('r_version_label : '.$results->getValue('r_version_label')); $isCurrent = in_array($results->getValue('r_version_label'), array('CURRENT', 'NEW')) ? true : $isCurrent;}
		JcLogger::info('isCurrent : '.$isCurrent);
		if ($isCurrent)
		{
			JcLogger::info('True');
			// Mark all versions to 'OLD'
			$query = new JfQuery();
			$sql = "UPDATE jm_sysobject_rp OBJECTS SET r_version_label = 'OLD'
					WHERE i_position = -2 AND r_object_id  IN
							(	SELECT r_object_id FROM jm_sysobject_sp
								WHERE i_chronicle_id IN ('".$strObjectIds."')
							)
					";
			$query->setSQL($sql);
			$query->execute($this->session);
			// Then mark the most recent version (antecedent) to 'CURRENT' if there is another version of this object
			$query = new JfQuery();
			$sql = "UPDATE jm_sysobject_rp OBJECTS SET r_version_label = 'CURRENT'
					WHERE r_version_label = 'OLD'
					AND r_object_id  IN
							(	SELECT r_object_id FROM jm_sysobject_sp
								WHERE i_is_deleted = '0' AND r_object_id IN
								(	SELECT i_antecedent_id FROM jm_sysobject_sp WHERE r_object_id IN ('".$strObjectIds."')	)
							)
					";
			$query->setSQL($sql);
			$query->execute($this->session);
			// If there is no other version (not deleted) of this object, then mark the version to 'DELETED'
			$query = new JfQuery();
			$sql = "SELECT COUNT(r_object_id) AS count_objects FROM jm_sysobject_sp WHERE i_is_deleted = '0' AND i_chronicle_id IN
						(SELECT i_chronicle_id FROM jm_sysobject_sp WHERE r_object_id IN ('".$strObjectIds."')	)";
			$query->setSQL($sql);
			$results = $query->execute($this->session);
			$countObjects = 0;
			while ($results->next())	{JcLogger::info('count_objects : '.$results->getValue('count_objects')); $countObjects = $results->getValue('count_objects');}
			JcLogger::info('countObjects : '.$countObjects);
			if ($countObjects == 1)
			{
				JcLogger::info('Count 1 object');
				$query = new JfQuery();
				$sql = "UPDATE jm_sysobject_rp OBJECTS SET r_version_label = 'DELETED'
						WHERE r_version_label IN ('CURRENT', 'NEW')
						AND r_object_id  IN ('".$strObjectIds."')";
				$query->setSQL($sql);
				$query->execute($this->session);
			}
		}
		// And change its r_modify_date / r_modifier attributes
//		$query = new JfQuery();
//		$sql = "UPDATE jm_sysobject_sp OBJECTS SET r_modifier = '".$user->getValue('user_name')."' AND r_modify_date = now() WHERE r_object_id  IN  (SELECT i_antecedent_id FROM jm_sysobject_sp WHERE r_object_id <> i_antecedent_id AND r_object_id IN ('".$strObjectIds."'))";
//		$query->setSQL($sql);
//		$query->execute($this->session);
		// // Then mark the object as 'DELETED'
		// $query = new JfQuery();
		// $sql = "UPDATE jm_sysobject_rp OBJECTS SET r_version_label = 'DELETED' WHERE r_version_label IN ('OLD', 'NEW', 'CURRENT') AND r_object_id  IN  ('".$strObjectIds."')";
		// $query->setSQL($sql);
		// $query->execute($this->session);
		// Finally set the deleted attribute to '1' (true)
		// And the r_modify_date / r_modifier
		$query = new JfQuery();
		$sql = "UPDATE jm_sysobject_sp OBJECTS SET i_is_deleted = '1', r_modifier = '".$user->getValue('user_name')."', r_modify_date = now() WHERE r_object_id  IN  ('".$strObjectIds."')";
		$query->setSQL($sql);
		$query->execute($this->session);
		// Create a fetch event for each object
		$session = $this->session;
		$auditTrailMgr = $session->getAuditTrailManager();
		$stringArgs = array(	'userName' => $user->getValue('user_name'),
								'userIP' => getenv("REMOTE_ADDR")	);
		$arrObjectIds = explode("', '", $strObjectIds);
		foreach ($arrObjectIds as $key => $strObjectId)
		{
			$auditTrailMgr->createAudit($strObjectId, 'delete', $stringArgs, null);
		}
	}

	/**
	 * Remove all versions of an object.
	 *
	 * @access	private
	 */
	private function removeObjectAllVersions($strObjectIds)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// JcLogger::info('removeObjectAllVersions : '.$strObjectIds);
		// Check that the user has enough permit (DELETE - 7) on the objects
		$security = new JcSecurity($this->session);
		foreach (explode("', '", $strObjectIds) as $key => $strObjectId)
		{
			$typedObject = new JcTypedObject();
			$typedObject->setObjectId($strObjectId);
			$security->addObject($typedObject);
		}
		$security->setThreshold(7);
		$security->execute();
		$security->removeObjects();
		$strObjectIds = implode("', '", $security->getObjectIds());
		// Run a query to update the single attribute values
		$strChronicleIds = '';
		$query = new JfQuery();
		$sql = "SELECT i_chronicle_id FROM jm_sysobject_sp WHERE r_object_id IN ('".$strObjectIds."')";
		$query->setSQL($sql);
		$result = $query->execute($this->session);
		while ($result->next())	{$strChronicleIds .= $result->getValue('i_chronicle_id')."', '";}
		$strChronicleIds = substr($strChronicleIds, 0, -4);
		$sql = "UPDATE jm_sysobject_sp OBJECTS SET i_is_deleted = '1' WHERE i_chronicle_id  IN  ('".$strChronicleIds."')";
		$query->setSQL($sql);
		$query->execute($this->session);
		// Then mark the object as 'CURRENT' (or 'RESTORED'?)
		$query = new JfQuery();
		$sql = "UPDATE jm_sysobject_rp OBJECTS SET r_version_label = 'DELETED' WHERE r_version_label IN ('CURRENT', 'NEW') AND r_object_id  IN  ('".$strObjectIds."')";
		$query->setSQL($sql);
		$query->execute($this->session);
		// Create a fetch event for each object
		$session = $this->session;
		$user = $this->user;
		$auditTrailMgr = $session->getAuditTrailManager();
		$stringArgs = array(	'userName' => $user->getValue('user_name'),
								'userIP' => getenv("REMOTE_ADDR")	);
		$arrObjectIds = explode(", ", $strObjectIds);
		foreach ($arrObjectIds as $key => $strObjectId)
		{
			$auditTrailMgr->createAudit($strObjectId, 'delete', $stringArgs, null);
		}
	}

	/**
	 * Remove an object from current folder.
	 *
	 * @access	private
	 */
	private function removeObjectUnlink($strObjectIds, $containerId)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		JcLogger::info('removeObjectUnlink : '.$strObjectIds.'; containerId :'.$containerId);
		$arrObjectIds = explode(", ", $strObjectIds);
		foreach ($arrObjectIds as $key => $strObjectId)
		{
			JcLogger::info('removeObjectUnlink : '.$strObjectId.'; containerId :'.$containerId);
			// Get the object specified by the object ID 'strObjectId'
			$session = $this->session;
			$perObj = $session->getObject(new JfId($strObjectId));
			$sysObj = JfUtils::cast($perObj, 'JfSysObject');
			// And unlink it from the current folder
			$sysObj->unlink(new JfId($containerId));
			$sysObj->save();
		}
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
	 * Remove an object.
	 *
	 * @access	private
	 */
	private function removeObjectFromFavorites($strObjectIds)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$objectIds = explode("', '", $strObjectIds);
		// Get the favorites
		$favorites = new JcBookMark();
		foreach ($objectIds as $key => $strObjectId)
		{
			$bookmarkObject = new JcBookMarkObject();
			$bookmarkObject->setObjectId($strObjectId);
			$favorites->removeObject($bookmarkObject);
		}
		// Save the favorites
		$favorites->save();
	}
}
?>