<?php
/**
 * The JfCopyNode class.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfCopyNode extends JfOperationNode
{
	/**
	* New folder Id
	*
	* @access	protected
	* @var		JfId
	*/
	protected $folderId;

	/**
	 * Constructor
	 *
	 */
	public function __construct($perObj, $folderId)
	{
		$this->perObj = $perObj;
		$this->folderId = $folderId;
	}

	/**
	 * Returns the new folder Id.
	 *
	 * @access	public
	 * @return	String			the new folder Id
	 * @throws	JfException		if a server error occurs
	 */
	public function getFolderId()
	{
		try
		{
			return $this->folderId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>