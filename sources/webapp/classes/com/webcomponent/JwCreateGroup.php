<?php
/**
 * Create Group webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwCreateGroup extends JwComponent
{
	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function __construct()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Run the parent method
		parent::__construct();
		// Get the user login info
		$user = $this->user;
		// Check if user has enough permission (client_capability must be 8)
		JcLogger::debug('client_capability : '.$user->getValue('client_capability'));
		if ($user->getValue('client_capability') < 8)	{throw new JcException('OBJECT_INVALID_ACCESS');}
	}

	/**
	 * Returns the ID of the 'World' group (base group).
	 *
	 * @access	private
	 * @return	String	the 'world' ID
	 */
	private function getWorldId()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$worldId = '0000000000000000';
		$sessionmanager = new JfSessionManager();
		$session = $sessionmanager->getSession('www_jmroy');
		$query = new JfQuery();
		$query->setSQL('SELECT r_object_id FROM jm_group_s WHERE group_name = \'world\'');
		$results = $query->execute($session);
		while ($results->next())	{$worldId = $results->getValue('r_object_id');}
		return $worldId;
	}

	/**
	 * Check if the current container is the 'World' group
	 *
	 * @access	public
	 */
	public function isWorld()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get current container ID
		$httpSession = new JcHttpSession();
		$pathSO = $httpSession->getAttribute('path');
		$containerId = (sizeof($pathSO) > 0 ? $pathSO[sizeof($pathSO) - 1] :'0000000000000000');
		return ($containerId == '0000000000000000' ? 'true' : 'false');
	}

	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function onOk()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the request and session objects
		$request = new JcHttpServletRequest();
		$httpSession = $request->getSession();
		// Get the data sent to the server
		$object_name = $request->getParameter('object_name');
		$type = $request->getParameter('type');
		$login = $request->getParameter('login');
		$password = $request->getParameter('password');
		$address = $request->getParameter('address');
		// Get current folder ID
		$pathSO = $httpSession->getAttribute('path');
		$containerId = (sizeof($pathSO) > 0 ? $pathSO[sizeof($pathSO) - 1] :'0000000000000000');
		// Show the parameters
		// JcLogger::info('$object_name : '.$object_name);
		// JcLogger::info('$type : '.$type);
		// JcLogger::info('$login : '.$login);
		// JcLogger::info('$password : '.$password);
		// JcLogger::info('$address : '.$address);
		// JcLogger::info('$containerId : '.$containerId);
		try
		{
			// Create the object
			$session = $this->session;
			if ($type == 'jm_user')
			{
				// Create a new SysObject
				$perObj = $session->newObject('jm_user');
				$sysObj = JfUtils::cast($perObj, 'JfSysObject');
				$login = ($login == '' ? $object_name : $login);
				$sysObj->setValue('user_name', $object_name);
				$sysObj->setValue('user_os_name', $object_name);
				$sysObj->setValue('user_login_name', $login);
				$sysObj->setValue('user_password', md5($password));
				$sysObj->setValue('user_address', $address);
				$sysObj->save();
				// JcLogger::info('onOk - new Id : '.$sysObj->getValue('r_object_id'));
				$groupObj = $session->getGroup(new JfId($containerId));
				$groupObj->addUser($sysObj->getValue('r_object_id'));
				$groupObj->save();
				// TODO - send an email to the user
				// $session->sendToDistributionListEx($userArr, null, 'Welcome to this new Content Management System. It will be very soon completely compatible with Documentum. So far only the dm_group_r and dm_user_r tables are different.', null, null, 'Welcome');
			}
			else if ($type == 'jm_group')
			{
				// Create a new SysObject
				$perObj = $session->newObject('jm_group');
				$sysObj = JfUtils::cast($perObj, 'JfSysObject');
				$sysObj->setValue('group_name', $object_name);
				$sysObj->setValue('login', $login);
				$sysObj->setValue('password', $password);
				$sysObj->setValue('group_address', $address);
				$sysObj->save();
				// JcLogger::info('onOk - new Id : '.$sysObj->getValue('r_object_id'));
				// Only add the group to a supergroup if it is not the 'World' group
				if ($containerId == '0000000000000000')	{$containerId = $this->getWorldId();}
				$groupObj = $session->getGroup(new JfId($containerId));
				$groupObj->addGroup($sysObj->getValue('r_object_id'));
				$groupObj->save();
			}
			else
			{
				JcLogger::info('Invalid parameter! Type should be user or group.');
			}
		}
		catch (JfException $exception)
		{
				JcLogger::info('An error occured : '.$exception->getMessage());
				throw new Exception($exception->getMessage());
		}
		return '';
	}
}
?>