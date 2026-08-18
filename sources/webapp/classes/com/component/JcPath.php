<?php
/**
 * The JcPath class.
 * Usage :
 *
 * $path = new JcPath();
 * $path->init($request);
 *
 * This class will set the path if needed.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcPath
{
	/**
	* Default path
	*
	* @access	private
	* @var		array
	*/
	private $path;

	/**
	 * Constructor
	 *
	 */
	public function __construct()	{}

	/**
	 * Initialize the path
	 *
	 * @access	public
	 * @param	JcHttpRequest	The request
	 */
	public function init($request)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get session object
		$httpsession = new JcHttpSession();
		// Get the requested path
		$path = (($request == null) ? null : $request->getParameter('path'));
		$event = (($request == null) ? '' : $request->getParameter('event'));
		$objectId = (($request == null) ? null : $request->getParameter('objectId'));
		$component = (($request == null) ? null : $request->getParameter('component'));
		// // Log the requested path
		// $strPath = ($path == null ? 'null' : $path);
		// JcLogger::info('Path : '.$strPath);
		// Only change path if the 'path' attribute has been set
		if ($path <> null)
		{
			// Init the path array
			$arrPath = $httpsession->getAttribute('path');
			if ($arrPath == null)	{$arrPath = array();}
			// If $path begins with './', add the container Id to the current path
			if (substr($path, 0, 2) == './' && in_array(substr($path, 2), $arrPath) === false)
			{
				$arrPath[] = substr($path, 2);
			}
			// Else if $path is blank ('/'), jump to the top directory
			else if ($path == '/')
			{
				$arrPath = array();
			}
			// Else $path begins with a '/' ('/0b001e2400786532/0b001e240ff5212a/0b001e240ff5212b'), set the path array :
			else if (substr($path, 0, 1) == '/')
			{
				$arrPath = explode('/', substr($path, 1));
			}
			// Else if $path contains an Id ('0b001e2400786532'), set the path array :
			else if (preg_match('/[a-zA-Z0-9]{16}/', $path))
			{
				$arrPath = $this->getFullPath($path);
			}
			// Else $path is a '.', do nothing :
			else if ($path == '.')
			{
			}
			// We shoudn't get to this part, throw an exception :
			else
			{
				throw new JcException('ERROR_INVALID_PATH');
			}
			// // Log the new path
			// foreach ($arrPath as $key => $value)	{JcLogger::info('arrPath['.$key.'] : '.$value);}
			// Set the path in the session
			$httpsession->setAttribute('path', $arrPath);
		}
		// For the home ecomponent
		else if ($component == 'home')
		{
		}
		// Else if there is an objectId in the request, do nothing :
		else if ($objectId <> null)
		{
		}
		// This occurs when we click on a component with a null path
		else if ($path == null && $request <> null && $event == 'jump')
		{
			// Reset the path if we click on the repository link from the doclist component
			$componentList = $httpsession->getAttribute('component');
			$origin = end($componentList);
			if ($component == $origin && in_array($component, array('doclist', 'usermanagement', 'types')))	{$httpsession->setAttribute('path', null);}
			// Reset the path if the current path type doesn't match the component
			if (in_array($component, array('doclist', 'usermanagement', 'types')) && $component <> $this->getPathType())	{$httpsession->setAttribute('path', null);}
		}
	}

	/**
	 * Get the full path from an Id
	 *
	 * @todo	This method should be removed from here and added to the location component once Estancia will be Documentum compatible
	 * @access	public
	 * @param	String	The folder Id
	 */
	public function getFullPath($objectId)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the full path
		$fullPath = array();
		try
		{
			// Get the session details
			$sessionmanager = new JfSessionManager();
			$session = $sessionmanager->getSession('www_jmroy');
			$user = $session->getLoginInfo();
			// Initialise the query object
			$query = new JfQuery();
			// Initialise the parentId
			$parentId = $objectId;
			while ($parentId <> '123456')
			{
				$owner = ''; $permit = '';
				$fullPath[] = $parentId;
				$sql = "SELECT jm_sysobject_s.r_object_id, owner_name, r_accessor_permit, i_folder_id
						FROM	jm_sysobject_s, jm_sysobject_r,
							(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
							FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit
						WHERE	jm_sysobject_s.r_object_id = '".$parentId."'
							AND jm_sysobject_s.acl_id = table_permit.acl_id
							AND jm_sysobject_s.r_object_id = jm_sysobject_r.r_object_id
							AND i_position = -1";
				$query->setSQL($sql);
				$result = $query->execute($session);
				while ($result->next())
				{
					$owner = $result->getValue('owner_name');
					$permit = $result->getValue('r_accessor_permit');
					$parentId = $result->getValue('i_folder_id');
				}
				// JcLogger::info('owner : '.$owner);
				// JcLogger::info('permit : '.$permit);
				// JcLogger::info('parentId : '.$parentId);
				if ($permit < 3 && $owner <> $user->getValue('user_name'))	{throw new JcException('ERROR_INVALID_ACCESS');}
			}
		}
		catch (JcException $exception )
		{
			JcLogger::info('Exception : '.$exception);
			$fullPath = array();
		}
		return array_reverse($fullPath);
	}
	/**
	 * Get the path type (doclist, usermanagement or types)
	 *
	 * @access	private
	 * @return	String	The path type
	 */
	private function getPathType()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$pathType = '';
		// Init the path array
		$httpsession = new JcHttpSession();
		$arrPath = $httpsession->getAttribute('path');
		if ($arrPath == null)	{$arrPath = array();}
		$extension = substr(end($arrPath), 0, 2);
		if (in_array($extension, array('11', '12')))	{$pathType = 'usermanagement';}
		else if (in_array($extension, array('03')))		{$pathType = 'types';}
		else											{$pathType = 'doclist';}
		return $pathType;
	}
}
?>