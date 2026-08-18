<?php
/**
 * The Properties Precondition class.
 *
 * @package		com.action
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JpPropertiesPrecondition
{
	/**
	 * Returns the location of an ini file.
	 *
	 * @access	public
	 * @param	String	the type of ini file (client, server or webapp)
	 * @return	String	the location of the file.
	 */
	public function JpPropertiesPrecondition() {}

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
		// For emails :
		$arrExtensions = array('03', '11', '1b', '12', '27', '45', '4d');
		if (in_array(substr($object->getValue('r_object_id'), 0, 2), $arrExtensions))	{return true;}
		// Check that the object contains the owner name and accessor permit
		if ($object->getValue('r_accessor_permit') == '' || $object->getValue('owner_name') == '')	{return false;}
		$user = $session->getLoginInfo();
		return ($object->getValue('r_accessor_permit') <= 1 && $user->getValue('user_name') <> $object->getValue('owner_name')) ? false : true;
	}
}
?>