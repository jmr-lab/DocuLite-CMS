<?php
/**
 * The JcDataGrid class.
 * Usage :
 *
 * $datagrid = new JcDataGrid($this->user, $this->nlsProperties);
 * $datagrid->setModal(true);
 * $datagrid->setTitle($this->name);
 * $datagrid->setObjects($this->objects);
 * $datagrid->render();
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcDataGrid
{
	/**
	 * Component Name (doclist, recyclebin, ...)
	 *
	 * @access	private
	 * @var		array
	 */
	private $component;

	/**
	 * List of properties
	 *
	 * @access	private
	 * @var		array
	 */
	private $nlsProperties;

	/**
	 * Object class (perobj or modalperobj)
	 *
	 * @access	protected
	 * @var		string
	 */
	private $objectclass = 'perobj';

	/**
	 * List of objects
	 *
	 * @access	private
	 * @var		array
	 */
	private $objects;

	/**
	 * Search string
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $strSearch = '';

	/**
	* Title
	*
	* @access	private
	* @var		String
	*/
	private $title = 'object_name';

	/**
	* Current user
	*
	* @access	private
	* @var		JfUser
	*/
	private $user;

	/**
	 * Constructor
	 *
	 * @param	JfUser	Current user
	 */
	public function __construct($user, $nlsProperties)
	{
		$this->nlsProperties = $nlsProperties;
		$this->user = $user;
	}

	/**
	 * array_fill_keys doesn't exist in free.fr
	 *
	 * @access	protected
	 * @param	array	the message
	 * @return	array	the localized message
	 */
	protected function array_fill_keys($keyArray, $value)
	{
		foreach($keyArray as $key => $valueArray)
		{
			$filledArray[$valueArray] = $value;
		}
		return $filledArray;
	}

	/**
	 * Get a localized message
	 *
	 * @access	private
	 * @param	String	the message
	 * @return	String	the localized message
	 */
	protected function getString($message)
	{
		$lmessage = JcUtils::getString($this->nlsProperties, strtoupper($message));
		return ((strtoupper($lmessage) == strtoupper($message) && $message <> strtoupper($message)) ? $message : $lmessage);
	}

	/**
	 * Render the grid
	 *
	 * @access	public
	 */
	public function render()
	{
	}

	/**
	 * Get the component
	 *
	 * @access	protected
	 * @return	String		The current component
	 */
	protected function getComponent()
	{
		return $this->component;
	}

	/**
	 * Get the object class
	 *
	 * @access	protected
	 * @param	boolean		is the grid modal
	 */
	protected function getObjectClass()
	{
		return $this->objectclass;
	}

	/**
	 * Get the objects
	 *
	 * @access	protected
	 * @return	array		The objects to display
	 */
	protected function getObjects()
	{
		return $this->objects;
	}

	/**
	 * Get the page number
	 *
	 * @access	protected
	 * @return	String		The current page number
	 */
	protected function getPageNumber()
	{
		return $this->pageNumber;
	}

	/**
	 * Get the search string
	 *
	 * @access	protected
	 * @return	String		The search string
	 */
	protected function getSearch()
	{
		return $this->strSearch;
	}

	/**
	 * Get the title
	 *
	 * @access	protected
	 * @return	String		The title to display (default to 'object_name')
	 */
	protected function getTitle()
	{
		return $this->title;
	}

	/**
	 * Get the user
	 *
	 * @access	protected
	 * @return	String		The current user
	 */
	protected function getUser()
	{
		return $this->user;
	}

	/**
	 * Set the component
	 *
	 * @access	public
	 * @param	String	The component name
	 */
	public function setComponent($component)
	{
		$this->component = $component;
	}

	/**
	 * Set the grid as modal
	 *
	 * @access	public
	 * @param	boolean		is the grid modal
	 */
	public function setModal($modal)
	{
		if ($modal)	{$this->objectclass = 'modalperobj';}
	}

	/**
	 * Set the objects
	 *
	 * @access	public
	 * @param	array		The objects to display
	 */
	public function setObjects($objects)
	{
		$this->objects = $objects;
	}

	/**
	 * Set the page number
	 *
	 * @access	public
	 * @param	int		The page number
	 */
	public function setPageNumber($pageNumber)
	{
		$this->pageNumber = $pageNumber;
	}

	/**
	 * Set the search string
	 *
	 * @access	public
	 * @param	String		The search string
	 */
	public function setSearch($strSearch)
	{
		$this->strSearch = $strSearch;
	}

	/**
	 * Set the title
	 *
	 * @access	public
	 * @param	String		The title to display (default to 'object_name')
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
}
?>