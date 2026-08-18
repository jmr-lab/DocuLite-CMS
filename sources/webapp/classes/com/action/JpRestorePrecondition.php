<?php
/**
 * The Restore Precondition class.
 *
 * @package		com.action
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JpRestorePrecondition
{
	/**
	 * Returns the location of an ini file.
	 *
	 * @access	public
	 * @param	String	the type of ini file (client, server or webapp)
	 * @return	String	the location of the file.
	 */
	public function JpRestorePrecondition() {}

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
		// Check that the object contains the owner name and accessor permit
		if ($object->getValue('r_accessor_permit') == '' || $object->getValue('owner_name') == '')	{return false;}
		$flag = false;
		if ($object->getValue('i_is_deleted') == 1)	{$flag = true;}
		$user = $session->getLoginInfo();
		if ($object->getValue('r_accessor_permit') == 7 && $flag)	{return true;}
		else if (($object->getValue('owner_name') == $user->getValue('user_name')) && $flag)	{return true;}
		else if (($object->getValue('owner_name') == $user->getValue('user_login_name')) && $flag)	{return true;}
		else 	{return false;}
	}
}
?>