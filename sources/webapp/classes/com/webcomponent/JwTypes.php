<?php
/**
 * The Types webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwTypes extends JwObjectList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'object_name', 'properties', 'owner_name');

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
			$properties = array('open' => '', 'close' => '');
			// Set the link
			$jscript = "javascript:properties('".$targetId."');";
			$properties['open'] = '<a href="'.$jscript.'" class="link">';
			$properties['close'] = '</a>';
			// Init the current component
			$component = 'types';
			// Set the link
			$jscript = "javascript:postServerEvent('objectlist', 'jump', '".$component."', null, 'path=./".$targetId."');";
			$link['open'] = '<a href="'.$jscript.'" style="font-weight: bold; color: blue; text-decoration: underline;">';
			$link['close'] = '</a>';
			// Has the target Id any child?
			$flag = (($object->getValue('has_child') == 0) ? false : true);
			// Set the link
			if (!$flag)	{$object->setValue('_link_name_', $properties);}
			else	{$object->setValue('_link_name_', $link);}
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
		return 'jm_type.png';
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
		if ($path == null)	{return 'Types';}
		return array_pop($path);
//		return 'Types';
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
		$link = "javascript:postServerEvent('objectlist', 'jump', 'types', null, 'path=/');";
		$strPath = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/jm_type_16.png"><a href="'.$link.'">Types</a><span> / </span>';
		foreach ($path as $key=>$value)
		{
			$lkpath .= '/'.$key;
			$link = "javascript:postServerEvent('objectlist', 'jump', 'types', null, 'path=".$lkpath."');";
			$strPath .= '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/jm_type_16.png"><a href="'.$link.'">'.$value.'</a><span> / </span>';
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
		$this->initPath();
		// Init the current type name
		$typeName = '';
		$path = $this->path;
		if ($path == null)	{$typeName = '';}
		else	{$typeName = array_pop($path);}
		// Init the current folder ID
		$httpsession = $request->getSession();
		$path = $httpsession->getAttribute('path');
		$folderId = (($path == null) ? '' : array_pop($path));

		$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, name AS object_name, owner AS owner_name, '3' AS r_accessor_permit, '' AS owner_name,
					'jm_type' AS r_object_type, 'Type' AS description, '' AS r_modify_date,
					CASE WHEN r_object_id IN (
						SELECT DISTINCT s1.r_object_id
						FROM jm_type_s s1, jm_type_s s2
						WHERE s1.super_name = '".$typeName."'
							AND s1.name = s2.super_name)
					THEN 1
					ELSE 0
					END AS has_child
				FROM jm_type_s
				WHERE super_name = '".$typeName."'";

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
			$query = new JfQuery();
			$query->setSQL('SELECT r_object_id, name FROM jm_type_s WHERE r_object_id IN ('.$strListIDs.')');
			$results = $query->execute($this->session);
			while ($results->next())	{$objects[$results->getValue('r_object_id')] = $results->getValue('name');}
			foreach ($listIDs as $key=>$value)	{	if (isset($objects[$value]))	{$this->path[$value] = $objects[$value];}	}
		}
		// Get the current container ID
		$pathIds = (($this->path == null) ? null : array_keys($this->path));
		$this->folderId = (($pathIds == null) ? '0000000000000000' : array_pop($pathIds));
	}
}
?>