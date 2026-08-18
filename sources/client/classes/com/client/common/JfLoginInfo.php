<?php
/**
 * The login info class.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfLoginInfo
{
	/**
	* User currently logged in
	*
	* The $user variable is an array containing the currently logged in user name, password and client_capability :
	* 		$user = array(
	* 			'r_object_id' => '0000000000000000',
	* 			'user_name' => 'Test User',
	* 			'user_login_name' => 'testuser',
	* 			'client_capability' => 'Administrator',
	* 			'default_folder' => '0000000000000000',
	* 			'acl_id' => '0000000000000000',
	* 			'r_object_type' => 'jm_user'
	* 		);
	*
	* @todo check if needed
	* @access private
	* @var JfSession
	*/
	private $user = array();

	/**
	 * Constructor
	 *
	 * This function initialize the session
	 *
	 * @param JfSessionManager jfsessionmanager the session manager
	 * @param array user the logged in user
	 * @throws JfException if a server error occurs
	 */
	public function __construct($user)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'($user)');
		// Set the user object
		$this->user = $user;
	}

	/**
	 * Returns the value of an attribute as a string.
	 *
	 * @access public
	 * @return String the value of the attribute
	 * @param attributeName - the name of the attribute
	 * @throws JfException - if a server error occurs
	 */
	public function getValue($attributeName)
	{
		try
		{
			// Return the value if no error occured
			return $this->user[$attributeName];
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>