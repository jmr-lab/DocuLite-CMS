<?php
/**
 * Login webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwLogin extends JwComponent
{
	/**
	 * Get a list of accessible repositories (configured in the client.ini file)
	 *
	 * @access	public
	 * @return	String	A list of repositories.
	 */
	public function getRepositories()
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Read the client ini file
		$properties = JfUtils::getProperties(JfUtils::getIniFile('client'), true);
		// Get the server and database names
		$arrRepositories = JfUtils::getRepositories($properties);
		foreach ($arrRepositories as $key => $value)
		{
			$repository = $value['DOCBASE_NAME'];
			$server = $value['SERVER'];
			echo '<jm:option value="'.$value['DATABASE'].'">'.strtoupper($repository).' ('.$server.')</jm:option>';
		}
		// echo '<jm:option value="estancia">ESTANCIA (www.jmroy.free.fr)</jm:option>';
		// echo '<jm:option value="documentum">DOCUMENTUM (www.jmroy.free.fr)</jm:option>';
	}

	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function onOk()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the request object
		$request = new JcHttpServletRequest();
		// Get the login/password
		$user_name = $request->getParameter('user_name');
		$password = $request->getParameter('password');
		$keeploggedin = $request->getParameter('keeploggedin');
		$docbroker = $request->getParameter('docbroker');
		// JfLogger::info('Docbroker : '.$docbroker);
		// For user 'guest', reset the password :
		if ($user_name == 'guest')	{$password = '';}
		// Init the message to display
		$message = '<div class="warningmessage">'.$this->getString('WARNING').'</div>';
		// Authentication to the repository
		try
		{
			$sessionmanager = new JfSessionManager();
			$identity = array('repository' => $docbroker, 'username' => $user_name, 'password' => $password);
			$sessionmanager->setIdentity($docbroker, $identity);
			$sessionmanager->authenticate($docbroker);
			$script = "	<script>
						$('#user_name').attr('disabled', 'disabled');
						$('#password').attr('disabled', 'disabled');
						$('#docbroker').attr('disabled', 'disabled');
						$('#keeploggedin').attr('disabled', 'disabled');
						$('.right').html('<div style=\"height: 14px;\"><!-- --></div>');
						window.location.href = window.location.href.split(/\?|#/)[0];
						</script>";
			$message = $this->getString('SUCCESSFULLY_LOGGED_IN').$script;
		}
		catch (JfException $exception)
		{
			$message = $this->getString('ERROR').' : '.$exception->getMessage();
		}
		// Reset session object (reset component)
		$httpsession = new JcHttpSession();
		$httpsession->removeAttribute('component');
		// Set the docbroker
		$httpsession->setAttribute('docbroker', $docbroker);
		// Set the welcome message
		$httpsession->setAttribute('welcome', true);
		// Set a cookie if the user has asked for one
//		JcLogger::info(__CLASS__.'.'.__FUNCTION__.'($keeploggedin : '.$keeploggedin.')');
		if ($keeploggedin == 'true')
		{
			$session = $sessionmanager->getSession('estancia');
			$logininfo = $session->getLoginInfo();
			$userId = $logininfo->getValue('r_object_id');
			$ticket = md5($userId.md5($password));
			$cookie = new JcCookie();
			$cookie->setCookie("user[username]", $user_name);
			$cookie->setCookie("user[ticket]", $ticket);
		}
		return $message;
	}
}
?>