<?php
/**
 * JcBookMark class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcBookMark
{
	/**
	 * Array of objects (copied in the favorites) stored in the session
	 *
	 * @access	private
	 * @var		array
	 */
	private $arrObjectList = array();

	/**
	 * Constructor
	 *
	 * This function initialize the current bookmark object.
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// Get the session object
		$httpSession = new JcHttpSession();
		// Get objects from the session
		$this->arrObjectList = $httpSession->getAttribute('favorites');
	}

	/**
	 * Add an object to the favorites
	 *
	 * @access	public
	 * @param	JcBookMarkObject	bookmarkObject	the object to add
	 */
	public function addObject($bookmarkObject)
	{
		if (sizeof($this->arrObjectList) == 0 || array_search($bookmarkObject, $this->arrObjectList) === false)	{$this->arrObjectList[] = $bookmarkObject;}
	}

	/**
	 * Get an object specified by its Id.
	 *
	 * @access	public
	 * @param	String	strObjectId		the object Id to retrieve
	 * @return	array	An object
	 */
	public function getObject($strObjectId)
	{
		if (sizeof($this->arrObjectList) > 0)
		{
			foreach ($this->arrObjectList as $key => $bookmarkObject)
			{
				if ($strObjectId == $bookmarkObject->getObjectId())	{return $bookmarkObject;}
			}
		}
		return null;
	}

	/**
	 * Get the value of the parameter.
	 *
	 * If it doesn't exist, null will be returned. If it exists but is empty, it will be a string.
	 *
	 * @access	public
	 * @param	String	name	the name of the parameter
	 * @return	array	A list of object Ids
	 */
	public function getObjectIds()
	{
		$arrObjectId = array();
		if (sizeof($this->arrObjectList) > 0)
		{
			foreach ($this->arrObjectList as $key => $bookmarkObject)
			{
				$arrObjectId[] = $bookmarkObject->getObjectId();
			}
		}
		return $arrObjectId;
	}

	/**
	 * Add an object to the favorites
	 *
	 * @access	public
	 * @param	JcClipBoardObject	bookmarkObject	the object to add
	 */
	public function removeObject($removeObject)
	{
		foreach ($this->arrObjectList as $key => $bookmarkObject)
		{
			if ($bookmarkObject->getObjectId() == $removeObject->getObjectId())	{unset($this->arrObjectList[$key]);}
		}
	}

	/**
	 * Save the favorites to the session
	 *
	 * @access	public
	 */
	public function save()
	{
		// Get the session object
		$httpSession = new JcHttpSession();
		// Save the favorites
		$httpSession->setAttribute('favorites', $this->arrObjectList);
	}
}
?>