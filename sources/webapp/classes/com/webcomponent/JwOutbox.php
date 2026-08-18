<?php
/**
 * The Outbox webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwOutbox extends JwMailbox
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('flag', 'icon', 'event', 'recipient', 'date_sent');

	/**
	 * Returns the title name (My Documents).
	 *
	 * @access	public
	 * @return	String	the title name
	 */
	public function getTitleImage()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return 'outbox.png';
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
		return $this->getString('SENT');
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

		$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, event, item_name, date_sent, dequeued_by,
					'jm_mail' AS r_object_type, '' AS description, name, SUBSTR(message, 1, 30) AS message, priority
				FROM jmi_queue_item_s
				WHERE sent_by = '".$user->getValue('user_name')."' AND delete_flag = 0";

		$queryObj = new JcQuery($sql);

		$this->setSQL($queryObj);
		parent::init();
	}

}
?>