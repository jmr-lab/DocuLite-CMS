<?php
/**
 * A utility class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcUtils
{
	/**
	 * Returns the location of an ini file.
	 *
	 * @access	public
	 * @param	String	the type of ini file (client, server or webapp)
	 * @return	String	the location of the file.
	 */
	public static function getIniFile($type = 'webapp')
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$file = _SERVER_ROOT_.'/webapp/config/webapp.ini';
		switch ($type)
		{
			case 'actions':
				$file = _SERVER_ROOT_.'/webapp/config/actions.ini';
				break;
			case 'columns':
				$file = _SERVER_ROOT_.'/webapp/config/columns.ini';
				break;
			case 'components':
				$file = _SERVER_ROOT_.'/webapp/config/components.ini';
				break;
			case 'languages':
				$file = _SERVER_ROOT_.'/webapp/config/languages.ini';
				break;
			case 'tags':
				$file = _SERVER_ROOT_.'/webapp/config/tags.ini';
				break;
			default:
				$file = _SERVER_ROOT_.'/webapp/config/estancia.ini';
				break;
		}
		return $file;
	}

	/**
	 * Returns the content of the help file
	 *
	 * @access	public
	 * @param	String	the language used
	 * @return	String	the content of the help file
	 */
	public static function getHelpFile($lang)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$file = _SERVER_ROOT_.'/webapp/help/index_'.$lang.'.html';
		if (!file_exists($file))	{$file = _SERVER_ROOT_.'/webapp/help/index_fr.html';}
		return file_get_contents($file);
	}

	/**
	 * Returns the content of a file
	 *
	 * @access	public
	 * @param	String	the file name
	 * @param	String	the language used
	 * @return	String	the content of the file
	 */
	public static function getFile($fileName, $lang)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$file = _SERVER_ROOT_.'/webapp/help/'.$fileName.'_'.$lang.'.html';
		if (!file_exists($file))	{$file = _SERVER_ROOT_.'/webapp/help/'.$fileName.'_fr.html';}
		return file_get_contents($file);
	}

	/**
	 * Returns the values of a NLS properties file
	 *
	 * @access	public
	 * @param	String	the language used
	 * @return	array	the properties
	 */
	public static function getNLSProperties($lang)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$file = _SERVER_ROOT_.'/webapp/config/properties_'.$lang.'.ini';
//		if (!file_exists($file))	{return null;}
		return parse_ini_file($file);
	}

	/**
	 * Returns the values of a properties file
	 *
	 * @access	public
	 * @param	String	the file absolute path to look in
	 * @return	array	the properties
	 */
	public static function getProperties($filepath)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$arrIniFiles = array('actions.ini', 'columns.ini', 'components.ini', 'tags.ini');
		if (!in_array(basename($filepath), $arrIniFiles) && !file_exists($filepath))	{return null;}
		return parse_ini_file($filepath, true);
	}

	/**
	 * Returns a property value
	 *
	 * @access	public
	 * @param	array	the properties
	 * @param	String	the key to look for
	 * @return	String	the property value
	 */
	public static function getPropertyValue($properties, $section, $key)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		if (!isset($properties[$section][$key]))	{return null;}
		return $properties[$section][$key];
	}

	/**
	 * Returns the string value of the nls Id
	 *
	 * @access		public
	 * @param		String			the Nls Id
	 * @return		String			the String value
	 */
	public static function getString($nlsproperties, $nlsId)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
//		$msg = ucfirst(strtolower(str_replace('_', ' ', $nlsId)));
		if (isset($nlsproperties[$nlsId]))	{$msg = $nlsproperties[$nlsId];}
		else if (ctype_upper(str_replace('_', '', $nlsId)))	{$msg = ucfirst(strtolower(str_replace('_', ' ', $nlsId)));}
		else	{$msg = ucfirst($nlsId);}
		return $msg;
	}

	/**
	 * Get the current date time
	 * This function returns the current date time including milliseconds.
	 *
	 * @access	public
	 * @return	String	the date time
	 */
	public static function getTime($time)
	{
		if ($time <> '')	{$time = date("Y-m-d H:i", strtotime($time));}
		return $time;
	}
}
?>