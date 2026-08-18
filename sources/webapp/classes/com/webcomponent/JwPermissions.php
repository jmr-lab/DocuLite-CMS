<?php
/**
 * The Permissions webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwPermissions extends JwObjectList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'object_name', 'properties', 'description', 'owner_name');

	/**
	 * World Identifier
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $worldId = '0000000000000000';

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
			// Init the target Id
			$targetId = $object->getValue('r_object_id');
			// Init the link
			$link = array('open' => '', 'close' => '');
			// Set the link
			$jscript = "javascript:properties('".$targetId."');";
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
		return 'security.png';
	}

	/**
	 * Returns the title name (World).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleName()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return 'Permissions';
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

		$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, object_name, owner_name, r_is_internal, '3' AS r_accessor_permit, owner_name,
					'jm_acl' AS r_object_type, 'ACL' AS description, '' AS r_modify_date
				FROM jm_acl_s
				WHERE r_is_internal = false
					OR (r_is_internal = true AND owner_name = '".$user->getValue('user_name')."')";

		$queryObj = new JcQuery($sql);
		$this->setSQL($queryObj);
		parent::init();
	}
}
?>