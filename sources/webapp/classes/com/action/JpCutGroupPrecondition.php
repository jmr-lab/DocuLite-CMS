<?php
/**
 * The Cut Group Precondition class.
 *
 * @package		com.action
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JpCutGroupPrecondition
{
	/**
	 * Returns the location of an ini file.
	 *
	 * @access	public
	 * @param	String	the type of ini file (client, server or webapp)
	 * @return	String	the location of the file.
	 */
	public function JpCutGroupPrecondition() {}

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
		// Get the user details
		$user = $session->getLoginInfo();
		// Check the user client capability
		return ($user->getValue('client_capability') == 8 && in_array(substr($object->getValue('r_object_id'), 0, 2), array('11', '12'))) ? true : false;
	}
}
?>