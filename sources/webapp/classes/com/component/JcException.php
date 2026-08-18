<?php
/**
 * The exception handler.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcException extends Exception
{
	/**
	* Fatal error
	*
	* A fatal exception occurs when the application is down (no access to the RDBMS for example)
	*
	* @access	public
	* @var		int
	*/
	public static $JC_EXCEPTION_FATAL = 0;

	/**
	* Normal error
	*
	* A normal error can occur when the application tries to access a non-existent table.
	*
	* @access	public
	* @var		int
	*/
	public static $JC_EXCEPTION_ERROR = 1;

	/**
	* Warning
	*
	* This is a warning message, not an error.
	*
	* @access	public
	* @var		int
	*/
	public static $JC_EXCEPTION_WARNING = 2;

	/**
	* Info
	*
	* @access	public
	* @var		int
	*/
	public static $JC_EXCEPTION_INFO = 3;

	/**
	* Debug
	*
	* This level has been set for debugging.
	*
	* @access	public
	* @var		int
	*/
	public static $JC_EXCEPTION_DEBUG = 4;

	/**
	* Array containing all error messages
	*
	* @access	private
	* @var		array
	*/
	private $errMessages = array();

	/**
	 * Constructor
	 *
	 * Redefine the exception so message isn't optional
	 *
	 * @param	String	message
	 * @param	int		code
	 */
	public function __construct($message, $code = 1)
	{
		// Make sure everything is assigned properly
		parent::__construct($message, $code);
		// Append the error message
		$this->append('ERROR : '.$message);
	}

	/**
	 * Append the error message
	 *
	 * @access	public
	 * @param	String	message
	 */
	public function append($message)
	{
		// If there is already an error message in the stack
		// then add ... before the message
		if (sizeof($this->errMessages) > 0)	{$message = '... '.$message;}
		// Set the error message
		$this->errMessages[] = $message;
		// Write the message to the log file
//		JcLogger::write($message, $this->getCode());
	}

	/**
	 * Returns the stack trace as a String. This is useful for debugging purposes.
	 *
	 * @access	public
	 * @return	String	the stack trace
	 */
	public function getStackTraceAsString()
	{
		$strMessage = '';
		foreach ($this->errMessages as $key=>$msg)	{$strMessage .= '<br>'.$msg;}
		if (strlen($strMessage) > 2)	{$strMessage = substr($strMessage, 4, strlen($strMessage));}
		return $strMessage;
	}

	/**
	 * Custom string representation of object
	 *
	 * @access	public
	 * @return	String	the string representation of this exception
	 */
	public function __toString()
	{
		return $this->getStackTraceAsString();
	}
}
?>