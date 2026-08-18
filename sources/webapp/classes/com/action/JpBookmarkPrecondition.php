<?php
/**
 * The Bookmark Precondition class.
 *
 * @package		com.action
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JpBookmarkPrecondition
{
	/**
	 * Returns the location of an ini file.
	 *
	 * @access	public
	 * @param	String	the type of ini file (client, server or webapp)
	 * @return	String	the location of the file.
	 */
	public function JpBookmarkPrecondition() {}

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
		// Case User, Group, ACL, Format, Type or Workflow
		if (in_array($object->getValue('r_object_type'), array('jm_group', 'jm_user', 'jm_acl', 'jm_format', 'jm_type', 'jm_workflow')))	{return false;}
		// Get the favorites object
		$favorites = new JcBookMark();
		if (in_array($object->getValue('r_object_id'), $favorites->getObjectIds()))	{return false;}
		// Get current user
		$user = $session->getLoginInfo();
		$userName = array($user->getValue('user_name'), $user->getValue('user_login_name'), $user->getValue('r_object_id'));
		// Otherwise
		return ($object->getValue('r_accessor_permit') <= 1 && $user->getValue('user_name') <> $object->getValue('owner_name')) ? false : true;
	}
}
?>