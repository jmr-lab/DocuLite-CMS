<?php
/**
 * The JfCheckinNode class.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfCheckinNode extends JfOperationNode
{
	/**
	* File Name
	*
	* @access	protected
	* @var		String
	*/
	protected $strFileName;

	/**
	* Version
	*
	* @access	protected
	* @var		String
	*/
	protected $strVersion;

	/**
	 * Constructor
	 *
	 */
	public function __construct($perObj, $strVersion, $strFileName)
	{
		$this->perObj = $perObj;
		$this->strVersion = $strVersion;
		$this->strFileName = $strFileName;
	}

	/**
	 * Returns the checkin version.
	 *
	 * @access	public
	 * @return	String			the checkin version
	 * @throws	JfException		if a server error occurs
	 */
	public function getCheckinVersion()
	{
		try
		{
			return $this->strVersion;
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
}
?>