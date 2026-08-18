<?php
/**
 * The JwMailProperties webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwMailProperties extends JwProperties
{
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
	 * Get the description of an email
	 *
	 * @access	public
	 * @return	String	the description
	 */
	public function getDescription()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$description = $this->getValue('item_name');
		if ($description == '')	{$description = $this->getValue('message');}
		return $description;
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
							'r_object_type' => 'eml',
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
	 * Get the title ('User' or 'Group')
	 *
	 * @access	public
	 * @return	String	the title
	 */
	public function getTitle()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		echo $this->getValue('event');
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
	}
}
?>