<?php
/**
 * Create webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwCreate extends JwComponent
{
	/**
	 * Get the user's name
	 *
	 * @access	public
	 */
	public function getUserName()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the user login info
		$user = $this->user;
		return $user->getValue('user_name');
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
		$templateId = $request->getParameter('templates');
		$aclId = $request->getParameter('permissions');
		// Get current folder ID
		$pathSO = $httpSession->getAttribute('path');
		$containerId = (sizeof($pathSO) > 0 ? $pathSO[sizeof($pathSO) - 1] :'0000000000000000');
		// Show the parameters
		// JcLogger::info('$object_name : '.$object_name);
		// JcLogger::info('$type : '.$type);
		// JcLogger::info('$templateId : '.$templateId);
		// JcLogger::info('$containerId : '.$containerId);
		// JcLogger::info('$aclId : '.$aclId);
		// If the current component is 'home', then use the user default folder
		$component = current($httpSession->getAttribute('component'));
		if ($component == 'home')	{$user = $this->user; $containerId = $user->getValue('default_folder');}
		try
		{
			// Create the object
			$session = $this->session;
			if ($type == 'jm_document')
			{
				// Get the template object
				$template = $session->getObject(new JfId($templateId));
				$template = JfUtils::cast($template, 'JfSysObject');
				// Create a new SysObject
				$perObj = $session->newObject('jm_document');
				$sysObj = JfUtils::cast($perObj, 'JfSysObject');
				$sysObj->setObjectName($object_name);
				$aclObj = $session->getACL(new JfId($aclId));
				$sysObj->setACL($aclObj);
				$sysObj->setValue('a_content_type', $template->getValue('a_content_type'));
				$sysObj->setRepeatingValue('i_folder_id', '0', $containerId);
				$sysObj->setFile($template->getFile());
				$sysObj->setValue('r_content_size', filesize(_SERVER_ROOT_.$template->getFile()));
				// Lock the object
				$sysObj->setValue('r_lock_machine', $session->getLoginUserName());
				$sysObj->setValue('r_lock_date', date("Y-m-d H:i:s"));
				$sysObj->setValue('r_lock_owner', $session->getLoginUserId());
				// Mark it as 'NEW' and save
//				$sysObj->mark('NEW');
				$sysObj->save();
				// JcLogger::info('onOk - new Id : '.$sysObj->getValue('r_object_id'));
			}
			else if ($type == 'jm_folder')
			{
				// Create a new SysObject
				$perObj = $session->newObject('jm_folder');
				$sysObj = JfUtils::cast($perObj, 'JfSysObject');
				$sysObj->setObjectName($object_name);
				$aclObj = $session->getACL(new JfId($aclId));
				$sysObj->setACL($aclObj);
				$sysObj->setRepeatingValue('i_folder_id', '0', $containerId);
				$sysObj->save();
//				JcLogger::info('onOk - new Id : '.$sysObj->getValue('r_object_id'));
			}
			else
			{
				JcLogger::info('Invalid parameter! Type should be document or folder.');
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