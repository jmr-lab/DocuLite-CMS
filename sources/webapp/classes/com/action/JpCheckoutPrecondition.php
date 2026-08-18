<?php
/**
 * The Checkout Precondition class.
 *
 * @package		com.action
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JpCheckoutPrecondition
{
	/**
	 * Returns the location of an ini file.
	 *
	 * @access	public
	 * @param	String	the type of ini file (client, server or webapp)
	 * @return	String	the location of the file.
	 */
	public function JpCheckoutPrecondition() {}

	/**
	 * Return whether this action can be called by the specified object.
	 *
	 * @access	public
	 * @param	JfSession	the session object
	 * @param	array		the object
	 * @return	boolean		whether the action can be called by the specified object
	 */
	public function queryExecute($session, $object)
	{
		// JcLogger::info('object[\'r_object_id\'] : '.$object['r_object_id']);
		// JcLogger::info('object[\'r_lock_owner\'] : '.$object['r_lock_owner']);
		if ($object->getValue('r_lock_owner') <> '')	{return false;}
		if ($object->getValue('i_is_deleted'))	{return false;}
		if ($object->getValue('r_accessor_permit') == '' || $object->getValue('owner_name') == '')	{return false;}
		// Case User, Group, ACL, Format, Type or Workflow
		if (in_array($object->getValue('r_object_type'), array('jm_group', 'jm_user', 'jm_acl', 'jm_format', 'jm_type', 'jm_workflow')))	{return false;}
		// Otherwise
		$user = $session->getLoginInfo();
		if ($object->getValue('r_accessor_permit') >= 5)	{return true;}
		else if ($object->getValue('owner_name') == $user->getValue('user_name'))	{return true;}
		else if ($object->getValue('owner_name') == $user->getValue('user_login_name'))	{return true;}
		else 	{return false;}
	}
}
?>