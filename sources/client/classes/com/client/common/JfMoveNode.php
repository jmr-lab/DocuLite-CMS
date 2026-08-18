<?php
/**
 * The JfMoveNode class.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfMoveNode extends JfOperationNode
{
	/**
	* Is it a move (true) or a link (false) operation?
	*
	* @access	protected
	* @var		boolean
	*/
	protected $bMove;

	/**
	* New folder Id
	*
	* @access	protected
	* @var		JfId
	*/
	protected $newFolderId;

	/**
	* Old folder Id
	*
	* @access	protected
	* @var		JfId
	*/
	protected $oldFolderId;

	/**
	 * Constructor
	 *
	 */
	public function __construct($perObj, $bMove, $oldFolderId, $newFolderId)
	{
		$this->perObj = $perObj;
		$this->bMove = $bMove;
		$this->oldFolderId = $oldFolderId;
		$this->newFolderId = $newFolderId;
	}

	/**
	 * Returns the move operation.
	 *
	 * @access	public
	 * @return	String			the move operation
	 * @throws	JfException		if a server error occurs
	 */
	public function getMoveOperation()
	{
		try
		{
			return $this->bMove;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the new folder Id.
	 *
	 * @access	public
	 * @return	String			the new folder Id
	 * @throws	JfException		if a server error occurs
	 */
	public function getNewFolderId()
	{
		try
		{
			return $this->newFolderId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the old folder Id.
	 *
	 * @access	public
	 * @return	String			the old folder Id
	 * @throws	JfException		if a server error occurs
	 */
	public function getOldFolderId()
	{
		try
		{
			return $this->oldFolderId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>