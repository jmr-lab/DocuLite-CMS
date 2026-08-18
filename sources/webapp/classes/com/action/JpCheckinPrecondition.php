<?php
/**
 * The Checkin Precondition class.
 *
 * @package		com.action
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JpCheckinPrecondition
{
	/**
	 * Returns the location of an ini file.
	 *
	 * @access	public
	 * @param	String	the type of ini file (client, server or webapp)
	 * @return	String	the location of the file.
	 */
	public function JpCheckinPrecondition() {}

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
		if ($object->getValue('r_lock_owner') == '' || $object->getValue('r_object_id') == '')	{return false;}
		if (in_array(substr($object->getValue('r_object_id'), 0, 2), array('0b', '0c')))	{return false;}
		$user = $session->getLoginInfo();
		// Check permission
		if ($object->getValue('r_accessor_permit') <= 1 && $user->getValue('user_name') <> $object->getValue('owner_name'))	{return false;}
		if ($object->getValue('r_lock_owner') == $user->getValue('user_name'))	{return true;}
		else if ($object->getValue('r_lock_owner') == $user->getValue('user_login_name'))	{return true;}
		else if ($object->getValue('r_lock_owner') == $user->getValue('r_object_id'))	{return true;}
		else 	{return false;}
	}
}
?>