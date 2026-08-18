<?php
/**
 * JwSearchResults webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwSearchResults extends JwDocList
{
	/**
	 * Returns the title image (My Documents).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleImage()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return 'search.png';
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
		return $this->getString('SEARCH_RESULTS');
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
//		$strPath = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/repository_16.png">'.$this->strSearch;
		$strPath = '<span>'.$this->getString('NAME').' : </span><span style="font-weight: bold; font-style:italic;">'.$this->strSearch.'</span>';
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

		// Added to test the search
		$request = new JcHttpServletRequest();
		// Get the search parameters
		$strSearch = addslashes($request->getParameter('search'));
		$strType = $request->getParameter('type');
		$strField = $request->getParameter('field');
		$strCondition = $request->getParameter('condition');
		$strInput = $request->getParameter('input_text');

		JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(Search : '.$request->getParameter('search').')');
		// JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(Type : '.$request->getParameter('type').')');
		// JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(Field : '.$request->getParameter('field').')');
		// JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(Condition : '.$request->getParameter('condition').')');
		// JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(Input : '.$request->getParameter('input_text').')');

		switch ($strType)
		{
			case 'jm_document':		// Document
				$sql = "SELECT SQL_CALC_FOUND_ROWS jm_document_sp.r_object_id, r_object_type, object_name, owner_name, r_creation_date, r_modify_date,
							r_accessor_permit, r_content_size, r_lock_owner, i_contents_id, a_content_type,
							CASE WHEN jm_format_s.dos_extension IS NULL THEN a_content_type ELSE jm_format_s.dos_extension END AS dos_extension,
							CASE WHEN jm_format_s.description IS NULL THEN CASE WHEN a_content_type <> '' THEN CONCAT(UCASE(a_content_type), ' File') END ELSE jm_format_s.description END AS description
						FROM jm_document_sp
						LEFT JOIN jm_format_s ON a_content_type = jm_format_s.name
						INNER JOIN (
							SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
							FROM v_users_acls
							WHERE r_object_id = '".$user->getValue('r_object_id')."'
							GROUP BY acl_id
							) AS table_permit ON jm_document_sp.acl_id = table_permit.acl_id
						INNER JOIN jm_sysobject_r r1 ON jm_document_sp.r_object_id = r1.r_object_id
						WHERE r1.i_position = '-2'
							AND r1.r_version_label <> 'OLD'
							AND jm_document_sp.i_is_deleted = false
							AND (r_accessor_permit > 1 OR owner_name = '".$user->getValue('user_name')."')
							AND MATCH(jm_document_sp.object_name) AGAINST ('".$strInput."' IN BOOLEAN MODE)";
				$order = array("CASE WHEN jm_document_sp.r_object_type IN ('jm_cabinet', 'jm_folder') THEN NULL ELSE jm_document_sp.r_object_type END" => "ASC");
				$this->strSearch = $strInput;
				break;
			case 'jm_folder':		// Folder
				$sql = "SELECT SQL_CALC_FOUND_ROWS jm_folder_sp.r_object_id, r_object_type, object_name, owner_name, r_creation_date, r_modify_date,
							r_accessor_permit, r_content_size, r_lock_owner, i_contents_id, a_content_type,
							CASE WHEN jm_format_s.dos_extension IS NULL THEN a_content_type ELSE jm_format_s.dos_extension END AS dos_extension,
							CASE WHEN jm_format_s.description IS NULL THEN CASE WHEN a_content_type <> '' THEN CONCAT(UCASE(a_content_type), ' File') END ELSE jm_format_s.description END AS description
						FROM jm_folder_sp
						LEFT JOIN jm_format_s ON a_content_type = jm_format_s.name
						INNER JOIN (
							SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
							FROM v_users_acls
							WHERE r_object_id = '".$user->getValue('r_object_id')."'
							GROUP BY acl_id
							) AS table_permit ON jm_folder_sp.acl_id = table_permit.acl_id
						INNER JOIN jm_sysobject_r r1 ON jm_folder_sp.r_object_id = r1.r_object_id
						WHERE r1.i_position = '-2'
							AND r1.r_version_label <> 'OLD'
							AND jm_folder_sp.i_is_deleted = false
							AND (r_accessor_permit > 1 OR owner_name = '".$user->getValue('user_name')."')
							AND MATCH(jm_folder_sp.object_name) AGAINST ('".$strInput."' IN BOOLEAN MODE)
							AND jm_folder_sp.r_object_type IN ('jm_cabinet', 'jm_folder')";
				$order = array("CASE WHEN jm_folder_sp.r_object_type IN ('jm_cabinet', 'jm_folder') THEN NULL ELSE jm_folder_sp.r_object_type END" => "ASC");
				$this->strSearch = $strInput;
				break;
			case 'jmi_queue_item':	// Formats
				$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, event, item_name, date_sent,
					'jm_mail' AS r_object_type, '' AS description, sent_by, SUBSTR(message, 1, 30) AS message, priority
						FROM jmi_queue_item_s
						WHERE (name = '".$user->getValue('user_name')."' OR sent_by = '".$user->getValue('user_name')."')
							AND delete_flag = 0
							AND (message LIKE '%".$strInput."%' OR event LIKE '%".$strInput."%')";
				$order = array();
				$this->strSearch = $strInput;
				$this->columns = array('flag', 'icon', 'event', 'sent_by', 'date_sent');
				break;
			case 'jm_format':		// Format
				$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, name AS object_name, description AS dos_extension,
							'jm_format' AS r_object_type, 'Format' AS description, '' AS r_modify_date
						FROM jm_format_s
						WHERE MATCH(dos_extension) AGAINST ('".$strInput."' IN BOOLEAN MODE)";
				$this->strSearch = $strInput;
				$this->columns = array('icon', 'object_name', 'properties', 'dos_extension');
				break;
			default:				// No type defined (quick search)
				$sql = "SELECT SQL_CALC_FOUND_ROWS jm_sysobject_s.r_object_id, r_object_type, object_name, owner_name, r_creation_date, r_modify_date,
							r_accessor_permit, r_content_size, r_lock_owner, i_contents_id, a_content_type,
							CASE WHEN jm_format_s.dos_extension IS NULL THEN a_content_type ELSE jm_format_s.dos_extension END AS dos_extension,
							CASE WHEN jm_format_s.description IS NULL THEN CASE WHEN a_content_type <> '' THEN CONCAT(UCASE(a_content_type), ' File') END ELSE jm_format_s.description END AS description
						FROM jm_sysobject_s
						LEFT JOIN jm_format_s ON a_content_type = jm_format_s.name
						INNER JOIN (
							SELECT acl_id, MAX(r_accessor_permit) AS r_accessor_permit
							FROM v_users_acls
							WHERE r_object_id = '".$user->getValue('r_object_id')."'
							GROUP BY acl_id
							) AS table_permit ON jm_sysobject_s.acl_id = table_permit.acl_id
						INNER JOIN jm_sysobject_r r1 ON jm_sysobject_s.r_object_id = r1.r_object_id
						WHERE r1.i_position = '-2'
							AND r1.r_version_label <> 'OLD'
							AND jm_sysobject_s.i_is_deleted = false
							AND (r_accessor_permit > 1 OR owner_name = '".$user->getValue('user_name')."')
							AND MATCH(jm_sysobject_s.object_name) AGAINST ('".$strSearch."' IN BOOLEAN MODE)";
				$order = array("CASE WHEN jm_sysobject_s.r_object_type IN ('jm_cabinet', 'jm_folder') THEN NULL ELSE jm_sysobject_s.r_object_type END" => "ASC");
				$this->strSearch = $strSearch;
				break;
		}


		$queryObj = new JcQuery($sql);
		$queryObj->setOrderByClauses($order);
//		$this->fastsearch = true;

		$this->setSQL($queryObj);
		parent::init();
		
		// Change text of the search textbox
		echo "<script>$('#txtsearch').val('".$this->getString('SEARCH')."');</script>";
	}
}
?>