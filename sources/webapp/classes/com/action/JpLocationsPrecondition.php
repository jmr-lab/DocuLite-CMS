<?php
/**
 * The Locations Precondition class.
 *
 * @package		com.action
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JpLocationsPrecondition
{
	/**
	 * Returns the location of an ini file.
	 *
	 * @access	public
	 * @param	String	the type of ini file (client, server or webapp)
	 * @return	String	the location of the file.
	 */
	public function JpLocationsPrecondition() {}

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
		if ($object->getValue('r_accessor_permit') == '' || $object->getValue('owner_name') == '' || $object->getValue('r_content_size') == '')	{return false;}
		if ($object->getValue('i_is_deleted'))	{return false;}
		$user = $session->getLoginInfo();
		$flag = ($object->getValue('r_accessor_permit') <= 2 && $user->getValue('user_name') <> $object->getValue('owner_name')) ? false : true;
		return ($object->getValue('r_content_size') > 0 && $flag) ? true : false;
	}
}
?>