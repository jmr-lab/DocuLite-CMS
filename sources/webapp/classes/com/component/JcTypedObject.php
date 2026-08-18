<?php
/**
 * JcTypedObject class.
 *
 * Usage :
 * $typedObject = new JcTypedObject();
 * $typedObject->setObjectId($strObjectId);
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcTypedObject
{
	/**
	 * Values
	 *
	 * @access	private
	 * @var		array
	 */
	private $arrValues = array();

	/**
	 * Constructor
	 *
	 * This function initialize the current object.
	 *
	 * @access	public
	 */
	public function __construct()	{}

	/**
	 * Get the value of the object ID.
	 *
	 * @access	public
	 * @return	String	The object ID
	 */
	public function getObjectId()
	{
		
		return $this->getValue('r_object_id');
	}

	/**
	 * Get a value of the object.
	 *
	 * @access	public
	 * @return	String	The attribute name
	 */
	public function getValue($strName)
	{
		$value = isset($this->arrValues[$strName]) ? $this->arrValues[$strName] : '';
		return $value;
	}

	/**
	 * Set the value of the object ID.
	 *
	 * @access	public
	 * @param	String	strObjectId	the object Id
	 */
	public function setObjectId($strObjectId)
	{
		$this->setValue('r_object_id', $strObjectId);
	}

	/**
	 * Set a value.
	 *
	 * @access	public
	 * @param	String	strName		the name of the attribute
	 * @param	String	strValue	the value of the attribute
	 */
	public function setValue($strName, $strValue)
	{
		$this->arrValues[$strName] = $strValue;
	}
}
?>