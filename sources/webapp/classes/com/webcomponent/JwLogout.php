<?php
/**
 * Login webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwLogout extends JwComponent
{
	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function onOk()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Disconnect from the repository
		$sessionmanager = new JfSessionManager();
		$session = $sessionmanager->getSession('www_jmroy');
		$session->disconnect();
		// Init the message to display
		$message = '<div class="warningmessage">'.$this->getString('WARNING').'</div>';
		$script = "	<script>
					$('.right').html('<div style=\"height: 14px;\"><!-- --></div>');
					window.location.href = window.location.href.split(/\?|#/)[0];
					</script>";
		$message = $this->getString('SUCCESSFULLY_LOGGED_OUT').$script;
		// Reset session object (reset component)
		$httpsession = new JcHttpSession();
		$httpsession->removeAttribute('component');
		$httpsession->removeAttribute('folderId');
		$httpsession->removeAttribute('clipboard');
		$httpsession->removeAttribute('lang');
		$httpsession->removeAttribute('view');
		$httpsession->removeAttribute('repository');
		$httpsession->removeAttribute('page');
		// Remove the cookies and reset the session variables
		$logininfo = $session->getLoginInfo();
		$userId = $logininfo->getValue('r_object_id');
		$username = $logininfo->getValue('user_login_name');
		$cookie = new JcCookie();
		$cookie->removeCookie("user[username]", $username);
		$cookie->removeCookie("user[ticket]", $userId);
		return $message;
	}
}
?>