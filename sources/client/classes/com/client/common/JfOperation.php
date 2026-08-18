<?php
/**
 * The JfOperation class.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfOperation
{
	/**
	* Version
	*
	* @access	protected
	* @var		array(JfCheckinOperationNode)
	*/
	protected $operationNode = array();

	/**
	 * Set the object to the operation.
	 *
	 * @access	public
	 * @param	perObj					the object
	 * @return	JfOperationNode			An operation node
	 * @throws	JfException				if a server error occurs
	 */
	public function add($perObj)
	{
		try
		{
			$operationNode = new JfOperationNode($perObj, $this->strVersion, $this->strFileName);
			$this->operationNode[] = $operationNode;
			return $operationNode;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
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
			return $flag;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>