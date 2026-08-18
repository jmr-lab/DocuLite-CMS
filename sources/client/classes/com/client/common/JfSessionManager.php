<?php
/**
 * A session manager.
 *
 * @package com.core.common
 * @author Jean-Marie Roy
 * @copyright Jean-Marie Roy 2011
 * @version 3.0
 */
class JfSessionManager
{
	/**
	* Session
	*
	* @access private
	* @var JfSession
	*/
	private $session;

	/**
	* Array containing the connection identity
	*
	* @access private
	* @var array
	*/
	private $identity = array();

	/**
	 * Constructor
	 *
	 * This function initialize the session manager
	 *
	 * @throws JfException if a server error occurs
	 */
	public function __construct()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Connect to the RDBMS if necessary
//			if (!mysql_ping())	{JfQuery::connect();}
			JfQuery::connect();
			// A session has already been initialised
			if (isset($_SESSION['_USER_']))	{$this->session = new JfSession($this, $_SESSION['_USER_'], $_SESSION['_REPOSITORY_']);}
			// No session found
			else	{$this->session = new JfSession($this, null, null);}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Authenticate to the repository
	 *
	 * Takes the repository name as an argument and throws an exception on bad user credentials.
	 * No session handle is returned.
	 *
	 * @access public
	 * @param String repository - repository name
	 * @throws JfException if the method cannot connect to the server.
	 */
	public function authenticate($repository)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$repository.')');
		try
		{
			// Init the variable $isAlreadyLoggedIn to false
			$isAlreadyLoggedIn = false;
			// Set the session
			$session = $this->session;
			// Checks the identity is correctly populated
			// or if the repository is empty
			if ($repository == '')	{throw new JfException('SESSION_EMPTY_REPOSITORY_NAME');}
			// User name must be set
			if ($this->identity['username'] == '')	{JfLogger::info('SESSION_EMPTY_USER_NAME');}
			// Set the repository details : read the client ini file
			$docbaseConfig = array();
			$properties = JfUtils::getProperties(JfUtils::getIniFile('client'), true);
			$arrRepositories = JfUtils::getRepositories($properties);
			foreach ($arrRepositories as $key => $value)
			{
				if ($value['DATABASE'] == $repository)
				{
					// Add the repository config name to the list of values :
					$value['VALUE'] = $key;
					// Then create a JfTypedObject
					$docbaseConfig = $value;
				}
			}
			// Re connect to the RDBMS
			global $isConnected; $isConnected = false;
			JfQuery::connect();
			// Init the user
			$userObj = $session->getObjectByQualification('jm_user WHERE user_login_name = \''.$this->identity['username'].'\'');
			// if the user is already logged in
			if ($this->identity['username'] == $session->getLoginUserName())	{JfLogger::info('SESSION_USER_ALREADY_LOGGED_IN');$isAlreadyLoggedIn = true;}
			// if the user is inactive or locked (cannot login)
			if ($userObj->getValue('user_state') > 0)	{throw new JfException ('SESSION_USER_LOCKED_OR_INACTIVE');}
			// if the password or the ticket is wrong
			if (isset($this->identity['password']) && ($userObj->getValue('user_password') <> $this->identity['password']))
				{throw new JfException ('SESSION_INVALID_USER');}
			if (isset($this->identity['ticket']) && (md5($userObj->getValue('r_object_id').$userObj->getValue('user_password')) <> $this->identity['ticket']))
				{throw new JfException ('SESSION_INVALID_USER');}
			// Get the number of failed_auth_attempt
			$failed_auth_attempt = $userObj->getValue('failed_auth_attempt');

			// Set the session user
			$user = array(
				'r_object_id' => $userObj->getValue('r_object_id'),
				'user_name' => $userObj->getValue('user_name'),
				'user_login_name' => $userObj->getValue('user_login_name'),
				'client_capability' => $userObj->getValue('client_capability'),
				'default_folder' => $userObj->getValue('default_folder'),
				'acl_id' => $userObj->getValue('acl_id'),
				'r_object_type' => 'jm_user'
			);
			$this->session = new JfSession($this, $user, $docbaseConfig);

			// @todo - Reset the last login time value
//			$userObj->setValue('last_login_utc_time', date("Y-m-d H:i:s"));
//			$userObj->save();
			// @todo - end

			// Eventually reset the user's details
			if ($failed_auth_attempt > 0)
			{
				$userObj->setValue('failed_auth_attempt', 0);
				$userObj->save();
			}
			// Create a login event if the user is not logged in
			if (!$isAlreadyLoggedIn)
			{
				$session = $this->session;
				$auditTrailMgr = $session->getAuditTrailManager();
				$languages = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
				$stringArgs = array(	'userName' => $userObj->getValue('user_name'),
										'userIP' => getenv("REMOTE_ADDR"),
										'browser' => '',
										'os' => '',
										'referer' => getenv("HTTP_REFERER"),
										'language' => $languages[0],
										'details' => $_SERVER['HTTP_USER_AGENT']	);
				$auditTrailMgr->createAudit($userObj->getValue('r_object_id'), 'login', $stringArgs, null);
			}
		}
		catch (JfException $exception)
		{
			// Get the user's name
			$userName = '';
			// Change the user's details
			if (isset($failed_auth_attempt) && isset($userObj))
			{
				if ($failed_auth_attempt > 1)	{$userObj->setValue('user_state', 2);}
				if ($failed_auth_attempt > -1)	{$userObj->setValue('failed_auth_attempt', 1 + $failed_auth_attempt);}
				if ($failed_auth_attempt > -1 && $userObj->getValue('r_object_id') <> '')	{$userObj->save();}
				$userName = $userObj->getValue('user_name');
			}
			// Create a failed login event
			if ($userName <> 'guest' && $userName <> '')
			{
				$session = $this->session;
				$auditTrailMgr = $session->getAuditTrailManager();
				$languages = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
				$stringArgs = array(	'userName' => $userName,
										'userIP' => getenv("REMOTE_ADDR"),
										'browser' => '',
										'os' => '',
										'referer' => getenv("HTTP_REFERER"),
										'language' => $languages[0],
										'details' => $_SERVER['HTTP_USER_AGENT']	);
				$auditTrailMgr->createAudit('', 'logon_failure', $stringArgs, null);
			}
			// And throw an exception
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Get a session object.
	 *
	 * To get the session handle a service implementation should call the getSession method.
	 * The session can be retrieved as many times as required.
	 * When the client is done the session must be released using the release method.
	 * If a JfSession has not been established for the the specified repository, a new session is automatically created.
	 * (The session object is cached)
	 *
	 * @access	public
	 * @param	String		repository - repository name
	 * @return	JfSession	an array to the query results.
	 * @throws	JfException	if the session hasn't been established.
	 */
	public function getSession($repository)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$repository.')');
		try
		{
			if (!isset($_SESSION['_USER_']))	{throw new JfException('SESSION_INVALID_SESSION');}
			if (isset($this->session))	{return $this->session;}
			else	{throw new JfException('SESSION_INVALID_SESSION');}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}

	/**
	 * Sets the identity for "manual" user authentication.
	 *
	 * The identity must not already exist in the session manager's idenity list, otherwise an exception is thrown.
	 * Use "clearIdentity" first if you want to overwrite in any case.
	 *
	 * @access public
	 * @param String repository - repository name
	 * @param array identity - array set to the user credentials for that repository, like user name and password.
	 * @throws JfException Identity for this repository is already set.
	 */
	public function setIdentity($repository, $identity)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$repository.', $identity)');
		try
		{
			// Checks the identity is correctly populated
			// If the user name is empty
			if (!isset($identity['username']) || $identity['username'] == '')	{throw new JfException ('SESSION_EMPTY_USER_NAME');}
			// or if the repository is empty
			if (!isset($identity['repository']) || $identity['repository'] == '')	{throw new JfException ('SESSION_EMPTY_REPOSITORY_NAME');}
			// $identity['username'] = 'jdoe';
			// $identity['password'] = 'qwerty';
			// $identity['ticket'] = 'qwerty';
			$this->identity = array(	"username" => $identity['username'],
										"repository" => $repository);
			if (isset($identity['password']))	{$this->identity['password'] = md5($identity['password']);}
			if (isset($identity['ticket']))	{$this->identity['ticket'] = $identity['ticket'];}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}
}
?>