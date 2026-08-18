<?php
/**
 * An Estancia session.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfSession
{
	/**
	* User currently logged in
	*
	* The $user variable is an array containing the currently logged in user name, password and client_capability.
	*
	* @todo check if needed
	* @access private
	* @var JfLoginInfo
	*/
	private $user;

	/**
	 * Constructor
	 *
	 * This function initialize the session
	 *
	 * @param	JfSessionManager	jfsessionmanager the session manager
	 * @param	array				user the logged in user
	 * @throws	JfException			if a server error occurs
	 */
	public function __construct($sessionmanager, $user, $docbaseConfig)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'($sessionmanager, $user)');
		// Check that this session was initialised by a JfSessionManager object
		if (get_class($sessionmanager) <> 'JfSessionManager')	{throw new JfException('SESSION_INVALID_ARGUMENT');}
		// If no user specified then return
		if (!isset($user))	{return;}
		// Checks the user object is valid
		if (!is_array($user))	{throw new JfException('SESSION_INVALID_ARGUMENT');}
		if (!isset($user['r_object_id']) || $user['r_object_id'] == '')	{throw new JfException('SESSION_INVALID_ARGUMENT');}
		// Set the user object
		$this->user = new JfLoginInfo($user);
		// Set the session object and variable
		$_SESSION['_USER_'] = $user;
		// Set the docbase config
		$_SESSION['_REPOSITORY_'] = $docbaseConfig;
	}

	/**
	 * Destructor
	 *
	 */
	public function __destruct()
	{
		// Logger
		JfLogger::dump();
	}

	/**
	 * Closes an explicit database transaction and cancels any changes made since the call to beginTrans().
	 *
	 * @access	public
	 * @throws	JfException	if a server error occurs
	 */
	public function abortTrans()
	{
		mysql_query("ROLLBACK");
	}

	/**
	 * Opens an explicit database transaction.
	 *
	 * @access	public
	 * @throws	JfException	if a server error occurs
	 */
	public function beginTrans()
	{
		mysql_query("START TRANSACTION");
		mysql_query("BEGIN");
	}

	/**
	 * Commits all changes made after a beginTrans() method call.
	 *
	 * @access	public
	 * @throws	JfException	if a server error occurs
	 */
	public function commitTrans()
	{
		mysql_query("COMMIT");
	}

	/**
	 * Changes a password.
	 *
	 * @access public
	 * @param String oldPasswd the old password
	 * @param String newPasswd the new password
	 * @throws JfException if a server error occurs
	 */
	public function changePassword($oldPasswd, $newPasswd)	{}

	/**
	 * Removes items from an inbox that were placed there using the queue method.
	 *
	 * @access public
	 * @param JfId stampId the object ID of the dmi_queue_item object associated with the item that you want to remove from the queue.
	 * @throws JfException if a server error occurs
	 */
	public function dequeue($stampId)	{}

	/**
	 * Disconnects the session.
	 *
	 * After a session is disconnected, all subsequent calls made using that session cause the exception JfException.SESSION_DISCONNECTED to be thrown.
	 *
	 * @access public
	 * @throws JfException if a server error occurs
	 */
	public function disconnect()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__."()");
		// Unset the session and variable
		unset($_SESSION['_REPOSITORY_']);
		unset($_SESSION['_USER_']);
		// create an audit event
		$auditTrailMgr = $this->getAuditTrailManager();
		$user = $this->getLoginInfo();
		$stringArgs = array(	'userName' => $user->getValue('user_name'),
								'userIP' => getenv("REMOTE_ADDR"),
								'browser' => '',
								'os' => '',
								'referer' => '',
								'language' => '',
								'details' => $_SERVER['HTTP_USER_AGENT']	);
		$auditTrailMgr->createAudit($user->getValue('r_object_id'), 'logout', $stringArgs, null);
	}

	/**
	 * Flush the cache.
	 *
	 * @access public
	 * @throws JfException if a server error occurs
	 */
	public function flushCache()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__."()");
		// Unset the session objects
		unset($_SESSION['_OBJECTS_']);
	}

	/**
	 * Remove the specified object from the cache.
	 *
	 * @access public
	 * @param JfId objectId the object ID of the object to remove from the cache.
	 * @throws JfException if a server error occurs
	 */
	public function flushObject($objectId)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$objectId.__toString().')');
		// Get the object ID as a String
		$strObjectId = $objectId->toString();
		// Unset the session object
		unset($_SESSION['_OBJECTS_'][$strObjectId]);
	}

	/**
	 * Returns an ACL object.
	 *
	 * @access public
	 * @param JfId aclId - the Id of the ACL
	 * @return JfACL the ACL object; null if the server cannot find the specified ACL object.
	 * @throws JfException if a server error occurs
	 */
	public function getACL($aclId)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'($aclId)');
		try
		{
			if (get_class($aclId) <> 'JfId')	{throw new JfException('SESSION_INVALID_ARGUMENT');}
			return new JfACL($this, $aclId);
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Retrieves the Audit Trail manager for this session.
	 *
	 * @access public
	 * @return JfAuditTrailManager An audit trail manager for this session.
	 * @throws JfException if a server error occurs
	 */
	public function getAuditTrailManager()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return new JfAuditTrailManager($this);
	}

	/**
	 * Returns the name of the RDBMS.
	 *
	 * @access public
	 * @return String the name of the RDBMS.
	 * @throws JfException if a server error occurs
	 */
	public function getDBMSName()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Read the ini file
			$properties = JfUtils::getProperties(JfUtils::getIniFile('client'));
			// Returns the docbase ID
			return JfUtils::getPropertyValue($properties, 'DOCBASE_NAME');
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Identifies the default ACL for the server.
	 *
	 * The default ACL is the ACL that the server assigns to a new object if an ACL is not explicitly associated with the object.
	 * This method returns the ACL associated with the user who created the object.
	 *
	 * @access public
	 * @return JfId The ACL Id.
	 * @throws JfException if a server error occurs
	 */
	public function getDefaultACL()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			$logininfo = $this->user;
//			return new JfId($logininfo->getValue('acl_id'));
//			JfLogger::info('Default ACL : '.$logininfo->getValue('acl_id'));
			return $logininfo->getValue('acl_id');
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the config of the repository.
	 *
	 * @access	public
	 * @return	array		the repository config (documentum or estancia).
	 * @throws	JfException	if a server error occurs
	 */
	public function getDocbaseConfig()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Returns the docbase config
			return (isset($_SESSION['_REPOSITORY_'])) ? $_SESSION['_REPOSITORY_'] : 'REPOSITORY_1';
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the ID of the repository.
	 *
	 * @access public
	 * @return String the repository ID.
	 * @throws JfException if a server error occurs
	 */
	public function getDocbaseId()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Read the ini file
			$properties = JfUtils::getProperties(JfUtils::getIniFile('client'));
			// Returns the docbase ID
			return JfUtils::getPropertyValue($properties, 'DOCBASE_ID');
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the name of the repository.
	 *
	 * @access public
	 * @return String the repository name.
	 * @throws JfException if a server error occurs
	 */
	public function getDocbaseName()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Read the ini file
			$properties = JfUtils::getProperties(JfUtils::getIniFile('client'));
			// Returns the docbase ID
			return JfUtils::getPropertyValue($properties, 'DOCBASE_NAME');
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns an Estancia group object.
	 *
	 * The following code example demonstrates how to obtain a JfGroup :
	 *
     * $idObj = new JfId("0900d5bb8001f900");
     * $groupObj = $session->getGroup(idObj);
     * if ($groupObj->getObjectId()->getId() == "1200d5bb8001f900")
     * {
     * 	// Successfully fetched object...
     * }
	 *
	 * @access	public
	 * @param	JfId				groupId a JfId object that contains the object ID.
	 * @return	JfGroup				a group object.
	 * @throws	JfException			if the persistent object specified with groupId doesn't exist, or a server error occurs.
	 */
	public function getGroup($groupId)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'($groupId)');
		try
		{
			if (get_class($groupId) <> 'JfId')	{throw new JfException('SESSION_INVALID_ARGUMENT');}
			return new JfGroup($this, $groupId);
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the login information used to establish this session.
	 *
	 * @access public
	 * @return JfLoginInfo the login information used to establish this session.
	 * @throws JfException if a server error occurs
	 */
	public function getLoginInfo()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			return $this->user;
		}
		catch (JfException $exception)
		{
			// Throw an exception
			JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'(Exception : )'.$exception->getMessage());
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the repository name of the current user.
	 *
	 * @access public
	 * @return String the name of the current user.
	 * @throws JfException if a server error occurs
	 */
	public function getLoginUserName()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
//			if (!$this->isClientAuthenticated())	{throw new JfException('SESSION_DISCONNECTED');}
			$logininfo = $this->user;
			return (isset($logininfo) ? $logininfo->getValue('user_login_name') : '');
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the repository ID of the current user.
	 *
	 * @access public
	 * @return JfId the ID of the current user.
	 * @throws JfException if a server error occurs
	 */
	public function getLoginUserId()
	{
		// Logger
		JfLogger::debug(__CLASS__.__FUNCTION__."()");
		try
		{
			if (!$this->isClientAuthenticated())	{throw new JfException('SESSION_DISCONNECTED');}
			$logininfo = $this->user;
			return $logininfo->getValue('r_object_id');
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns an Estancia server object.
	 *
	 * The following code example demonstrates how to obtain a JfPersistentObject :
	 *
     * $idObj = new JfId("0900d5bb8001f900");
     * $perObj = $session->getObject(idObj);
     * if ($perObj->getObjectId()->getId() == "0900d5bb8001f900")
     * {
     * 	// Successfully fetched object...
     * }
	 *
	 * @access public
	 * @param JfId objectId a JfId object that contains the object ID.
	 * @return JfPersistentObject a persistent object.
	 * @throws JfException if the persistent object specified with objectID doesn't exist, or a server error occurs.
	 */
	public function getObject($objectId)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'($objectId)');
		try
		{
			if (get_class($objectId) <> 'JfId')	{throw new JfException('SESSION_INVALID_ARGUMENT');}
			return new JfPersistentObject($this, $objectId);
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns an Estancia server object that satisfies the SQL qualification.
	 *
	 * The following code example demonstrates how to obtain a JfSysObject object given a SQL query:
	 *
	 * $sysObj = (JfSysObject)$session->getObjectByQualification("jm_document where r_object_id='0900d5bb8001f900' and object_name='testObject'");
	 * if ($sysObj->getObjectName() == "testObject")
	 * {
	 * 	// Successfully fetched object...
	 * }
	 *
	 * @access public
	 * @param String qualification a SQL qualification consisting of that portion of a SELECT statement beginning with the keyword FROM.
	 * The SQL qualification uniquely identifies an object in a repository.
	 * @return JfPersistentObject a persistent object. If no object matches qualification, the returned object is null.
	 * @throws JfException if a server error occurs
	 */
	public function getObjectByQualification($qualification)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$qualification.')');
		try
		{
			// $qualification = "jm_document where r_object_id='0900d5bb8001f900' and object_name='testObject'";
			// $table = "jm_document";
			$table = preg_replace('/\W.*/', '', $qualification);
			// $where = "where r_object_id='0900d5bb8001f900' and object_name='testObject'";
			$where = substr($qualification, strlen($table) + 1, strlen($qualification));
			// Query the single attributes table
			$query = new JfQuery();
			$sql = 'SELECT * FROM '.$table.'_sp '.$where;
			$query->setSQL($sql);
			$results = $query->execute($this);
			while ($results->next())	{$attrValues = $results->getResult();}
			// Case error "
			if (!isset($attrValues['r_object_id']))	{throw new JfException('ERROR_INVALID_OBJECT');}
			// and the repeating attributes table
			$sql = 'SELECT * FROM '.$table.'_rp WHERE r_object_id = \''.$attrValues['r_object_id'].'\'';
			$query->setSQL($sql);
			$results = $query->execute($this);
			while ($results->next())	{$r_attrValues[] = $results->getTypedObject();}
			// Return the persistent object associated with these arrays
			if (isset($r_attrValues))	{$arrObj = array("attrValue" => $attrValues, "r_attrValue" => $r_attrValues);}
			else						{$arrObj = array("attrValue" => $attrValues);}
			return new JfPersistentObject($this, $arrObj);
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns an enumeration of persistent objects using data obtained through a query.
	 *
	 * The query doesn't need to contain all the object attributes but there are a minimum subset of attributes that must be present.
	 * All queries must include r_object_id and i_vstamp attribute.
	 * The query must include r_object_type, r_aspect_name, i_is_replica and i_is_reference if the object has these attributes.
	 * The optionalTypeName is required if the object does not have the attribute r_object_type, eg: jm_user.
	 * When a query is done on jmi_queue_item, the attribute "source_docbase" must be included in the query.
	 *
	 * Note that the objects returned by this call may not be fully populated with data.
	 * Only data present in the query will exist in the returned objects.
	 * As long as your subsequent access to the objects only reference the populated data then no "fetch" of the full object will occur.
	 * As soon as you try to access object data that is not present then an internal "fetch" will be triggered to obtain the remaining data.
	 * This process is transparent to the object user.
	 *
	 * @access public
	 * @param String sql the query to get the objects
	 * @param String optionalTypeName
	 * @return array a list of persistent objects.
	 * @throws JfException if a server error occurs
	 */
	public function getObjectsByQuery($sql, $optionalTypeName = '')
	{
		// Logger
		JfLogger::debug(__CLASS__.__FUNCTION__.'('.$sql.__toString().', '.$optionalTypeName.__toString().')');
		try
		{
			// $sql = "jm_document where object_name='testObject'";
			// $table = "jm_document";
			$table = preg_replace('/\W.*/', '', $sql);
			// $where = "where object_name='testObject'";
			$where = substr($sql, strlen($table) + 1, strlen($sql));
			// Query the single attributes table
			$sql = 'SELECT * FROM '.$table.'_sp '.$where;
			$query->setSQL($sql);
			while($attrValues = $query->execute($this))	{$col[$attrValues['r_object_id']]['attrValue'] = $attrValues;}
			// and the repeating attributes table
			$sql = 'SELECT * FROM '.$table.'_rp '.$where;
			$query->setSQL($sql);
			while($r_attrValues = $query->execute($this))
			{
				foreach ($r_attrValues as $attrName=>$attrValue)
				{
					if ($attrName <> 'r_object_id' && $attrValue <> '' && $attrValue <> 'NULL')	{$col[$r_attrValues['r_object_id']]['r_attrValue'][] = $attrValue;}
				}
			}
			// If no object were found then return null
			if (sizeof($col) == 0)	{return null;}
			// Return the persistent objects associated with these arrays
			foreach ($col as $objId=>$objValue)	{$objects[] = new JfPersistentObject($this, $objValue);}
			return $objects;
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns a user object that contains information about the repository user.
	 * This method takes the user's repository ID as a parameter and returns the jm_user object for that user.
	 * If the userId is empty or null, the method will return a JfUser object corresponding to the logged in user.
	 *
	 * @access public
	 * @param JfId userId the user's ID
	 * @return JfUser a JfUser object.
	 * @throws JfException if a server error occurs
	 */
	public function getUser($userId)	{}

	/**
	 * Stateful connection information.
	 * Client may optionally authenticate to the server (i.e. the client identity is known to the server) to support session communication.
	 *
	 * @access public
	 * @return boolean true if the session is over a connection for which the identity of the client is known to the server.
	 * @throws JfException if a server error occurs
	 */
	public function isClientAuthenticated()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__."()");
		$logininfo = $this->user;
		if (!isset($logininfo) || $logininfo->getValue('r_object_id') == '')	{return false;}
		else	{return true;}
	}

	/**
	 * Indicates whether the session is still connected to a repository server.
	 *
	 * If the session has been disconnected, all subsequent calls made using the session will cause an exception JfException.SESSION_DISCONNECTED to be thrown.
	 *
	 * @access	public
	 * @return	boolean		true if this session is connected to a Database server; false if this session has been disconnected.
	 * @throws	JfException	if a server error occurs
	 */
	public function isConnected()	{}

	/**
	 * Creates a persistent object given a specified object type.
	 *
	 * The new object doesn't have an object id ('0000000000000000'), and is not committed to a repository until you call the save method.
	 * The following code example demonstrates how to create a jm_document object:
	 *
	 * $sysObj = (JfSysObject)$session->newObject("jm_document");
	 * $sysObj->setObjectName("testObject");
	 * $sysObj->setSubject("DFC Example Doc");
	 * $sysObj->setContentType("crtext");
	 * $sysObj->setFile("c:\dfctest.txt");
	 * $sysObj->link("/DFCTest");
	 * $sysObj->save();
	 *
	 * @access public
	 * @param String typeName the object type of the new object, with underscores. For example, enter jm_document to create a document object.
	 * @return a JfPersistentObject interface to the new object.
	 * @throws JfException if a server error occurs
	 */
	public function newObject($typeName)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$typeName.')');
		try
		{
			return new JfPersistentObject($this, $typeName);
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Creates and starts an ad-hoc workflow.
	 *
	 * Use this method when you want to create and start a workflow that is not based on a workflow template.
	 * The following code example demonstrates how to route an object to a user and a group:
	 *
	 * $userObj = $sess->getObjectByQualification("jm_user where user_name='Jean-Marie'");
	 * $userList[] = $userObj->getObjectId();
	 * $groupObj = $sess->getObjectByQualification("jm_group where group_name='Administrators'");
	 * $groupList[] = $groupObj->getObjectId();
	 * $objList[] = new JfId("0900d5bb8001f900");
	 * $flags = DF_REQ_SIGN_OFF | DF_REQ_END_NOTIFICATION;
	 * $wfId = $session->sendToDistributionListEx($userList, $groupList, "Please review", $objList, 5, $flags);
	 *
	 * @access public
	 * @param array toUsers an array to the users that are assigned workflow tasks.
	 * @param array toGroups an array to the groups that are assigned workflow tasks.
	 * @param String instructions - task instructions for the recipients of workflow tasks.
	 * @param array objectIDs a list of document IDs you want to the specified users and groups. You may only distribute persistent objects in a workflow.
	 * @param int priority An integer value corresponding to the priority level.
	 * The following list maps the valid values to their corresponding priority levels:
	 * Integer		Priority Level
	 * 1			Low
	 * 5			Medium
	 * 10			High
	 * @param boolean flags Flags can be any combination of these options :
	 * JfWorkflow.JF_REQ_END_NOTIFICATION, JfWorkflow.JF_REQ_SIGN_OFF, JfWorkflow.JF_SEQUENTIAL,
	 * JfWorkflow.JF_ALLOW_REJECT_PREVIOUS, and JfWorkflow.JF_ALLOW_REJECT_INITIATOR bitwise OR'ed together.
	 * @return JfId a JfId interface to the ad-hoc workflow.
	 * @throws JfException if the server fails to create the ad-hoc workflow.
	 */
	public function sendToDistributionListEx($toUsers, $toGroups, $subject, $instructions, $objectIDs, $priority, $flags)
	{
		// Logger
//		JcLogger::info(__CLASS__.__FUNCTION__.'('.$toUsers.__toString().', '.$toGroups.__toString().', '.$instructions.__toString().', '.$objectIDs.__toString().', '.$priority.__toString().', '.$flags.__toString().')');
		try
		{
			$toUsersGroups = array_merge((array)$toUsers, (array)$toGroups);
			foreach ($toUsersGroups as $index=>$user)
			{
				// Get Sender
				$sender = $this->getLoginInfo();
				// @todo - create a workflow if the message length is over 255 characters
				$queueitem = $this->newObject('jmi_queue_item');
				$queueitem->setValue('name', $user);
				$queueitem->setValue('event', $flags);
				$queueitem->setValue('event', $subject);
				if (strlen($instructions) < 255)	{$queueitem->setValue('message', $instructions);}
				$queueitem->setValue('sent_by', $sender->getValue('user_name'));
				$queueitem->setValue('date_sent', date("Y-m-d H:i:s"));
				$queueitem->save();
				$queueId = $queueitem->getValue('r_object_id');
				
				// Only create a new workflow if the message size if more than 255
				if (strlen($instructions) >= 255)
				{
					// Create a new Document and its associated content object
					$perObj = $this->newObject('jm_document');
					$document = JfUtils::cast($perObj, 'JfSysObject');
					$document->setObjectName($subject);
					// TODO - Set an ACL / owner / location to the document
			//		$document->setACL($_POST['acl']);
			//		$document->setValue('a_content_type', $this->getFormat($_POST['format']));
			//		$document->setRepeatingValue('i_folder_id', '0', $this->getFolderId());
					// Create a temporary file : 20120510084523012.eml
					$milli = floor(1000 * microtime());
					while (strlen($milli) < 3)	{$milli = '0'.$milli;}
					$fileName = '/temp/'.date("YmdHis").$milli.'.eml';
					$file = fopen(_SERVER_ROOT_.$fileName, "w");
					fputs($file, $instructions);
					fclose($file);
					$document->setFile($fileName);
					$document->setValue('owner_name', 'admin');
					$document->save();
					$documentId = $document->getValue('r_object_id');
//					JfLogger::info('documentId : '.$documentId);
					// Get the current user name (default is 'guest')
//					JfLogger::info('user : '.$sender->getValue('user_name'));
					// // Create a new Workflow and its associated woritem and package
					$workflow = $this->newObject('jm_workflow');
					$workflow->setValue('object_name', date("d/m/Y H:i:s").' QuickFlow'); 
					$workflow->setValue('supervisor_name', $sender->getValue('user_name')); 
					$workflow->setValue('r_creator_name', $sender->getValue('user_name')); 
					$workflow->setValue('r_start_date', date("Y-m-d H:i:s")); 
					$workflow->save();
					$workflowId = $workflow->getValue('r_object_id');
//					JfLogger::info('workflowId : '.$workflowId);
					$workItem = $this->newObject('jmi_workitem');
					$workItem->setValue('r_creation_date', date("Y-m-d H:i:s"));
					$workItem->setValue('r_workflow_id', $workflowId); 
					$workItem->setValue('r_queue_item_id', $queueId); 
					$workItem->save();
					$workItemId = $workItem->getValue('r_object_id');
//					JfLogger::info('workItemId : '.$workItemId);
					$package = $this->newObject('jmi_package');
					$package->setValue('r_workflow_id', $workflowId); 
					$package->setRepeatingValue('r_component_id', 0, $documentId); 
					$package->save();
					$packageId = $package->getValue('r_object_id');
//					JfLogger::info('packageId : '.$packageId);
					// Unlink (delete) the temporary file
					unlink(_SERVER_ROOT_.$fileName);
				}
			}
		}
		catch (JfException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>