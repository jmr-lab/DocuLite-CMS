<?php
/**
 * An Estancia system object.
 *
 * This is the base type for all system objects stored in the repository.
 *
 * @package		com.core.object
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfSysObject extends JfPersistentObject
{
	/**
	 * Adds a new rendition to the object. This operation is not committed until a save or checkin is performed.
	 *
	 * The following code example demonstrates how to add a Word Perfect rendition to the sysobject :
	 *
	 * $sysObj = $sess->getObjectByQualification("dm_document where r_object_id='0900d5bb8001f900'");
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * $sysObj->addRendition("chap_1.wp7", "wp7");
	 * $sysObj->save();
	 *
	 *
	 * @access public
	 * @param String fileName - specifies the file that contains the content.
	 * @param String formatName - specifies the content's file format.
	 * @throws JfException - if a server error occurs
	 */
	public function addRendition($fileName, $formatName)
	{
		try
		{
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Attaches a business policy object to the object.
	 *
	 * Business policies define object life cycles.
	 * Each business policy defines a series of states (such as review or approved) through which an object progresses.
	 * A policy includes requirements that must be met before the object can move from one state to the next,
	 * actions to be performed before the object enters a state, and postprocessing to perform after entering a new state.
	 *
	 * The following code example demonstrates how to attach a business policy object to a sysobject
	 * where the initial state of the lifecycle is defined as "preliminary":
	 *
	 * $busPolicyObj = $sess->getObject(new JfId("0900d5bb8001fd49"));
	 * $busPolicyObj = JfUtils::cast($busPolicyObj, 'JfSysObject');
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001f900"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * $sysObj->attachPolicy($busPolicyObj->getObjectId(), "preliminary", "");
	 *
	 * @access public
	 * @param JfId policyId - the object id of the business policy object.
	 * @param String state - the state of the business policy. The state can be the position of the state or the state name.
	 * @param String scope - is the key to the dm_alias_set type for retrieving the alias value mapping defined there.
	 * @throws JfException - if a server error occurs
	 */
	public function attachPolicy($policyId, $state = '', $scope = '')
	{
		try
		{
			$this->setValue('r_policy_id', $policyId->getId());
			// Default : set the current state to 0, bypass the $state argument value
			$this->setValue('r_current_state', '0');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Creates and checks out a new version of an object as a "branch".
	 *
	 * @access public
	 * @param String versionLabel - specifies the object version from which you want to branch.
	 * @return JfId the object id of the new version of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function branch($versionLabel)
	{
		try
		{
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Removes an intention lock without saving any changes that may have been made to the locked object.
	 *
	 * Executing cancelCheckout clears the object's r_lock_machine attribute
	 * (when an object is checked out, this attribute is set to the name of the client machine on which the user who locked the object was working).
	 *
	 * The following code example demonstrates how to cancel a checked out object:
	 *
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001fd49"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * if ($sysObj->isCheckedOut())
	 * {
	 * 		$sysObj->cancelCheckout();
	 * }
	 *
	 * @access public
	 * @throws JfException - if a server error occurs
	 */
	public function cancelCheckout()
	{
		try
		{
			$this->revert();
			$this->setValue('r_lock_machine', '');
			$this->setValue('r_lock_date', '');
			$this->setValue('r_lock_owner', '');
			$this->save();
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Creates a new version of this object and removes the lock from the previous version.
	 *
	 * To use the checkin method, the following conditions must be met:
	 *
	 * The object must have been retrieved from the repository with a checkout method.
	 * The user must have at least Version permission on the object.
	 * The user must have at least Write permission on the cabinet or folder in which the object is stored if the repository is running with folder security. 
	 *
	 * Note: Folder security is on by default when a repository is configured.
	 * The folder security setting is recorded in the folder_security attribute of the docbase config object.
	 *
	 * The checkin method unlocks an object and saves any changes you may have made to either its attributes or its content.
	 * (Note that to make changes to the content, you must issue explicit methods, such as getFile and setFile,
	 * to get and set the content after you checkout the object and before you issue the checkin).
	 *
	 * Executing this method clears the object's r_lock_machine, r_lock_owner, and r_lock_date attributes and sets the a_archive attribute to FALSE.
	 * Additionally, if the object is a policy object, the method sets the r_definition_state to DRAFT for the new version and clones the following attributes:
	 *
	 * entry_criteria_id
	 * user_criteria_id
	 * action_object_id
	 * user_action_id
	 * type_override_id 
	 *
	 * The primary content associated with the new version is stored in the storage area specified in default_storage attribute
	 * of the content's dm_format object if that attribute is set. If the storage area is not defined in the format object,
	 * the content is stored in the storage area identified in the default_storage attribute of the object's object type.
	 * You can override either default storage area by executing an explicit method to set the a_storage_type attribute of the object before the check in.
	 *
	 * The following code example demonstrates how to checkin an object using the default version labels, and to not retain a lock on the newly created version:
	 *
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001fd49"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * if ($sysObj->isCheckedOut())
	 * {
	 * 		$sysObj->setFile($sysObj->getObjectName());
	 * 		$newSysObjId = $sysObj->checkin(false, "");
	 * }
	 *
	 * @access public
	 * @param boolean keepLock - set to true to place a lock on the newly created version.
	 * @param String versionLabels - defines the version label for the new version.
	 * You can specify more than one label. If you do not define a label, the server automatically gives the new version
	 * an implicit version label and the symbolic label "CURRENT".
	 * @return JfId the object id of the new version of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function checkin($keepLock = false, $versionLabels = '')
	{
		try
		{
			// Clone the current object ($this)
			$newObj = clone $this;
			// Get a new object Id
			$newObjId = JfUtils::getNewId($session, $this->getValue('r_object_type'));
			// Get some old values
			$oldObjId = $this->getValue('r_object_id');
			$oldChronicleObjId = $this->getValue('i_chronicle_id');
			$lockMachine = $this->getValue('r_lock_machine');
			$lockDate = $this->getValue('r_lock_date');
			$lockOwner = $this->getValue('r_lock_owner');
			// Save the current object and remove the lock on it
			$this->cancelCheckout();
			// Set the new object Id on the new object
			$newObj->attrValue['r_object_id'] = $ewObjId;
			// Reset some values
			$newObj->setValue('i_vstamp', '0');
			// Set version related attributes
			$newObj->setValue('i_antecedent_id', $oldObjId);
			$newObj->setValue('i_chronicle_id', $oldChronicleObjId);
			// Eventually keep lock
			if ($keepLock)
			{
				$newObj->setValue('r_lock_machine', $lockMachine);
				$newObj->setValue('r_lock_date', $lockDate);
				$newObj->setValue('r_lock_owner', $lockOwner);
			}
			// Save the new object
			$newObj->save();
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Places a lock on the object.
	 *
	 * The user executing the method must have at least Version permission on the object being checked out.
	 * Executing checkout sets the r_lock_machine attribute for the checked out object.
	 * This attribute records the machine name of the client machine on which the user is working.
	 *
	 * The following code example demonstrates how to checkout an object and copy the file from the Estancia server to the local drive (current working directory):
	 *
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001fd49"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * if ($sysObj->isCheckedOut())
	 * {
	 * 		$sysObj->checkout();
	 * 		$sysObj->getFile($sysObj->getObjectName());
	 * }
	 *
	 * @access public
	 * @throws JfException - if a server error occurs
	 */
	public function checkout()
	{
		try
		{
			$session = $this->getSession();
			$this->setValue('r_lock_machine', $session->getLoginUserName());
			$this->setValue('r_lock_date', date("Y-m-d H:i:s"));
			$this->setValue('r_lock_owner', $session->getLoginUserId());
			$this->save();
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * This method will demote the sysobject from its current normal state to the previous normal state,
	 * or to the base state if the toBase  parameter is set to true.
	 * If state is not null, the current state should be the state right after the state specified.
	 *
	 * You cannot use this method in a user-defined transaction. 
	 *
	 * @access public
	 * @param String state - the state to which the sysobject will be demoted. This can be the name of the state or position number.
	 * @param boolean toBase - set this to true if sysobject is to be demoted to the base state.
	 * If the state is specified with this flag as true, the state should be the base state.
	 * @throws JfException - if a server error occurs
	 */
	public function demote($state = '', $toBase = false)
	{
		try
		{
			// if $toBase has been set, then the new state will be 0 (base state)
			if ($toBase)	{$state = 0;}
			// if $state is undefined then take the previous state
			if ($state == '')	{$state = $this->getValue('r_current_state') - 1;}
			// if $state is defined and greater than the previous state then take the previous state
			if ($state >= $this->getValue('r_current_state'))	{$state = $this->getValue('r_current_state') - 1;}
			// if $state is negative then take the base state (0)
			if ($state < 0)	{$state = 0;}
			// Set the new current state
			$this->setValue('r_current_state', $state);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Destroys all versions of the object. You must have DELETE permission for the original version of the object.
	 *
	 * @access public
	 * @throws JfException - if a server error occurs
	 */
	public function destroyAllVersions()
	{
		try
		{
			if (!in_array('jm_sysobject', $this->getSuperTypeNames()))	{return $this->destroy();}
			$query = new JfQuery();
			// Update the single attribute values
			$sql = "UPDATE jm_sysobject_sp OBJECTS SET i_is_deleted = '1' WHERE i_chronicle_id  =  '".$this->getValue('i_chronicle_id')."'";
			$query->setSQL($sql);
			$query->execute($this->getSession());
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Detaches an existing business policy object from the object.
	 *
	 * The following code example demonstrates how to detach a business policy object from a sysobject;
	 *
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001f900"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * $sysObj->detachPolicy();
	 *
	 * @access public
	 * @throws JfException - if a server error occurs
	 */
	public function detachPolicy()
	{
		try
		{
			$this->setValue('r_policy_id', '0000000000000000');
			// Reset the current state to 0
			$this->setValue('r_current_state', '0');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Marks an object as unchangeable (and optionally its components), if the object is a virtual document.
	 *
	 * @access public
	 * @param boolean freezeComponents - Indicates whether you want to freeze the components that make up the specified object's assembly as well as the object itself.
	 * Set this flag to true to freeze the assembled components as well as the object.
	 * This flag is only set if the specified object is a virtual document that has an associated assembly.
	 * @throws JfException - if a server error occurs
	 */
	public function freeze($freezeComponents = false)
	{
		try
		{
			$this->setValue('r_frozen_flag', true);
			$this->setValue('r_immutable_flag', true);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the date the content of this object was last accessed. This method returns the value of the r_access_date attribute of the object.
	 *
	 * @access public
	 * @return String the value of the r_access_date attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getAccessDate()
	{
		try
		{
			return $this->getValue('r_access_date');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns an ACL associated with the object. It is based on the getACLId.
	 *
	 * @access public
	 * @return JfACL an ACL associated with the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getACL()
	{
		try
		{
			return new JfACL($this->getACLId());
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the name of the associated ACL. This method returns the value of the acl_id attribute of the object.
	 *
	 * @access public
	 * @return JfId the value of the acl_id attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getACLId()
	{
		try
		{
			return new JfId($this->getValue('acl_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the object id of the object's parent. This method returns the value of the i_antecedent_id attribute of the object.
	 *
	 * @access public
	 * @return JfId the i_antecedent_id attribute of the object
	 * @throws JfException - if a server error occurs
	 */
	public function getAntecedentId()
	{
		try
		{
			return new JfId($this->getValue('i_antecedent_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the a_application_type attribute of the object.
	 *
	 * @access public
	 * @return String the a_application_type attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getApplicationType()
	{
		try
		{
			return $this->getValue('a_application_type');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the authors attribute at the specified index.
	 *
	 * @access public
	 * @param int index - specifies the index at which the author is placed.
	 * @return String the authors attribute at the specified index.
	 * @throws JfException - if a server error occurs
	 */
	public function getAuthors($index)
	{
		try
		{
			return $this->getRepeatingValue('authors', $index);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the number of values the authors attribute has.
	 *
	 * @access public
	 * @return int the number of values the authors attribute has.
	 * @throws JfException - if a server error occurs
	 */
	public function getAuthorsCount()
	{
		try
		{
			return $this->getValueCount('authors');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the object id of the cabinet that is the object's primary storage location. 
	 *
	 * @access public
	 * @return JfId the value of the i_cabinet_id attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getCabinetId()
	{
		try
		{
			return new JfId($this->getValue('i_cabinet_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the object id of the root object in the version tree.
	 *
	 * @access public
	 * @return JfId the value of the i_chronicle_id attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getChronicleId()
	{
		try
		{
			return new JfId($this->getValue('i_chronicle_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Copies this object's content from the server into a String.
	 * The following code example demonstrates how to copy an objects content from the server into memory :
	 * 
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001f900"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * $strContent = $sysObj->getContent();
	 *
	 * @access public
	 * @return a string containing the objects content.
	 * @throws JfException - if a server error occurs
	 */
	public function getContent()
	{
		try
		{
			if ($this->getPermit() < 3)	{throw new JfException ('OBJECT_INVALID_ACCESS');}
			$file = _DOCUMENT_ROOT_.'/data/content_storage_01/'.$this->content;
			if (!file_exists($file))	{return 'FILE_MISSING';}
			return file_get_contents($file);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the object id of the content object for an object that has only one content.
	 *
	 * @access public
	 * @return JfId the value of the i_contents_id attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getContentsId()
	{
		try
		{
			return new JfId($this->getValue('i_contents_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the number of bytes in the content.
	 *
	 * @access public
	 * @return long the value of the r_content_size attribute of the object. 
	 * @throws JfException - if a server error occurs
	 */
	public function getContentSize()
	{
		try
		{
			return $this->getValue('r_content_size');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the file format of the object's content.
	 *
	 * @access public
	 * @return String the value of the a_content_type attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getContentType()
	{
		try
		{
			return $this->getValue('a_content_type');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the r_creation_date attribute of the object. 
	 *
	 * @access public
	 * @return String the r_creation_date attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getCreationDate()
	{
		try
		{
			return $this->getValue('r_creation_date');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the name of the creator.
	 *
	 * @access public
	 * @return JfId the value of the r_creator_id attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getCreatorId()
	{
		try
		{
			return new JfId($this->getValue('r_creator_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the current state.
	 *
	 * @access public
	 * @return int the value of the r_current_state attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getCurrentState()
	{
		try
		{
			return $this->getValue('r_current_state');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Retrieves a content file from the server.
	 *
	 * Content files associated with objects are ordered within the object.
	 * The position of any particular content file within an object is determined by its page number.
	 * The first content associated with an object has the page number zero, and the page numbers of subsequent contents increment by one with each content.
	 * When you want to obtain any content file other than the first, whose page number is zero, you must use the method getFileEx.
	 *
	 * The following code example demonstrates how to checkout an object and copy the file from the server to the local drive (current working directory):
	 *
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001fd49"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * if (!$sysObj->isCheckedOut())
	 * {
	 * 		$sysObj->checkout();
	 * 		$sysObj->getFile($sysObj->getObjectName());
	 * }
	 *
	 * @access public
	 * @param String fileName - specifies the location where you want to put the copy of the retrieved content file.
	 * If set to null, the content will be placed in the temporary area.
	 * @return String the file location of the retrieved file.
	 * @throws JfException - if a server error occurs
	 */
	public function getFile($fileName = '')
	{
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// If $fileName is null then the content will be placed in the temporary area :
			// JcLogger::info('fileName : '.$fileName);
			$permit = $this->getPermit();
			// JcLogger::info('Permit : '.$permit);
			if ($permit < 3)	{throw new JfException ('OBJECT_INVALID_ACCESS');}
			if ($fileName == '')
			{
				$session = $this->getSession();
				// Create a temporary area associated with the user Id
				$userFolder = '/temp/'.$session->getLoginUserId();
				if (!file_exists(_SERVER_ROOT_.$userFolder))	{mkdir(_SERVER_ROOT_.$userFolder);}
				// Remove invalid characters from fileName
				$invalid = array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
				$fileName = str_replace($invalid, '', $this->getValue('object_name'));
				// $fileName = '/temp/11001e240200108d/mydocument.docx'
				$extension = JfUtils::getDOSExtension($this->content);
				if (strtoupper(substr($fileName, -strlen($extension))) == strtoupper($extension))	{$extension = '';}
				else	{$extension = '.'.$extension;}
				$fileName = $userFolder.'/'.$fileName.$extension;
				// JcLogger::info('fileName (1) : '.$fileName);
			}
			if ($this->content == '')	{throw new JfException('FILE_ERROR');}
			$source = _DOCUMENT_ROOT_.'/data/content_storage_01/'.$this->content;
			$target = _SERVER_ROOT_.$fileName;
			// JcLogger::info('source : '.$source);
			// JcLogger::info('target : '.$target);
			if (!file_exists($source))	{throw new JfException('FILE_MISSING');}
			else if (file_exists($target) && md5($source) == md5($target))	{return $fileName;}
			if (copy($source, $target))	{return $fileName;}
			else	{throw new JfException('FILE_ERROR');}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			// JcLogger::warning('Exception : '.$exception->getMessage());
			JfLogger::warning(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.' : Content missing : '.$this->content.')');
			throw $exception;
		}
	}

	/**
	 * Returns the object id of the folder linked to this object at the given index.
	 *
	 * @access public
	 * @param int index - specifies the index at which the folder id is placed.
	 * @return JfId the value of the i_folder_id attribute of the object given the specified index.
	 * @throws JfException - if a server error occurs
	 */
	public function getFolderId($index)
	{
		try
		{
			return new JfId($this->getRepeatingValue('i_folder_id', $index));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the number of folders linked to the object.
	 *
	 * @access public
	 * @return int the number of folders linked to the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getFolderIdCount()
	{
		try
		{
			return $this->getValueCount('i_folder_id');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns whether the document is marked for full-text indexing.
	 *
	 * @access public
	 * @return boolean the value of the a_full_text attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getFullText()
	{
		try
		{
			return $this->getValue('a_full_text');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the group name to which this object belongs.
	 *
	 * @access public
	 * @return JfId the value of the group_id attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getGroupId()
	{
		try
		{
			return new JfId($this->getValue('group_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the object-level permission assigned to the group for this object.
	 *
	 * @access public
	 * @return int the value of the group_permit attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getGroupPermit()
	{
		try
		{
			return $this->getValue('group_permit');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns whether any users have registered to receive events for the object.
	 *
	 * @access public
	 * @return boolean true if users have registered to receive events for the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getHasEvents()
	{
		try
		{
			return $this->getValue('r_has_events');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns whether this object is the CURRENT object in the version tree.
	 *
	 * @access public
	 * @return boolean the value of the i_has_folder attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getHasFolder()
	{
		try
		{
			return $this->getValue('i_has_folder');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the implicit version label from r_version_label.
	 * For example, if the version labels for an object are "1.2,CURRENT", the string "1.2" will be returned.
	 *
	 * @access public
	 * @return String the implicit version label from r_version_label.
	 * @throws JfException - if a server error occurs
	 */
	public function getImplicitVersionLabel()
	{
		try
		{
			return $this->getRepeatingValue('r_version_label', 0);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the keywords attribute at the specified index.
	 *
	 * @access public
	 * @param int index - specifies the index at which the keyword is placed. 
	 * @return String the keywords attribute at the specified index.
	 * @throws JfException - if a server error occurs
	 */
	public function getKeywords($index)
	{
		try
		{
			return $this->getRepeatingValue('keywords', $index);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the number of keywords for the object.
	 *
	 * @access public
	 * @return int the number of values the keywords attribute has.
	 * @throws JfException - if a server error occurs
	 */
	public function getKeywordsCount()
	{
		try
		{
			return $this->getValueCount('keywords');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Indicates whether this version is the most recent version of the object on a particular branch in the version tree.
	 *
	 * @access public
	 * @return boolean the value of the i_latest_flag attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getLatestFlag()
	{
		try
		{
			return $this->getValue('i_latest_flag');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the number of objects linked to the object.
	 *
	 * @access public
	 * @return int the value of the r_link_cnt attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getLinkCount()
	{
		try
		{
			return $this->getValue('r_link_cnt');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Returns the current maximum order number assigned to a component.
	 *
	 * @access public
	 * @return int the value of the r_link_high_cnt attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getLinkHighCount()
	{
		try
		{
			return $this->getValue('r_link_high_cnt');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the date this object was locked.
	 *
	 * @access public
	 * @return String the value of the r_lock_date attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getLockDate()
	{
		try
		{
			return $this->getValue('r_lock_date');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the name of the client machine on which the object was locked.
	 *
	 * @access public
	 * @return String the value of the r_lock_machine attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getLockMachine()
	{
		try
		{
			return $this->getValue('r_lock_machine');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the Id of the user who locked the object.
	 *
	 * @access public
	 * @return JfId the value of the r_lock_owner attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getLockOwner()
	{
		try
		{
			return new JfId($this->getValue('r_lock_owner'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the comment specified by the user.
	 *
	 * @access public
	 * @return String the value of the log_entry attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getLogEntry()
	{
		try
		{
			return $this->getValue('log_entry');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the Id of the user who made the last modification.
	 *
	 * @access public
	 * @return JfId the value of the r_modifier_id attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getModifierId()
	{
		try
		{
			return new JfId($this->getValue('r_modifier_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the r_modify_date attribute of the object.
	 *
	 * @access public
	 * @return String the value of the r_modify_date attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getModifyDate()
	{
		try
		{
			return $this->getValue('r_modify_date');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the object_name attribute of the object.
	 *
	 * @access public
	 * @return int the value of the object_name attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getObjectName()
	{
		try
		{
			return $this->getValue('object_name');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the Id of this object's owner.
	 *
	 * @access public
	 * @return JfId the value of the owner_id attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getOwnerId()
	{
		try
		{
			return new JfId($this->getValue('owner_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the object-level permission assigned to the owner of the object.
	 *
	 * @access public
	 * @return int the value of the owner_permit attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getOwnerPermit()
	{
		try
		{
			return $this->getValue('owner_permit');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
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
	public function getPermit()
	{
		try
		{
			// @todo
			if ($this->permit == '')	{$this->permit = parent::getPermit();}
			// JcLogger::info('JfSysobject->getPermit() : '.$this->permit);
			return $this->permit;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the object id of attached business policy object.
	 *
	 * @access public
	 * @return JfId the object id of attached business policy object.
	 * @throws JfException - if a server error occurs
	 */
	public function getPolicyId()
	{
		try
		{
			return new JfId($this->getValue('r_policy_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the number of folder references made by the object.
	 *
	 * @access public
	 * @return int the value of the i_reference_cnt attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getReferenceCount()
	{
		try
		{
			return $this->getValue('i_reference_cnt');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the resume state.
	 *
	 * @access public
	 * @return int the value of the r_resume_state attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getResumeState()
	{
		try
		{
			return $this->getValue('r_resume_state');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the subject attribute of the object. 
	 *
	 * @access public
	 * @return String the value of the subject attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getSubject()
	{
		try
		{
			return $this->getValue('subject');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the title attribute of the object. 
	 *
	 * @access public
	 * @return String the value of the title attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getTitle()
	{
		try
		{
			return $this->getValue('title');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the r_object_type attribute of the object. 
	 *
	 * @access public
	 * @return String the value of the r_object_type attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getTypeName($type = '')
	{
		try
		{
			return $this->getValue('r_object_type');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the version label at the specified index.
	 *
	 * @access public
	 * @param int index - specifies the index at which the version label is placed.
	 * @return String the value of the r_version_label attribute of the object at the given index.
	 * @throws JfException - if a server error occurs
	 */
	public function getVersionLabel($index)
	{
		try
		{
			return $this->getRepeatingValue('r_version_label', $index);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the number of version labels. For example, the version label "1.0,CURRENT" will yield a count of 2.
	 *
	 * @access public
	 * @return int the number of version labels.
	 * @throws JfException - if a server error occurs
	 */
	public function getVersionLabelCount()
	{
		try
		{
			return $this->getValueCount('r_version_label');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Runs a query to find all the versions of the object and returns the query results as a collection.
	 * The attrNames parameter is used to specify the "select" portion of the query.
	 * If attrNames  is null, the following default attributes are used:
	 * r_version_label, r_modify_date, r_modifier, log_entry, object_name, r_object_type, a_content_type, r_lock_owner, r_link_cnt, r_object_id.
	 *
	 * Note: For reasons of backward compatibility, the executed query contains the following clause:
	 * "ORDER BY r_modify_date DESC, r_object_id".
	 * If the caller of this method overrides the default set of attributes by passing a non-null value for attrNames, the query may fail if:
	 *
	 * 1) the passed-in set of attributes does not include both r_modify_date and r_object_id AND
	 * 2) the distinct_query_results flag has been set to true in the server.ini file of the underlying repository.
	 *
	 * The default value for the distinct_query_results flag is false, so this is not normally a problem.
	 * However, if you are overriding the default set of attributes, it is recommended that you include the attributes r_modify_date and r_object_id
	 * so that the code is portable across docbases regardless of how they are configured.
	 *
	 * @access	public
	 * @param	String			attrNames - specifies a comma-delimited list of the desired attributes. If set to null, the query uses a default set of attributes.
	 * @return	JfCollection	a collection of JfTypedObject objects for all the versions of the object.
	 * @throws	JfException		if a server error occurs
	 */
	public function getVersions($attrNames = '')
	{
		try
		{
			// Init the session
			$session = $this->getSession();
			// Init the attribute names to return
//			if ($attrNames == '')	{$attrNames = 'r_version_label, r_modify_date, r_modifier, log_entry, object_name, r_object_type, a_content_type, r_lock_owner, r_link_cnt, r_object_id';}
			if ($attrNames == '')	{$attrNames = 'r_modify_date, r_modifier, log_entry, object_name, r_object_type, a_content_type, r_lock_owner, r_link_cnt, r_object_id';}
			// Prepare the query
			$query = new JfQuery();
			$sql = "SELECT ".$attrNames." FROM jm_sysobject_sp WHERE i_is_deleted = false AND i_chronicle_id  =  '".$this->getValue('i_chronicle_id')."' ORDER BY r_modify_date DESC, r_object_id";
			$query->setSQL($sql);
			return $query->execute($session);
			// if ($this->getObjectType() <> 'jm_document')
			// {
				// $versions[] = $this->getObjectId();
				// return $versions;
			// }

			// $attrNames = 'attr1,attr2,attr3...';
			// $attr_names = explode (',', $attrNames);	// $attr_names[0]='attr1', $attr_names[1]='attr2', ...
			// $i = 0;
			// $query = new JfQuery();
			// $sql = 'SELECT '.$attrNames.' FROM jm_document_sp WHERE i_is_deleted = false AND i_chronicle_id  =  \''.$this->getValue('i_chronicle_id').'\' ORDER BY r_modify_date ASC';
			// $query->setSQL($sql);
			// @todo
			// while($data = $query->execute())
			// {
				// foreach ($attr_names as $key=>$value)
				// {
					// $versions[$i][trim($value)] = $data[trim($value)];
				// }
				// $i++;
			// }
			// if (sizeof($versions) == 0)	{$versions[] = $this->getObjectId();}
			// return $versions;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns an array of workflows the document currently participates in.
	 * The attributes in the returned collection are r_workflow_id plus any additional attributes passed in as an argument.
	 *
	 * @access public
	 * @param String additionalAttributes - for the query string, and is in the form of a comma delimited string
	 * @param String orderBy - contains the attribute name to be used for the order by clause.
	 * @return array workflows the document currently participates in.
	 * @throws JfException - if a server error occurs
	 */
	public function getWorkflows($additionalAttributes, $orderBy)
	{
		try
		{
			// @todo
//			return $this->getValue('title');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the object-level permission assigned to the world for the object.
	 *
	 * @access public
	 * @return int the value of the world_permit attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function getWorldPermit()
	{
		try
		{
			return $this->getValue('world_permit');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns the a_archive attribute of the object.
	 *
	 * @access public
	 * @return boolean the value of the a_archive attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function isArchived()
	{
		try
		{
			return $this->getValue('a_archive');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns whether this object is checked out. 
	 *
	 * @access public
	 * @return boolean true if this object is checked out.
	 * @throws JfException - if a server error occurs
	 */
	public function isCheckedOut()
	{
		try
		{
			$flag = false;
			if (($this->getValue('r_lock_owner') <> '') && ($this->getValue('r_lock_owner') <> NULL))	{$flag = true;}
			return $flag;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns whether this object is checked out by the specified user.
	 * If the specified user i.e. userId is null or an empty JfId, the user Id from the current session is used.
	 *
	 * @access public
	 * @param JfId userId - identifies the Id of the user.
	 * @return boolean true if this object is checked out by the specified user.
	 * @throws JfException - if a server error occurs
	 */
	public function isCheckedOutBy($userId = '')
	{
		try
		{
			$flag = false;
			$session = $this->getSession();
			if (get_class($userId) <> 'JfId')	{$userId = $session->getLoginUserId();}
			if ($this->getValue('r_lock_owner') == $userId->getId())	{$flag = true;}
			return $flag;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns whether this object was specifically frozen and is now unchangeable.
	 *
	 * @access public
	 * @return boolean the value of the r_frozen_flag attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function isFrozen()
	{
		try
		{
			return $this->getValue('r_frozen_flag');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Indicates if this object is visible to the end users.
	 *
	 * @access public
	 * @return boolean the value of the a_is_hidden attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function isHidden()
	{
		try
		{
			return $this->getValue('a_is_hidden');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns whether this object can be changed.
	 *
	 * @access public
	 * @return boolean the value of the r_immutable_flag attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function isImmutable()
	{
		try
		{
			return $this->getValue('r_immutable_flag');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns whether the object is public or not.
	 *
	 * @access public
	 * @return boolean the value of the r_is_public attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function isPublic()
	{
		try
		{
			return $this->getValue('r_is_public');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Returns whether the object is a reference object.
	 *
	 * @access public
	 * @return boolean the value of the i_is_reference attribute of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function isReference()
	{
		try
		{
			return $this->getValue('i_is_reference');
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Associates the object with a folder or cabinet.
	 *
	 * The first execution of link for an object defines the object's primary link, the place where the object is stored in the repository.
	 * Subsequent executions link the object to other folders or cabinets. These are called secondary links.
	 *
	 * Executing link has the following permission requirements:
	 *
	 * To create a primary link, the user must have at least Write permission on the object and Change Location permission on the object.
	 * To create a secondary link, the user must have at least Browse permission on the object and Change Location permission for the object.
	 * If the repository is running under folder security, the user must also have at least Write permission on the folder or cabinet
	 * to which the object is being linked. 
	 *
	 * You cannot execute link against a cabinet object. That is, cabinets cannot be linked to folders or other cabinets.
	 * A link operation is not committed until a save or checkin is performed.
	 *
	 * The following code example demonstrates how to create a document and link it to a specific cabinet and folder:
	 *
	 * $sysObj = $sess->newObject("jm_document");
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * $sysObj->setObjectName("testDoc");
	 * $sysObj->setContentType("crtext");
	 * $sysObj->setFile("c:\\New Text Document.txt");
	 * $sysObj->link("/Temp/Examples");
	 * $sysObj->save();
	 *
	 * @access public
	 * @param String folderSpec - Defines the folder or cabinet to which you want to link the object.
	 * You can use either the folder or cabinet's object ID or its folder path.
	 * @throws JfException - if a server error occurs
	 */
	public function link($folderSpec)
	{
		try
		{
			// @todo - check permissions on current object and on folder
			// and allow the ability to use the folder path.
			if (get_class($folderSpec) == 'JfId' && $this->findValue('i_folder_id', $folderSpec->getId()) == -1)	{$this->setRepeatingValue('i_folder_id', $this->getValueCount('i_folder_id'), $folderSpec->getId());}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Assigns one or more symbolic version labels to an object.
	 *
	 * Before you can use the mark method, you must checkout the specified object.
	 * After you execute the mark method, you must checkin or save the object to keep the new label or labels.
	 *
	 * Note: If the object is a virtual document, you must use checkin the the object; you cannot use save to make the changes effective.
	 *
	 * The following code example demonstrates how to create a document and assign a symbolic version label.
	 * Assuming the prior version label is "1.0,CURRENT", the code below will set it to "1.1,APPROVED,CURRENT":
	 *
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001fd49"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * if ($sysObj->isCheckedOut())
	 * {
	 * 		$sysObj->checkout();
	 * }
	 * $sysObj->mark("APPROVED");
	 * $sysObj->checkin(false, "");
	 *
	 * @access public
	 * @param String versionLabels - defines the label that you want to assign to the object.
	 * You can specify one implicit label and/or one or more symbolic labels.
	 * @throws JfException - if a server error occurs
	 */
	public function mark($versionLabels)
	{
		try
		{
			// Set the label to CURRENT except for newly created objects (NEW)
			$label = ($this->status == 1) ? 'NEW' : 'CURRENT';
			if ($this->getValueCount('r_version_label') == 0)
			{
				$this->setRepeatingValue('r_version_label', '0', '1.0');
				$this->setRepeatingValue('r_version_label', '1', $label);
			}
			else if ($this->getValueCount('r_version_label') == 1)
			{
				$this->setRepeatingValue('r_version_label', '1', $label);
			}
			$index = $this->getValueCount($versionLabels);
			$this->setRepeatingValue('r_version_label', $index, $versionLabels);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Promotes the sysobject to the state specified in the state argument.
	 *
	 * @access public
	 * @param String state - is the state to which sysobject is to be promoted. If empty, the object will be promoted to the next normal state.
	 * @param boolean override - optionally, set this to true to force the sysobject to be promoted, regardless of other conditions.
	 * @param boolean fTestOnly - set this to true to test if the promote can be done. This will not actually promote the object.
	 * @throws JfException - if a server error occurs
	 */
	public function promote($state = '', $override = false, $fTestOnly = false)
	{
		try
		{
			// if $state is undefined then take the previous state
			if ($state == '')	{$state = $this->getValue('r_current_state') + 1;}
			// if $state is defined and greater than the previous state then take the previous state
			if ($state <= $this->getValue('r_current_state'))	{$state = $this->getValue('r_current_state') + 1;}
			// if $state is zero or negative then take to the first state (1)
			if ($state < 1)	{$state = 1;}
			// Set the new current state
			$this->setValue('r_current_state', $state);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Places the object on a specified queue.
	 *
	 * The following code example demonstrates how to queue an item into a user's inbox :
	 *
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001fd49"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * $userObj = $sess->getObjectByQualification("jm_user where user_name='Jean-Marie'");
	 * $userId = $userObj->getObjectId();
	 * $inboxId = $sysObj->queue($userId, "EventName", 1, false, "08/09/2000", "Please review.");
	 *
	 * @access public
	 * @param JfId queueOwner - identifies the queue where you want to place the object. Specify the user Id of the queue's owner.
	 * @param String event - provides information to be interpreted by the application about the specified object.
	 * @param int priority - defines an application- or user-interpreted priority level for the queued item.
	 * @param boolean sendMail - directs the server to send an electronic message to the queue's owner.
	 * @param String dueDate - a date for the completion of the work represented by the queued object.
	 * @param String message - defines a message to the owner of the queue on which you are placing the task. 
	 * @return JfId returns the id of an inbox object.
	 * @throws JfException - if a server error occurs
	 */
	public function queue($queueOwner, $event, $priority, $sendMail, $dueDate, $subject, $message)
	{
		try
		{
			// @todo
			$session = $this->getSession();
			$toUsers[] = $queueOwner;
			$objectIDs[] = $this->getObjectId();
			return $session->sendToDistributionListEx($toUsers, null, $subject, $message, $objectIDs, $priority, $flags);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Assigns an ACL object to the object.
	 *
	 * An ACL is assigned to an object when it is created.
	 * The ACL can remain with the object for the life of the object, or you can replace the ACL with a call to setACL.
	 *
	 * The following code example demonstrates how to assign a private ACL to an object, then reset the object back to the original ACL settings :
	 *
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001fd49"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * JfACL $oldACL = $sysObj->getACL();
	 * JfACL $newACL = $sess->getACL(new JfId("4500d5bb8001gd54"));
	 * $sysObj->setACL($newACL);
	 * $sysObj->save();
	 * $sysObj->setACL($oldACL);
	 * $sysObj->save();
	 *
	 * @access public
	 * @param JfACL acl - the ACL object to assign to the sysobject.
	 * @throws JfException - if a server error occurs
	 */
	public function setACL($acl)
	{
		try
		{
			$this->setValue('acl_id', $acl->getObjectId()->getId());
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the a_application_type attribute of the object.
	 *
	 * @access public
	 * @param String type - specifies the application type.
	 * @throws JfException - if a server error occurs
	 */
	public function setApplicationType($type)
	{
		try
		{
			$this->setValue('a_application_type', $type);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the a_archive attribute of the object.
	 *
	 * @access public
	 * @param boolean archived - specifies the object has been archived.
	 * @throws JfException - if a server error occurs
	 */
	public function setArchived($archived)
	{
		try
		{
			$this->setValue('a_archive', $archived);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the authors attribute at a specified index.
	 *
	 * @access public
	 * @param int index - specifies the index at which the author is placed
	 * @param String author - specifies the author at the specified index
	 * @throws JfException - if a server error occurs
	 */
	public function setAuthors($index, $author)
	{
		try
		{
			$this->setRepeatingValue('authors', $index, $author);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the content file of this object or replaces an existing content.
	 * This operation is not committed until a save or a checkin is performed.
	 *
	 * The following code example demonstrates how to create a document:
	 *
	 * $sysObj = $sess->newObject("jm_document");
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * $sysObj->setObjectName("testDoc");
	 * $sysObj->setContentType("crtext");
	 * $sysObj->setFile("c:\\textdoc.txt");
	 * $sysObj->save();
	 *
	 * @access public
	 * @param String fileName - the file that contains the content.
	 * @throws JfException - if a server error occurs
	 */
	public function setFile($fileName)
	{
		try
		{
			// JcLogger::info('setFile->fileName : '.$fileName);
			$this->content = $fileName;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets whether the document is marked for full-text indexing.
	 * This method sets the value of the a_full_text attribute of the object.
	 *
	 * @access public
	 * @param boolean fullText - specifies whether the document is marked for full-text indexing.
	 * @throws JfException - if a server error occurs
	 */
	public function setFullText($fullText) 
	{
		try
		{
			$this->setValue('a_full_text', $fullText);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Set the group Id to which this object belongs.
	 * This method sets the value of the group_id attribute of the object.
	 *
	 * @access public
	 * @param JfId groupId - specifies the group Id to which this object belongs.
	 * @throws JfException - if a server error occurs
	 */
	public function setGroupId($groupId)
	{
		try
		{
			$this->setValue('group_id', $groupId->getId());
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the object-level group permission for the object.
	 * This method sets the value of the group_permit attribute of the object.
	 *
	 * @access public
	 * @param int permit - specifies the object-level group permission to set.
	 * @throws JfException - if a server error occurs
	 */
	public function setGroupPermit($permit)
	{
		try
		{
			$this->setValue('group_permit', $permit);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the visibility of the object for end users.
	 * This method sets the a_is_hidden attribute of the object.
	 *
	 * @access public
	 * @param boolean isHidden - specifies the visibility of the object for end users.
	 * @throws JfException - if a server error occurs
	 */
	public function setHidden($isHidden)
	{
		try
		{
			$this->setValue('a_is_hidden', $isHidden);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the r_is_virtual_doc attribute for the object.
	 * Note that clearing this flag will not autmatically cause isVirtualDocument to return false;
	 * isVirtualDocument will return true for as long as there are containment children.
	 *
	 * @access public
	 * @param boolean is_virtual_doc - If true, the document will be treated as a virtual document even if it has no children.
	 * If false, the document will not be treated as a virtual document ONLY if it has no children.
	 * @throws JfException - if a server error occurs
	 */
	public function setIsVirtualDocument($is_virtual_doc)
	{
		try
		{
			$this->setValue('r_is_virtual_doc', $is_virtual_doc);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the keywords attribute at a specified index.
	 *
	 * @access public
	 * @param int index - specifies the index at which the keyword is placed.
	 * @param String keyword - specifies the keyword at the specified index.
	 * @throws JfException - if a server error occurs
	 */
	public function setKeywords($index, $keyword)
	{
		try
		{
			$this->setRepeatingValue('keywords', $index, $keyword);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the a_link_resolved attribute of the object.
	 *
	 * @access public
	 * @param boolean linkResolved - specifies the object has been archived.
	 * @throws JfException - if a server error occurs
	 */
	public function setLinkResolved($linkResolved)
	{
		try
		{
			$this->setValue('a_link_resolved', $linkResolved);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the comment specified by the user.
	 * This method sets the value of the log_entry attribute of the object.
	 *
	 * @access public
	 * @param String logEntry - specifies the user-defined comment
	 * @throws JfException - if a server error occurs
	 */
	public function setLogEntry($logEntry)
	{
		try
		{
			$this->setValue('log_entry', $logEntry);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the object_name attribute of the object.
	 *
	 * @access public
	 * @param String objectName - specifies the name of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function setObjectName($objectName)
	{
		try
		{
			$this->setValue('object_name', $objectName);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the Id of the object's owner.
	 * This method sets the value of the owner_id attribute of the object.
	 *
	 * @access public
	 * @param JfId ownerId - specifies the Id of the object's owner.
	 * @throws JfException - if a server error occurs
	 */
	public function setOwnerId($ownerId)
	{
		try
		{
			$this->setValue('owner_id', $ownerId);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Assigns the object-level permission to the owner of the object.
	 * This method sets the value of the owner_permit attribute of the object.
	 *
	 * @access public
	 * @param int permit - specifies the object-level permission to the owner of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function setOwnerPermit($permit)
	{
		try
		{
			$this->setValue('owner_permit', $permit);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the resolution label for the object.
	 * This sets the value of the resolution_label attribute of the object.
	 *
	 * @access public
	 * @param String label - specifies the resolution label for the object.
	 * @throws JfException - if a server error occurs
	 */
	public function setResolutionLabel($label)
	{
		try
		{
			$this->setValue('resolution_label', $label);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the a_status attribute of the object.
	 *
	 * @access public
	 * @param String status - specifies status.
	 * @throws JfException - if a server error occurs
	 */
	public function setStatus($status)
	{
		try
		{
			$this->setValue('a_status', $status);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the subject attribute of the object.
	 *
	 * @access public
	 * @param String subject - specifies the subject of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function setSubject($subject)
	{
		try
		{
			$this->setValue('subject', $subject);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the title attribute of the object.
	 *
	 * @access public
	 * @param String title - specifies the title of the object.
	 * @throws JfException - if a server error occurs
	 */
	public function setTitle($title)
	{
		try
		{
			$this->setValue('title', $title);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Sets the object-level permission assigned to the world for the object.
	 * This method sets the value of the world_permit attribute of the object.
	 *
	 * @access public
	 * @param int permit - specifies the object-level permission assigned to the world for the object.
	 * @throws JfException - if a server error occurs
	 */
	public function setWorldPermit($permit)
	{
		try
		{
			$this->setValue('world_permit', $permit);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Unfreezes a frozen object, and optionally if the object is a virtual document, it unfreezes the object's associated assembly.
	 *
	 * @access public
	 * @param boolean thawComponents - Indicates whether you want to unfreeze the assembly associated with the specified object.
	 * Set this to true to unfreeze the assembly.
	 * @throws JfException - if a server error occurs
	 */
	public function unfreeze($thawComponents)
	{
		try
		{
			$this->setValue('r_frozen_flag', false);
			$this->setValue('r_immutable_flag', false);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Removes a link between the object and a folder or cabinet.
	 *
	 * Executing unlink has the following permission requirements:
	 *
	 * To unlink an object from a primary link, the user must have at least Write permission on the object and Change Location permission on the object.
	 * To unlink an object from a secondary link, the user must have at least Browse permission on the object and Change Location permission for the object.
	 * If the repository is running under folder security, the user must also have at least Write permission on the folder or cabinet from which
	 * the object is being unlinked. 
	 *
	 * The folders and cabinets to which an object is linked are recorded in the repeating attribute i_folder_id.
	 * The first recorded folder or cabinet (in i_folder_id[0]) represents the object's primary link.
	 * The folders or cabinets recorded in the remaining index positions in the attribute represent the object's secondary links.
	 *
	 * When you unlink an object, the entry in the attribute for the specified folder or cabinet is removed
	 * and the entries in subsequent index positions are shifted up one position.
	 *
	 * Documents and folders must have at least one link to a folder or cabinet.
	 * Consequently, if you unlink an object from its only link, you must relink it to some folder or cabinet before saving or checking in the object.
	 *
	 * This operation is not committed until a save or a checkin is performed.
	 * The following code example demonstrates how unlink an object from a source cabinet and relink it to a target cabinet:
	 *
	 * $sysObj = $sess->getObject(new JfId("0900d5bb8001fd49"));
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * $sysObj->unlink("/DFCSourceCab");
	 * $sysObj->link("/DFCTargetCab");
	 * $sysObj->save();
	 *
	 * @access public
	 * @param String folderSpec - Defines the folder or cabinet from which you want to unlink the object.
	 * You can use either the folder or cabinet's object ID or its folder path.
	 * @throws JfException - if a server error occurs
	 */
	public function unlink($folderSpec)
	{
		try
		{
			// @todo - check permissions on current object and on folder
			// and allow the ability to use the folder path.
			if (get_class($folderSpec) == 'JfId')
			{
				for ($i = 0; $i < $this->getValueCount('i_folder_id'); $i++)
				{
					if ($this->getRepeatingValue('i_folder_id', $i) == $folderSpec->getId())	{$this->remove('i_folder_id', $i);}
				}
			}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	 /**
	 * Removes symbolic labels from an object.
	 *
	 * @access public
	 * @param String versionLabels - Defines the label that you want to remove from the object.
	 * You can specify one implicit label and/or one or more symbolic labels.
	 * @throws JfException - if a server error occurs
	 */
	public function unmark($versionLabels)
	{
		try
		{
			for ($i = 0; $i < $this->getValueCount('r_version_label'); $i++)
			{
				if ($this->getRepeatingValue('r_version_label', $i) == $versionLabels && $i >= 2)	{$this->remove('r_version_label', $i);}
			}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}
}
?>