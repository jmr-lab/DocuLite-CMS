<?php
/**
 * SelectPermission webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwSelectPermission extends JwModalList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'object_name', 'description', 'owner_name');

	/**
	 * Empty message
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $empty_message = 'NO_HISTORY';

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
			// Init the link
			$link = array('open' => '<span style="font-size: 12px; font-weight: bold; color: #5F5F5F; text-decoration: none;">', 'close' => '</span>');
			// Set the link
			$object->setValue('_link_name_', $link);
			$this->objects[$index] = $object;
		}
	}

	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function init()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$this->objectclass = 'modalperobj';
		// Get the user login info
		$user = $this->user;

		$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, object_name, owner_name, IF(r_is_internal = '0', 'Public', 'Private') AS description,
					'3' AS r_accessor_permit, owner_name, 'jm_acl' AS r_object_type, '' AS r_modify_date
				FROM jm_acl_s
				WHERE r_is_internal = false
					OR (r_is_internal = true AND owner_name = '".$user->getValue('user_name')."')";

		$queryObj = new JcQuery($sql);
		$this->setSQL($queryObj);
		// Force the 'details' view to be used
		$this->view = 'details';
		$this->objectgridcontent = 'nestedobjectgridcontent';
		parent::init();
		// Init current component to 'History'
//		$this->component = 'history';
	}

	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function onOk()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the request info
		$request = new JcHttpServletRequest();
		$session = $this->session;
		// Get modalObjList
		$modalObjList = $request->getParameter('modalObjList');
		$objectId = $request->getParameter('objectId');
		JcLogger::debug('modalObjList : '.$modalObjList);
		JcLogger::debug('objectId : '.$objectId);
		// Get the user login info
		$user = $this->user;
		JcLogger::debug('user_name : '.$user->getValue('user_name'));
		// Get the permission and the ACL name
		$arr_permit = array(1 => 'NONE', 2 => 'BROWSE', 3 => 'READ', 4 => 'RELATE', 5 => 'VERSION', 6 => 'WRITE', 7 => 'DELETE');
		$strPermission = '';
		// First set the deleted attribute to '0' (false)
		$query = new JfQuery();
		// $sql = "SELECT r_object_id, object_name, owner_name, IF(r_is_internal = '0', 'Public', 'Private') AS description,
					// '3' AS r_accessor_permit, owner_name, 'jm_acl' AS r_object_type, '' AS r_modify_date
				// FROM jm_acl_s
				// WHERE r_is_internal = false
					// OR (r_is_internal = true AND owner_name = '".$user->getValue('user_name')."')";
		$sql = "SELECT r_accessor_permit, jm_sysobject_s.owner_name AS owner_name, jm_acl_s.object_name AS object_name
				FROM	jm_sysobject_s, jm_acl_s,
						(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
						FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit
				WHERE	jm_sysobject_s.r_object_id = '".$objectId."'
				AND table_permit.acl_id = '".$modalObjList."'
				AND jm_acl_s.r_object_id = '".$modalObjList."'
				AND (jm_acl_s.r_is_internal = false
				OR (r_is_internal = true AND jm_acl_s.owner_name = '".$user->getValue('user_name')."'))";

		$query->setSQL($sql);
		$result = $query->execute($session);
		while ($result->next())
		{
			$permit = $result->getValue('r_accessor_permit');
			if ($result->getValue('owner_name') == $user->getValue('user_name'))	$permit = 7;
			$strPermission = $this->getString($arr_permit[$permit]);
			$strPermission .= ' ('.$result->getValue('object_name').')';
		}

		echo "	<script>
				var returnflag = '".$strPermission."';
				_callback('".$modalObjList."', returnflag);
				var _callback = function(objectId, value)	{};
				</script>";
		return false;
	}
}
?>