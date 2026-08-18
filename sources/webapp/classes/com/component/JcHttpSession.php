<?php
/**
 * PHP implementation of the HttpSession class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcHttpSession
{
	/**
	 * Constructor
	 *
	 * This function initialize the current session object.
	 *
	 * @access	public
	 */
	public function __construct()
	{
	}

	/**
	 * Returns the object bound with the specified name in this session, or null if no object is bound under the name.
	 *
	 * @access	public
	 * @param	String	name	a string specifying the name of the object.
	 * @return	String	the object with the specified name.
	 */
	public function getAttribute($name)
	{
		$attr = ((isset($_SESSION['webapp'][$name])) ? $_SESSION['webapp'][$name] : null);
		return $attr;
	}

	/**
	 * Removes the object bound with the specified name from this session.
	 * If the session does not have an object bound with the specified name, this method does nothing.
	 *
	 * @access	public
	 * @param	String	name	the name to which the object is bound; cannot be null.
	 */
	public function removeAttribute($name)
	{
		unset($_SESSION['webapp'][$name]);
	}

	/**
	 * Binds an object to this session, using the name specified.
	 * If an object of the same name is already bound to the session, the object is replaced.
	 *
	 * @access	public
	 * @param	String	name	the name to which the object is bound; cannot be null.
	 * @param	String	value	the object to be bound.
	 */
	public function setAttribute($name, $value)
	{
		$_SESSION['webapp'][$name] = $value;
	}
}
?>