<?php
/**
 * The JcCookie class.
 * Usage :
 *
 * $cookie = new JcCookie();
 * $user = $cookie->getCookie('user');
 * $cookie->setCookie('user', $user);
 * $cookie->removeCookie('user');
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcCookie
{
	/**
	* Number of seconds in one month (approx.)
	*
	* @access	private
	* @var		integer
	*/
	private $oneMonth;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		$this->oneMonth = 30 * 24 * 60 * 60 + time(); 
	}

	/**
	 * Get the value of an attribute of the cookie
	 *
	 * @access	public
	 * @param	String	the name of the cookie
	 * @return	String	the value of the cookie
	 */
	public function getCookie($name)
	{
		// Return the value
		return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : null;
	}

	/**
	 * Remove the value of an attribute of the cookie
	 *
	 * @access	public
	 * @param	String	the name of the cookie
	 */
	public function removeCookie($name, $value)
	{
		setcookie($name, $value, "-1", "/");
		setcookie($name, $value, "0", "/");
	}

	/**
	 * Set the value of an attribute of the cookie
	 *
	 * @access	public
	 * @param	String	the name of the cookie
	 * @param	String	the value of the cookie
	 */
	public function setCookie($name, $value)
	{
		setcookie($name, $value, $this->oneMonth, "/");
	}
}
?>