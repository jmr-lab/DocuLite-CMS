<?php
/**
 * The JcComponentList class.
 * Usage :
 *
 * $components = new JcComponentList($components);
 * ...
 * $images->getIcon($object);
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcComponentList
{
	/**
	 * List of components
	 *
	 * @access	private
	 * @var		String
	 */
	private $components = array();

	/**
	 * Constructor
	 *
	 * @param	array	List of objects
	 * @param	int		the size
	 */
	public function __construct($components)
	{
		$this->components = $components;
	}

	/**
	 * Get the first component
	 *
	 * @access	public
	 * @return	String the first component
	 */
	public function getFirstComponent()
	{
		return $this->components[0];
	}

	/**
	 * Get the last component
	 *
	 * @access	public
	 * @return	String the last component
	 */
	public function getLastComponent()
	{
		return end($this->components);
	}

	/**
	 * Get the number of components in the stack
	 *
	 * @access	public
	 * @return	int 	the number of components
	 */
	public function getComponentsSize()
	{
		return sizeof($this->components);
	}
}
?>