<?php
/**
 * An Estancia persistent object.
 *
 * This is the base type for all objects stored in the repository.
 *
 * @package com.core.object
 * @author Jean-Marie Roy
 * @copyright Jean-Marie Roy 2011
 * @version 3.0
 */
class JfTypedObject
{
	/**
	* Array containing all single values
	*
	* @access protected
	* @var array
	*/
	protected $attrValue = array();

	/**
	* Path to the content file :
	*
	* The content can be either '06001e2409f212f1.txt' or '/tmp/mydocument.txt'
	*
	* @todo Check if it is really needed in this class
	* @access protected
	* @var String
	*/
	protected $content;

	/**
	* Whether the current object has been modified
	*
	* @todo 	Check if it is really needed in this class
	* @access	protected
	* @var		false
	*/
	protected $modified = false;

	/**
	* Path to the original content file.
	*
	* @access protected
	* @var array
	*/
	protected $orig_content;

	/**
	* Array containing all original single values
	*
	* This variable is used to store all single values
	* and reset the attrValue array in case of a revert call.
	*
	* @access protected
	* @var array
	*/
	protected $orig_attrValue = array();

	/**
	* Array containing all original repeating values
	*
	* This variable is used to store all repeating values
	* and reset the r_attrValue array in case of a revert call.
	*
	* @access protected
	* @var array
	*/
	protected $orig_r_attrValue = array();

	/**
	* User permission on the current object (default to NONE)
	*
	* @todo Check if it is really needed in this class
	* @access protected
	* @var int
	*/
	protected $permit;

	/**
	* Array containing all multiple values
	*
	* @access protected
	* @var array
	*/
	protected $r_attrValue = array();

	/**
	* Session through which the object was originally requested.
	*
	* @todo Check if it is really needed in this class
	* @access protected
	* @var JfSession
	*/
	private $session;

	/**
	* Status of the current object
	*
	* Values can be 0 if the object hasn't been fecthed
	* 1 in creation mode (empty object except for the r_object_type value)
	* 2 if the object has been fetched
	* 3 if the object has been saved
	* 4 if the object has been deleted
	*
	* @todo Check if it is really needed in this class
	* @access protected
	* @var int
	*/
	protected $status = 0;

	/**
	* List of type names by extension
	*
	* An object with a type integer 9 is a jm_document...
	*
	* @todo Check if it is really needed in this class
	* @access protected
	* @var array
	*/
	protected static $typeNames = array(
						'69' => 'jm_acl',						'76' => 'jm_activity',						'81' => 'jm_aggr_domain',
						'102' => 'jm_alias_set',				'20' => 'jm_api',							'13' => 'jm_assembly',
						'95' => 'jm_audit_trail',				'52' => 'jm_blob_ticket',					'64' => 'jm_blobstore',
						'84' => 'jm_buildin_expr',				'109' => 'jm_ca_store_tag',					'12' => 'jm_cabinet',
						'51' => 'jm_change_record',				'4' => 'jm_collection',						'86' => 'jm_cond_expr',
						'87' => 'jm_cond_id_expr',				'5' => 'jm_containment',					'6' => 'jmr_content',
						'106' => 'jm_dd_attr_info',				'104' => 'jm_dd_common_info',				'78' => 'jm_dd_info',
						'105' => 'jm_dd_type_info',				'107' => 'jm_display_config',				'54' => 'jm_dist_comp_record',
						'44' => 'jm_distributedstore',			'60' => 'jm_docbase_config',				'68' => 'jm_docbaseid_map',
						'63' => 'jm_docbroker',					'9' => 'jm_document',						'80' => 'jm_domain',
						'48' => 'jm_dump_object_record',		'47' => 'jm_dump_record',					'29' => 'jm_event',
						'88' => 'jm_expr_code',					'82' => 'jm_expression',					'97' => 'jm_externalstore_file_tag',
						'99' => 'jm_externalstore_free_tag',	'96' => 'jm_externalstore_tag',				'98' => 'jm_externalstore_url_tag',
						'94' => 'jm_federation',				'34' => 'jm_file',							'40' => 'jm_filestore',
						'11' => 'jm_folder',					'101' => 'jm_foreign_key',					'39' => 'jm_format',
						'15' => 'jm_fulltext',					'59' => 'jm_fulltext_index',				'85' => 'jm_func_expr',
						'18' => 'jm_group',						'37' => 'jm_inbox',							'31' => 'jm_index',
						'89' => 'jm_key',						'110' => 'jm_lightweight_tag',				'42' => 'jm_linkedstore',
						'43' => 'jm_linkrecord',				'83' => 'jm_literal_expr',					'50' => 'jm_load_object_record',
						'49' => 'jm_load_record',				'58' => 'jm_location',						'16' => 'jm_method',
						'62' => 'jm_mount_point',				'41' => 'jm_netstore',						'79' => 'jm_nls_dd_info',
						'65' => 'jm_note',						'2' => 'jm_object',							'35' => 'jm_otherfile',
						'23' => 'jm_outputdevice',				'73' => 'jm_package',						'103' => 'jm_plugin',
						'70' => 'jm_policy',					'75' => 'jm_process',						'10' => 'jm_query',
						'27' => 'jmi_queue_item',				'72' => 'jm_recovery',						'71' => 'jm_reference',
						'25' => 'jm_registered',				'38' => 'jm_registry',						'55' => 'jm_relation',
						'56' => 'jm_relationtype',				'66' => 'jm_remotestore',					'67' => 'jm_remoteticket',
						'45' => 'jm_replica_record',			'24' => 'jm_router',						'108' => 'jm_scope_config',
						'32' => 'jm_sequence',					'61' => 'jm_server_config',					'1' => 'jm_session',
						'53' => 'jm_staged_document',			'14' => 'jm_store',							'100' => 'jm_subcontent',
						'8' => 'jm_sysobject',					'33' => 'jm_transaction_log',				'3' => 'jm_type',
						'46' => 'jm_type_info',					'21' => 'jm_type_manager',					'17' => 'jm_user',
						'90' => 'jm_value_assist',				'93' => 'jm_value_func',					'91' => 'jm_value_list',
						'92' => 'jm_value_query',				'28' => 'jm_verity_coll',					'36' => 'jm_verity_index',
						'30' => 'jm_vstamp',					'77' => 'jm_workflow',						'74' => 'jm_workitem',
					);

	/**
	 * Constructor
	 *
	 * This function creates a new object by cloning one existing.
	 *
	 * $newObject = clone $object;
	 *
	 */
	function __clone()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Set r_object_id
		$this->attrValue['r_object_id'] = '0000000000000000';
		// Set r_version_label
		$this->r_attrValue['r_version_label'][0] = '';
		// Set the i_vstamp attribute to 0
		$this->attrValue['i_vstamp'] = 0;
		// The current object is in creation mode
		$this->status = 1;
	}

	/**
	 * Constructor
	 *
	 * This function initialize the protected variables (arrays of single and multiple values)
	 *
	 * by using $input as an array of values :
	 * $attrValues = array("object_name" => "John", "owner_name" => "jmadmin", ...);
	 * $r_attrValues = array("keywords" => array("0" => "Book"), ...);
	 * $object = new JfPersistentObject($session, array("attrValue" => $attrValues, "r_attrValue" => $r_attrValues));
	 *
	 * by querying the database using $input if it is a JfId object.
	 * $object = new JfPersistentObject($session, new JfId('0912345670004567'));
	 *
	 * or by creating a new object if $input is an object type string :
	 * $object = new JfPersistentObject($session, 'jm_document');
	 *
	 * @param	JfSession	The session object that called this class.
	 * @param	Value		The input value can be either a string (object ID) or an array of values
	 * @todo				Check if input is a valid array for this class in case it is not a string
	 * @throws	JfException	if a server error occurs
	 */
	function __construct($session, $input)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'($session, $input)');
		// Check that the session is valid
		if (get_class($session) <> 'JfSession')	{throw new JfException('OBJECT_INVALID_ARGUMENT');}
		// and set the session for this object
		$this->session = $session;

		// Case 1 : the input value is not a string
		// we assume its an array
		if (is_array($input))
		{
			if (isset($input['attrValue']))	{$this->attrValue = $input['attrValue'];}
			else							{$this->attrValue = $input;}
//			$this->attrValue = $input['attrValue'];
			if (isset($input['r_attrValue']))	{$this->r_attrValue = $input['r_attrValue'];}
			// Set the object type from its ID
			if (substr($this->getValue('r_object_id'), 0, 2) == '00')	{$this->attrValue['r_object_id'] = '';}
			if ($this->getValue('r_object_id') <> '' && $this->getValue('r_object_type') == '')
			{
				$objectId = new JfId($this->getValue('r_object_id'));
				$this->attrValue['r_object_type'] = $this->getObjectTypeName($objectId->getTypePart());
			}
			// The current object is considered as fetched
			$this->status = 2;
		}
		// Case 2 : the input value is a JfId object
		// we first check whether it is a valid ID or not
		// then we set the object type.
		else if (is_object($input) && get_class($input) == 'JfId')
		{
			// Check whether input is a valid ID or not
			if (!$input->isObjectId())	{throw new JfException('OBJECT_INVALID_OBJECT_ID');}
			// input is a valid ID
			// Set r_object_id and r_object_type
			$this->attrValue['r_object_id'] = $input->getId();
			$this->attrValue['r_object_type'] = $this->getObjectTypeName($input->getTypePart());
			// Fetch the object
			$this->fetch();
			// Set the status as 'fetched'
			$this->status = 2;
		}
		// Case 3 : the input value is an object type name (ie jm_document, jm_acl, ...)
		// the object doesnot exist yet (creation mode)
		else if (substr($input, 0, 3) == 'jm_' || substr($input, 0, 4) == 'jmi_' || substr($input, 0, 4) == 'jmc_' || substr($input, 0, 4) == 'jmr_')
		{
			// Check if table exists
			$sql = "SHOW TABLES LIKE '".$input."_sp'";
			$query = new JfQuery();
			$query->setSQL($sql);
			$result = $query->execute($session);
			if ($query->getResultCount() == 0)	{throw new JfException('OBJECT_INVALID_OBJECT_TYPE');}
			// Set r_object_id and r_object_type
			$this->attrValue['r_object_id'] = '0000000000000000';
			$this->attrValue['r_object_type'] = $input;
			// Set the i_vstamp attribute to 0
			$this->attrValue['i_vstamp'] = 0;
			// The current object is in creation mode
			$this->status = 1;
		}
		// The input value is not a valid string
		// throw an error
		else	{throw new JfException('OBJECT_INVALID_OBJECT');}

		// @todo check whether fields have been populated correctly
		// r_object_id must have been set and must be a valid ID
		if ($this->getValue('r_object_id') <> '')
		{
			$objectId = new JfId($this->getValue('r_object_id'));
			if (!$objectId->isObjectId() && !$objectId->isNull())	{throw new JfException('OBJECT_INVALID_OBJECT_ID');}
			// r_object_type must have been set and must be a valid type
//			$type = $this->getValue('r_object_type');
//			if (substr($type, 0, 3) <> 'jm_' && substr($type, 0, 4) <> 'jmi_' && substr($type, 0, 4) <> 'jmc_' && substr($type, 0, 4) <> 'jmr_')	{throw new JfException('OBJECT_INVALID_OBJECT_TYPE');}
		}
		// Set the original attribute values
		$this->orig_attrValue = $this->attrValue;
		$this->orig_r_attrValue = $this->r_attrValue;
	}

	/**
	 * Appends a value to a repeating attribute.
	 *
	 * @access public
	 * @param attributeName - the name of the attribute
	 * @param value - the variant value to append.
	 * @throws JfException - if a server error occurs
	 */
	public function appendValue($attributeName, $value)
	{
		try
		{
			// The object has been modified
			$this->modified = true;
			// @todo - fetch the current object if its status is 0 ?
			// if ($this->status == 0)	{$this->fetch();}
			// Append the attribute value
			$this->r_attrValue[$attributeName][] = $value;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Searches through the values in a repeating attribute and returns the index of the first value that matches the specified value.
	 *
	 * @access	public
	 * @return	int				the index of the value or -1 if the value can't be found.
	 * @param	attributeName	the name of the repeating attribute
	 * @param	value			the variant value to locate.
	 * @throws	JfException		if a server error occurs
	 */
	public function findValue($attributeName, $value)
	{
		try
		{
			$index = -1;
			$i = 0;
			while (($i < $this->getValueCount($attributeName)) && ($index == -1))
			{
				if ($this->getRepeatingValue($attributeName, $i) == $value)	{$index = $i;}
				$i++;
			}
			// Return the index if no error occured
			return $index;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns an JfAttr representing the attribute located at the position attrIndex in this object's list of attributes.
	 *
	 * @access	public
	 * @param	index		the index position of the attribute among the object's types
	 * @return	JfAttr		a JfAttr interface to the attribute 
	 * @throws	JfException	if a server error occurs
	 */
	public function getAttr($index)
	{
		try
		{
			// @toto currently returning a String, need to create the JfAttr class
			$arrAttributes = array_keys($this->attrValue);
			return $arrAttributes[$index];
//			return new JfAttr(array_keys($this->attrValue)[$index]);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the number of attributes belonging to this object. 
	 *
	 * @access	public
	 * @return	int			the number of attributes belonging to the calling object 
	 * @throws	JfException	if a server error occurs
	 */
	public function getAttrCount()
	{
		try
		{
			return sizeof($this->attrValue);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the r_object_id attribute of the object as an JfId object.
	 *
	 * @access public
	 * @return JfId the object ID
	 * @throws JfException - if a server error occurs
	 */
	public function getObjectId()
	{
		try
		{
			return new JfId($this->getValue('r_object_id'));
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the value of a repeating attribute as a string.
	 *
	 * You can call this method on a single-valued attribute as long as the valueIndex is 0.
	 * Note that negative values are interpreted as 0. For example, passing -2 for valueIndex will return the value at index 0.
	 *
	 * @access public
	 * @return String the value of the attribute
	 * @param attributeName - the name of the attribute
	 * @param valueIndex - the index position of the value among the values stored in the repeating attribute.
	 * @throws JfException - if a server error occurs
	 */
	public function getRepeatingValue($attributeName, $valueIndex)
	{
		try
		{
			// Fetch the current object if its status is 0
			// if ($this->status == 0)	{$this->fetch();}
			// Prepare the valueIndex
			if ($valueIndex < 0)	{$valueIndex = 0;}
			// Get the attribute value
			if (isset($this->attrValue[$attributeName]) && ($valueIndex == 0))	{$attrValue = $this->attrValue[$attributeName];}
			else if (isset($this->r_attrValue[$attributeName][$valueIndex]))	{$attrValue = $this->r_attrValue[$attributeName][$valueIndex];}
//			else	{throw new JfException ('OBJECT_OBJECT_GET_ATTR_ERROR');}
			else	{$attrValue = '';}
			// Return the value if no error occured
			return $attrValue;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Gets the session through which the object was originally requested. 
	 *
	 * @todo
	 * @access public
	 * @return JfSession - the session on which the object was originally obtained
	 * @throws JfException
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Returns the type of the object based on its number.
	 * For example : getObjectTypeName('9') should return 'jm_document'
	 *
	 * @access private
	 * @param int the type integer (9 for a jm_document)
	 * @return String the type name
	 * @throws JfException
	 */
	private function getObjectTypeName($type)
	{
		if (!isset(self::$typeNames[$type]))	{throw new JfException('OBJECT_INVALID_OBJECT_TYPE');}
		return self::$typeNames[$type];
	}

	/**
	 * Returns the value of an attribute as a string.
	 *
	 * If the attribute attributeName is a repeating attribute, the value at index 0 is returned.
	 * If attributeName is an attribute without any values, false is returned.
	 *
	 * @access public
	 * @return String the value of the attribute
	 * @param attributeName - the name of the attribute
	 * @throws JfException - if a server error occurs
	 */
	public function getValue($attributeName)
	{
		try
		{
			// Fetch the current object if its status is 0
			// if ($this->status == 0)	{$this->fetch();}
			// Get the attribute value
			if (isset($this->attrValue[$attributeName]))	{$attrValue = $this->attrValue[$attributeName];}
			else if (isset($this->r_attrValue[$attributeName][0]))	{$attrValue = $this->r_attrValue[$attributeName][0];}
			else	{$attrValue = false;}
			// Return the value if no error occured
			return $attrValue;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the number of values stored in an attribute.
	 *
	 * This will always return 1 for single-valued attributes, and return a non-negative number for repeating attributes.
	 *
	 * @access public
	 * @return int the number of values stored in the attribute
	 * @param attributeName - the name of the attribute
	 * @throws JfException - if a server error occurs
	 */
	public function getValueCount($attributeName)
	{
		try
		{
			// Fetch the current object if its status is 0
			// if ($this->status == 0)	{$this->fetch();}
			// Get the value count
			if (isset($this->attrValue[$attributeName]))	{$attrValueCount = 1;}
			else if (isset($this->r_attrValue[$attributeName]))	{$attrValueCount = sizeof($this->r_attrValue[$attributeName]);}
			else	{$attrValueCount = 0;}
			// Return the number of values if no error occured
			return $attrValueCount;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Indicates whether an attribute is a repeating attribute.
	 *
	 * @access public
	 * @param attributeName - the name of the attribute
	 * @return boolean - true if the attribute is repeating; false if it is not.
	 * @throws JfException - if a server error occurs
	 */
	public function isAttrRepeating($attributeName)
	{
		try
		{
			// Basic checks
			if (isset($this->r_attrValue[$attributeName]))	{return true;}
			if (isset($this->attrValue[$attributeName]))	{return false;}

			// Need to check the jm_type tables as this attribute is not defined in the current object
			$flag = false;
			$sql = 'SELECT attr_repeating FROM jm_type_s, jm_type_r WHERE jm_type_s.r_object_id = jm_type_r.r_object_id AND jm_type_s.name = \''.$this->getValue('r_object_type').'\' AND jm_type_r.attr_name = \''.$attributeName.'\'';
			$query = new JfQuery();
			$query->setSQL($sql);
			$col = $query->execute($this->session);
			while($col->next())	{$attr_repeating = $$col->getValue('attr_repeating');}
			if ($attr_repeating == 1)	{$flag = true;}
			return $flag;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}

	}

	/**
	 * Removes a value stored in a repeating attribute.
	 *
	 * The remaining rows in the attribute are renumbered.
	 *
	 * @access	public
	 * @param	attributeName	the name of the attribute
	 * @param	valueIndex		the index position where you want to remove a value.
	 * @throws	JfException		if a server error occurs
	 */
	public function remove($attributeName, $valueIndex)
	{
		try
		{
			// The object has been modified
			$this->modified = true;
			// Fetch the current object if its status is 0
			// if ($this->status == 0)	{$this->fetch();}
			// Remove the value
			if (!isset($this->r_attrValue[$attributeName][$valueIndex]))	{throw new JfException ('OBJECT_OBJECT_SET_ATTR_ERROR');}
			unset($this->r_attrValue[$attributeName][$valueIndex]);
			$this->r_attrValue[$attributeName] = array_values($this->r_attrValue[$attributeName]);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Removes all values stored in a repeating attribute.
	 *
	 * @access public
	 * @param attributeName - the name of the attribute
	 * @throws JfException - if a server error occurs
	 */
	public function removeAll($attributeName)
	{
		try
		{
			// The object has been modified
			$this->modified = true;
			// Fetch the current object if its status is 0
			// if ($this->status == 0)	{$this->fetch();}
			// Remove all values
			if (isset($this->r_attrValue[$attributeName]))	{unset($this->r_attrValue[$attributeName]);}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.".".__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Sets a value of a repeating attribute.
	 *
	 * You can call this method on a single-valued attribute as long as the valueIndex is 0.
	 * Note that negative values are interpreted as 0.
	 * For example, passing -1 for valueIndex will set the value at index 0.
	 *
	 * @access public
	 * @param attributeName - the name of the attribute
	 * @param valueIndex - the index position where you want to remove a value.
	 * @param value - the variant value.
	 * @throws JfException - if a server error occurs
	 */
	public function setRepeatingValue($attributeName, $valueIndex, $value)
	{
		try
		{
			// The object has been modified
			$this->modified = true;
			// Throw an error if attributeName is 'r_object_id', 'r_object_type' or 'i_vstamp'
			$forbidden = array('r_object_id', 'r_object_type', 'i_vstamp');
			if (in_array($attributeName, $forbidden))	{throw new JfException ('OBJECT_OBJECT_SET_ATTR_ERROR');}
			// @todo - fetch the current object if its status is 0 ?
			if ($this->status == 0)	{$this->fetch();}
			// Prepare the valueIndex
			if ($valueIndex < 0)	{$valueIndex = 0;}
			// Set the attribute value
			if (isset($this->attrValue[$attributeName]) && ($valueIndex == 0))	{$this->attrValue[$attributeName] = $value;}
			else if (isset($this->attrValue[$attributeName]) && ($valueIndex > 0))	{throw new JfException ('OBJECT_OBJECT_SET_ATTR_ERROR');}
			else if (isset($this->r_attrValue[$attributeName][$valueIndex]))	{$this->r_attrValue[$attributeName][$valueIndex] = $value;}
			else if (isset($this->r_attrValue[$attributeName]) && ($valueIndex >= $this->getValueCount($attributeName)))	{$this->r_attrValue[$attributeName][] = $value;}
			else if (!isset($this->r_attrValue[$attributeName]))	{$this->r_attrValue[$attributeName][] = $value;}
			else	{throw new JfException ('OBJECT_OBJECT_SET_ATTR_ERROR');}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Sets the value of an attribute.
	 *
	 * If the attribute is a repeating attribute, this will set the value at index 0.
	 *
	 * @access public
	 * @param attributeName - the name of the attribute
	 * @param value - the variant value.
	 * @throws JfException - if a server error occurs
	 */
	public function setValue($attributeName, $value)
	{
		try
		{
			// The object has been modified
			$this->modified = true;
			// Throw an error if attributeName is 'r_object_id', 'r_object_type' or 'i_vstamp'
			$forbidden = array('r_object_id', 'r_object_type', 'i_vstamp');
			if (in_array($attributeName, $forbidden))	{throw new JfException ('OBJECT_OBJECT_SET_ATTR_ERROR');}
			// @todo - fetch the current object if its status is 0 ?
			if ($this->status == 0)	{$this->fetch();}
			// Set the attribute value
			if (isset($this->r_attrValue[$attributeName][0]))	{$this->r_attrValue[$attributeName][0] = $value;}
			else	{$this->attrValue[$attributeName] = $value;}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

}

?>