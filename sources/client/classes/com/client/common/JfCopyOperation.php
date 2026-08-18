<?php
/**
 * The JfCopyOperation class.
 *
 * Usage :
 * $copyOperation = new JfCopyOperation(true);
 * $copyOperation->setFolderId('0b001e240ff52145');
 * $copyObject = $session->getObject(new JfId('09001e240ffe1c91'));
 * $copyOperation->add($copyObject);
 * $copyOperation->execute();
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfCopyOperation extends JfOperation
{
	/**
	* Version
	*
	* @access	protected
	* @var		array(JfCopyNode)
	*/
	protected $copyNode = array();

	/**
	* Folder Id
	*
	* @access	protected
	* @var		JfId
	*/
	protected $folderId;

	/**
	 * Constructor
	 *
	 * @param	boolean		the type of operation (move or link)
	 */
	public function __construct()	{}

	/**
	 * Set the object to the operation.
	 *
	 * @access	public
	 * @param	perObj					the object
	 * @return	JfCopyNode		An operation node
	 * @throws	JfException				if a server error occurs
	 */
	public function add($perObj)
	{
		try
		{
			$copyNode = new JfCopyNode($perObj, $this->folderId);
			$this->copyNode[] = $copyNode;
			return $copyNode;
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
			foreach ($this->copyNode as $index => $node)
			{
				$perObj = $node->getObject();
				$objectId = $perObj->getObjectId();
				$strObjectId = $objectId->getId();
				// Log Informations
				// JcLogger::info("objectId : '".$strObjectId."'");
				// JcLogger::info("FolderId : '".$node->getFolderId()."'");
				// Do the copy operation
				$newObj = clone $perObj;
				$sysObj = JfUtils::cast($newObj, 'JfSysObject');
				$sysObj->removeAll('i_folder_id');
				$sysObj->link(new JfId($node->getFolderId()));
				// Save this new object
				$sysObj->save();
				// Create a move/link event for each object
				$auditTrailMgr->createAudit($strObjectId, 'copy', $stringArgs, null);
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
	 * Set the folder Id.
	 *
	 * @access	public
	 * @param	folderId		the folderId
	 * @throws	JfException		if a server error occurs
	 */
	public function setFolderId($folderId)
	{
		try
		{
			$this->folderId = $folderId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>