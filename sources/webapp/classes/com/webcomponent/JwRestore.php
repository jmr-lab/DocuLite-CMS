<?php
/**
 * JwRestore webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwRestore extends JwModalList
{
	/**
	* RESTORE_OBJECT
	*
	* Restore the current object specified by its ID.
	*
	* @access	private
	* @var		int
	*/
	private static $RESTORE_OBJECT = 0;

	/**
	* RESTORE_ALL_VERSIONS
	*
	* Restore all versions of the current object.
	*
	* @access	private
	* @var		int
	*/
	private static $RESTORE_ALL_VERSIONS = 1;

	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('checkout', 'icon', 'object_name', 'description', 'restore');

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
							AND s2.i_is_deleted = true
					GROUP BY s1.r_object_id
				) AS _version_
				ON _version_.r_object_id = s1.r_object_id
				WHERE	s1.i_is_deleted = true
						AND s1.r_object_id = r1.r_object_id
						AND s1.r_object_id IN ('".$objectList."')
						AND r1.i_position = '-2'
						AND r1.r_version_label <> 'OLD'
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
		JcLogger::info(__CLASS__.'.'.__FUNCTION__.'()');
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
//		foreach ($action as $key => $value)	{JcLogger::info('action['.$key.'] : '.$value);}
		if (sizeof($action) == 0) return '';
		$strObjectsToRestore = ''; $strObjectsToRestoreAll = '';
		foreach ($action as $key => $value)
		{
			switch ($value)
			{
				case 0:		// RESTORE_OBJECT
					$strObjectsToRestore .= ', '.$key;
					break;
				case 1:		// RESTORE_ALL_VERSIONS
					$strObjectsToRestoreAll .= ', '.$key;
					break;
				default:
					break;
			}
		}
		if (strlen($strObjectsToRestore) > 0)	{$this->restoreObject(substr($strObjectsToRestore, 2));}
		if (strlen($strObjectsToRestoreAll) > 0)	{$this->restoreObjectAllVersions(substr($strObjectsToRestoreAll, 2));}
		return '';
	}

	/**
	 * Remove an object.
	 *
	 * @access	private
	 */
	private function restoreObject($strObjectIds)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		JcLogger::info('restoreObject : '.$strObjectIds);
		// Get the user login info
		$user = $this->user;
		// First set the deleted attribute to '0' (false)
		$query = new JfQuery();
		$sql = "UPDATE jm_sysobject_sp OBJECTS SET i_is_deleted = '0', r_modifier = '".$user->getValue('user_name')."', r_modify_date = now() WHERE r_object_id  IN  ('".$strObjectIds."')";
		$query->setSQL($sql);
		$query->execute($this->session);
		// Then mark the most recent version (antecedent) to 'OLD' if there is another version of this object
		$query = new JfQuery();
		$sql = "UPDATE jm_sysobject_rp OBJECTS SET r_version_label = 'OLD' WHERE r_version_label = 'CURRENT' AND r_object_id  IN  (SELECT i_antecedent_id FROM jm_sysobject_sp WHERE r_object_id <> i_antecedent_id AND r_object_id IN ('".$strObjectIds."'))";
		$query->setSQL($sql);
		$query->execute($this->session);
		// Finally mark the object as 'CURRENT'
		$query = new JfQuery();
		$sql = "UPDATE jm_sysobject_rp OBJECTS SET r_version_label = 'CURRENT' WHERE r_version_label = 'DELETED' AND r_object_id  IN  ('".$strObjectIds."')";
		$query->setSQL($sql);
		$query->execute($this->session);
		// Create a fetch event for each object
		$session = $this->session;
		$auditTrailMgr = $session->getAuditTrailManager();
		$stringArgs = array(	'userName' => $user->getValue('user_name'),
								'userIP' => getenv("REMOTE_ADDR")	);
		$arrObjectIds = explode(", ", $strObjectIds);
		foreach ($arrObjectIds as $key => $strObjectId)
		{
			$auditTrailMgr->createAudit($strObjectId, 'restore', $stringArgs, null);
		}
	}

	/**
	 * Remove all versions of an object.
	 *
	 * @access	private
	 */
	private function restoreObjectAllVersions($strObjectIds)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		JcLogger::info('restoreObjectAllVersions : '.$strObjectIds);
		// Get the user login info
		$user = $this->user;
		// Run a query to update the single attribute values
		$strChronicleIds = '';
		$query = new JfQuery();
		$sql = "SELECT i_chronicle_id FROM jm_sysobject_sp WHERE r_object_id IN ('".$strObjectIds."')";
		$query->setSQL($sql);
		$result = $query->execute($this->session);
		while ($result->next())	{$strChronicleIds .= $result->getValue('i_chronicle_id')."', '";}
		$strChronicleIds = substr($strChronicleIds, 0, -4);
		$sql = "UPDATE jm_sysobject_sp OBJECTS SET i_is_deleted = '0' WHERE i_chronicle_id  IN  ('".$strChronicleIds."')";
		$query->setSQL($sql);
		$query->execute($this->session);
		// Finally mark the object as 'CURRENT'
		$query = new JfQuery();
		$sql = "UPDATE jm_sysobject_rp OBJECTS SET r_version_label = 'CURRENT' WHERE r_version_label = 'DELETED' AND r_object_id  IN  ('".$strObjectIds."')";
		$query->setSQL($sql);
		$query->execute($this->session);
		// Create a fetch event for each object
		$session = $this->session;
		$auditTrailMgr = $session->getAuditTrailManager();
		$stringArgs = array(	'userName' => $user->getValue('user_name'),
								'userIP' => getenv("REMOTE_ADDR")	);
		$arrObjectIds = explode(", ", $strObjectIds);
		foreach ($arrObjectIds as $key => $strObjectId)
		{
			$auditTrailMgr->createAudit($strObjectId, 'restore', $stringArgs, null);
		}
	}
}
?>