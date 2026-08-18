<?php
/**
 * JwMyDocuments webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwRecycleBin extends JwObjectList
{
	/**
	 * Returns the title image (Recycle Bin).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleImage()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return 'recyclebin.png';
	}

	/**
	 * Returns the title name (Recycle Bin).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleName()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return $this->getString('RECYCLE_BIN');
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

		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT jm_sysobject_s.r_object_id, r_object_type, object_name, owner_name, r_creation_date, r_modify_date,
						r_accessor_permit, r_content_size, r_lock_owner, i_contents_id, a_content_type, jm_sysobject_s.i_is_deleted,
						CASE WHEN jm_format_s.dos_extension IS NULL THEN a_content_type ELSE jm_format_s.dos_extension END AS dos_extension,
						CASE WHEN jm_format_s.description IS NULL THEN CONCAT(UCASE(a_content_type), ' File') ELSE jm_format_s.description END AS description
				FROM	(jm_sysobject_s, jm_sysobject_r r1, jm_sysobject_r r2,
						(SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
						FROM v_users_acls WHERE r_object_id = '".$user->getValue('r_object_id')."' GROUP BY acl_id) AS table_permit)
				LEFT JOIN jm_format_s ON a_content_type = jm_format_s.name
				WHERE	jm_sysobject_s.i_is_deleted = true
						AND jm_sysobject_s.r_object_id = r1.r_object_id
						AND r1.i_position = '-2'
						AND r1.r_version_label = 'DELETED'
						AND r1.r_object_id = r2.r_object_id
						AND jm_sysobject_s.acl_id = table_permit.acl_id
						AND (r_accessor_permit > 1 OR owner_name = '".$user->getValue('user_name')."')";

		$queryObj = new JcQuery($sql);
		$order = array("CASE WHEN jm_sysobject_s.r_object_type IN ('jm_cabinet', 'jm_folder') THEN NULL ELSE jm_sysobject_s.r_object_type END" => "ASC");
		$queryObj->setOrderByClauses($order);

		$this->setSQL($sql);
		parent::init();
	}
}
?>