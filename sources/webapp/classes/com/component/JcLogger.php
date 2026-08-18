<?php
/**
 * Logger class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcLogger
{
	/**
	* Fatal
	*
	* @access public
	* @var int
	*/
	public static $JC_LOGGER_FATAL = 0;

	/**
	* Normal
	*
	* @access public
	* @var int
	*/
	public static $JC_LOGGER_ERROR = 1;

	/**
	* Warning
	*
	* @access public
	* @var int
	*/
	public static $JC_LOGGER_WARNING = 2;

	/**
	* Info
	*
	* @access public
	* @var int
	*/
	public static $JC_LOGGER_INFO = 3;

	/**
	* Debug
	*
	* @access public
	* @var int
	*/
	public static $JC_LOGGER_DEBUG = 4;

	/**
	* List of debugging levels
	*
	* @access private
	* @var array
	*/
	private static $levelMessage = array(	'0' => 'FATAL',
											'1' => 'ERROR',
											'2' => 'WARNING',
											'3' => 'INFO',
											'4' => 'DEBUG');

	/**
	 * Constructor
	 *
	 * This function initialize the logger
	 *
	 */
	public function __construct()	{}

	/**
	 * Destructor
	 *
	 * This function destructs the logger
	 *
	 */
	public function __destruct()	{}

   /**
	 * Logs a message with the JcLogger::JC_LOGGER_FATAL logging level.
	 *
	 * @access public
	 * @param String message - the message to log.
	 */
	public static function fatal($message)
	{
		self::write($message, self::$JC_LOGGER_FATAL);
	}

	/**
	 * Logs a message with the JcLogger::JC_LOGGER_ERROR logging level.
	 *
	 * @access public
	 * @param String message - the message to log.
	 */
	public static function error($message)
	{
		self::write($message, self::$JC_LOGGER_ERROR);
	}

	/**
	 * Logs a message with the JcLogger::JC_LOGGER_WARNING logging level.
	 *
	 * @access public
	 * @param String message - the message to log.
	 */
	public static function warning($message)
	{
		self::write($message, self::$JC_LOGGER_WARNING);
	}

	/**
	 * Logs a message with the JcLogger::JC_LOGGER_INFO logging level.
	 *
	 * @access public
	 * @param String message - the message to log.
	 */
	public static function info($message)
	{
		self::write($message, self::$JC_LOGGER_INFO);
	}

	/**
	 * Logs a message with the JcLogger::JC_LOGGER_DEBUG logging level.
	 *
	 * @access public
	 * @param String message - the message to log.
	 */
	public static function debug($message)
	{
		self::write($message, self::$JC_LOGGER_DEBUG);
	}

	/**
	 * Writes a message to a log file.
	 *
	 * The message will be displayed like this :
	 * 2010-10-23 15:23:51,740 [Jean-Marie:127.0.0.1] com.component.JcUtils.getProperties(’tags’)
	 *
	 * @access private
	 * @todo add a threshold to write messages to the log file.
	 * @param String message - the message to log.
	 * @param int level - the message type (error, info, ...).
	 */
	public static function write($message, $level)
	{
		// Get max level of logging
		$minLevel = self::$JC_LOGGER_INFO;
		if (isset($_SESSION['_LOGGING_']))	{$minLevel = $_SESSION['_LOGGING_'];}
		// If level is more than min level then don't write anything in the log file
		if ($level > $minLevel)	{return;}
		// Set the time zone
		date_default_timezone_set('Europe/Berlin');
		//	Write in the $_SESSION variable
		$user = '';
		if (isset($_SESSION['_USER_']['user_login_name']))	{$user = $_SESSION['_USER_']['user_login_name'];}
		if ($user == '')	{$user = 'guest';}
		// Add date to the string
		$milli = floor(1000 * microtime());
		while (strlen($milli) < 3)	{$milli = '0'.$milli;}
		// @todo - Remove any extended character from the message (such as '\n')
		// The message will be displayed like this :
		// 2010-10-23 15:23:51,740 [INFO:Jean-Marie:127.0.0.1] com.core.object.JfDocument.getString(’owner_name’)
		$message = date("Y-m-d H:i:s").','.$milli."\t".'['.self::$levelMessage[$level].':'.$user.':'.getenv("REMOTE_ADDR").']'."\t".$message;
		$GLOBALS['APPLOG'][] = $message;
	}

	/**
	 * dump the messages to the log file.
	 *
	 * @access public
	 */
	public static function dump()
	{
		if (!isset($GLOBALS['APPLOG']))	{return;}
		//	Open the log file for writing
		if($fp = fopen(_SERVER_ROOT_.'/webapp/logs/ESTANCIA_'.date("Ymd").'.log','a'))
		{
			foreach ($GLOBALS['APPLOG'] as $key=>$value)
			{
				fputs($fp, "\n");
				fputs($fp, $value);
			}
			fclose($fp);
		}
		unset($GLOBALS['APPLOG']);
	}
}
?>