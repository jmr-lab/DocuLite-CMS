<?php
/**
 * The JfOperationNode class.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfOperationNode
{
	/**
	* Persistent Object
	*
	* @access	protected
	* @var		JfPersistentObject
	*/
	protected $perObj;

	/**
	 * Constructor
	 *
	 */
	public function __construct($perObj, $strVersion, $strFileName)
	{
		$this->perObj = $perObj;
	}

	/**
	 * Returns the object.
	 *
	 * @access	public
	 * @return	JfPersistentObject	the object
	 * @throws	JfException			if a server error occurs
	 */
	public function getObject()
	{
		try
		{
			return $this->perObj;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>