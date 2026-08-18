<?php
/**
 * The Estancia webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwEstancia extends JwObjectList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'object_name', 'comment');

	/**
	 * Get a link to the target
	 *
	 * @access	public
	 * @return	String	the link
	 */
	protected function setLinks()
	{
		if (!isset($this->objects) || sizeof($this->objects) == 0)	{return;}
		foreach ($this->objects as $index => $object)
		{
			// Init the target
			$target = $object->getValue('r_object_type');
			// Init the link
			$link = array('open' => '', 'close' => '');
			// Set the link
			switch ($target)
			{
				case 'repository':		// Repository
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'doclist', null, null);";
					break;
				case 'home':			// Home
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'home', null, null);";
					break;
				case 'checkedout':			// Checked out
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'checkedout', null, null);";
					break;
				case 'mydocuments':			// My documents
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'mydocuments', null, null);";
					break;
				case 'search':	// Search
					$jscript = "javascript:postServerEvent('objectlist', 'nest', 'search', null, null);";
					break;
				case 'recyclebin':	// Recycle bin
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'recyclebin', null, null);";
					break;
			}
			$link['open'] = '<a href="'.$jscript.'" class="link">';
			$link['close'] = '</a>';
			// Set the link
			$object->setValue('_link_name_', $link);
			$this->objects[$index] = $object;
		}
	}

	/**
	 * Returns the title name (My Documents).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleImage()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return 'publish.png';
	}

	/**
	 * Returns the title name (My Documents).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleName()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$session = $this->session;
		return $this->getString($session->getDocbaseName());
	}

	/**
	 * Returns the title path.
	 *
	 * @access	public
	 * @return	String	the current path
	 */
	public function getTitlePath()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return '';
	}

	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function init()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the user login info
		$user = $this->user;

		$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, object_name, r_object_type, comment
				FROM (
					SELECT '0000000000000001' AS r_object_id, '".$this->getString('REPOSITORY')."' AS object_name, 'repository' AS r_object_type, 'Browse the repository.' AS comment
					UNION SELECT '0000000000000002' AS r_object_id, '".$this->getString('HOME')."' AS object_name, 'home' AS r_object_type, 'Home folder.' AS comment
					UNION SELECT '0000000000000003' AS r_object_id, '".$this->getString('CHECKED_OUT')."' AS object_name, 'checkedout' AS r_object_type, 'List of all objects checked out by me.' AS comment
					UNION SELECT '0000000000000004' AS r_object_id, '".$this->getString('MY_DOCUMENTS')."' AS object_name, 'mydocuments' AS r_object_type, 'List of all objects I am currently owning.' AS comment
					UNION SELECT '0000000000000005' AS r_object_id, '".$this->getString('SEARCH')."' AS object_name, 'search' AS r_object_type, 'Search the repository.' AS comment
					UNION SELECT '0000000000000005' AS r_object_id, '".$this->getString('RECYCLE_BIN')."' AS object_name, 'recyclebin' AS r_object_type, 'List of all deleted objects.' AS comment
				) AS table_admin";

		$queryObj = new JcQuery($sql);
		$order = array("r_object_id" => "ASC");
		$queryObj->setOrderByClauses($order);
		$this->setSQL($queryObj);
		parent::init();
	}
}
?>