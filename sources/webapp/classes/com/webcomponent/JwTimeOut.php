<?php
/**
 * TimeOut webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwTimeOut extends JwComponent
{
	/**
	 * Constructor
	 *
	 * This function initialize the current user0
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		// Remove some session attributes
		$httpsession = new JcHttpSession();
		$httpsession->removeAttribute('path');
		$httpsession->removeAttribute('component');
	}
}
?>