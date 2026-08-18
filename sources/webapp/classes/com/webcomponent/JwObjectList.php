<?php
/**
 * The ObjectList webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwObjectList extends JwComponent
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('checkout', 'icon', 'object_name', 'properties', 'r_content_size', 'description', 'r_modify_date');

	/**
	* Columns properties
	*
	* @access	protected
	* @var		array
	*/
	protected $columnsProps = array();

	/**
	 * Component Name (doclist, recyclebin, ...)
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $component;

	/**
	 * Boolean to increase search speed
	 *
	 * @access	protected
	 * @var		boolean
	 */
	protected $fastsearch = false;

	/**
	 * Folder ID
	 *
	 * @access	private
	 * @var		String
	 */
	protected $folderId;

	/**
	 * Folder Object
	 *
	 * @access	private
	 * @var		JfTypedObject
	 */
	protected $folderObj;

	/**
	 * Empty message
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $empty_message = 'EMPTY_FOLDER';

	/**
	 * Column name to display
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $name = 'object_name';

	/**
	 * Object class (perobj or modalperobj)
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $objectclass = 'perobj';

	/**
	 * Object grid content div ID
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $objectgridcontent = 'objectgridcontent';

	/**
	 * Objects returned by a query
	 *
	 * @access	private
	 * @var		array
	 */
	protected $objects;

	/**
	 * Order of the query (default is 'ASC')
	 *
	 * @access	private
	 * @var		String
	 */
	protected $order = 'ASC';

	/**
	 * Page Number
	 *
	 * @access	protected
	 * @var		int
	 */
	protected $pageNumber = 1;

	/**
	 * Page Count
	 *
	 * @access	private
	 * @var		int
	 */
	private $pageCount = 1;

	/**
	 * Path
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $path = null;

	/**
	 * Results Count
	 *
	 * @access	private
	 * @var		int
	 */
	private $resultsCount = 1;

	/**
	 * Results Size (default is 30)
	 *
	 * @access	private
	 * @var		int
	 */
	protected $resultsSize = 30;

	/**
	 * Search string
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $strSearch = '';

	/**
	 * SQL Query
	 *
	 * @access	private
	 * @var		JcQuery
	 */
	private $sql = null;

	/**
	 * Sort order of the query.
	 *
	 * @access	private
	 * @var		String
	 */
	protected $sort = '';

	/**
	 * View (thumbnails or details)
	 *
	 * @access	private
	 * @var		String
	 */
	protected $view = '';

	/**
	 * Checks the permit on the current folder
	 *
	 * @access	protected
	 */
	protected function checkPermit()	{}

	/**
	 * Get the breadbrumb values (sort, order, strSearch, component...)
	 *
	 * @access	public
	 * @return	array	a list of values
	 */
	public function getBreadCrumbValues()
	{
		$values = array(	'sort' => $this->sort,
							'order' => $this->order,
							'view' => $this->view,
							'strSearch' => $this->strSearch,
							'component' => $this->component,
							'resultsCount' => $this->resultsCount,
							'resultsSize' => $this->resultsSize,
							'pageNumber' => $this->pageNumber
						);
		return $values;
	}

	/**
	 * Get the datagrid values (sort, order, strSearch, component...)
	 *
	 * @access	public
	 * @return	array	a list of values
	 */
	public function getDataGridValues()
	{
		if ($this->view == 'details')	{$this->objectgridcontent = 'objectgridcontentdetail';}
		$values = array(	'session' => $this->session,
							'folderObj' => $this->folderObj,
							'objects' => $this->objects,
							'view' => $this->view,
							'user' => $this->user,
							'nlsProperties' => $this->nlsProperties,
							'objectgridcontent' => $this->objectgridcontent,
							'columns' => $this->columns,
							'columnsProps' => $this->columnsProps,
							'component' => $this->component,
							'empty_message' => $this->empty_message,
							'fastsearch' => $this->fastsearch,
							'pageNumber' => $this->pageNumber,
							'strSearch' => $this->strSearch,
							'sort' => $this->sort,
							'order' => $this->order,
							'name' => $this->name
						);
		return $values;
	}

	/**
	 * Get the SQL query for the list
	 *
	 * @access	protected
	 * @return	String	the SQL query
	 */
	protected function getSQL()
	{
		return $this->sql;
	}

	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function init()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Checks the permit on the current folder
		$this->checkPermit();
		// Get the request and session objects
		$request = new JcHttpServletRequest();
		$httpsession = $request->getSession();
		// Init the current component
		$component = (($httpsession->getAttribute('component') <> null) ? $httpsession->getAttribute('component') : array(0 => 'doclist'));
		$this->component = end($component);
		// Get the current page number
		$page = new JcPage();
		$this->pageNumber = $page->getPage($request);
		// Init the current view
		$this->view = (($httpsession->getAttribute('view') <> null) ? $httpsession->getAttribute('view') : 'thumbnails');
		$httpsession->setAttribute('view', $this->view);
		// Init the current sort/order
		$this->order = (($httpsession->getAttribute('order') <> null) ? $httpsession->getAttribute('order') : 'ASC');
		$httpsession->setAttribute('order', $this->order);
		$this->sort = (($request->getParameter('sort') <> '') ? $request->getParameter('sort') : '');
		// Init the result size
		$this->resultsSize = (($httpsession->getAttribute('results') <> null) ? $httpsession->getAttribute('results') : '30');
		if ($this->objectgridcontent == 'nestedobjectgridcontent')	{$this->resultsSize = 30;}
		// Set the begin and the end of the result set
		$begin = $this->resultsSize * ($this->pageNumber - 1);
		// Launch the query
		$session = $this->session;
		$query = new JfQuery();
		$queryObj = $this->getSQL();
		$queryObj->setLimitClauses(array('offset' => $begin, 'row_count' => $this->resultsSize));
		if ($this->fastsearch == true)	{}
		else if ($this->sort <> '')	{$queryObj->setOrderByClauses(array($this->sort => $this->order));}
		else					{$queryObj->setOrderByClauses(array("object_name" => "ASC", "r_modify_date" => "DESC", "date_sent" => "DESC", "time_stamp" => "DESC"));}
		$query->setSQL($queryObj->getStatement());
		$col = $query->execute($session);
		while ($col->next())	{$this->objects[] = $col->getTypedObject();}
		// Get the number of results
		$sql = 'SELECT FOUND_ROWS() AS result';
		$query->setSQL($sql);
		$result = $query->execute($session);
		$this->resultsCount = $result->getValue('result');
		// If the details view is called then query the format table to get a description of the objects
//		if ($this->view == 'details')	{$this->initDescription($session);}
		// Init the links and lock icons to the objects
		$this->setLinks();
		$this->setLockIcons();
		// Set the columns properties
		$this->columnsProps = JcUtils::getProperties(JcUtils::getIniFile('columns'));
		return;
	}

	/**
	 * Set the links to the target
	 *
	 * @access	protected
	 */
	protected function setLinks()
	{
		if (!isset($this->objects) || sizeof($this->objects) == 0)	{return;}
		foreach ($this->objects as $index => $object)
		{
			// Init the target Id
			$targetId = $object->getValue('r_object_id');
			// Init the link
			$link = array('open' => '', 'close' => '');
			// Init the current component
			$component = ($this->component == 'home' || $this->component == '') ? 'doclist' : $this->component;
			$targetType = array('0b', '0c', '12');
			// Set the link
			$jscript = "javascript:postServerEvent('objectlist', 'jump', '".$component."', null, 'path=./".$targetId."');";
			if ($component == 'doclist' || $component == 'favorites')	{$jscript = "javascript:open('".$targetId."');";}
			if (substr($targetId, 0, 2) == '11')	{$jscript = "javascript:properties('".$targetId."');";}
			else if (!in_array(substr($targetId, 0, 2), $targetType))	{$jscript = "javascript:view('".$targetId."');";}
			$link['open'] = '<a href="'.$jscript.'" class="link">';
			$link['close'] = '</a>';
			$object->setValue('_link_name_', $link);
			$this->objects[$index] = $object;
		}
	}

	/**
	 * Set the lock icons associated with the objects
	 * $object = array('r_object_id' => '0b001e2400786532', 'r_object_type' => 'jm_document', 'dos_extension' = > 'png', ...)
	 *
	 * @access	protected
	 */
	protected function setLockIcons()
	{
		if (!isset($this->objects) || sizeof($this->objects) == 0)	{return;}
		$user = $this->user;
		foreach ($this->objects as $index => $object)
		{
			$lock = '';
			// The object is not locked
			if ($object->getValue('r_lock_owner') == '')									{$lock = '&nbsp;';}
			// The object is locked by the current user
			else if ($object->getValue('r_lock_owner') == $user->getValue('user_name')
					|| $object->getValue('r_lock_owner') == $user->getValue('r_object_id'))	{$lock = 'checkedout_16';}
			// The object is locked by someone else
			else																	{$lock = 'checkedoutred_16';}

			if ($lock <> '&nbsp;')	{$lock = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/'.$lock.'.png">';}

			$object->setValue('_lock_icon_', $lock);
			$this->objects[$index] = $object;
		}
	}

	/**
	 * Set the SQL query for the list
	 *
	 * @access	protected
	 * @param	String	the SQL query
	 */
	protected function setSQL($query)
	{
		if (is_string($query))					{$queryObj = new JcQuery($query);}
		elseif (get_class($query) == 'JcQuery')	{$queryObj = $query;}
		if ($this->sql == null)	{$this->sql = $queryObj;}
	}
}
?>