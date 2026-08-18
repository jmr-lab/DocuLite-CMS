<?php
/**
 * JwCheckout webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwCheckout extends JwModalList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('checkout', 'icon', 'object_name', 'description', 'r_modify_date');

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
			$link['open'] = '<input type="hidden" name="objectId[]" value="'.$object->getValue('r_object_id').'"><span style="font-size: 12px; font-weight: bold; color: #5F5F5F;">';
			$link['close'] = '</span>';
			// Set the link
			$object->setValue('_link_name_', $link);
			$this->objects[$index] = $object;
		}
	}

	/**
	 * Get a short version of a string :
	 *
	 * 'Microsoft Office Word Document 8.0-2003 (Windows)' will become 'Microsoft Office Wor...'
	 *
	 * @access	protected
	 * @param	String	The message to truncate
	 * @return	String	The message
	 */
	protected function getShortString($message)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		if (strlen($message) > 20)	{$message = substr($message, 0, 20).'...';}
		return $message;
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
		// Get the object info
		$request = new JcHttpServletRequest();
		$objectList = $request->getParameter('objectId');
		$objectList = str_replace(",", "','", $objectList);

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT s1.r_object_id, s1.r_object_type, s1.object_name,
						s1.owner_name, s1.r_creation_date, s1.r_modify_date, s1.r_immutable_flag,
						r_accessor_permit, s1.r_content_size, s1.r_lock_owner, s1.i_contents_id, s1.a_content_type,
						CASE WHEN jm_format_s.dos_extension IS NULL THEN s1.a_content_type ELSE jm_format_s.dos_extension END AS dos_extension,
						CASE WHEN jm_format_s.description IS NULL THEN CONCAT(UCASE(s1.a_content_type), ' File') ELSE jm_format_s.description END AS description
				FROM	(jm_sysobject_s s1, jm_sysobject_r r1,
						(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
						FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit)
				LEFT JOIN jm_format_s ON s1.a_content_type = jm_format_s.name
				WHERE	s1.i_is_deleted = false
						AND s1.r_object_id = r1.r_object_id
						AND s1.r_object_id IN ('".$objectList."')
						AND r1.i_position = '-2'
						AND s1.acl_id = table_permit.acl_id
						AND s1.r_lock_owner = ''
						AND (r_accessor_permit > 1 OR s1.owner_name = '".$user->getValue('user_name')."')
				GROUP BY s1.r_object_id";

		$queryObj = new JcQuery($sql);
		$order = array("CASE WHEN s1.r_object_type IN ('jm_cabinet', 'jm_folder') THEN NULL ELSE s1.r_object_type END" => "ASC");
		$queryObj->setOrderByClauses($order);

		$this->setSQL($queryObj);

		// Force the 'details' view to be used
		$this->view = 'details';
		$this->objectgridcontent = 'nestedobjectgridcontent';
		parent::init();
	}

	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function onOk()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the request and session objects
		$request = new JcHttpServletRequest();
		$httpSession = $request->getSession();
		// Get the data sent to the server
		$objectIds = $request->getParameter('objectId');
		$strObjectIds = implode($objectIds, "', '");
//		JcLogger::info("strObjectIds : '".$strObjectIds."'");
		// Get the user login info
		$user = $this->user;
		// First set the deleted attribute to '0' (false)
		$query = new JfQuery();
		$sql = "UPDATE jm_sysobject_sp OBJECTS 	SET	r_lock_owner = '".$user->getValue('user_name')."',
													r_lock_date  = now(),
													r_lock_machine = '".getenv("REMOTE_ADDR")."',
													r_modifier = '".$user->getValue('user_name')."',
													r_modify_date = now()
												WHERE r_object_id  IN  ('".$strObjectIds."')";
		$query->setSQL($sql);
		$query->execute($this->session);
		// Create a fetch event for each object
		$session = $this->session;
		$auditTrailMgr = $session->getAuditTrailManager();
		$stringArgs = array(	'userName' => $user->getValue('user_name'),
								'userIP' => getenv("REMOTE_ADDR")	);
		$arrObjectIds = explode(", ", $strObjectIds);
		foreach ($objectIds as $key => $strObjectId)
		{
			$auditTrailMgr->createAudit($strObjectId, 'checkout', $stringArgs, null);
		}
		return '';
	}
}
?>