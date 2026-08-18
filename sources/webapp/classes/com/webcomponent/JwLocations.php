<?php
/**
 * JwLocations webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwLocations extends JwDocList
{
	/**
	 * Checks the permit on the current object
	 *
	 * @access	protected
	 */
	protected function checkPermit()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'(folderId : '.$this->folderId.')');
		// Get the request info
		$request = new JcHttpServletRequest();
		// Init the current object ID
		$httpsession = $request->getSession();
		$path = $httpsession->getAttribute('path');
		$objectId = (($path == null) ? '0000000000000000' : array_pop($path));
		// Check the permit on the current object
		if (strlen($objectId) > 2 && $objectId <> '0000000000000000')
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
					WHERE	jm_sysobject_s.r_object_id = '".$objectId."'
						AND jm_sysobject_s.acl_id = table_permit.acl_id";
			$query->setSQL($sql);
			$results = $query->execute($this->session);
			if ($query->getResultCount() == 1)
			{
				while ($results->next())
				{
//					$this->folderObj = $results->getTypedObject();
					$owner = $results->getValue('owner_name');
					$permit = $results->getValue('r_accessor_permit');
				}
				if ($owner == $user->getValue('user_name'))	{$permit = 7;}
			}
			if ($permit < 3)	{throw new JfException ($this->getString('OBJECT_INVALID_ACCESS'));}
		}
	}

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
			// javascript:postServerEvent('objectlist', 'jump', 'doclist', null, 'path=');
			$jscript = "javascript:postServerEvent('objectlist', 'jump', 'doclist', null, 'path=".$targetId."');";
			$link['open'] = '<a href="'.$jscript.'" class="link">';
			$link['close'] = '</a>';
			// Set the link
			$object->setValue('_link_name_', $link);
			$this->objects[$index] = $object;
		}
	}

	/**
	 * Returns the title name (Locations).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleImage()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return 'jm_folder.png';
	}

	/**
	 * Returns the title name (Locations).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleName()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return $this->getString('LOCATIONS');
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
		$path = $this->path;
		$strPath = 	parent::getTitlePath();
		// $this->path = array('0b001e240d6126bc' => 'Documentation', '0b001e2400786532' = > 'Templates', ...)
		if ($path == null)	{return '';}
		$strPath .= '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/unknown_16.png">';
		$strPath .= '<span style="font-weight: bold; text-decoration:underline;">'.array_pop($path).'</span>';
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
		// Prevent user to create/import objects in the current component
		$this->folderId = '0000000000000000';
		// Reset the minimum permit to 3
		$this->minPermit = 3;
		// Get the user login info
		$user = $this->user;
		// Get the request info
		$request = new JcHttpServletRequest();
		// Init the current object ID
		$objectId = $request->getParameter('objectId');

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT jm_sysobject_s.r_object_id, r_object_type, object_name, owner_name, r_creation_date, r_modify_date,
						r_accessor_permit, r_content_size, r_lock_owner, i_contents_id, a_content_type, a_content_type AS dos_extension, '' AS description
				FROM	(jm_sysobject_s, jm_sysobject_r,
						(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
						FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit)
				WHERE	jm_sysobject_s.i_is_deleted = false
						AND jm_sysobject_s.r_object_id = jm_sysobject_r.r_object_id
						AND jm_sysobject_r.i_position = '-2'
						AND jm_sysobject_r.r_version_label <> 'OLD'
						AND jm_sysobject_s.r_object_id IN
						(SELECT i_folder_id FROM jm_sysobject_r WHERE i_folder_id <> '' AND i_folder_id IS NOT NULL AND r_object_id = '".$objectId."')
						AND jm_sysobject_s.acl_id = table_permit.acl_id
						AND (r_accessor_permit > 1 OR owner_name = '".$user->getValue('user_name')."')";


		$queryObj = new JcQuery($sql);
		// $order = array("CASE WHEN jm_sysobject_s.r_object_type IN ('jm_cabinet', 'jm_folder') THEN NULL ELSE jm_sysobject_s.r_object_type END" => "ASC");
		// $queryObj->setOrderByClauses($order);

		$this->setSQL($queryObj);
		// Change the title path to add the current object
		$httpsession = $request->getSession();
		$path = $httpsession->getAttribute('path');
		$path[] = $objectId;
		$httpsession->setAttribute('path', $path);
		// Run the parent method
		parent::init();
		// Remove the last element of the path
		$path = $httpsession->getAttribute('path');
		$objectId = array_pop($path);
		$httpsession->setAttribute('path', $path);
	}
}
?>