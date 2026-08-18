<?php
/**
 * The AclProperties webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwAclProperties extends JwProperties
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

	// /**
	 // * Get a thumbnail
	 // *
	 // * @access	private
	 // * @return	array	the thumnail icon
	 // */
	// private function getThumbnail($path, $content, $dos)
	// {
		// $thumbnail = 'tn_'.$content.$dos;
		// if (!file_exists(_SERVER_ROOT_.'/data/thumbnail_storage_01/'.$thumbnail))	{$thumbnail = $dos;}
		// return $thumbnail;
	// }

	/**
	 * Displays the permission list
	 *
	 * @access	public
	 */
	public function getPermissionList()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$perObj = $this->perObj;
		$arr_permit = array(1 => 'NONE', 2 => 'BROWSE', 3 => 'READ', 4 => 'RELATE', 5 => 'VERSION', 6 => 'WRITE', 7 => 'DELETE');
		// $accessorCount = $perObj->getAccessorCount();
		// for ($index = 0; $index < $accessorCount; $index++)
		// {
			// echo '<div class="labeltitle">';
			// echo '<span>'.$perObj->getAccessorName($index).' :&nbsp;</span>';
			// echo '<span class="label">'.$perObj->getAccessorPermit($index).'</span>';
			// echo '</div>';
		// }
		$accessorCount = $perObj->getValueCount('r_accessor_name');
		for ($index = 0; $index < $accessorCount; $index++)
		{
			// $index = 0, 2, 4, ...
			if (($index & 1) == 0)	{echo '<div class="labeltitle">';}
			echo '<span>'.$perObj->getRepeatingValue('r_accessor_name', $index).' :&nbsp;</span>';
			echo '<span class="label">'.$arr_permit[$perObj->getRepeatingValue('r_accessor_permit', $index)].'</span>';
			// $index = 1, 3, 5, ... or $index = $accessorCount - 1
			if ((($index & 1) == 1) || $index == ($accessorCount - 1))	{echo '</div>';}
		}
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
		// Get attributes
		$this->attributes['acl_name'] = '';
		$query = new JfQuery();
		$sql = "SELECT object_name FROM jm_acl_s WHERE r_object_id = '".$perObj->getValue('acl_id')."'";
		$query->setSQL($sql);
		$result = $query->execute($session);
		while ($result->next())	{$this->attributes['acl_name'] = $result->getValue('object_name');}
	}
}
?>