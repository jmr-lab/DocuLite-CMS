<?php
/**
 * The JfImportOperation class.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfImportOperation extends JfOperation
{
	/**
	* Version
	*
	* @access	protected
	* @var		array(JfImportNode)
	*/
	protected $importNode = array();

	/**
	* ACL Id
	*
	* @access	protected
	* @var		String
	*/
	protected $strACLId;

	/**
	* Folder Id
	*
	* @access	protected
	* @var		String
	*/
	protected $strFolderId;

	/**
	* Format
	*
	* @access	protected
	* @var		String
	*/
	protected $strFormat;

	/**
	 * Constructor
	 *
	 */
	public function __construct()	{}

	/**
	 * Set the object to the operation.
	 *
	 * @access	public
	 * @param	strFileName				the object
	 * @param	strObjectName			the object name
	 * @return	JfImportNode			An operation node
	 * @throws	JfException				if a server error occurs
	 */
	public function add($obj)
	{
		try
		{
			$importNode = new JfImportNode($obj['path'], $obj['name'], $this->strACLId, $this->strFolderId, $this->strFormat);
			$this->importNode[] = $importNode;
			return $importNode;
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
			// For each node
			foreach ($this->importNode as $index => $node)
			{
				// Log Informations
				// JcLogger::info("strACLId : '".$node->getACLId()."'");
				// JcLogger::info("strFilePath : '".$node->getFilePath()."'");
				// JcLogger::info("strFolderId : '".$node->getFolderId()."'");
				// JcLogger::info("strFormatId : '".$node->getFormat()."'");
				$perObj = $session->newObject('jm_document');
				$sysObj = JfUtils::cast($perObj, 'JfSysObject');
				$sysObj->setObjectName(basename($node->getObjectName()));
				$aclObj = $session->getACL(new JfId($node->getACLId()));
				$sysObj->setACL($aclObj);
				$sysObj->setValue('a_content_type', $node->getFormat());
				$sysObj->setRepeatingValue('i_folder_id', '0', $node->getFolderId());
				$sysObj->setFile($node->getFilePath());
				$sysObj->setValue('r_content_size', filesize(_SERVER_ROOT_.$node->getFilePath()));
				$sysObj->save();
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
	 * Set the ACL Id to the import operation.
	 *
	 * @access	public
	 * @param	strACLId		the ACL Id
	 * @throws	JfException		if a server error occurs
	 */
	public function setACL($strACLId)
	{
		try
		{
			$this->strACLId = $strACLId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Set the Folder Id to the import operation.
	 *
	 * @access	public
	 * @param	strFolderId		the folder Id
	 * @throws	JfException		if a server error occurs
	 */
	public function setFolderId($strFolderId)
	{
		try
		{
			$this->strFolderId = $strFolderId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Set the Format to the import operation.
	 *
	 * @access	public
	 * @param	strFormat		the format
	 * @throws	JfException		if a server error occurs
	 */
	public function setFormat($strFormat)
	{
		try
		{
			$this->strFormat = $strFormat;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>