<?php
/**
 * The SysObj Properties webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwSysObjProperties extends JwProperties
{
	/**
	 * List of attributes ('Public' instead of '45001e2406f9101c', ...)
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $attributes = array();

	/**
	 * Object ID
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $objectId;

	/**
	 * Object
	 *
	 * @access	protected
	 * @var		JfPersistentObject
	 */
	// protected $perObj;

	/**
	 * Constructor
	 *
	 * This function initialize the current user0
	 *
	 */
	public function __construct()
	{
		// Get the user login info
		$sessionmanager = new JfSessionManager();
		$session = $sessionmanager->getSession('www_jmroy');
		$this->session = $session;
		$this->user = $session->getLoginInfo();
		// Get the language used
		$httpsession = new JcHttpSession();
		$lang = $httpsession->getAttribute('lang');
		$this->nlsProperties = JcUtils::getNLSProperties($lang);
		// Get the object info
		$request = new JcHttpServletRequest();
		$this->objectId = $request->getParameter('objectId');
	}

	/**
	 * Get the attribute value from the attributes array
	 *
	 * @access	public
	 * @return	String	the attribute value
	 */
	public function getAttrValue($attr)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$attributes = $this->attributes;
		return $attributes[$attr];
	}

	/**
	 * Get the checked out icon
	 *
	 * @access	public
	 * @return	String	the checked out icon
	 */
	public function getCheckedOutIcon()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$perObj = $this->perObj;
		$user = $this->user;
		$lock = $perObj->getValue('r_lock_owner');
		if ($lock <> '')	{$lock = 'checkedout_16.png';}

		// // The object cannot be locked
		// if (!isset($perObj->getValue('r_lock_owner')))									{$lock = '';}
		// The object is not locked
		if ($perObj->getValue('r_lock_owner') == '')									{$lock = '';}
		// The object is locked by the current user
		else if ($perObj->getValue('r_lock_owner') == $user->getValue('user_name')
				|| $perObj->getValue('r_lock_owner') == $user->getValue('r_object_id'))	{$lock = 'checkedout_16.png';}
		// The object is locked by someone else
		else																			{$lock = 'checkedoutred_16.png';}

		echo $lock;
	}

	/**
	 * Get the icon associated with an object
	 * $object = array('r_object_id' => '0b001e2400786532', 'r_object_type' => 'jm_document', 'a_content_type' = > 'png', ...)
	 *
	 * @access	public
	 * @param	boolean	path	whether we need the path or not
	 * @return	String	the icon name
	 */
	public function getObjectIcon($path = false)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$perObj = $this->perObj;
		$propArr = array(	'r_object_id' => $perObj->getValue('r_object_id'),
							'r_object_type' => $perObj->getValue('r_object_type'),
							'a_content_type' => $perObj->getValue('a_content_type'),
							'i_contents_id' => $perObj->getValue('i_contents_id')
						);
		$images = new JcIconList($this->user);
		$icon = $images->getIcon(new JfTypedObject($this->session, $propArr));
		// $icon = '/estancia3.0/webapp/themes/default/images/icons/php.png';
		// We just want to get the name of the icon : 'php.png'
		$bPath = ($path) ? 'true' : 'false';
		if (!$path)	{$icon = substr($icon, strrpos($icon, '/') + 1);}
		if (substr($icon, 0, 3) == 'tn_')	{$icon = $perObj->getValue('a_content_type').'.png';}
		echo $icon;
	}

	/**
	 * Get the object name
	 *
	 * @access	public
	 * @return	String	the object name
	 */
	public function getObjectName()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$perObj = $this->perObj;
		$name = ($perObj->getValue('object_name') <> '') ? $perObj->getValue('object_name') : 'Properties';
		echo $name;
	}

	/**
	 * Get the current date time
	 * This function returns the current date time including milliseconds.
	 *
	 * @access	public
	 * @return	String	the date time
	 */
	public function getSize($format)
	{
		$perObj = $this->perObj;
		$size = $perObj->getValue('r_content_size');
		$formatted_size = number_format($size, 0, ' ', ' ');
		$byte = $this->getString('BYTE_SYMBOL');
		if ($size == 0)	{$formatted_size = '';}
		else if ($size == 1)	{$formatted_size = $size.' '.$this->getString('BYTE');}
		else					{$formatted_size = $formatted_size.' '.$this->getString('BYTES');}
		return $formatted_size;
	}

	/**
	 * Get a thumbnail
	 *
	 * @access	private
	 * @return	array	the thumnail icon
	 */
	private function getThumbnail($path, $content, $dos)
	{
		$thumbnail = 'tn_'.$content.$dos;
		if (!file_exists(_SERVER_ROOT_.'/data/thumbnail_storage_01/'.$thumbnail))	{$thumbnail = $dos;}
		return $thumbnail;
	}

	/**
	 * Get the attribute value
	 *
	 * @access	public
	 * @return	String	the attribute value
	 */
	public function getValue($attribute, $isName = false)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$perObj = $this->perObj;
		$value = $perObj->getValue($attribute);
		$value = ($isName) ? ucfirst($value) : $value;
		return $value;
	}

	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function init()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// global $perObj;
		$session = $this->session;
		// if ($perObj == null)	{$perObj = $session->getObject(new JfId($this->objectId));}
		$this->perObj = $session->getObject(new JfId($this->objectId));
		$perObj = $this->perObj;
		// $this->perObj = $perObj;
		$user = $this->user;
		$arr_permit = array(1 => 'NONE', 2 => 'BROWSE', 3 => 'READ', 4 => 'RELATE', 5 => 'VERSION', 6 => 'WRITE', 7 => 'DELETE');
		// Get attributes
		$this->attributes['acl_name'] = ''; $this->attributes['permission'] = '';
		$query = new JfQuery();
//		$sql = "SELECT object_name FROM jm_acl_s WHERE r_object_id = '".$perObj->getValue('acl_id')."'";
		$sql = "SELECT r_accessor_permit, jm_sysobject_s.owner_name AS owner_name, r_lock_owner, jm_acl_s.object_name AS object_name
				FROM	jm_sysobject_s, jm_acl_s,
						(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
						FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit
				WHERE	jm_sysobject_s.r_object_id = '".$perObj->getValue('r_object_id')."'
				AND jm_sysobject_s.acl_id = table_permit.acl_id
				AND jm_sysobject_s.acl_id = jm_acl_s.r_object_id";
		$query->setSQL($sql);
		$result = $query->execute($session);
		while ($result->next())
		{
			$permit = $result->getValue('r_accessor_permit');
			if ($result->getValue('owner_name') == $user->getValue('user_name'))	$permit = 7;
			$this->attributes['acl_name'] = $result->getValue('object_name');
			$this->attributes['permission'] = $this->getString($arr_permit[$permit]);
			$arrUserNames = array($user->getValue('r_object_id'), $user->getValue('user_name'));
			if ($result->getValue('r_lock_owner') <> '' && in_array($result->getValue('r_lock_owner'), $arrUserNames) && $permit > 5)	{$this->bReadOnly = 'false';}
			else if ($result->getValue('r_lock_owner') == '' && $permit > 5)	{$this->bReadOnly = 'false';}
		}
		$this->attributes['acl_name'] = $this->attributes['permission'].' ('.$this->attributes['acl_name'].')';
		// Get full names ('r_creator_name', 'r_modifier', 'owner_name')
//		$sql = "SELECT object_name FROM jm_acl_s WHERE r_object_id = '".$perObj->getValue('acl_id')."'";
		// Get version labels
		$this->attributes['r_version_label'] = '';
		for ($i = 0; $i < $perObj->getValueCount('r_version_label'); $i++)
		{
			$this->attributes['r_version_label'] .= $perObj->getRepeatingValue('r_version_label', $i).', ';
		}
		if (strlen($this->attributes['r_version_label']) > 0)	{$this->attributes['r_version_label'] = substr($this->attributes['r_version_label'], 0, -2);}
		echo '<input type="hidden" name="objectId" value="'.$this->objectId.'">';
	}

	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function onOk()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Get the request object
			$request = new JcHttpServletRequest();
			// Get the login/password
			$objectId = $request->getParameter('objectId');
			$name = $request->getParameter('object_name');
			$title = $request->getParameter('title');
			$subject = $request->getParameter('subject');
			$permissionId = $request->getParameter('permissionId');
			if ($name == '')	{throw new JfException($this->getString('ERROR_PROPERTIES_EMPTY_NAME'));}
			JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(name : '.$name.')');
			JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(title : '.$title.')');
			JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(subject : '.$subject.')');
			JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(ACL : '.$permissionId.')');
			$session = $this->session;
			$perObj = $session->getObject(new JfId($objectId));
			$perObj->setValue('object_name', $name);
			$perObj->setValue('title', $title);
			$perObj->setValue('subject', $subject);
			if ($permissionId <> '')	$perObj->setValue('acl_id', $permissionId);
			$perObj->save();
			/**
				Pater noster, qui es in caelis
				Sanctificetur nomen tuum;
				Adveniat regnum tuum;
				Fiat voluntas tua
				sicut in caelo et in terra.
				Panem nostrum quotidianum da nobis hodie,
				et dimitte nobis debita nostra
				sicut et nos dimittimus debitoribus nostris
				et ne nos inducas in tentationem
				sed libera nos a malo.
				Amen
			*/
		}
		catch (JfException $exception)
		{
			$exception->append('An error occured.');
			throw $exception;
		}
	}
}
?>