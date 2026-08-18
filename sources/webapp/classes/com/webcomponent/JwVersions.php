<?php
/**
 * JwVersions webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwVersions extends JwDocList
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
	 * Returns the title name (Versions).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleImage()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return 'versions.png';
	}

	/**
	 * Returns the title name (Versions).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleName()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return $this->getString('VERSIONS');
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
		$strPath = 	parent::getTitlePath();
		// $this->path = array('0b001e240d6126bc' => 'Documentation', '0b001e2400786532' = > 'Templates', ...)
		$path = $this->path;
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
		// Get the user login info
		$user = $this->user;
		// Get the request info
		$request = new JcHttpServletRequest();
		// Init the current object ID
		$objectId = $request->getParameter('objectId');

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT jm_sysobject_s.r_object_id, r_object_type, r1.r_version_label,
						(SELECT GROUP_CONCAT( r_version_label ORDER BY r_version_label SEPARATOR ', ' ) AS r_version_label
						FROM jm_sysobject_rp
						WHERE jm_sysobject_rp.r_object_id = jm_sysobject_s.r_object_id ORDER BY i_position DESC) AS object_name,
						owner_name, r_creation_date, r_modify_date,
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
						AND r1.r_object_id = r2.r_object_id
						AND jm_sysobject_s.i_chronicle_id IN (SELECT i_chronicle_id FROM jm_sysobject_s WHERE r_object_id = '".$objectId."')
						AND jm_sysobject_s.acl_id = table_permit.acl_id
						AND (r_accessor_permit > 1 OR owner_name = '".$user->getValue('user_name')."')
				ORDER BY object_name";


		$queryObj = new JcQuery($sql);

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