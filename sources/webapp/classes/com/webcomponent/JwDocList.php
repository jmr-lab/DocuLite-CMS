<?php
/**
 * The DocList webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDocList extends JwObjectList
{
	/**
	 * Checks the permit on the current folder
	 *
	 * @access	protected
	 */
	protected function checkPermit()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'(folderId : '.$this->folderId.')');
		if (strlen($this->folderId) > 2 && in_array(substr($this->folderId, 0, 2), array('0b', '0c')))
		{
			// Check that the user has at least READ access on the current folder
			$user = $this->user;
			$permit = 1;
			$owner = '';
			$query = new JfQuery();
			$sql = "SELECT jm_sysobject_s.r_object_id, owner_name, r_accessor_permit
					FROM	jm_sysobject_s,
						(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
						FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit
					WHERE	jm_sysobject_s.r_object_id = '".$this->folderId."'
						AND jm_sysobject_s.acl_id = table_permit.acl_id";
			$query->setSQL($sql);
			$results = $query->execute($this->session);
			if ($query->getResultCount() == 1)
			{
				while ($results->next())
				{
					$this->folderObj = $results->getTypedObject();
//					if ($user->getValue('client_capability') == 8)	{$this->folderObj['r_accessor_permit'] = 7;}
					$owner = $results->getValue('owner_name');
					$permit = $results->getValue('r_accessor_permit');
				}
				if ($owner == $user->getValue('user_name'))	{$permit = 7;}
			}
			if ($permit < 3)	{throw new JfException ($this->getString('OBJECT_INVALID_ACCESS'));}
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
		$icon = 'jm_folder.png';
		// Get the user login info
		$user = $this->user;
		// If the path is null
		if ($this->path == null)	{$icon = 'repository.png';}
		// If the object is the home folder of the user
		else if ($this->folderId == $user->getValue('default_folder'))	{$icon = 'home.png';}
		return $icon;
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
		$path = $this->path;
		if ($path == null)	{return $this->getString('REPOSITORY');}
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
		$link = "javascript:postServerEvent('objectlist', 'jump', 'doclist', null, 'path=/');";
		$strPath = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/repository_16.png"><a href="'.$link.'">www_jmroy</a><span> / </span>';
		foreach ($path as $key=>$value)
		{
			$lkpath .= '/'.$key;
			$link = "javascript:postServerEvent('objectlist', 'jump', 'doclist', null, 'path=".$lkpath."');";
			$strPath .= '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/folder_16.png"><a href="'.$link.'">'.$value.'</a><span> / </span>';
		}
		// Return the path
		return $strPath;
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
		$httpsession = $request->getSession();
		$path = $httpsession->getAttribute('path');
		$this->initPath($path);
		// Init the current folder ID
		$folderId = (($path == null) ? '123456' : array_pop($path));

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT jm_sysobject_s.r_object_id, r_object_type, object_name, owner_name, r_creation_date, r_modify_date,
						r_accessor_permit, r_content_size, r_lock_owner, i_contents_id, a_content_type,
						CASE WHEN jm_format_s.dos_extension IS NULL THEN a_content_type ELSE jm_format_s.dos_extension END AS dos_extension,
						CASE WHEN jm_format_s.description IS NULL THEN CASE WHEN a_content_type <> '' THEN CONCAT(UCASE(a_content_type), ' File') END ELSE jm_format_s.description END AS description
				FROM	(jm_sysobject_s, jm_sysobject_r r1, jm_sysobject_r r2,
						(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
						FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit)
				LEFT JOIN jm_format_s ON a_content_type = jm_format_s.name
				WHERE	jm_sysobject_s.i_is_deleted = false
						AND jm_sysobject_s.r_object_id = r1.r_object_id
						AND r1.i_position = '-2'
						AND r1.r_version_label <> 'OLD'
						AND r1.r_object_id = r2.r_object_id
						AND r2.i_folder_id = '".$folderId."'
						AND jm_sysobject_s.acl_id = table_permit.acl_id
						AND (r_accessor_permit > 1 OR owner_name = '".$user->getValue('user_name')."')";

		$queryObj = new JcQuery($sql);
		$order = array("CASE WHEN jm_sysobject_s.r_object_type IN ('jm_cabinet', 'jm_folder') THEN NULL ELSE jm_sysobject_s.r_object_type END" => "ASC");
		$queryObj->setOrderByClauses($order);

		$this->setSQL($queryObj);
		parent::init();
	}

	/**
	 * Init the path
	 *
	 * @access	protected
	 */
	protected function initPath($listIDs)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the names of IDs included in the path array
		// $listIDs = array('0' => '0b001e240d6126bc', '1' = > '0b001e2400786532', ...);
		// We want to get :
		// $this->path = array('0b001e240d6126bc' => 'Documentation', '0b001e2400786532' = > 'Templates', ...)
		$httpsession = new JcHttpSession();
		if ($listIDs <> null)
		{
			$strListIDs = '';
			$objects = array();
			foreach ($listIDs as $key=>$value)	{$strListIDs .= ', \''.$value.'\'';}
			$strListIDs = substr($strListIDs, 2);
			// Execute a query on all IDs
			$query = new JfQuery();
			$query->setSQL('SELECT r_object_id, object_name FROM jm_sysobject_s WHERE r_object_id IN ('.$strListIDs.')');
			$results = $query->execute($this->session);
			while ($results->next())	{$objects[$results->getValue('r_object_id')] = $results->getValue('object_name');}
			foreach ($listIDs as $key=>$value)	{	if (isset($objects[$value]))	{$this->path[$value] = $objects[$value];}	}
		}
		// Get the current container ID
		if (trim($this->folderId) == '')
		{
			$pathIds = (($this->path == null) ? null : array_keys($this->path));
			$this->folderId = (($pathIds == null) ? '0000000000000000' : array_pop($pathIds));
		}
	}
}
?>