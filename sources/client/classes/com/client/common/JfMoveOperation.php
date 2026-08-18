<?php
/**
 * The JfMoveOperation class.
 *
 * Usage :
 * $moveOperation = new JfMoveOperation(true);
 * $moveOperation->setOldFolderId('0b001e24085218d8');
 * $moveOperation->setNewFolderId('0b001e240ff52145');
 * $moveObject = $session->getObject(new JfId('09001e240ffe1c91'));
 * $moveOperation->add($moveObject);
 * $moveOperation->execute();
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfMoveOperation extends JfOperation
{
	/**
	* Version
	*
	* @access	protected
	* @var		array(JfMoveNode)
	*/
	protected $moveNode = array();

	/**
	* Is it a move (true) or a link (false) operation?
	*
	* @access	protected
	* @var		boolean
	*/
	protected $bMove = true;

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
	 * @param	boolean		the type of operation (move or link)
	 */
	public function __construct($bMove)
	{
		$this->bMove = $bMove;
	}

	/**
	 * Set the object to the operation.
	 *
	 * @access	public
	 * @param	perObj					the object
	 * @return	JfMoveOperationNode		An operation node
	 * @throws	JfException				if a server error occurs
	 */
	public function add($perObj)
	{
		try
		{
			$moveNode = new JfMoveNode($perObj, $this->bMove, $this->oldFolderId, $this->newFolderId);
			$this->moveNode[] = $moveNode;
			return $moveNode;
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
			// Get the user details
			$sessionmanager = new JfSessionManager();
			$session = $sessionmanager->getSession('www_jmroy');
			$user = $session->getLoginInfo();
			// JcLogger::info('User : '.$user->getValue('r_object_id'));
			// Prepare the audit manager to create a 'checkin' event
			$auditTrailMgr = $session->getAuditTrailManager();
			$stringArgs = array(	'userName' => $user->getValue('user_name'),
									'userIP' => getenv("REMOTE_ADDR")	);
			// List of all object IDs to move
			$arrObjectIds = array(); $arrObjectIdsNew = array();
			// For each node
			foreach ($this->moveNode as $index => $node)
			{
				$perObj = $node->getObject();
				$objectId = $perObj->getObjectId();
				$strObjectId = $objectId->getId();
				$move = $node->getMoveOperation() ? 'move' : 'link';
				// Log Informations
				// JcLogger::info("bMove : '".$move."'");
				// JcLogger::info("objectId : '".$strObjectId."'");
				// JcLogger::info("oldFolderId : '".$node->getOldFolderId()."'");
				// JcLogger::info("newFolderId : '".$node->getNewFolderId()."'");
				// Do the move/link
				$sysObj = JfUtils::cast($perObj, 'JfSysObject');
				$sysObj->link(new JfId($node->getNewFolderId()));
				if ($node->getMoveOperation())	{$sysObj->unlink(new JfId($node->getOldFolderId()));}
				$sysObj->save();
				// Create a move/link event for each object
				$auditTrailMgr->createAudit($strObjectId, $move, $stringArgs, null);
			}
			// Return true/false
			return $flag;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Set the new folder Id.
	 *
	 * @access	public
	 * @param	folderId		the new folderId
	 * @throws	JfException		if a server error occurs
	 */
	public function setNewFolderId($folderId)
	{
		try
		{
			$this->newFolderId = $folderId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Set the old folder Id.
	 *
	 * @access	public
	 * @param	folderId		the old folderId
	 * @throws	JfException		if a server error occurs
	 */
	public function setOldFolderId($folderId)
	{
		try
		{
			$this->oldFolderId = $folderId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>