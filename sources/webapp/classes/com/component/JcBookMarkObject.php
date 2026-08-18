<?php
/**
 * JcBookMarkObject class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcBookMarkObject
{
	/**
	 * Object ID
	 *
	 * @access	private
	 * @var		String
	 */
	private $strObjectId;

	/**
	 * Constructor
	 *
	 * This function initialize the current bookmark object.
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
		return $this->strObjectId;
	}

	/**
	 * Set the value of the object ID.
	 *
	 * @access	public
	 * @param	String	strObjectId	the object Id
	 */
	public function setObjectId($strObjectId)
	{
		$this->strObjectId = $strObjectId;
	}
}
?>