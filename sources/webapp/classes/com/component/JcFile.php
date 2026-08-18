<?php
/**
 * The JcFile.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcFile
{
	/**
	* File Path
	*
	* @access	private
	* @var		String
	*/
	private $strFilePath;

	/**
	 * Constructor
	 *
	 * @param	String	File path
	 */
	public function __construct($strFilePath)
	{
		$this->strFilePath = $strFilePath;
	}

	/**
	 * Check if file exists
	 *
	 * @access	public
	 * @return	boolean		whether the file exists or not
	 */
	public function exists()
	{
		// Init the flag
		$flag = false;
		// Check if the file exists
		if (file_exists($this->strFilePath))	{$flag = true;}
		// Return the flag
		return $flag;
		// Write the message to the log file
		// JcLogger::write($message, $this->getCode());
	}
}
?>