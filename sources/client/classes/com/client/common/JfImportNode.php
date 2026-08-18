<?php
/**
 * The JfImportNode class.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfImportNode extends JfOperationNode
{
	/**
	* File Name
	*
	* @access	protected
	* @var		String
	*/
	protected $strFileName;

	/**
	* Object Name
	*
	* @access	protected
	* @var		String
	*/
	protected $strObjectName;

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
	public function __construct($strFileName, $strObjectName, $strACLId, $strFolderId, $strFormat)
	{
		$this->strFileName = $strFileName;
		$this->strObjectName = $strObjectName;
		$this->strACLId = $strACLId;
		$this->strFolderId = $strFolderId;
		$this->strFormat = $strFormat;
	}

	/**
	 * Returns the ACL Id.
	 *
	 * @access	public
	 * @return	String			the ACL Id
	 * @throws	JfException		if a server error occurs
	 */
	public function getACLId()
	{
		try
		{
			return $this->strACLId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the file path.
	 *
	 * @access	public
	 * @return	String			the file path
	 * @throws	JfException		if a server error occurs
	 */
	public function getFilePath()
	{
		try
		{
			return $this->strFileName;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the Folder Id.
	 *
	 * @access	public
	 * @return	String			the Folder Id
	 * @throws	JfException		if a server error occurs
	 */
	public function getFolderId()
	{
		try
		{
			return $this->strFolderId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the Format.
	 *
	 * @access	public
	 * @return	String			the Format
	 * @throws	JfException		if a server error occurs
	 */
	public function getFormat()
	{
		try
		{
			return $this->strFormat;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the object name.
	 *
	 * @access	public
	 * @return	String			the object name
	 * @throws	JfException		if a server error occurs
	 */
	public function getObjectName()
	{
		try
		{
			return $this->strObjectName;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>