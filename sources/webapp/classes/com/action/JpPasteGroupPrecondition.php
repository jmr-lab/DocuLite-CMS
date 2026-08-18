<?php
/**
 * The Paste Group Precondition class.
 *
 * @package		com.action
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JpPasteGroupPrecondition
{
	/**
	 * Returns the location of an ini file.
	 *
	 * @access	public
	 * @param	String	the type of ini file (client, server or webapp)
	 * @return	String	the location of the file.
	 */
	public function JpPasteGroupPrecondition() {}

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
		// Do not display the 'Paste' action if the clipboard is empty
		// Get the objects from the clipboard
		$clipboard = new JcClipBoard();
		$objectIds = $clipboard->getObjectIds();
		if (sizeof($objectIds) == 0)	return false;
		// Check that there is at least one user or one group in the clipboard
		$flag = false;
		foreach ($objectIds as $key => $objectId)	{if (in_array(substr($objectId, 0, 2), array('11', '12')))	$flag = true;}
		// Get the user details
		$user = $session->getLoginInfo();
		// Check the user client capability
		return ($user->getValue('client_capability') == 8 && in_array(substr($object->getValue('r_object_id'), 0, 2), array('11', '12'))) ? $flag : false;
	}
}
?>