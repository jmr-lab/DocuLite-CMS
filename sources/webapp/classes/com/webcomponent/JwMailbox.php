<?php
/**
 * The Mailbox webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwMailbox extends JwObjectList
{
	/**
	 * Empty message
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $empty_message = 'NO_MAIL';

	/**
	 * Column name to display
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $name = 'event';

	/**
	 * Get the datagrid values (sort, order, strSearch, component...)
	 *
	 * @access	public
	 * @return	array	a list of values
	 */
	public function getDataGridValues()
	{
		$values = array(	'session' => $this->session,
							'folderObj' => $this->folderObj,
							'objects' => $this->objects,
							'view' => 'details',
							'user' => $this->user,
							'nlsProperties' => $this->nlsProperties,
							'objectgridcontent' => 'objectgridcontentmail',
							'columns' => $this->columns,
							'columnsProps' => $this->columnsProps,
							'component' => $this->component,
							'empty_message' => $this->empty_message,
							'fastsearch' => $this->fastsearch,
							'pageNumber' => $this->pageNumber,
							'strSearch' => $this->strSearch,
							'sort' => $this->sort,
							'order' => $this->order,
							'name' => $this->name
						);
		return $values;
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
		// Return the path
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
		parent::init();
		$this->setStatus();
	}

	/**
	 * Get the icon associated with an object
	 * $object = array('r_object_id' => '0b001e2400786532', 'r_object_type' => 'jm_document', 'a_content_type' = > 'png', ...)
	 *
	 * @access	protected
	 * @param	array	object			an array representing the object
	 * @param	int		size			the icon size (64)
	 * @return	String	the icon path and name
	 */
	protected function setStatus()
	{
		if (!isset($this->objects) || sizeof($this->objects) == 0)	{return;}
		foreach ($this->objects as $index => $object)
		{
			$class = 'unread';
			$dequeuedBy = preg_replace('/\s+/', ' ', $object->getValue('dequeued_by'));
			if (trim($dequeuedBy) <> '')	{$class = 'read';}
			$object->setValue('_status_', $class);
			$this->objects[$index] = $object;
		}
	}

	/**
	 * Get the icon associated with an object
	 * $object = array('r_object_id' => '0b001e2400786532', 'r_object_type' => 'jm_document', 'a_content_type' = > 'png', ...)
	 *
	 * @access	protected
	 * @param	array	object			an array representing the object
	 * @param	int		size			the icon size (64)
	 * @return	String	the icon path and name
	 */
	protected function setLockIcons()
	{
		if (!isset($this->objects) || sizeof($this->objects) == 0)	{return;}
		$user = $this->user;
		foreach ($this->objects as $index => $object)
		{
			$flag = array(0 => 'green', 1 => 'orange', 2 => 'red');
			$icon = $flag[$object->getValue('priority')].'.png';
			$icon = _APP_ROOT_.'/webapp/themes/default/images/icons/'.$icon;
			$icon = '<img src="'.$icon.'" style="width: 16px; height: 16px;">';
			$object->setValue('_lock_icon_', $icon);
			$this->objects[$index] = $object;
		}
	}

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
			$mailId = $object->getValue('r_object_id');
			// Init the link
			$link = array('open' => '', 'close' => '');
			// Set the link
			$jscript = "javascript:getMessage('".$mailId."');";
			$link['open'] = '<a href="'.$jscript.'" style="color: #5F5F5F; text-decoration: none;">';
			$link['close'] = '</a>';
			$object->setValue('_link_name_', $link);
			$this->objects[$index] = $object;
		}
	}
}
?>