<?php
/**
 * The JfList class.
 *
 * This class provides functionality that encapsulates Vector operations.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfList
{
	/**
	* Vector
	*
	* @access	private
	* @var		array
	*/
	private $arrList;

	/**
	 * Constructor
	 *
	 * This function initialize the list
	 *
	 * @param array the list
	 */
	public function __construct($value)
	{
		// Set the list
		$this->arrList = $value;
	}

	/**
	 * Returns the value of an attribute as a string.
	 *
	 * @access	public
	 * @param	index		the index of the attribute
	 * @return	String		the value of the attribute
	 */
	public function getValue($index)
	{
		// Return the value if no error occured
		return (isset($this->arrList[$index]) ? $this->arrList[$index] : '');
	}
}
?>