<?php
/**
 * The UserProperties webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwUserProperties extends JwProperties
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
	protected $perObj;

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
	 * Get the client capability :
	 * consumer, contributor, coordinator or sysadmin.
	 *
	 * @access	public
	 * @return	String	the client capability
	 */
	public function getClientCapability()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$arrccap = array('0' => 'USER_CONSUMER', '2' => 'USER_CONTRIBUTOR', '4' => 'USER_COORDINATOR', '8' => 'USER_SYSADMIN');
		$perObj = $this->perObj;
		$capability = $perObj->getValue('client_capability');
		return $this->getString($arrccap[$capability]);
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
		if (!$path)	{$icon = substr($icon, strrpos($icon, '/') + 1);}
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
		$objectId = $this->objectId;
		$perObj = $this->perObj;
		$name = (substr($this->objectId, 0, 2) == '11') ? $perObj->getValue('user_name') : $perObj->getValue('group_name');
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
	 * Get the title ('User' or 'Group')
	 *
	 * @access	public
	 * @return	String	the title
	 */
	public function getTitle()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$objectId = $this->objectId;
		$name = (substr($this->objectId, 0, 2) == '11') ? $this->getString('USER') : $this->getString('GROUP');
		echo $name;
	}

	/**
	 * Get the user state :
	 * active, inactive, locked or locked and inactive.
	 *
	 * @access	public
	 * @return	String	the user state
	 */
	public function getUserState()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$arrustate = array('0' => 'USER_ACTIVE', '1' => 'USER_INACTIVE', '2' => 'USER_LOCKED', '3' => 'USER_LOCKED_INACTIVE');
		$perObj = $this->perObj;
		$state = $perObj->getValue('user_state');
		return $this->getString($arrustate[$state]);
	}

	/**
	 * Get the attribute value
	 *
	 * @access	public
	 * @return	String	the attribute value
	 */
	public function getValue($attribute)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$perObj = $this->perObj;
		return $perObj->getValue($attribute);
	}

	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function init()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$session = $this->session;
		$this->perObj = $session->getObject(new JfId($this->objectId));
		$perObj = $this->perObj;
		// Get current user
		$user = $this->user;
		// Get read-only properties (only sysadmin can change properties)
		if ($user->getValue('client_capability') == '8')	{$this->bReadOnly = 'false';}
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
			// Get the user client capability
			// if ($this->user->getValue('client_capability') < 8)	{throw new JcException('INVALID_USER_CAPABILITY');}
			// Get the request object
			$request = new JcHttpServletRequest();
			// Get the login/password
			$objectId = $request->getParameter('objectId');
			$name = $request->getParameter('object_name');
			$address = $request->getParameter('email');
			$description = $request->getParameter('description');
			$user_state = $request->getParameter('user_state');
			$client_capability = $request->getParameter('client_capability');

			if ($name == '')	{throw new JfException($this->getString('ERROR_EMPTY_NAME'));}
			$session = $this->session;
			$perObj = $session->getObject(new JfId($objectId));
			// Case User
			if (substr($objectId, 0, 2) == '11')
			{
				$perObj->setValue('user_name', $name);
				$perObj->setValue('user_os_name', $name);
				$perObj->setValue('user_address', $address);
				$perObj->setValue('description', $description);
				$perObj->setValue('user_state', $user_state);
				$perObj->setValue('client_capability', $client_capability);
			}
			// Case Group
			else if (substr($objectId, 0, 2) == '12')
			{
				$perObj->setValue('group_name', $name);
				$perObj->setValue('group_display_name', $name);
				$perObj->setValue('group_address', $address);
			}
			$perObj->save();
		}
		catch (JfException $exception)
		{
			JcLogger::info('Exception : '.$exception->getMessage());
			$exception->append('An error occured.');
			throw $exception;
		}
	}
}
?>