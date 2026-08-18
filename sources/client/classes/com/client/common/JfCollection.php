<?php
/**
 * An Estancia collection object.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfCollection
{
	/**
	* Array of arrays :
	*
	* This array contains a collection of objects :
	* $col = array(	0 => array("r_object_id" => "0900d5bb8001f900", "object_name" => "Document 1", ...),
	*				1 => array("r_object_id" => "0900d5bb8001f901", "object_name" => "Document 2", ...),
	* 				2 => array("r_object_id" => "0900d5bb8001f902", "object_name" => "Document 3", ...), ...
	* 				);
	* 
	* @access private
	* @var String
	*/
	private $collection;

	/**
	* Check if the next function has ever been accessed (position of the current element)
	*
	* @access	private
	* @var		boolean
	*/
	private $current = false;

	/**
	* Session object
	*
	* @access	private
	* @var		JfSession
	*/
	private $session;

	/**
	 * Constructor
	 *
	 * This function initialize the collection
	 *
	 * @param	Array		The collection
	 * @throws	JfException	if a server error occurs
	 */
	public function __construct($session, $col)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Check if id is a string
		if (!is_array($col))	{throw new JfException('COMMON_INVALID_COLLECTION');}
		// Set the session
		$this->session = $session;
		// Set the collection
		$this->collection = $col;
	}

	/**
	 * Returns the current part of the collection.
	 *
	 * @access	public
	 * @return	Array	the current part of the collection.
	 */
	public function getResult()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return current($this->collection);
	}

	/**
	 * Returns the current part of the collection.
	 *
	 * @access	public
	 * @return	Array	the current part of the collection.
	 */
	public function getTypedObject()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return new JfTypedObject($this->session, current($this->collection));
	}

	/**
	 * Returns the next part of the collection.
	 *
	 * @access	public
	 * @return	Array	the next part of the collection.
	 * If there is no next part returns false.
	 */
	public function next()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$arr = ($this->current === false) ? current($this->collection) : next($this->collection);
		$this->current = true;
		return $arr;
	}

	/**
	 * Returns the portion of the ID that identifies the repository.
	 *
	 * @access	public
	 * @param	String	Index name
	 * @return	String	the value specified by the name.
	 */
	public function getValue($name)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$arr = current($this->collection);
		return $arr[$name];
	}
}
?>