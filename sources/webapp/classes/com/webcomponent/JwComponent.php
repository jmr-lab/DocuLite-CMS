<?php
/**
 * The Component webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwComponent
{
	/**
	* Properties array
	*
	* @access	protected
	* @var		array
	*/
	protected $nlsProperties = array();

	/**
	 * An object
	 *
	 * @access	protected
	 * @var		JfPersistentObject
	 */
	protected $perObj;

	/**
	 * The current session
	 *
	 * @access	protected
	 * @var		JfSession
	 */
	protected $session;

	/**
	 * User connected to the current session
	 *
	 * @access	protected
	 * @var		JcLoginInfo
	 */
	protected $user;

	/**
	 * Constructor
	 *
	 * This function initialize the current user0
	 *
	 */
	public function __construct()
	{
		// Get the user login info
		try
		{
			$sessionmanager = new JfSessionManager();
			$session = $sessionmanager->getSession('www_jmroy');
			$this->session = $session;
			$this->user = $session->getLoginInfo();
		}
		catch (Exception $exception )	{}
		// Get the file name
		$httpsession = new JcHttpSession();
		$lang = $httpsession->getAttribute('lang');
		$this->nlsProperties = JcUtils::getNLSProperties($lang);
	}

	/**
	 * Check the access to a list of objects
	 *
	 * @access	protected
	 * @param	array	arrObjectIds	an array of object Ids
	 * @param	int		level			the minimum access level
	 */
	protected function checkAccess($arrObjectIds, $access)
	{
		$security = new JcSecurity($this->session);
		foreach ($arrObjectIds as $key => $strObjectId)
		{
			$typedObject = new JcTypedObject();
			$typedObject->setObjectId($strObjectId);
			$security->addObject($typedObject);
		}
		$security->setThreshold($access);
		$security->execute();
	}

	/**
	 * Get the error message associated with the current session
	 *
	 * @access	public
	 * @return	String	the error message
	 */
	public function getErrorMessage()
	{
		$httpsession = new JcHttpSession();
		$error = $httpsession->getAttribute('error');
		$httpsession->setAttribute('error', null);
		return $error;
	}

	/**
	 * Get the current date time
	 * This function returns the current date time including milliseconds.
	 *
	 * @access	public
	 * @return	String	the date time
	 */
	public function getString($message)
	{
		$lmessage = JcUtils::getString($this->nlsProperties, strtoupper($message));
		return ((strtoupper($lmessage) == strtoupper($message) && $message <> strtoupper($message)) ? $message : $lmessage);
	}

	/**
	 * Set an object
	 *
	 * @access	public
	 * @param	JfPersistentObject	an object
	 */
	public function setObject($perObj)
	{
		$this->perObj = $perObj;
	}
}
?>