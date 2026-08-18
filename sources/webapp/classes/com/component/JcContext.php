<?php
/**
 * The JcContext component.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcContext
{
	/**
	* Http Session
	*
	* @access	private
	* @var		JcHttpSession
	*/
	private $httpsession;

	/**
	* Components
	*
	* @access	private
	* @var		JcComponentList
	*/
	private $components;

	/**
	 * Constructor
	 *
	 * @param	String	File path
	 */
	public function __construct()
	{
		// Get the session
		$this->httpsession = new JcHttpSession();
		// Init the component list
		$this->components = new JcComponentList($this->httpsession->getAttribute('component'));
	}

	/**
	 * Get the component list
	 *
	 * @access	public
	 * @return	JcComponentList	the component list
	 */
	public function getComponents()
	{
		return $this->components;
	}

	/**
	 * Get the language
	 *
	 * @access	public
	 * @return	String	the language
	 */
	public function getLanguage()
	{
		return $this->httpsession->getAttribute('lang');
	}
}
?>