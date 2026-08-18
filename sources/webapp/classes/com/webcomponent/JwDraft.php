<?php
/**
 * The Draft webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDraft extends JwMailbox
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('flag', 'icon', 'event', 'sent_by', 'date_sent');

	/**
	 * Returns the title name (My Documents).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleImage()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return 'draft.png';
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
		return $this->getString('DRAFTS');
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

		$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, event, item_name, date_sent,
					'jm_mail' AS r_object_type, '' AS description, name, SUBSTR(message, 1, 30) AS message, priority
				FROM jmi_queue_item_s
				WHERE sent_by = '".$user->getValue('user_name')."' AND delete_flag = 0 AND priority = 10";

		$queryObj = new JcQuery($sql);
		$order = array("priority" => "DESC");
		$queryObj->setOrderByClauses($order);
		$this->setSQL($queryObj);

		parent::init();
	}
}
?>