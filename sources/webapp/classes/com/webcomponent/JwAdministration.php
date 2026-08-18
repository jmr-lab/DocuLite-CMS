<?php
/**
 * The Administration webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwAdministration extends JwObjectList
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
				case 'jm_group':		// User Management
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'usermanagement', null, null);";
					break;
				case 'jm_acl':			// Security
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'security', null, null);";
					break;
				case 'jm_format':			// Formats
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'formats', null, null);";
					break;
				case 'jm_type':			// Types
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'types', null, null);";
					break;
				case 'configuration':	// Configuration
					$jscript = "javascript:postServerEvent('objectlist', 'nest', 'configuration', null, null);";
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
		return 'administration.png';
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
		return $this->getString('ADMINISTRATION');
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
					SELECT '0000000000000001' AS r_object_id, '".$this->getString('USER_MANAGEMENT')."' AS object_name, 'jm_group' AS r_object_type, 'Manage the users and groups of the system. Only administrators can create / modify users or groups. However anyone can modify his own profile.' AS comment
					UNION SELECT '0000000000000002' AS r_object_id, '".$this->getString('SECURITY')."' AS object_name, 'jm_acl' AS r_object_type, 'Manage the permissions of the system. Only administrators can create / modify permission sets.' AS comment
					UNION SELECT '0000000000000003' AS r_object_id, '".$this->getString('FORMATS')."' AS object_name, 'jm_format' AS r_object_type, 'List of the formats used by the system (AutoCAD, Microsoft Word, JPEG, ...)' AS comment
					UNION SELECT '0000000000000004' AS r_object_id, '".$this->getString('TYPES')."' AS object_name, 'jm_type' AS r_object_type, 'List of the types used by the system (users, groups, documents, folders, permissions, ...)' AS comment
					UNION SELECT '0000000000000005' AS r_object_id, '".$this->getString('CONFIGURATION')."' AS object_name, 'configuration' AS r_object_type, 'Configure some of the properties of the system such as the default language, home or type of display.' AS comment
				) AS table_admin";

		$queryObj = new JcQuery($sql);
		$order = array("r_object_id" => "ASC");
		$queryObj->setOrderByClauses($order);
		$this->setSQL($queryObj);
		parent::init();
	}
}
?>