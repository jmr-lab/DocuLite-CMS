<?php
/**
 * An Estancia object identifier.
 *
 * @package com.core.common
 * @author Jean-Marie Roy
 * @copyright Jean-Marie Roy 2011
 * @version 3.0
 */
class JfId
{
	/**
	* Object Id
	*
	* The Id is a string that contains information on the object type, on the repository and on the object itself.
	* For example : 0900d5bb8001f900 where
	* 09 is the object type (jm_document),
	* 00d5bb8 is the repository id in hexadecimal, in that case 875448 in decimal,
	* 001f900 is the object unique id in the repository.
	*
	* @access private
	* @var String
	*/
	private $objectId;

	/**
	 * Constructor
	 *
	 * This function initialize the object Id
	 *
	 * @param String The object Id
	 * @throws JfException - if a server error occurs
	 */
	public function __construct($id)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// JfLogger::info('id : '.$id);
		// Check if id is a string
		if (!is_string($id))	{throw new JfException('COMMON_INVALID_ID');}
		// Check if the length is 16
		if (strlen($id) <> 16)	{throw new JfException('COMMON_INVALID_ID');}
		// set the object Id
		$this->objectId = $id;
	}

	/**
	 * Returns the portion of the ID that identifies the repository.
	 *
	 * @access public
	 * @return String - the portion of the ID that identifies the repository.
	 * If this ID was created from a string that does not represent a repository object, the results are undefined.
	 */
	public function getDocbaseId() 
	{
		return substr($this->objectId, 2, 7);
	}

	/**
	 * Returns a string representation of the ID.
	 *
	 * @access public
	 * @return String - the ID as a string.
	 */
	public function getId()
	{
		return $this->objectId;
	}

	/**
	 * Returns the portion of the ID that represents the object type.
	 *
	 * The type part is converted to an integer.
	 * For example, if the underlying ID is "099af3ce800001ff", this method returns 9.
	 * If the ID is "469af3ce80000200", this method returns 70 decimal (46 hex).
	 *
	 * @access public
	 * @return int - an integer expressing the object type.
	 */
	public function getTypePart()
	{
		return hexdec(substr($this->objectId, 0, 2));
	}

	/**
	 * Indicates whether this IDfId object represents a null ID. ("0000000000000000").
	 *
	 * @access public
	 * @return boolean - true if this object represents the NULLID, or false if it is not.
	 */
	public function isNull()
	{
		$flag = false;
		if ($this->objectId == '0000000000000000')	{$flag = true;}
		return $flag;
	}

	/**
	 * Indicates whether this ID is a valid object ID.
	 *
	 * @todo
	 * @access public
	 * @return boolean - true if this id is a valid object ID, or false if it is not.
	 */
	public function isObjectId()
	{
		$flag = false;
		// Only check if the length is 16
		if (strlen($this->objectId) == 16)	{$flag = true;}
		return $flag;
	}

	/**
	 * Returns a string representation of the ID.
	 *
	 * @access public
	 * @return String - the ID as a string.
	 */
	public function toString()
	{
		return $this->objectId;
	}

}
?>