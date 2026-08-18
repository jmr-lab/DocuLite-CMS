<?php
ob_start();
/**
 * Constants definitions
 *
 * _APP_ROOT_ = /localhost/webapp
 * _SERVER_ROOT_ = C:/.../xampplite/htdocs/estancia
 * _DOCUMENT_ROOT_ = C:/.../xampplite/htdocs
 */
$path = substr($_SERVER['PHP_SELF'], 1);
$_APP_ROOT_ = '/'.substr($path, 0, strpos($path, '/'));
$_SERVER_ROOT_ = $_SERVER["DOCUMENT_ROOT"].$_APP_ROOT_;
$_DOCUMENT_ROOT_ = $_SERVER["DOCUMENT_ROOT"];
// Remove the last character if it is a slash ('/')
if (substr($_APP_ROOT_, -1) == '/')	{$_APP_ROOT_ = trim(substr($_APP_ROOT_, 0, -1));}
define("path", $path);
define("_APP_ROOT_", $_APP_ROOT_);
define("_SERVER_ROOT_", $_SERVER_ROOT_);
define("_DOCUMENT_ROOT_", $_DOCUMENT_ROOT_);
// Set var isConnected to false
$isConnected = false;
// @Todo : this will have to be changed during the rebuild of the properties
$perObj;

/**
 * Class include
 *
 */
function __autoload($class_name)
{
	// Location
	$location = '';
	// type can be Jf, Jc, Jw or Jp...
	$type = substr($class_name, 0, 2);
	// Objects
	if (in_array($class_name, array('JfACL', 'JfContent', 'JfGroup', 'JfTypedObject', 'JfPersistentObject', 'JfSysObject')))
		$location = 'client/classes/com/client/object';
	else if (in_array($class_name, array('JwPanel', 'JwTag')) || substr($class_name, -3) == 'Tag')
		$location = 'webapp/classes/com/control';
	else if ($type == 'Jf')
		$location = 'client/classes/com/client/common';
	else if ($type == 'Jc')
		$location = 'webapp/classes/com/component';
	else if ($type == 'Jw')
		$location = 'webapp/classes/com/webcomponent';
	else if ($type == 'Jp')
		$location = 'webapp/classes/com/action';
	// Require the class specified by its location and name
	require _SERVER_ROOT_.'/'.$location.'/'.$class_name.'.php';
}

@session_start();
// Reporte toutes les erreurs PHP
error_reporting(-1);
// define custom handler
set_error_handler('errorHandler');

// custom handler code
function errorHandler($code, $message, $file, $line)
{
	$arrCode = array(
						E_ERROR => 'Error',						E_WARNING => 'Warning',
						E_PARSE => 'E_PARSE',					E_NOTICE => 'Notice',
						E_CORE_ERROR => 'E_CORE_ERROR',			E_CORE_WARNING => 'E_CORE_WARNING',
						E_COMPILE_ERROR => 'E_COMPILE_ERROR',	E_COMPILE_WARNING => 'E_COMPILE_WARNING',
						E_USER_ERROR => 'E_USER_ERROR',			E_USER_WARNING => 'E_USER_WARNING',
						E_USER_NOTICE => 'E_USER_NOTICE',		E_STRICT => 'E_STRICT'
					);

	$errorCode = array(
						E_ERROR => 'Error',						E_PARSE => 'E_PARSE',
						E_NOTICE => 'Notice',					E_CORE_ERROR => 'E_CORE_ERROR',
						E_COMPILE_ERROR => 'E_COMPILE_ERROR',	E_USER_ERROR => 'E_USER_ERROR',
						E_STRICT => 'E_STRICT'
					);

	$strCode = (isset($arrCode[$code]) ? $arrCode[$code] : 'UNDEFINED ERROR');

	// Notice: Undefined variable: test in H:\xampp\htdocs\webapp\classes\com\component\JcServerEventManager.php on line 39
	$errMessage = '<strong>'.$strCode.' :</strong> '.$message.' in <strong>'.$file.'</strong> on line <strong>'.$line.'</strong>';
	
	// Put the message in the session
	$_SESSION['phperror'][] = $errMessage;

	// Write any error to a log file
//	if (in_array($arrCode[$code], $errorCode))
//	{
		if($fp = fopen(_SERVER_ROOT_.'/webapp/logs/ERROR_'.date("Ymd").'.log','a'))
		{
			fputs($fp, "\n");
			fputs($fp, $errMessage);
			fclose($fp);
		}
//	}
}

// function shutdown()
// {
    // // This is our shutdown function, in 
    // // here we can do any last operations
    // // before the script is complete.

    // echo 'Script executed with success', PHP_EOL;
	// JcLogger::dump();
// }

// register_shutdown_function('shutdown');

// session_cache_limiter('none');
header('Content-Type: text/html; charset=iso-8859-1');
// header('Cache-control: max-age='.(60*60*24*365));
// header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*365));

?>