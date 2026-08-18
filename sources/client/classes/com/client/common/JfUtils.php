<?php
/**
 * A utility class.
 *
 * @package com.core.common
 * @author Jean-Marie Roy
 * @copyright Jean-Marie Roy 2011
 * @version 3.0
 */
class JfUtils
{
	/**
	* List of type names by extension
	*
	* An object with a type integer 9 is a jm_document...
	*
	* @todo Check if it is really needed in this class
	* @access protected
	* @var array
	*/
	public static $typeNames = array(
						'69' => 'jm_acl',						'76' => 'jm_activity',						'81' => 'jm_aggr_domain',
						'102' => 'jm_alias_set',				'20' => 'jm_api',							'13' => 'jm_assembly',
						'95' => 'jm_audit_trail',				'52' => 'jm_blob_ticket',					'64' => 'jm_blobstore',
						'84' => 'jm_buildin_expr',				'109' => 'jm_ca_store_tag',					'12' => 'jm_cabinet',
						'51' => 'jm_change_record',				'4' => 'jm_collection',						'86' => 'jm_cond_expr',
						'87' => 'jm_cond_id_expr',				'5' => 'jm_containment',					'6' => 'jmr_content',
						'106' => 'jm_dd_attr_info',				'104' => 'jm_dd_common_info',				'78' => 'jm_dd_info',
						'105' => 'jm_dd_type_info',				'107' => 'jm_display_config',				'54' => 'jm_dist_comp_record',
						'44' => 'jm_distributedstore',			'60' => 'jm_docbase_config',				'68' => 'jm_docbaseid_map',
						'63' => 'jm_docbroker',					'9' => 'jm_document',						'80' => 'jm_domain',
						'48' => 'jm_dump_object_record',		'47' => 'jm_dump_record',					'29' => 'jm_event',
						'88' => 'jm_expr_code',					'82' => 'jm_expression',					'97' => 'jm_externalstore_file_tag',
						'99' => 'jm_externalstore_free_tag',	'96' => 'jm_externalstore_tag',				'98' => 'jm_externalstore_url_tag',
						'94' => 'jm_federation',				'34' => 'jm_file',							'40' => 'jm_filestore',
						'11' => 'jm_folder',					'101' => 'jm_foreign_key',					'39' => 'jm_format',
						'15' => 'jm_fulltext',					'59' => 'jm_fulltext_index',				'85' => 'jm_func_expr',
						'18' => 'jm_group',						'37' => 'jm_inbox',							'31' => 'jm_index',
						'89' => 'jm_key',						'110' => 'jm_lightweight_tag',				'42' => 'jm_linkedstore',
						'43' => 'jm_linkrecord',				'83' => 'jm_literal_expr',					'50' => 'jm_load_object_record',
						'49' => 'jm_load_record',				'58' => 'jm_location',						'16' => 'jm_method',
						'62' => 'jm_mount_point',				'41' => 'jm_netstore',						'79' => 'jm_nls_dd_info',
						'65' => 'jm_note',						'2' => 'jm_object',							'35' => 'jm_otherfile',
						'23' => 'jm_outputdevice',				'73' => 'jmi_package',						'103' => 'jm_plugin',
						'70' => 'jm_policy',					'75' => 'jm_process',						'10' => 'jm_query',
						'27' => 'jmi_queue_item',				'72' => 'jm_recovery',						'71' => 'jm_reference',
						'25' => 'jm_registered',				'38' => 'jm_registry',						'55' => 'jm_relation',
						'56' => 'jm_relationtype',				'66' => 'jm_remotestore',					'67' => 'jm_remoteticket',
						'45' => 'jm_replica_record',			'24' => 'jm_router',						'108' => 'jm_scope_config',
						'32' => 'jm_sequence',					'61' => 'jm_server_config',					'1' => 'jm_session',
						'53' => 'jm_staged_document',			'14' => 'jm_store',							'100' => 'jm_subcontent',
						'8' => 'jm_sysobject',					'33' => 'jm_transaction_log',				'3' => 'jm_type',
						'46' => 'jm_type_info',					'21' => 'jm_type_manager',					'17' => 'jm_user',
						'90' => 'jm_value_assist',				'93' => 'jm_value_func',					'91' => 'jm_value_list',
						'92' => 'jm_value_query',				'28' => 'jm_verity_coll',					'36' => 'jm_verity_index',
						'30' => 'jm_vstamp',					'77' => 'jm_workflow',						'74' => 'jmi_workitem',
					);

	/**
	 * Cast an object to the specified class
	 *
	 * $perObj = $session->getObject(new JfId('09001e240ffe1ca9'));
	 * $sysObj = JfUtils::cast($perObj, 'JfSysObject');
	 *
	 * @access public
	 * @param Object the object to cast
	 * @param String the class name
	 * @return Object the casted object
	 */
	public static function cast($obj, $class)
	{
		if (get_class($obj) <> $class)
		{
			$serObj = serialize($obj);
			// Replace O:18:"JfPersistentObject" with O:11:"JfSysObject"
			$serClass = preg_replace('/O:'.strlen(get_class($obj)).':"'.get_class($obj).'"/', 'O:'.strlen($class).':"'.$class.'"', $serObj);
			$obj = unserialize($serClass);
		}
		return $obj;
	}

	/**
	 * Encode a string
	 *
	 * @access private
	 * @param String the text
	 * @param String the key
	 * @return String the encoded string
	 */
	private static function decode($text, $key)
	{
		$text = self::generateKey(base64_decode($text), $key);
		$tmp = "";
		for ($ctr=0; $ctr<strlen($text); $ctr++)
		{
			$md5 = substr($text, $ctr, 1);
			$ctr++;
			$tmp.= (substr($text, $ctr, 1) ^ $md5);
		}
		$tmp = substr($tmp, 0, strlen($tmp) - strlen($key));
		return $tmp;
	}

	/**
	 * Encode a string
	 *
	 * @access private
	 * @param String the text
	 * @param String the key
	 * @return String the encoded string
	 */
	private static function encode($text, $key)
	{
		$text .= $key;
		srand((double)microtime()*1000000);
		$keycode = md5(rand(0,32000) );
		$cpt = 0;
		$tmp = "";
		for ($ctr=0; $ctr<strlen($text); $ctr++)
		{
			if ($cpt == strlen($keycode))	{$cpt = 0;}
			$tmp .= substr($keycode, $cpt, 1).(substr($text, $ctr, 1) ^ substr($keycode, $cpt, 1) );
			$cpt++;
		}
		return base64_encode(self::generateKey($tmp, $key) );
	}

	/**
	 * Generates a key
	 *
	 * @access private
	 * @param String the text
	 * @param String the keycode
	 * @return String the key
	 */
	private static function generateKey($text, $keycode)
	{
		$keycode = md5($keycode);
		$cpt = 0;
		$tmp = "";
		for ($ctr=0; $ctr<strlen($text); $ctr++)
		{
			if ($cpt == strlen($keycode))	{$cpt = 0;}
			$tmp .= substr($text, $ctr, 1) ^ substr($keycode, $cpt, 1);
			$cpt++;
		}
		return $tmp;
	}

	/**
	 * Returns the database name
	 *
	 * @access	public
	 * @param	array		the properties
	 * @return	String		the database name
	 * @throws	JfException	if the property doesn't exist.
	 */
	public static function getDatabaseName($properties)
	{
		return self::getPropertyValue($properties, 'DATABASE');
	}

	/**
	 * Returns the decimal of a string.
	 *
	 * @access public
	 * @param String the hexadecimal value to convert
	 * @return int the decimal value
	 */
	public static function getDecimal($hexadecimal)
	{
		return hexdec($hexadecimal);
	}

	/**
	 * Returns the repository name
	 *
	 * @access public
	 * @param array the properties
	 * @return String the repository name
	 */
	public static function getDocbaseName($properties)
	{
		return self::getPropertyValue($properties, 'DOCBASE_NAME');
	}

	/**
	 * Returns the DOS extension of a file
	 *
	 * @access public
	 * @param String the file name
	 * @return String the DOS extension
	 */
	public static function getDOSExtension($file)
	{
		// $file = '/usr/tmp/Word Document.doc';
		// $filename = 'Word Document.doc';
		$filename = basename($file);
		$dos_extension = '';
		// If a dot "." can be found then the DOS extension will be the right part of the file name
		// $dos_extension = 'doc';
		if (strripos($filename, '.') >= 0)	{$dos_extension = substr($filename, strripos($filename, '.') + 1);}
		// Return the lower case of the DOS extension (in case it is like 'JPG')
		return strtolower($dos_extension);
	}

	/**
	 * Returns the server name of the filestore
	 *
	 * @access public
	 * @param array the properties
	 * @return String the server name
	 * @throws JfException if the property doesn't exist.
	 */
	public static function getFilestoreName($properties)
	{
		return self::getPropertyValue($properties, 'STORAGE');
	}

	/**
	 * Returns the hexadecimal value of an integer.
	 *
	 * @access public
	 * @param int the decimal value to convert
	 * @return String the hexadecimal value
	 */
	public static function getHexaDecimal($decimal)
	{
		$hexadecimal = dechex($decimal);
		// Force the hexadecimal string to be 7 characters length
		if (strlen($hexadecimal) > 7)	{$hexadecimal = substr($hexadecimal, strlen($hexadecimal) - 7, 7);}
		elseif (strlen($hexadecimal) < 7)	{while (strlen($hexadecimal) < 7)	{$hexadecimal = '0'.$hexadecimal;}}
		return $hexadecimal;
	}

	/**
	 * Returns the location of an ini file.
	 *
	 * @access public
	 * @param String the type of ini file (client, server or webapp)
	 * @return String the location of the file.
	 */
	public static function getIniFile($type = 'webapp')
	{
		$file = _SERVER_ROOT_.'/client/config/client.ini';
		switch ($type)
		{
			case 'client':
				$file = _SERVER_ROOT_.'/client/config/client.ini';
				break;
			case 'server':
				$file = _SERVER_ROOT_.'/server/config/server.ini';
				break;
			default:
				$file = _SERVER_ROOT_.'/webapp/config/estancia.ini';
				break;
		}
		return $file;
	}

	/**
	 * Get a new ID.
	 *
	 * @access	public
	 * @param	JfSession	the current session
	 * @param	String		the object type
	 * @return	String		the new ID string.
	 * @throws	JfException	if a server error occurs
	 */
	public static function getNewId($session, $objectType)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__."()");
		try
		{
			$docbaseId = $session->getDocbaseId();
			$objectId = '0000000000000000';
			$extension = '00';

			// Get the latest object ID
			$sql  = 'SELECT SUBSTR(r_object_id, 1, 2) AS extension, SUBSTR(r_object_id, LENGTH(r_object_id) - 6) AS object_id ';
			$sql .= 'FROM '.$objectType.'_s ORDER BY object_id DESC LIMIT 0, 1;';
			$query = new JfQuery();
			$query->setSQL($sql);
			$results = $query->execute($session);
			$extension = $results->getValue('extension');
			$objectId = $results->getValue('object_id');
			$newShortId = 1 + JfUtils::getDecimal($objectId);
			// If extension is equal to 00, then there is no object in the table
			if ($extension == '00' || $extension == '')
			{
				// Get the extension based on the object type
				// ie jm_document should return 09
				$array_extensions = array_flip(self::$typeNames);
				$extension = dechex($array_extensions[$objectType]);
				// Eventually add a 0 in front of the extension
				if (strlen($extension) == 1)	{$extension = '0'.$extension;}
				if ($extension == '00')	{throw new JfException('OBJECT_INVALID_EXTENSION');}
			}
//			JcLogger::info('Object ID : '.$extension.JfUtils::getHexaDecimal($docbaseId).JfUtils::getHexaDecimal($newShortId));
			return $extension.JfUtils::getHexaDecimal($docbaseId).JfUtils::getHexaDecimal($newShortId);
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the decimal of a string.
	 *
	 * @access public
	 * @param String the hexadecimal value to convert
	 * @return int the decimal value
	 */
	public static function getPrivateKey()
	{
		return '1234567890';
	}

	/**
	 * Returns the values of a properties file
	 *
	 * @access	public
	 * @param	String		the file absolute path to look in
	 * @return	array		the properties
	 * @throws	JfException	if the file doesn't exist.
	 */
	public static function getProperties($filepath, $all = false)
	{
		if (!file_exists($filepath))	{throw new JfException('UTILS_FILE_DOESNT_EXIST');}
		$properties = parse_ini_file($filepath, true);
		// Get the repository
		$repository = 'REPOSITORY_1';
		if (isset($_SESSION['_REPOSITORY_']))
		{
			$configList = new JfList($_SESSION['_REPOSITORY_']);
			$repository = ($configList->getValue('VALUE') <> '') ? $configList->getValue('VALUE') : 'REPOSITORY_1';
		}
		// JfLogger::info('$repository : '.$repository);
		return ($all ? $properties : $properties[$repository]);
	}

	/**
	 * Returns a property value
	 *
	 * @access public
	 * @param array the properties
	 * @param String the key to look for
	 * @return String the property value
	 */
	public static function getPropertyValue($properties, $key)
	{
		if (!isset($properties[$key]))	{throw new JfException('UTILS_PROPERTY_DOESNT_EXIST');}
		return $properties[$key];
	}

	/**
	 * Returns a list of available repositories
	 *
	 * @access	public
	 * @param	array	the properties
	 * @return	array	the repositories
	 */
	public static function getRepositories($properties)
	{
		$repositories = array();
		foreach ($properties as $key => $value)	{	if (substr($key, 0, 10) == 'REPOSITORY')	{$repositories[$key] = $value;}	}
		return $repositories;
	}

	/**
	 * Returns the server name
	 *
	 * @access	public
	 * @param	array		the properties
	 * @return	String		the server name
	 * @throws	JfException	if the property doesn't exist.
	 */
	public static function getServerName($properties)
	{
		return self::getPropertyValue($properties, 'SERVER');
	}

	/**
	 * Returns the user name
	 *
	 * @access public
	 * @param array the properties
	 * @return String the user name
	 */
	public static function getUserName($properties)
	{
		return self::getPropertyValue($properties, 'LOGIN');
	}

	/**
	 * Returns the user password
	 *
	 * @access public
	 * @param array the properties
	 * @return String the user password
	 */
	public static function getUserPassword($properties)
	{
		return self::decode(self::getPropertyValue($properties, 'PASSWORD'), self::getPrivateKey());
	}
}
?>