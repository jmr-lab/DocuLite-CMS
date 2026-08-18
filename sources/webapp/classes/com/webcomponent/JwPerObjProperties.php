<?php
/**
 * The PerObj Properties webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwPerObjProperties extends JwProperties
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
		echo 'unknown.png';
	}

	/**
	 * Get the object type
	 *
	 * @access	public
	 * @return	String	the object type
	 */
	public function getObjectType()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$objectTag = substr($this->objectId, 0, 2);
		$type_names = JfUtils::$typeNames;
		$type = (isset($type_names[hexdec($objectTag)])) ? $type_names[hexdec($objectTag)] : '';
		echo $type;
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
		$value = '';
		if ($attribute == 'r_object_id')	{$value = $this->objectId;}
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
	}
}
?>