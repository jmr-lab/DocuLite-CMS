<?php
/**
 * JcSecurity class.
 *
 * Usage :
 * $security = new JcSecurity();
 * foreach ($objectIds as $key => $strObjectId)
 * {
 *		$typedObject = new JcTypedObject();
 *		$typedObject->setObjectId($strObjectId);
 *		$security->addObject($typedObject);
 * }
 *
 * Get all permissions associated with the objects :
 * $security->execute();
 * $permission['09001e240ffe1d28'] = $security->getAccess('09001e240ffe1d28');
 *
 * Remove all objects with insufficient permission (less than 'READ') :
 * $security->setThreshold(3);
 * $security->removeObjects();
 * $arrObjectIds = $security->getObjectIds();
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcSecurity
{
	/**
	 * Array of objects
	 *
	 * @access	private
	 * @var		array
	 */
	private $arrObjectList = array();

	/**
	 * Threshold
	 *
	 * @access	private
	 * @var		integer
	 */
	private $threshold = 0;

	/**
	 * Current Session
	 *
	 * @access	private
	 * @var		JfSession
	 */
	private $session;

	/**
	 * Constructor
	 *
	 * This function initialize the current security object.
	 *
	 * @access	public
	 */
	public function __construct($session)	{$this->session = $session;}

	/**
	 * Add an object to the clipboard
	 *
	 * @access	public
	 * @param	JcTypedObject	typedObject	the object to add
	 */
	public function addObject($typedObject)
	{
		if (sizeof($this->arrObjectList) == 0 || array_search($typedObject, $this->arrObjectList) === false)	{$this->arrObjectList[] = $typedObject;}
	}

	/**
	 * Get access to an object specified by its Id.
	 *
	 * @access	public
	 * @param	String	strObjectId		the object Id to retrieve
	 * @return	int		The access
	 */
	public function getAccess($strObjectId)
	{
		$typedObject = $this->getObject($strObjectId);
		return $typedObject->getValue('access_value');
	}

	/**
	 * Get an object specified by its Id.
	 *
	 * @access	public
	 * @param	String	strObjectId		the object Id to retrieve
	 * @return	array	An object
	 */
	public function getObject($strObjectId)
	{
		if (sizeof($this->arrObjectList) > 0)
		{
			foreach ($this->arrObjectList as $key => $typedObject)
			{
				if ($strObjectId == $typedObject->getObjectId())	{return $typedObject;}
			}
		}
		return null;
	}

	/**
	 * Get the value of the parameter.
	 *
	 * If it doesn't exist, null will be returned. If it exists but is empty, it will be a string.
	 *
	 * @access	public
	 * @param	String	name	the name of the parameter
	 * @return	array	A list of object Ids
	 */
	public function getObjectIds()
	{
		$arrObjectId = array();
		if (sizeof($this->arrObjectList) > 0)
		{
			foreach ($this->arrObjectList as $key => $typedObject)
			{
				$arrObjectId[] = $typedObject->getObjectId();
			}
		}
		return $arrObjectId;
	}

	/**
	 * Add an object to the clipboard
	 *
	 * @access	public
	 * @param	JcTypedObject	typedObject	the object to add
	 */
	public function removeObject($removeObject)
	{
		foreach ($this->arrObjectList as $key => $typedObject)
		{
			if ($typedObject->getObjectId() == $removeObject->getObjectId())	{unset($this->arrObjectList[$key]);}
		}
	}

	/**
	 * Set the Threshold
	 *
	 * @access	public
	 * @param	integer	threshold	the new threshold
	 */
	public function setThreshold($threshold)
	{
		$this->threshold = $threshold;
	}

	/**
	 * Run the security process
	 *
	 * @access	public
	 */
	public function execute()
	{
		$strObjectIds = implode("', '", $this->getObjectIds());
		$session = $this->session;
		$user = $session->getLoginInfo();
		$permit = 1;$owner = '';
		$query = new JfQuery();
		$sql = "SELECT jm_sysobject_s.r_object_id, owner_name, r_accessor_permit
				FROM	jm_sysobject_s,
					(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
					FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit
				WHERE	jm_sysobject_s.r_object_id IN ('".$strObjectIds."')
					AND jm_sysobject_s.acl_id = table_permit.acl_id";
		$query->setSQL($sql);
		$results = $query->execute($this->session);
		while ($results->next())
		{
			$permit = $results->getValue('r_accessor_permit');
			if ($results->getValue('owner_name') == $user->getValue('user_name'))	{$permit = 7;}
			$typedObject = $this->getObject($results->getValue('r_object_id'));
			if ($permit < $this->threshold)	{throw new JfException('OBJECT_INVALID_ACCESS');}
			$typedObject->setValue('access_value', $permit);
		}
	}

	/**
	 * Remove objects based on the threshold
	 *
	 * @access	public
	 */
	public function removeObjects()
	{
		foreach ($this->arrObjectList as $key => $typedObject)
		{
			// JcLogger::info('Access Value['.$key.'] : '.$typedObject->getValue('access_value'));
			if ($typedObject->getValue('access_value') < $this->threshold)	{unset($this->arrObjectList[$key]);}
		}
	}
}
?>