<?php
/**
 * The JfCheckinOperation class.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfCheckinOperation extends JfOperation
{
	/**
	* Version
	*
	* @access	protected
	* @var		array(JfCheckinNode)
	*/
	protected $checkinNode = array();

	/**
	* File Name
	*
	* @access	protected
	* @var		String
	*/
	protected $strFileName;

	/**
	* Version
	*
	* @access	protected
	* @var		String
	*/
	protected $strVersion;

	/**
	 * Constructor
	 *
	 */
	public function __construct()	{}

	/**
	 * Set the object to the operation.
	 *
	 * @access	public
	 * @param	perObj					the object
	 * @return	JfCheckinOperationNode	An operation node
	 * @throws	JfException				if a server error occurs
	 */
	public function add($perObj)
	{
		try
		{
			$checkinNode = new JfCheckinNode($perObj, $this->strVersion, $this->strFileName);
			$this->checkinNode[] = $checkinNode;
			return $checkinNode;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Checkin New Version
	 *
	 * @access	private
	 * @param	JfSession			The session object that called this method
	 * @param	JfPersistentObject	The persistent object
	 * @param	String				The file path and name
	 * @param	String				The version ('MINOR_VERSION', 'MAJOR_VERSION' or 'BRANCH_VERSION')
	 * @throws	JfException			if a server error occurs
	 */
	private function checkinNewVersion($session, $perObj, $strFileName, $strVersion)
	{
		try
		{
			// Get the old object Id
			$antObjectId = $perObj->getObjectId();
			$strAntObjectId = $antObjectId->getId();
			// Get the chronicle object Id
			$strChronicleObjectId = $perObj->getValue('i_chronicle_id');
			// JcLogger::info("strAntObjectId : '".$strAntObjectId."'");
			// // Get the new object Id
			// $strNewObjectId = JfUtils::getNewId($session, $perObj->getValue('r_object_type'));
			// JcLogger::info("strNewObjectId : '".$strNewObjectId."'");

			// Get the current version label
			$strCurrentVersion = $perObj->getRepeatingValue('r_version_label', '0');
			// JcLogger::info("strCurrentVersion : '".$strCurrentVersion."'");
			// Set the next version label
			$strNextVersion = $strCurrentVersion;
			if ($strVersion == 'MINOR_VERSION')			$strNextVersion = $this->getMinorVersion($session, $strChronicleObjectId, $strCurrentVersion);
			else if ($strVersion == 'MAJOR_VERSION')	$strNextVersion = $this->getMajorVersion($session, $strChronicleObjectId, $strCurrentVersion);
			else if ($strVersion == 'BRANCH_VERSION')	$strNextVersion = $this->getBranchVersion($session, $strChronicleObjectId, $strCurrentVersion);
			else										throw new JfException('VERSION_ERROR');
			// JcLogger::info("strNextVersion : '".$strNextVersion."'");

			// Get the user details
			$user = $session->getLoginInfo();
			// Create a new content of the file with the content provided
			// $contentObj = new JfContent($session);
			// $contentObj->setContent($strFileName);
			// $strContentId = $contentObj->save();
			// $strContentType = $contentObj->getContentType();

			// Clone the object and reset some of its attributes
			$newObj = clone $perObj;
			$newObj = JfUtils::cast($newObj, 'JfSysObject');
			// Set the content of the new object
//			$strFileName = "/temp/".$session->getLoginUserId()."/".basename($strFileName);
			// JcLogger::info('strFileName : '.$strFileName);
			$newObj->setFile($strFileName);
			$newObj->setValue('a_content_type', '');
			// Set the i_antecedent_id attribute
			$newObj->setValue('i_antecedent_id', $strAntObjectId);
			// Set the label (2.0, CURRENT)
			$newObj->setRepeatingValue('r_version_label', '0', $strNextVersion);
			$newObj->setRepeatingValue('r_version_label', '1', 'CURRENT');
			// Mark the new object as unlocked
			$newObj->setValue('r_lock_owner', '');
			$newObj->setValue('r_lock_date', '');
			$newObj->setValue('r_lock_machine', '');
			// Save this new object
			$newObj->save();
			// JcLogger::info('New object Id : '.$newObj->getValue('r_object_id'));
		}
		catch (JfException $exception)
		{
			JcLogger::info('Exception : '.$exception);
			throw $exception;
		}
	}

	/**
	 * Checkin Same Version
	 *
	 * @access	private
	 * @param	JfSession		The session object that called this method
	 * @param	String			The object Id
	 * @param	String			The file path and name
	 * @throws	JfException		if a server error occurs
	 */
	private function checkinSameVersion($session, $strObjectId, $strFileName)
	{
		// Get the user details
		$user = $session->getLoginInfo();
		// Just replace the content of the file with the content provided
		$contentObj = new JfContent($session);
		$contentObj->setContent($strFileName);
		$strContentId = $contentObj->save();
		$strContentType = $contentObj->getContentType();

		// Create the SQL to update the object (change of i_contents_id, a_content_type and r_content_size attributes)
		$query = new JfQuery();
		$sql = "UPDATE jm_sysobject_sp OBJECTS 	SET	r_lock_owner = '',
													r_lock_date  = '',
													r_lock_machine = '',
													i_contents_id = '".$strContentId."',
													a_content_type = '".$strContentType."',
													r_content_size = '".filesize(_SERVER_ROOT_.$strFileName)."',
													r_modifier = '".$user->getValue('user_name')."',
													r_modify_date = now()
												WHERE r_object_id = '".$strObjectId."'";
		$query->setSQL($sql);
		$query->execute($session);
	}

	/**
	 * Run the operation
	 *
	 * @access	public
	 * @return	boolean			whether the command was successful or not
	 * @throws	JfException		if a server error occurs
	 */
	public function execute()
	{
		try
		{
			$flag = false;
			// Get the user details
			$sessionmanager = new JfSessionManager();
			$session = $sessionmanager->getSession('www_jmroy');
			$user = $session->getLoginInfo();
			// JcLogger::info('User : '.$user->getValue('r_object_id'));
			// Prepare the audit manager to create a 'checkin' event
			$auditTrailMgr = $session->getAuditTrailManager();
			$stringArgs = array(	'userName' => $user->getValue('user_name'),
									'userIP' => getenv("REMOTE_ADDR")	);
			// List of all object IDs to check in
			$arrObjectIds = array(); $arrObjectIdsNew = array();
			// For each node
			foreach ($this->checkinNode as $index => $node)
			{
				$perObj = $node->getObject();
				$objectId = $perObj->getObjectId();
				$strObjectId = $objectId->getId();
				// Log Informations
				// JcLogger::info("FE - strFileName : '".$node->getFilePath()."'");
				// JcLogger::info("FE - strVersion : '".$node->getCheckinVersion()."'");
				// JcLogger::info("FE - objectId : '".$strObjectId."'");
				// Do the checkin : UPDATE for a SAME_VERSION checkin,
				// UPDATE and INSERT for all other cases
				if ($node->getCheckinVersion() == 'SAME_VERSION')	$this->checkinSameVersion($session, $strObjectId, $node->getFilePath());
				else												$this->checkinNewVersion($session, $perObj, $node->getFilePath(), $node->getCheckinVersion());
				// Delete the temporary file after the checkin
				if (file_exists(_SERVER_ROOT_.$node->getFilePath()) && $node->getFilePath() <> '')	unlink(_SERVER_ROOT_.$node->getFilePath());
				// Update the list of all object IDs to check in
				if ($node->getCheckinVersion() <> 'SAME_VERSION')	$arrObjectIds[] = $strObjectId;
				else												$arrObjectIdsNew[] = $strObjectId;
				// Create a checkin event for each object
				$auditTrailMgr->createAudit($strObjectId, 'checkin', $stringArgs, null);
			}
			$strObjectIds = implode($arrObjectIds, "', '");
			$strObjectIdsNew = implode($arrObjectIdsNew, "', '");
			// JcLogger::info("strObjectIds : '".$strObjectIds."'");
			// Then reset the lock attributes (r_lock_owner, r_lock_date and r_lock_machine) to ''
			$query = new JfQuery();
			$sql = "UPDATE jm_sysobject_sp OBJECTS 	SET	r_lock_owner = '',
														r_lock_date  = '',
														r_lock_machine = '',
														r_modifier = '".$user->getValue('user_name')."',
														r_modify_date = now()
													WHERE r_object_id  IN  ('".$strObjectIds."')";
			$query->setSQL($sql);
			$query->execute($session);
			// And mark the objects as 'OLD'
			$query = new JfQuery();
			$sql = "UPDATE jm_sysobject_rp OBJECTS 	SET	r_version_label = 'OLD'
													WHERE r_object_id  IN  ('".$strObjectIdsNew."')
													AND i_position = '-2'";
			$query->setSQL($sql);
			// $query->execute($session);
			// Return true/false
			return $flag;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Get the version number for a 'branch version' checkin :
	 *
	 * '1.0.1.0'
	 *
	 * @access	private
	 * @param	JfSession	The current session object
	 * @param	String		The chronicle object Id
	 * @param	String		The current version number
	 * @return	String		The next version number
	 */
	private function getBranchVersion($session, $strChronicleObjectId, $versionNumber)
	{
		$query = new JfQuery();
		$sql = "SELECT r_version_label FROM jm_sysobject_s s, jm_sysobject_r r
				WHERE s.i_chronicle_id = '".$strChronicleObjectId."'
						AND s.r_object_id = r.r_object_id
						AND i_position = -1
						AND r_version_label LIKE '".substr($versionNumber, 0, 3)."%'
				ORDER BY r_version_label DESC LIMIT 0, 1";
		$query->setSQL($sql);
		$results = $query->execute($session);
		while ($results->next())	{$versionNumber = $results->getValue('r_version_label');}
		$version = explode('.', $versionNumber);
		if (sizeof($version) == 2)	{return $versionNumber.'.1.0';}
		// There is already a branch version
		// $size = sizeof($version);
		// $version[$size - 2] += 1;
		// $version[$size - 1] = 0;
		$version[2] += 1;
		$version[3] = 0;
		return implode('.', $version);
	}

	/**
	 * Get the version number for a 'minor version' checkin :
	 *
	 * '1.1'
	 *
	 * @access	private
	 * @param	JfSession	The current session object
	 * @param	String		The chronicle object Id
	 * @param	String		The current version number
	 * @return	String		The next version number
	 */
	private function getMinorVersion($session, $strChronicleObjectId, $versionNumber)
	{
		$version = explode('.', $versionNumber);
		$minor = 1 + $version[sizeof($version) - 1];
//		return $version[0].'.'.$minor;
		return substr($versionNumber, 0, strripos($versionNumber, '.')).'.'.$minor;
	}

	/**
	 * Get the version number for a 'major version' checkin :
	 *
	 * '2.0'
	 *
	 * @access	private
	 * @param	JfSession	The current session object
	 * @param	String		The chronicle object Id
	 * @param	String		The current version number
	 * @return	String		The next version number
	 */

	private function getMajorVersion($session, $strChronicleObjectId, $versionNumber)
	{
		// JcLogger::info("versionNumber : '".$versionNumber."'");
		$query = new JfQuery();
		$sql = "SELECT r_version_label FROM jm_sysobject_s s, jm_sysobject_r r
				WHERE s.i_chronicle_id = '".$strChronicleObjectId."'
						AND s.r_object_id = r.r_object_id
						AND i_position = -1
						AND r_version_label LIKE '_.0'
				ORDER BY r_version_label DESC LIMIT 0, 1";
		$query->setSQL($sql);
		// JcLogger::info("sql : '".$sql."'");
		$results = $query->execute($session);
		while ($results->next())	{$versionNumber = $results->getValue('r_version_label');}
		// JcLogger::info("versionNumber : '".$versionNumber."'");
		$version = explode('.', $versionNumber);
		$version[0] += 1;
		$version[1] = 0;
		// JcLogger::info("New versionNumber : '".implode('.', $version)."'");
		return implode('.', $version);
	}

	/**
	 * Get the version number for a 'same version' checkin :
	 *
	 * '1.0'
	 *
	 * @access	private
	 * @param	JfSession	The current session object
	 * @param	String		The chronicle object Id
	 * @param	String		The current version number
	 * @return	String		The next version number
	 */
	private function getSameVersion($session, $strChronicleObjectId, $versionNumber)
	{
		return $versionNumber;
	}

	/**
	 * Set the file content to the checkin operation.
	 *
	 * @access	public
	 * @param	strFileName		the file name
	 * @throws	JfException		if a server error occurs
	 */
	public function setCheckinFile($strFileName)
	{
		try
		{
			$this->strFileName = $strFileName;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Set the version to the checkin operation.
	 *
	 * @access	public
	 * @param	strVersion		the version number
	 * @throws	JfException		if a server error occurs
	 */
	public function setCheckinVersion($strVersion)
	{
		try
		{
			$this->strVersion = $strVersion;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>