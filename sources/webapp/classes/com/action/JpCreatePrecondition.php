<?php
/**
 * The Create Precondition class.
 *
 * @package		com.action
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JpCreatePrecondition
{
	/**
	 * Returns the location of an ini file.
	 *
	 * @access	public
	 * @param	String	the type of ini file (client, server or webapp)
	 * @return	String	the location of the file.
	 */
	public function JpCreatePrecondition() {}

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
		// Get the user details
		$user = $session->getLoginInfo();
		// Check the user permissions on the object
		$flag = ($object->getValue('r_accessor_permit') < 4 || $user->getValue('client_capability') == 0) ? false : true;
		return ($object->getValue('owner_name') == $user->getValue('user_name') || $flag) ? true : false;
	}
}
?>