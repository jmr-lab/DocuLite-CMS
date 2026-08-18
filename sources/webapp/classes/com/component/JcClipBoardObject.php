<?php
/**
 * JcClipBoardObject class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcClipBoardObject
{
	/**
	 * Container ID
	 *
	 * @access	private
	 * @var		String
	 */
	private $strContainerId;

	/**
	 * Operation : can be 'cut' or 'copy'
	 *
	 * @access	private
	 * @var		String
	 */
	private $strOperation;

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
	 * This function initialize the current clipboard object.
	 *
	 * @access	public
	 */
	public function __construct()	{}

	/**
	 * Get the value of the container ID.
	 *
	 * @access	public
	 * @return	String	The container ID
	 */
	public function getContainerId()
	{
		return $this->strContainerId;
	}

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
	 * Get the operation name.
	 *
	 * @access	public
	 * @return	String	The operation name
	 */
	public function getOperation()
	{
		return $this->strOperation;
	}

	/**
	 * Set the value of the container ID.
	 *
	 * @access	public
	 * @param	String	strContainerId	the container Id
	 */
	public function setContainerId($strContainerId)
	{
		$this->strContainerId = $strContainerId;
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

	/**
	 * Set the name of the operation to apply on the object.
	 *
	 * @access	public
	 * @param	String	strOperation	The operation name
	 */
	public function setOperation($strOperation)
	{
		$this->strOperation = $strOperation;
	}
}
?>