<?php
/**
 * The UserManagement webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwUserManagement extends JwObjectList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'object_name', 'properties', 'description', 'r_modify_date');

	/**
	 * World Identifier
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $worldId = '0000000000000000';

	/**
	 * Checks the permit on the current folder
	 *
	 * @access	protected
	 */
	protected function checkPermit()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'(folderId : '.$this->folderId.')');
		$folderId = ($this->folderId == '0000000000000000' ? $this->worldId : $this->folderId);
		// Get the current group object
		$query = new JfQuery();
		$sql = "SELECT r_object_id, owner_name FROM jm_group_s WHERE r_object_id = '".$folderId."'";
		$query->setSQL($sql);
		$results = $query->execute($this->session);
		while ($results->next())	{$this->folderObj = $results->getTypedObject();}
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
		return 'jm_group.png';
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
		$path = $this->path;
		if ($path == null)	{return $this->getString('WORLD');}
//		return $this->getString(strtoupper(array_pop($path)));
		return array_pop($path);
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
		// $this->path = array('0b001e240d6126bc' => 'Documentation', '0b001e2400786532' = > 'Templates', ...)
		$path = $this->path;
		if ($path == null)	{return '';}
		array_pop($path);
		// Init the path to display the repository
		$lkpath = '';
		$link = "javascript:postServerEvent('objectlist', 'jump', 'usermanagement', null, 'path=/');";
		$strPath = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/jm_group_16.png"><a href="'.$link.'">'.$this->getString('WORLD').'</a><span> / </span>';
		foreach ($path as $key=>$value)
		{
			$lkpath .= '/'.$key;
			$link = "javascript:postServerEvent('objectlist', 'jump', 'usermanagement', null, 'path=".$lkpath."');";
			$strPath .= '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/jm_group_16.png"><a href="'.$link.'">'.$value.'</a><span> / </span>';
		}
		// Return the path
		return $strPath;
	}

	/**
	 * Returns the ID of the 'World' group (base group).
	 *
	 * @access	private
	 * @return	String	the 'world' ID
	 */
	private function getWorldId()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		if ($this->worldId == '0000000000000000')
		{
			$sessionmanager = new JfSessionManager();
			$session = $sessionmanager->getSession('www_jmroy');
			$query = new JfQuery();
			$query->setSQL('SELECT r_object_id FROM jm_group_s WHERE group_name = \'world\'');
			$results = $query->execute($session);
			while ($results->next())	{$this->worldId = $results->getValue('r_object_id');}
		}
		return $this->worldId;
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
		// Get the request info
		$request = new JcHttpServletRequest();
		// Init the current path
		$this->initPath();
		// Init the current folder ID
		$httpsession = $request->getSession();
		$path = $httpsession->getAttribute('path');
		$folderId = (($path == null) ? $this->getWorldId() : array_pop($path));

		if ($user->getValue('client_capability') > 4)
		{
			$sql = "SELECT r_object_id, group_display_name AS object_name, '3' AS r_accessor_permit, owner_name,
					'jm_group' AS r_object_type, 'Group' AS description, group_address AS address, r_modify_date
					FROM jm_group_s
					WHERE r_object_id IN
						(SELECT groups_ids FROM jm_group_rp WHERE r_object_id = '".$folderId."') 
					UNION SELECT r_object_id, user_name AS object_name, '3' AS r_accessor_permit, user_name AS owner_name,
					'jm_user' AS r_object_type, 'User' AS description, user_address AS address, r_modify_date
					FROM jm_user_s
					WHERE r_object_id IN
						(SELECT users_ids FROM jm_group_rp WHERE r_object_id = '".$folderId."')
					ORDER BY r_object_type, object_name";
		}
		else
		{
			$sql = "SELECT r_object_id, group_display_name AS object_name, '3' AS r_accessor_permit, owner_name,
					'jm_group' AS r_object_type, 'Group' AS description, group_address AS address, r_modify_date
					FROM jm_group_s
					WHERE r_object_id IN
						(SELECT groups_ids FROM jm_group_rp WHERE r_object_id = '".$folderId."')
						AND (is_private = false OR (is_private = true AND owner_name = '".$user->getValue('user_name')."'))
					UNION SELECT r_object_id, user_name AS object_name, '3' AS r_accessor_permit, user_name AS owner_name,
					'jm_user' AS r_object_type, 'User' AS description, user_address AS address, r_modify_date
					FROM jm_user_s
					WHERE (r_object_id = '".$user->getValue('r_object_id')."' OR r_object_id IN
						(SELECT child_id FROM jm_relation_s WHERE parent_id = '".$user->getValue('r_object_id')."'
						UNION SELECT parent_id FROM jm_relation_s WHERE child_id = '".$user->getValue('r_object_id')."'))
						AND r_object_id IN
							(SELECT users_ids FROM jm_group_rp WHERE r_object_id = '".$folderId."')
					ORDER BY r_object_type, object_name";
		}

		$queryObj = new JcQuery($sql);
		$this->setSQL($queryObj);
		parent::init();
	}

	/**
	 * Init the path
	 *
	 * @access	protected
	 */
	protected function initPath()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the names of IDs included in the path array
		// $listIDs = array('0' => '0b001e240d6126bc', '1' = > '0b001e2400786532', ...);
		// We want to get :
		// $this->path = array('0b001e240d6126bc' => 'Documentation', '0b001e2400786532' = > 'Templates', ...)
		$httpsession = new JcHttpSession();
		$listIDs = $httpsession->getAttribute('path');
		if ($listIDs <> null)
		{
			$strListIDs = '';
			$objects = array();
			foreach ($listIDs as $key=>$value)	{$strListIDs .= ', \''.$value.'\'';}
			$strListIDs = substr($strListIDs, 2);
			// Execute a query on all IDs
			$sessionmanager = new JfSessionManager();
			$session = $sessionmanager->getSession('www_jmroy');
			$query = new JfQuery();
			$query->setSQL('SELECT r_object_id, group_name FROM jm_group_s WHERE r_object_id IN ('.$strListIDs.')');
			$results = $query->execute($session);
			while ($results->next())	{$objects[$results->getValue('r_object_id')] = $results->getValue('group_name');}
			foreach ($listIDs as $key=>$value)	{	if (isset($objects[$value]))	{$this->path[$value] = $objects[$value];}	}
		}
		// Log the path
		// Get the current container ID
		if (trim($this->folderId) == '')
		{
			$pathIds = (($this->path == null) ? null : array_keys($this->path));
			$this->folderId = (($pathIds == null) ? '0000000000000000' : array_pop($pathIds));
		}
	}
}
?>