<?php
/**
 * JcClipBoard class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcClipBoard
{
	/**
	 * Array of objects (copied in the clipboard) stored in the session
	 *
	 * @access	private
	 * @var		array
	 */
	private $arrObjectList = array();

	/**
	 * Constructor
	 *
	 * This function initialize the current clipboard object.
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// Get the session object
		$httpSession = new JcHttpSession();
		// Get objects from the session
		$this->arrObjectList = $httpSession->getAttribute('clipboard');
	}

	/**
	 * Add an object to the clipboard
	 *
	 * @access	public
	 * @param	JcClipBoardObject	clipboardObject	the object to add
	 */
	public function addObject($clipboardObject)
	{
		if (sizeof($this->arrObjectList) == 0 || array_search($clipboardObject, $this->arrObjectList) === false)	{$this->arrObjectList[] = $clipboardObject;}
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
			foreach ($this->arrObjectList as $key => $clipboardObject)
			{
				if ($strObjectId == $clipboardObject->getObjectId())	{return $clipboardObject;}
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
			foreach ($this->arrObjectList as $key => $clipboardObject)
			{
				$arrObjectId[] = $clipboardObject->getObjectId();
			}
		}
		return $arrObjectId;
	}

	/**
	 * Add an object to the clipboard
	 *
	 * @access	public
	 * @param	JcClipBoardObject	clipboardObject	the object to add
	 */
	public function removeObject($removeObject)
	{
		foreach ($this->arrObjectList as $key => $clipboardObject)
		{
			if ($clipboardObject->getObjectId() == $removeObject->getObjectId())	{unset($this->arrObjectList[$key]);}
		}
	}

	/**
	 * Save the clipboard to the session
	 *
	 * @access	public
	 */
	public function save()
	{
		// Get the session object
		$httpSession = new JcHttpSession();
		// Save the clipboard
		$httpSession->setAttribute('clipboard', $this->arrObjectList);
	}
}
?>