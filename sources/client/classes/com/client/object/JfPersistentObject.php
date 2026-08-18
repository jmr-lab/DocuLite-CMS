<?php
/**
 * An Estancia persistent object.
 *
 * This is the base type for all objects stored in the repository.
 *
 * @package		com.core.object
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfPersistentObject extends JfTypedObject
{
	/**
	* If this object has been called internally
	*
	* @TODO		Check if it is really needed in this class
	* @access	protected
	* @var		Boolean
	*/
	private $internal;

	/**
	 * Constructor
	 *
	 * This function initialize the content variable
	 * @todo	Check if input is a valid array for this class in case it is not a string
	 *
	 * @param	JfSession	The session object that called this class.
	 * @param	Value		The input value can be either a string (object ID) or an array of values
	 * @throws	JfException	If a server error occurs
	 */
	function __construct($session, $input, $internal = 'false')
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'($session, $input)');
		// Set the internal value
		$this->internal = $internal;
		// Call to parent method
		parent::__construct($session, $input);
		// Get the content file
		if ($this->getValue('i_contents_id') <> '' && $this->getValue('a_content_type') <> '')
		{
			$dos_extension = '';
			$query = new JfQuery();
			$sql = 'SELECT dos_extension FROM jm_format_sp WHERE name =  \''.$this->getValue('a_content_type').'\' ';
			$query->setSQL($sql);
			$col = $query->execute($this->getSession());
			while($col->next())	{$dos_extension = $col->getValue('dos_extension');}
//			if ($dos_extension <> '')	{$this->content = $this->getValue('i_contents_id').'.'.$dos_extension;}
			if ($dos_extension == '')	{$dos_extension = $this->getValue('a_content_type');}
			$this->content = $this->getValue('i_contents_id').'.'.$dos_extension;
		}
		if ($this->content <> '')	{$this->orig_content = $this->content;}
	}

	/**
	 * Removes the object from the Documentum server.
	 *
	 * This method does not destroy multiple versions of an object, only the object pertaining to the instantiated persistent object. 
	 *
	 * @access public
	 * @throws JfException - if a server error occurs
	 */
	public function destroy()
	{
		try
		{
			// Be sure that we don't destroy an object containing other objects (such as a group or folder containing other groups, users, folders or documents)
			// Case 1 : jm_folder or jm_cabinet
			if (in_array(substr($this->getValue('r_object_id'), 0, 2), array('0b', '0c')))
			{
				$sql = "SELECT r_object_id FROM jm_sysobject_r WHERE i_folder_id = '".$this->getValue('r_object_id')."'";
				$query = new JfQuery();
				$query->setSQL($sql);
				$results = $query->execute($this->getSession());
				if ($query->getResultCount() > 0)	{throw new JfException('SYSOBJECT_NOT_EMPTY');}
			}
			// Case 2 : jm_group
			else if (in_array(substr($this->getValue('r_object_id'), 0, 2), array('12')))
			{
				$sql = "SELECT users_ids, groups_ids FROM jm_group_r WHERE r_object_id = '".$this->getValue('r_object_id')."'";
				$query = new JfQuery();
				$query->setSQL($sql);
				$results = $query->execute($this->getSession());
				$userId = ''; $groupId = '';
				while ($results->next())
				{
					if (!in_array($results->getValue('users_ids'), array('', 'NULL')))	{$userId = $results->getValue('users_ids');}
					if (!in_array($results->getValue('groups_ids'), array('', 'NULL')))	{$groupId = $results->getValue('groups_ids');}
				}
				if ($userId <> '' || $groupId <> '')	{throw new JfException('GROUP_NOT_EMPTY');}
			}

			// Delete single attribute values
			$sql = "DELETE FROM ".$this->getValue('r_object_type')."_s WHERE r_object_id = '".$this->getValue('r_object_id')."'";
			$query = new JfQuery();
			$query->setSQL($sql);
			$result = $query->execute($this->getSession());

			// Delete repeating attribute values
			$sql = "DELETE FROM ".$this->getValue('r_object_type')."_r WHERE r_object_id = '".$this->getValue('r_object_id')."'";
			$query = new JfQuery();
			$query->setSQL($sql);
			$result = $query->execute($this->getSession());

			// Reset the status of the current object
			$this->status = 4;

			// Create a fetch event for the object
			$session = $this->getSession();
			$user = $session->getLoginInfo();
			$auditTrailMgr = $session->getAuditTrailManager();
			$stringArgs = array(	'userName' => $user->getValue('user_name'),
									'userIP' => getenv("REMOTE_ADDR")	);
			$auditTrailMgr->createAudit($this->getObjectId()->toString(), 'delete', $stringArgs, null);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Fetches this object from the repository without placing a lock on the object.
	 *
	 * Use this method to ensure that you are working with the most recent version of the object.
	 * You must have at least BROWSE permission on an object to call the fetch method.
	 *
	 * Without a lock, there is no guarantee that you will be able to save any changes you make to the object
	 * since another user may checkout the object while you have it fetched. If you fetch an object,
	 * you cannot use the checkin method to write the object back to the repository. You must use the save method.
	 *
	 * @access public
	 * @param typeNameIgnored - The type name argument is ignored because the type name was set or determined at creation of the
	 * PersistentObject and is therefore already known. Overriding the value here would just cause an error. The parameter still
	 * exists for backward compatability, but should be specified as null.
	 * @return boolean
	 * @throws JfException - if a server error occurs
	 */
	public function fetch($typeNameIgnored = '')
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Throw an error if no object type has been defined
			if ($this->getValue('r_object_type') == '')	{throw new JfException ('OBJECT_INVALID_OBJECT_TYPE');}
			$objectType = $this->getValue('r_object_type');
			// Throw an error if we are in creation mode
			if ($this->status == 1)	{throw new JfException ('OBJECT_INVALID_OPERATION');}
			// Get single values
			$query = new JfQuery();
			$sql = 'SELECT * FROM '.$objectType.'_sp WHERE r_object_id =  \''.$this->getValue('r_object_id').'\' ';
			$query->setSQL($sql);
			$col = $query->execute($this->getSession());
			while ($col->next())
			{
				// $arrValues = (array) $col->getTypedObject();
				// $attrSingleValues = $arrValues['attrValue'];
				// foreach ($arrValues as $key => $value)	{JcLogger::info('arrValues['.$key.'] : '.$value);}
				$this->attrValue = $col->getResult();
			}
			$this->attrValue['r_object_type'] = $objectType;
			// Set the original attribute values
			$this->orig_attrValue = $this->attrValue;
			// Throw an exception if the user is not permitted to access the object
			if ($this->getPermit() == 1)	{throw new JfException ('OBJECT_INVALID_ACCESS');}
			// Get repeating values
			// Check if table exists
			$sql = "SHOW TABLES LIKE '".$this->getValue('r_object_type')."_rp'";
			$query = new JfQuery();
			$query->setSQL($sql);
			$query->execute($this->getSession());
			if ($query->getResultCount() == 0)	{return;}
			$query = new JfQuery();
			$sql = "SELECT * FROM ".$this->getValue('r_object_type')."_rp WHERE r_object_id =  '".$this->getValue('r_object_id')."' ORDER BY i_position DESC";
			$query->setSQL($sql);
			// For each record
			$col = $query->execute($this->getSession());
			// $col = array(
			//		'0' => array('r_object_id' => '0900000000000000', 'i_position' => -1, , 'i_folder_id' => '0b00000000000000', 'r_version_label' => '1.0'),
			//		'1' => array('r_object_id' => '0900000000000000', 'i_position' => -2, , 'i_folder_id' => '0b00000000000000', 'r_version_label' => 'CURRENT')
			//					);
			while ($col->next())
			{
				foreach ((array) $col->getResult() as $attrName=>$attrValue)
				{
					// Only set the value if it is not the r_object_id AND it is not null or empty
					if ($attrName <> 'r_object_id' && $attrValue <> '' && $attrValue <> 'NULL')	$this->r_attrValue[$attrName][] = $attrValue;
					else if ($attrValue == '' && !isset($this->r_attrValue[$attrName]))	{$this->r_attrValue[$attrName] = array();}
				}
			}
			// Set the original attribute values
			$this->orig_r_attrValue = $this->r_attrValue;
			// Create a fetch event if the object has not been fetched internally
			if ($this->internal == 'true')	{return;}
			$session = $this->getSession();
			$auditTrailMgr = $session->getAuditTrailManager();
			$user = $session->getLoginInfo();
			$stringArgs = array(	'userName' => $user->getValue('user_name'),
									'userIP' => getenv("REMOTE_ADDR")	);
			$auditTrailMgr->createAudit($this->getValue('r_object_id'), 'fetch', $stringArgs, null);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Get attributes.
	 *
	 * For example jm_document will return the following array :
	 * $attr_single = array("r_object_id", "object_name", "owner_name", ...);
	 * $attr_repeating = array("keywords", "i_folder_id", ...);
	 * $attributes = array("single" => $attr_single, "repeating" => $attr_repeating);
	 *
	 * @access private
	 * @param type_name - the name of the type
	 * @return array - a list of attributes.
	 * @throws JfException - if a server error occurs
	 */
	private function getAttributes($type_name)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$type_name.')');
		try
		{
			$attributes = array();
			// Run the query
			$query = new JfQuery();
			$sql = 'SELECT attr_name, attr_repeating FROM jm_type_s, jm_type_r WHERE jm_type_s.r_object_id = jm_type_r.r_object_id AND jm_type_s.name = \''.$type_name.'\' AND i_position < -start_pos';
			$query->setSQL($sql);
			// for each record
			$col = $query->execute($this->getSession());
			while ($col->next())
			{
				if ($col->getValue('attr_repeating') == 0)	{$attr_single[] = $col->getValue('attr_name');}
				else	{$attr_repeating[] = $col->getValue('attr_name');}
			}
			if (isset($attr_single))	{$attributes['single'] = $attr_single;}
			if (isset($attr_repeating))	{$attributes['repeating'] = $attr_repeating;}
			// Check that the attribute list is an array
			if (!is_array($attributes))	{throw new JfException ('OBJECT_SAVE_ERROR');}
			// Return the attribute list
			return $attributes;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Get the maximum number of attribute values.
	 *
	 * @access private
	 * @return int the maximum number of repeating attribute values.
	 * @throws JfException - if a server error occurs
	 */
	private function getMaxAttrValueCount()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			$nbMax = 0;
			foreach ($this->r_attrValue as $attrName=>$attrValue)
			{
				if ($attrName <> 'i_position')	{$nbMax = max($nbMax, $this->getValueCount($attrName));}
			}
			return $nbMax;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	 /**
	 * Returns an integer number that corresponds to the access permission level that the current user has for the object.
	 *
	 * @access public
	 * @return int the access permission level that the current user has for the object.
	 * @throws JfException - if a server error occurs
	 */
	protected function getPermit()
	{
		try
		{
			// Initialise the permit to 1 (No permission on the object)
			$this->permit = 1;
			// In creation mode we have DELETE access to the object
			if ($this->status == 1)	{return ($this->permit = 7);}
			$objectId = $this->getValue('r_object_id');
//			$userId = $session->getLoginUserId();
			// Get session
			$session = $this->getSession();
			$user = $session->getLoginInfo();
//			$userId = $user->getValue('r_object_id');
			// Users or Groups
			if (in_array(substr($objectId, 0, 2), array('11', '12')))
			{
				// TODO - we should allow coordinators to only delete coordinators, and sysadmin to not delete user jmadmin
				if ($user->getValue('client_capability') > 2)	{$this->permit = 7;}
				// A consumer or contributor can only read info on other users
				else if ($user->getValue('client_capability') <= 2)
				{
					$query = new JfQuery();
//					JcLogger::info('User Id : '.$user->getValue('r_object_id'));
					$sql = "SELECT r_object_id FROM jm_group_s
							WHERE (is_private = false OR (is_private = true AND owner_name = '".$user->getValue('user_name')."'))
								AND r_object_id = '".$objectId."'
							UNION SELECT r_object_id FROM jm_user_s
							WHERE (r_object_id = '".$user->getValue('r_object_id')."' OR r_object_id IN
								(SELECT child_id FROM jm_relation_s WHERE parent_id = '".$user->getValue('r_object_id')."'
								UNION SELECT parent_id FROM jm_relation_s WHERE child_id = '".$user->getValue('r_object_id')."'))
								AND r_object_id = '".$objectId."'";
					$query->setSQL($sql);
					// For each record
					$results = $query->execute($session);
//						foreach ($results as $key=>$value)	{JcLogger::info('User Object Id : '.$value['r_object_id']);}
					if ($query->getResultCount() == 1)	{$this->permit = 3;}
				}
			}
			// Formats, ACLs
			else if (in_array(substr($objectId, 0, 2), array('27', '45', '4d')))
//			else if (substr($objectId, 0, 2) == '45')
			{
				// sysadmin can delete all ACLs
				if ($user->getValue('client_capability') > 4)	{$this->permit = 7;}
				// A consumer or contributor can only read ACLs infos
				else	{$this->permit = 3;}
			}
			// Mails
			else if (in_array(substr($objectId, 0, 2), array('1b')))
			{
				// sysadmin can delete all mails
				if ($user->getValue('client_capability') > 4)	{$this->permit = 7;}
				// A consumer or contributor can only read mails infos
				else	{$this->permit = 3;}
			}
			else
			{
				$query = new JfQuery();
//				JcLogger::info('User Id : '.$user->getValue('r_object_id'));
				$sql = "SELECT jm_sysobject_s.r_object_id, owner_name, r_accessor_permit
						FROM	jm_sysobject_s,
							(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
							FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit
						WHERE	jm_sysobject_s.r_object_id = '".$objectId."'
							AND jm_sysobject_s.acl_id = table_permit.acl_id";
				$query->setSQL($sql);
				$results = $query->execute($session);
//				foreach ($results as $key=>$value)	{JcLogger::info('User Object Id : '.$value['r_object_id']);}
				if ($query->getResultCount() == 1)
				{
					$owner = '';$permit = 1;
					while ($results->next())	{$owner = $results->getValue('owner_name');$permit = $results->getValue('r_accessor_permit');}
					if ($owner == $user->getValue('user_name'))	{$permit = 7;}
					$this->permit = $permit;
				}
//				$this->permit = 7;
			}
			return $this->permit;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Get super type names.
	 *
	 * For example jm_document will return the following array :
	 * "jm_document", "jm_sysobject"
	 *
	 * @access private
	 * @return array - a list of type names.
	 * @throws JfException - if a server error occurs
	 */
	protected function getSuperTypeNames()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__."()");
		try
		{
			$super_name = $this->getValue('r_object_type');
			$flag = true;
			while (trim($super_name) <> '' && $flag)
			{
				$type_s[] = $super_name;
				$query = new JfQuery();
				// Generate SQL query
				$sql = 'SELECT super_name FROM jm_type_s WHERE name = \''.$super_name.'\'';
				$query->setSQL($sql);
				// Get super type name
				$col = $query->execute($this->getSession());
				while ($col->next())	{$super_name = $col->getValue('super_name');}
				// If for some reason the super name hasn't changed then break the loop
				if ($super_name == end($type_s))	{$flag = false;}
			}
			if (!$flag)	{throw new JfException ('OBJECT_INFINITE_LOOP');}
			return $type_s;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns true if this object has been deleted during this session.
	 * This method will not return true if the object was deleted on another session or by another user.
	 * In addition, if this method is called on a Sysobject and that object is the root version of a version tree, deleting this object sets this attribute to true. 
	 *
	 * @access public
	 * @return boolean - true if this object has been deleted during this session; false if it is not.
	 * @throws JfException - if a server error occurs
	 */
	public function isDeleted()
	{
		try
		{
			$flag = false;
			if ($this->status == 4)	{$flag = true;}
			return $flag;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}

	}

	/**
	 * Indicates whether unsaved changes have been made to this object. 
	 *
	 * @access public
	 * @return boolean - true if unsaved changes have been made to the object; false if not.
	 * @throws JfException - if a server error occurs
	 */
	public function isDirty()
	{
		try
		{
			$flag = false;
			if (is_array(array_diff($this->r_attrValue, $this->orig_r_attrValue)))	{$flag = true;}
			else if (is_array(array_diff($this->attrValue, $this->orig_attrValue)))	{$flag = true;}
			return $flag;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}

	}

	/**
	 * Indicates whether this object was created during the current session but has not yet been saved. 
	 *
	 * @access public
	 * @return boolean - true if the object was created during the current session but has not yet been saved.
	 * false if the object was created during the current session and saved, or if it was not created during the current session.
	 * @throws JfException - if a server error occurs
	 */
	public function isNew()
	{
		try
		{
			$flag = false;
			if ($this->status == 1)	{$flag = true;}
			return $flag;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}

	}

	/**
	 * Discards any changes to an object that have not been saved to a repository.
	 *
	 * @access public
	 * @throws JfException - if a server error occurs
	 */
	public function revert()
	{
		try
		{
			// Revert the attribute values
			$this->attrValue = $this->orig_attrValue;
			$this->r_attrValue = $this->orig_r_attrValue;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Saves an object to the content server.
	 *
	 * You must have WRITE permission on an object to call the save method.
	 *
	 * The save is generally used to save a newly created object in the content server.
	 * You can also use it when you want to save changes to an object without creating a new version.
	 * This method overwrites the previously saved version with the local copy of the object.
	 *
	 * This method might fail if you fetched the object from the content server rather than checked it out.
	 * Fetching an object does not place a lock on the object, and other users may have checked out or fetched and saved the object while you were working on it.
	 *
	 * Note that you cannot save a load record object while an explicit transaction is open. 
	 *
	 * @access public
	 * @throws JfException - if a server error occurs
	 */
	public function save()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Only save the object if it has been modified
			if ($this->content == $this->orig_content && !$this->modified)	return;	
			// Get the session
			$session = $this->getSession();
			// Start Transaction
			$session->beginTrans();
			// Check all values
			$this->saveCheckValues();
			// Get super type names
			$type_list = $this->getSuperTypeNames();
			// If the current object is a group we have to save it in a special way (because of the i_all_users_ids and i_all_supergroups_ids attributes)
			if (in_array('jm_group', $type_list))	{return $this->saveGroup($type_list);}
			// Only administrators can save users or groups
			// TODO - currently anyone can save subtype of users or groups
			$user = $session->getLoginInfo();
			$capability = $user->getValue('client_capability');
			if (in_array($this->getValue('r_object_type'), array('jm_group', 'jm_user')))	{if ($capability < 8)	throw new JfException('INVALID_USER_CAPABILITY');}
			// Prepare all values
			$this->savePrepareValues($type_list);
			// If a file has been associated with this object, save it to the repository
			if ($this->content <> $this->orig_content && in_array('jm_sysobject', $type_list))	{$this->saveFile();}
			// For each type get a list of attributes (repeating and non-repeating)
			foreach ($type_list as $key=>$type_name)
			{
				// JcLogger::info('Key : '.$key.' - Type name : '.$type_name);
				$attributes = $this->getAttributes($type_name);
				// Save the single values
				if (isset($attributes['single']))	{$this->saveSingleValues($type_name, $attributes['single']);}
				// If no single values defined then assume there is ONLY the r_object_id
				else	{$this->saveSingleValues($type_name, array('r_object_id'));}
				// Save the repeating values
				if (isset($attributes['repeating']))	{$this->saveRepeatingValues($type_name, $attributes['repeating']);}
			}
			// Reset the status of the current object
			$this->status = 3;
			// Finalize the save operation
			if ($this->status == 3)
			{
				// Revert the attribute values
				$this->orig_attrValue = $this->attrValue;
				$this->orig_r_attrValue = $this->r_attrValue;
				// Create a save event if the object has been saved
				$auditTrailMgr = $session->getAuditTrailManager();
				$stringArgs = array(	'userName' => $session->getLoginUserName(),
										'userIP' => getenv("REMOTE_ADDR")	);
				$auditTrailMgr->createAudit($this->getValue('r_object_id'), 'save', $stringArgs, null);
			}
			// Commit Transaction
			$session->commitTrans();
		}
		catch (JfException $exception)
		{
			// RollBack Transaction
			$session->abortTrans();
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Check values.
	 *
	 * This method is called from the main save method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveCheckValues()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$permit = $this->getPermit();
		// JcLogger::info('Permit : '.$permit);
		// Threshold to be able to save a document : 4 ("Annotate")
		if ($this->getPermit() < 3)	{throw new JfException ('OBJECT_INVALID_ACCESS');}
	}

	/**
	 * Save a content file to the repository.
	 *
	 * This method is called from the main save method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveFile()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		/**
		 * TODO
		 *
		 * We should replace the content of this method with the following lines :
		 *
		 * $contentObj = new JfContent($this->getSession());
		 * $contentObj->setContent($this->content);
		 * $contentId = $contentObj->save();
		 * // Now that we have a content Id we can store it in the i_contents_id field
		 * $this->setValue('i_contents_id', $contentId);
		 * // Set new values for a_content_type and r_content_size attributes
		 * $this->setValue('a_content_type', $contentObj->getContentType());
		 * $this->setValue('r_content_size', filesize($this->content));
		 *
		 */
		try
		{
			// The new content is stored in the protected 'content' variable
			// $this->content = '/estancia/temp/Word Document.doc';
			// If there is no content defined, use the old one
			if ($this->content == '')	{$this->content = $this->orig_content; $this->setValue('a_content_type', $this->saveGetContentType());}
			// If the content hasn't been modified, skip this step
			if ($this->content == $this->orig_content)	return;
			// _SERVER_ROOT_.$this->content = 'C:/.../xampplite/htdocs'.'/estancia/temp/Word Document.doc';
			// JcLogger::info("this->content : ".$this->content);
			// JcLogger::info("_SERVER_ROOT_.this->content : "._SERVER_ROOT_.$this->content);
			if (!is_file(_SERVER_ROOT_.$this->content))	{throw new JfException ('OBJECT_SAVE_FILE_DOESNT_EXIST');}
			// Try to find a content file with the same hash (to avoid having the same file twice in the repository)
			$hash = md5_file(_SERVER_ROOT_.$this->content);
			$query = new JfQuery();
			$sql = 'SELECT r_object_id FROM jmr_content_s WHERE r_content_hash = \''.$hash.'\'';
			$query->setSQL($sql);
			$result = $query->execute($this->getSession());
			$contentId = $result->getValue('r_object_id');
			// No content file found. Create a new content object
			if ($contentId == '' || $contentId == null)
			{
				$contentId = JfUtils::getNewId($this->getSession(), 'jmr_content');
				$sql = 'INSERT INTO jmr_content_s (r_object_id, r_content_hash) VALUES (\''.$contentId.'\', \''.$hash.'\')';
				$query->setSQL($sql);
				$result = $query->execute($this->getSession());
				// Copy the content file to the repository
				$target = _DOCUMENT_ROOT_.'/data/content_storage_01/'.$contentId.'.'.JfUtils::getDOSExtension($this->content);
				// @todo - JfFile
				$filemgr = new JfFile();
				$filemgr->move(_SERVER_ROOT_.$this->content, $target);
			}
			// Now that we have a content Id we can store it in the i_contents_id field
			$this->setValue('i_contents_id', $contentId);
			// Set new values for a_content_type and r_content_size attributes
			$this->setValue('a_content_type', $this->saveGetContentType());
			$this->setValue('r_content_size', filesize(_SERVER_ROOT_.$this->content));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Get the content type.
	 *
	 * This method is called from the main save method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveGetContentType()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			$type = $this->getValue('a_content_type');
			// We assume this field has been populated intentionnally
			// so there is no need to retrieve the content type
			if ($type <> '')	{return $type;}
			// Retrieve the content type
			// Get the DOS extension of the current object
			$dos_extension = JfUtils::getDOSExtension($this->content);
			$query = new JfQuery();
			$sql = 'SELECT name, (SELECT COUNT(r_object_id) FROM jm_sysobject_s WHERE a_content_type = name) AS number 
					FROM jm_format_sp 
					WHERE jm_format_sp.dos_extension = \''.$dos_extension.'\' 
					ORDER BY number ASC';
			$query->setSQL($sql);
			$result = $query->execute($this->getSession());
			while ($result->next())	{$type = $result->getValue('name');}
			// If no type was found then return the DOS extension
			if ($type == '')	{$type = $dos_extension;}
			return $type;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Save a group.
	 *
	 * This method is called from the main save method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveGroup($type_list)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Init session for this method
			$session = $this->getSession();
			// Get hidden parameters
			$_modify = $this->getValue('_modify');
			$_direction = $this->getValue('_direction');
			// Default : $modify is true
			if ($_modify == '')	{$_modify = true;}
			// Prepare all values
			if ($_modify)	{$this->savePrepareValues($type_list);}
			// Do not re calculate group specific attributes for 'World'
			if ($this->getValue('group_name') == 'world')	{return $this->saveGroupsDetails($type_list);}
			// Re calculate the i_supergroups_ids attribute
			if ($_direction <> 'up' && $this->status <> '1')
			{
				$this->removeAll('i_supergroups_ids');
				$query = new JfQuery();
				$sql = "SELECT DISTINCT i_supergroups_ids FROM jm_group_rp WHERE i_supergroups_ids IS NOT NULL AND i_supergroups_ids <> '' AND r_object_id IN (SELECT r_object_id FROM jm_group_rp WHERE groups_ids = '".$this->getValue('r_object_id')."') UNION SELECT r_object_id FROM jm_group_rp WHERE groups_ids = '".$this->getValue('r_object_id')."'";
				$query->setSQL($sql);
				$results = $query->execute($session);
				while ($results->next())	{$this->r_attrValue['i_supergroups_ids'][] = $results->getValue('i_supergroups_ids');}
			}
			// Re calculate the i_all_users_ids attribute
			if ($_direction <> 'down' && $this->status <> '1')
			{
				$this->removeAll('i_all_users_ids');
				$query = new JfQuery();
				$sql = 'SELECT DISTINCT i_all_users_ids FROM jm_group_rp WHERE i_all_users_ids IS NOT NULL AND i_all_users_ids <> \'\' AND r_object_id IN (SELECT groups_ids FROM jm_group_rp WHERE r_object_id = \''.$this->getValue('r_object_id').'\') UNION SELECT users_ids AS i_all_users_ids FROM jm_group_rp WHERE users_ids IS NOT NULL AND users_ids <> \'\' AND r_object_id = \''.$this->getValue('r_object_id').'\'';
				$query->setSQL($sql);
				$results = $query->execute($session);
				while ($results->next())	{$this->r_attrValue['i_all_users_ids'][] = $results->getValue('i_all_users_ids');}
				for ($i = 0; $i < $this->getValueCount('users_ids'); $i++)
				{
					$new_user_id = $this->getRepeatingValue('users_ids', $i);
					if (isset($this->r_attrValue['i_all_users_ids']) && !in_array($new_user_id, $this->r_attrValue['i_all_users_ids']))	{$this->r_attrValue['i_all_users_ids'][] = $new_user_id;}
				}
			}
			// Return the Id of the newly created object
			$this->saveGroupsDetails($type_list);
			// Re calculate the i_supergroups_ids attribute for all groups contained in the current group
			if ($_direction <> 'up')
			{
				for ($i = 0; $i < $this->getValueCount('groups_ids'); $i++)
				{
					$groupId = $this->getRepeatingValue('groups_ids', $i);
					if ($groupId <> '' && $groupId <> $this->getValue('r_object_id'))
					{
						$groupObj = new JfGroup($session, new JfId($groupId), 'true');
//						$groupObj = $session->getObject(new JfId($groupId));
						$groupObj->setValue('_modify', false);
						$groupObj->setValue('_direction', 'down');
						$groupObj->save();
					}
				}
			}
			// Re calculate the i_all_users_ids attribute for all groups containing the current group
			if ($_direction <> 'down')
			{
				$query = new JfQuery();
				$sql = "SELECT r_object_id FROM jm_group_rp WHERE groups_ids = '".$this->getValue('r_object_id')."' AND r_object_id <> '".$this->getValue('r_object_id')."' ORDER BY i_position DESC";
				$query->setSQL($sql);
				$results = $query->execute($session);
				while ($results->next())
				{
					$groupObj = new JfGroup($session, new JfId($results->getValue('r_object_id')), 'true');
//					$groupObj = $session->getObject(new JfId($results->getValue('r_object_id')));
					$groupObj->setValue('_modify', false);
					$groupObj->setValue('_direction', 'up');
					$groupObj->save();
				}
			}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Save a group.
	 *
	 * This method is called from the main save method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveGroupsDetails($type_list)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// For each type get a list of attributes (repeating and non-repeating)
			foreach ($type_list as $key=>$type_name)
			{
				$attributes = $this->getAttributes($type_name);
				// Save the single values
				if (isset($attributes['single']))	{$this->saveSingleValues($type_name, $attributes['single']);}
				// Save the repeating values
				if (isset($attributes['repeating']))	{$this->saveRepeatingValues($type_name, $attributes['repeating']);}
			}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Prepare values.
	 *
	 * This method is called from the main save method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function savePrepareValues($arrSuperTypeNames)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Keep the modified attribute
		$modified = $this->modified;
		// Set the session for the current method
		$session = $this->getSession();
		// Set an object ID if there is no one
		if ($this->getValue('r_object_id') == '0000000000000000')	{$this->attrValue['r_object_id'] = JfUtils::getNewId($session, $this->getValue('r_object_type'));}
		// Set the modification date and the creation date if it hasn't been set yet
		$this->setValue('r_modify_date', date("Y-m-d H:i:s"));
		if ($this->getValue('r_creation_date') == '')	{$this->setValue('r_creation_date', date("Y-m-d H:i:s"));}
		// Set the author, the modifier and the owner IDs
		$this->setValue('r_modifier_id', $session->getLoginUserId());
		if ($this->getValue('r_creator_id') == '')	{$this->setValue('r_creator_id', $session->getLoginUserId());}
		if ($this->getValue('owner_id') == '')	{$this->setValue('owner_id', $session->getLoginUserId());}
		// Set the author, the modifier and the owner names (for backward compatibility)
		$user = $session->getLoginInfo();
		$this->setValue('r_modifier', $user->getValue('user_name'));
		if ($this->getValue('r_creator_name') == '')	{$this->setValue('r_creator_name', $user->getValue('user_name'));}
		if ($this->getValue('owner_name') == '')	{$this->setValue('owner_name', $user->getValue('user_name'));}
		// Set an ACL if none has been set
		if ($this->getValue('acl_id') == '')	{$this->setValue('acl_id', $session->getDefaultACL());}
		// Set the version number - only mark the sysobject and its descendants as CURRENT
		if ($this->getRepeatingValue('r_version_label', '0') == '' && in_array('jm_sysobject', $arrSuperTypeNames))
		{
			$this->setRepeatingValue('r_version_label', '0', '1.0');
			$this->setValue('i_chronicle_id', $this->getValue('r_object_id'));
			$this->setValue('i_antecedent_id', $this->getValue('r_object_id'));
		}
		// Bug fix : Be sure that the object does have an i_chronicle_id and an i_antecedent_id attributes
		if ($this->getValue('i_chronicle_id') == '')	{$this->setValue('i_chronicle_id', $this->getValue('r_object_id'));}
		if ($this->getValue('i_antecedent_id') == '')	{$this->setValue('i_antecedent_id', $this->getValue('r_object_id'));}
		// Remove CURRENT from all other versions and mark 'this' as CURRENT
		// only mark the sysobject and its descendants as CURRENT
		// Set the label to CURRENT except for newly created objects (NEW)
		$label = ($this->status == 1) ? 'NEW' : 'CURRENT';
		if ($this->getRepeatingValue('r_version_label', '1') == '' && in_array('jm_sysobject', $arrSuperTypeNames))	{$this->setRepeatingValue('r_version_label', '1', $label);}
		if ($this->getRepeatingValue('r_version_label', '1') == 'CURRENT' && in_array('jm_sysobject', $arrSuperTypeNames) && $this->getValue('i_chronicle_id') <> $this->getValue('r_object_id'))
		{
			$query = new JfQuery();
			$sql = "UPDATE jm_sysobject_r OBJECTS SET r_version_label = 'OLD' WHERE i_position = '-2' AND r_object_id IN (SELECT r_object_id FROM jm_sysobject_s WHERE i_chronicle_id  =  '".$this->getValue('i_chronicle_id')."' AND r_object_id <> '".$this->getValue('r_object_id')."')";
			$query->setSQL($sql);
			$result = $query->execute($session);
		}
		// Groups : if the group_name value is set and not the group_display_name, change it to be the same
		if ($this->getValue('group_name') <> '' && $this->getValue('group_display_name') == '')	{$this->setValue('group_display_name', $this->getValue('group_name'));}
		// Keep the modified attribute
		$this->modified = $modified;
	}

	/**
	 * Save repeating values.
	 *
	 * This method is called from the main save method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveRepeatingValues($type_name, $attr_repeating)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Don't try to save anything if nothing was changed
			// JcLogger::info('Avant : '.md5(serialize($this->orig_r_attrValue)));
			// JcLogger::info('Apres : '.md5(serialize($this->r_attrValue)));
			if (md5(serialize($this->orig_r_attrValue)) == md5(serialize($this->r_attrValue)))	{return;}
			// generate SQL query to retrive the current single values
			$query = new JfQuery();
			$valueIndex = 0;
			$maxAttrValueCount = $this->getMaxAttrValueCount();
			$subsql[] = '';
			// generate SQL query
			$sql = 'SELECT * FROM '.$type_name.'_r WHERE r_object_id =  \''.$this->getValue('r_object_id').'\' ORDER BY i_position DESC';
			$query->setSQL($sql);
			$results = $query->execute($this->getSession());
			// for each record
			while ($results->next())
			{
				$oldValues = $results->getTypedObject();
				// generate SQL query for updating rows
				if ($valueIndex < $maxAttrValueCount)	{$subsql[] = $this->saveRepeatingValuesUpdate($type_name, $attr_repeating, $oldValues, $valueIndex);}
				else	{$subsql[] = $this->saveRepeatingValuesDelete($type_name, $attr_repeating, $oldValues, $valueIndex);}
				$valueIndex++;
			}
			// Sort queries array to run DELETE before UPDATE (bug fix)
			sort($subsql);
			// Save new values
			for($i = $valueIndex; $i < $maxAttrValueCount; $i++)	{$subsql[] = $this->saveRepeatingValuesInsert($type_name, $attr_repeating, null, $i);}
			// Remove empty strings from SQL array
			$subsql = array_merge(array_filter($subsql));
			// Launch queries
			for($i = 0; $i < sizeof($subsql); $i++)
			{
				$query = new JfQuery();
				$query->setSQL($subsql[$i]);
				$result = $query->execute($this->getSession());
//				if (!$result)	{throw new JfException ('OBJECT_SAVE_ERROR');}
			}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Delete repeating values from the database.
	 *
	 * This method is called from the saveRepeatingValues method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveRepeatingValuesDelete($type_name, $attr_repeating, $oldValues, $valueIndex)
	{
		try
		{
			$where = 'r_object_id = \''.$this->getValue('r_object_id').'\' AND i_position = \'-'.(1 + $valueIndex).'\'';
			$sql = 'DELETE FROM '.$type_name.'_r WHERE '.$where;
			return $sql;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Insert repeating values to the database.
	 *
	 * This method is called from the saveRepeatingValues method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveRepeatingValuesInsert($type_name, $attr_repeating, $oldValues, $valueIndex)
	{
		try
		{
			$names = 'r_object_id, i_position, ';
			$values = '\''.$this->getValue('r_object_id').'\', \'-'.(1 + $valueIndex).'\', ';
			foreach ($attr_repeating as $attrName=>$attrValue)
			{
				if ($attrValue <> 'i_position')
				{
					$newValue = addslashes($this->getRepeatingValue($attrValue, $valueIndex));
					if ($newValue <> '')
					{
						$names = $names.$attrValue.', ';
						$values = $values.'\''.$newValue.'\', ';
					}
				}
			}
			$names = substr($names, 0, strlen($names) - 2);
			$values = substr($values, 0, strlen($values) - 2);
			$sql = 'INSERT INTO '.$type_name.'_r ( '.$names.') VALUES ('.$values.')';
			return $sql;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Update repeating values to the database.
	 *
	 * This method is called from the saveRepeatingValues method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveRepeatingValuesUpdate($type_name, $attr_repeating, $oldValues, $valueIndex)
	{
		try
		{
			// JfLogger::info('r_object_id : '.$this->getValue('r_object_id'));
			$sql = '';
			$set = '';
			$where = 'r_object_id = \''.$this->getValue('r_object_id').'\' AND i_position = \'-'.(1 + $valueIndex).'\'';
			foreach ($oldValues as $attrName=>$attrValue)
			{
				$attrNewValue = $this->getRepeatingValue($attrName, $valueIndex);
				// JfLogger::info('attrName : '.$attrName.' - attrValue : '.$attrValue.' - attrNewValue : '.$attrNewValue);
//				if (is_object($attrNewValue))	JcLogger::info('attrName : '.$attrName.'; get_class(attrNewValue) : '.get_class($attrNewValue));
				$forbidden = array('r_object_id', 'i_position');
				if (!in_array($attrName, $forbidden) && $attrNewValue <> $attrValue)
				{
					$set = $set.$attrName.' = \''.addslashes($attrNewValue).'\', ';
				}
			}
			if ($set <> '')	{$set = substr($set, 0, strlen($set) - 2);}
			if ($set <> '')	{$sql = 'UPDATE '.$type_name.'_r OBJECTS SET '.$set.' WHERE '.$where;}
			return $sql;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Save single values.
	 *
	 * This method is called from the main save method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveSingleValues($type_name, $attr_single)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$type_name.', $attr_single)');
		try
		{
			// generate SQL query to retrive the current single values
			$query = new JfQuery();
			$sql = 'SELECT * FROM '.$type_name.'_s WHERE r_object_id =  \''.$this->getValue('r_object_id').'\'';
			$query->setSQL($sql);
			$result = $query->execute($this->getSession());
			$attr_orig = $result->getResult();

			// If the i_vstamp attributes are different then throw an error
			// This error can happen if someone else has recently updated the object
			// JfLogger::info("type_name : ".$type_name);
			// JfLogger::info("attr_orig['i_vstamp'] : ".$attr_orig['i_vstamp']);
			// JfLogger::info("this->getValue('i_vstamp') : ".$this->getValue('i_vstamp'));
			// JfLogger::info("this->status : ".$this->status);
			if (isset($attr_orig['i_vstamp']) && ($attr_orig['i_vstamp'] <> $this->getValue('i_vstamp')) && ($this->status > 2))	{throw new JfException ('OBJECT_SAVE_ERROR');}

			// If the object is new insert new values
			if ($this->isNew())										{$this->saveSingleValuesInsert($type_name, $attr_single);}
			// else update values
			else if (md5(serialize($this->orig_attrValue)) <> md5(serialize($this->attrValue)) && !$this->isNew())
				{$this->saveSingleValuesUpdate($type_name, $attr_single, $attr_orig);}

			// If we don't find anything and the object is not new,
			// or if we find something and the object is new then throw an error
//			else	{throw new JfException ('OBJECT_SAVE_ERROR');}
		}
		catch (JfException $exception)
		{
			JcLogger::info('$exception : '.$exception->getMessage());
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Insert single values to the database.
	 *
	 * This method is called from the saveSingleValues method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveSingleValuesInsert($type_name, $attr_single)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$type_name.', $attr_single)');
		try
		{
			$sql = 'INSERT INTO '.$type_name.'_s ( ';
			$names = 'r_object_id, ';
			$values = '\''.$this->getValue('r_object_id').'\', ';
			foreach ($attr_single as $attr_index=>$attrName)
			{
				// Only set the value if there is something in it
				if ($attrName <> 'r_object_id' && $this->getValue($attrName) <> false)
				{
					$names = $names.$attrName.', ';
					$values = $values.'\''.addslashes($this->getValue($attrName)).'\', ';
				}
			}
			$names = substr($names, 0, strlen($names) - 2);
			$values = substr($values, 0, strlen($values) - 2);
			$sql = $sql.$names.' ) VALUES ('.$values.')';
			$query = new JfQuery();
			$query->setSQL($sql);
			$result = $query->execute($this->getSession());
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Update single values to the database.
	 *
	 * This method is called from the saveSingleValues method.
	 *
	 * @access private
	 * @throws JfException - if a server error occurs
	 */
	private function saveSingleValuesUpdate($type_name, $attr_single, $attr_orig)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$type_name.', $attr_single, $attr_orig)');
		try
		{
			// Update object in the database
			$setstr = '';
			$sql = 'UPDATE '.$type_name.'_s OBJECTS SET ';
			foreach ($attr_single as $attr_index=>$attrName)
			{
				if (isset($attr_orig[$attrName]) && $attr_orig[$attrName] <> $this->getValue($attrName))	{$setstr = $setstr.$attrName.' = \''.addslashes($this->getValue($attrName)).'\', ';}
			}
			// If nothing has been changed then don't update the single values
			if (strlen($setstr) == 0)	{return;}
			$setstr = substr($setstr, 0, strlen($setstr) - 2);
			$sql = $sql.$setstr.' WHERE r_object_id =  \''.$this->getValue('r_object_id').'\'';
			$query = new JfQuery();
			$query->setSQL($sql);
			$result = $query->execute($this->getSession());
			// @todo - check if one record was updated
			// mysql_affected_rows() == 1
			// If result is false then the query was not successful
//			if (!$result)	{throw new JfException ('OBJECT_SAVE_ERROR');}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

}

?>