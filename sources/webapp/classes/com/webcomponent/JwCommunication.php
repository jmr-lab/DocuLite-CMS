<?php
/**
 * The JwCommunication webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwCommunication extends JwObjectList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'object_name', 'comment');

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
			// Init the target
			$target = $object->getValue('r_object_type');
			// Init the link
			$link = array('open' => '', 'close' => '');
			// Set the link
			switch ($target)
			{
				case 'inbox':		// Inbox
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'inbox', null, null);";
					break;
				case 'outbox':		// Outbox
					$jscript = "javascript:postServerEvent('objectlist', 'jump', 'outbox', null, null);";
					break;
			}
			$link['open'] = '<a href="'.$jscript.'" class="link">';
			$link['close'] = '</a>';
			// Set the link
			$object->setValue('_link_name_', $link);
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
		return 'communication.png';
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
		return $this->getString('COMMUNICATION');
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

		$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, object_name, r_object_type, comment
				FROM (
					SELECT '0000000000000001' AS r_object_id, '".$this->getString('INBOX')."' AS object_name, 'inbox' AS r_object_type, 'Inbox' AS comment
					UNION SELECT '0000000000000002' AS r_object_id, '".$this->getString('SENT')."' AS object_name, 'outbox' AS r_object_type, 'Outbox' AS comment
				) AS table_admin";

		$queryObj = new JcQuery($sql);
		$order = array("r_object_id" => "ASC");
		$queryObj->setOrderByClauses($order);
		$this->setSQL($queryObj);
		parent::init();
	}
}
?>