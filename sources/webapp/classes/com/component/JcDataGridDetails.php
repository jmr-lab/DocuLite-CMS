<?php
/**
 * The JcDataGridDetails class.
 * Usage :
 *
 * $datagrid = new JcDataGridDetails($this->user, $this->nlsProperties);
 * $datagrid->setObjectGridContent($this->objectgridcontent);
 * $datagrid->setColumns($this->columns);
 * $datagrid->setColumnsProperties($this->columnsProps);
 * $datagrid->setComponent($this->component);
 * $datagrid->setModal(true);	// Optional
 * $datagrid->setEmptyMessage($this->empty_message);	// Optional
 * $datagrid->setFastSearch($this->fastsearch);	// Optional
 * $datagrid->setPageNumber($this->pageNumber);
 * $datagrid->setSort($this->sort, $this->order, $this->sortView);
 * $datagrid->setTitle($this->name);
 * $datagrid->setObjects($this->objects);
 * $datagrid->render();
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcDataGridDetails extends JcDataGrid
{
	/**
	 * Object grid content
	 *
	 * @access	private
	 * @var		string
	 */
	private $empty_message = 'EMPTY_FOLDER';

	/**
	 * Boolean to increase search speed
	 *
	 * @access	private
	 * @var		boolean
	 */
	private $fastsearch = false;

	/**
	* Cell info
	*
	* @access	private
	* @var		array
	*/
	private $cellInfo = array();

	/**
	 * List of columns
	 *
	 * @access	private
	 * @var		array
	 */
	private $columns = array('checkout', 'icon', 'object_name', 'properties', 'r_content_size', 'description', 'r_modify_date');

	/**
	* Columns properties
	*
	* @access	private
	* @var		array
	*/
	private $columnsProps = array();

	/**
	 * Order of the query (default is 'ASC')
	 *
	 * @access	private
	 * @var		String
	 */
	protected $order = 'ASC';

	/**
	 * Sort order of the query.
	 *
	 * @access	private
	 * @var		String
	 */
	private $sort = '';

	/**
	 * Sort view.
	 *
	 * @access	private
	 * @var		String
	 */
	private $sortView = array();

	/**
	 * Object grid content
	 *
	 * @access	private
	 * @var		string
	 */
	private $objectgridcontent = 'objectgridcontentdetail';

	/**
	 * Get the Yes/No message depending on a boolean given
	 *
	 * @access	private
	 * @param	int		the boolean (0 or 1)
	 * @return	String	the message ('Yes' or 'No')
	 */
	private function getAttrRepeating($repeating)
	{
		$arr_repeating = array(0 => 'MSGNO', 1 => 'MSGYES');
		return $this->getString($arr_repeating[$repeating]);
	}

	/**
	 * Get the attribute type (boolean for 0, integer for 1...)
	 *
	 * @access	private
	 * @param	int		the attribute type (0, 1, 2, ...)
	 * @return	String	the attribute type (boolean, integer, ...)
	 */
	private function getAttrType($type)
	{
		$arr_type = array(0 => 'BOOLEAN', 1 => 'INTEGER', 2 => 'STRING', 3=> 'ID', 4=> 'DATETIME', 5 => 'DOUBLE');
		return $this->getString($arr_type[$type]);
	}

	/**
	 * Get the current date time
	 * This function returns the current date time including milliseconds.
	 *
	 * @access	private
	 * @return	String	the date time
	 */
	private function getSize($object)
	{
		$size = $object->getValue('r_content_size');
		$formatted_size = $size;
		$byte = $this->getString('BYTE_SYMBOL');
//		$byte = 'b';
		if ($size == 0)	{$formatted_size = '&nbsp;';}
		else if ($size == 1)	{$formatted_size = $size.' '.$this->getString('BYTE');}
		else if ($size < 1024)	{$formatted_size = $size.' '.$this->getString('BYTES');}
		else if (($size > 1024) && ($size < 1048576))	{$formatted_size = floor($size/1024).' K'.$byte;}
		else if ($size > 1048576)	{$formatted_size = floor($size/1048576).' M'.$byte;}
		return $formatted_size;
	}

	/**
	 * Get the sort view link
	 *
	 * @access	public
	 * @return	String	the sort view link.
	 */
	private function getSortView($column)
	{
		return $this->sortView[$column];
	}

	/**
	 * Render the grid
	 *
	 * @access	public
	 */
	public function render()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Set the sortView array
		$this->setSortView();
		// Get the objects
		$objects = $this->getObjects();
		// Title
		$this->showDetailsTitle();
		// No Result Found
		if (!isset($objects) || sizeof($objects) == 0)	{return $this->showDetailsEmpty();}
		// Get the clipboard
		$clipboard = new JcClipBoard();
		// Get the current container Id
		$httpSession = new JcHttpSession();
		$pathSO = $httpSession->getAttribute('path');
		$containerIds = (($pathSO <> null && sizeof($pathSO) > 0) ? $pathSO : array('0000000000000000'));
		// Content
		echo '<div id="'.$this->objectgridcontent.'">';
		$images = new JcIconList($this->getUser());
		foreach ($objects as $object)
		{
			$link = $object->getValue('_link_name_');
			$icon = $images->getIcon($object, 16);
			$flag = $object->getValue('_lock_icon_');
			$status = $object->getValue('_status_');
			$objectId = $object->getValue('r_object_id');
			$isFolder = in_array(substr($objectId, 0, 2), array('0b', '0c')) ? true : false;

			echo '<div class="cellline '.$this->getObjectClass().' '.$status.'" id="'.$objectId.'">';
			echo '<ul>';

			// Get the clipboard operation (cut / copy) :
			$clipboardObject = $clipboard->getObject($objectId);
			if ($clipboardObject <> null)	{$operation = $clipboardObject->getOperation();}

			// Get previous component
			$componentList = $httpSession->getAttribute('component');
			$component = end($componentList);
			$component = prev($componentList);
//			JcLogger::info('$component : '.$component);

			// Update de permission on the object
			$user = $this->getUser();
			if ($user->getValue('user_name') == $object->getValue('owner_name'))	$object->setValue('r_accessor_permit', '7');

			// For each column display a new cell
			foreach ($this->columns as $key => $value)
			{
				// Init cell
				$cell = array('multiline' => false, 'name' => $value);
				// Get columns properties (size, ...)
				$align = isset($this->cellInfo[$key]['align']) ? $this->cellInfo[$key]['align'] : '';
				$content = isset($this->cellInfo[$key]['content']) ? $this->cellInfo[$key]['content'] : '';
				$size = isset($this->cellInfo[$key]['size']) ? $this->cellInfo[$key]['size'] : '';
				if ($size <> '')	{$cell['width'] = $size;}
				if ($align <> '')	{$cell['text-align'] = $align;}
				else				{$cell['text-align'] = 'left';}
				// Fill the content of the cell. Can be :
				// an icon (checkout, flag...),
				// a text with a link (name),
				// an icon with a link (properties)
				// or a text with no link (date modified)
//				JcLogger::info(__CLASS__.'.'.__FUNCTION__.'($value : '.$value.')');
				switch ($value)
				{
					// Empty
					case 'empty':
						$cell['content'] = '&nbsp;';
						break;
					// Priority icon (flag)
					case ($value == 'flag' || $value == 'checkout'):
						$cell['content'] = $flag;
						break;
					// Object icon
					case 'icon':
						$cell['content'] = '<img src="'.$icon.'">';
						break;
					// Subject
					case ($value == 'object_name' || $value == 'event'):
						$cell['content'] = $link['open'].$object->getValue($value).$link['close'];
						$cell['name'] = 'recipient';
						break;
					// Properties
					case 'properties':
						$cell['content'] = '<a href="javascript:properties(\''.$object->getValue('r_object_id').'\');"><img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/info_16.png"></a>';
						break;
					// Sender
					case 'r_content_size':
						$cell['content'] = '<span>'.$this->getSize($object).'</span>';
						break;
					// Description / Sender
					case ($value == 'description' || $value == 'sent_by'):
						$cell['content'] = '<span>'.htmlentities($object->getValue($value)).'</span>';
						$cell['multiline'] = true;
						break;
					// Recipient
					case 'recipient':
						$cell['content'] = '<span>'.$object->getValue('name').'</span>';
						break;
					// Attribute type
					case 'attr_type':
						$cell['content'] = '<span>'.$this->getAttrType($object->getValue('attr_type')).'</span>';
						break;
					// Attribute Repeating
					case 'attr_repeating':
						$cell['content'] = '<span>'.$this->getAttrRepeating($object->getValue('attr_repeating')).'</span>';
						break;
					// Date sent
					case ($value == 'date_sent' || $value == 'r_modify_date' || $value == 'r_start_date'):
						$cell['content'] = '<span>'.JcUtils::getTime($object->getValue($value)).'</span>';
						break;
					// Object name
					case ($value == 'object_name_span'):
						$cell['content'] = '<span style="font-size: 12px; font-weight: bold; color: #5F5F5F;">'.$object->getValue($value).'</span>';
						break;
					// File
					case ($value == 'file'):
						if ($isFolder)	break;
						$cell['content'] = '<span>';
						$cell['content'] .= '<input type="hidden" name="objectId['.$objectId.']" value="'.$object->getValue('r_object_id').'">';
						$cell['content'] .= '<input name="file['.$objectId.']" id="file" type="file" size="23" class="select_text">';
						$cell['content'] .='</span>';
						break;
					// Version
					case ($value == 'version'):
						$cell['content'] = '<span>';
						$cell['content'] .= '<jm:dropdownlist name="version['.$objectId.']" id="version" style="width: 150px; font-size: 11px;">';
						if ($object->getValue('r_accessor_permit') >= '6' && $object->getValue('r_version_label') <> 'OLD')	{$cell['content'] .= '<jm:option value="SAME_VERSION">'.$this->getString('SAME_VERSION').'</jm:option>';}
						if (!$isFolder && $object->getValue('r_accessor_permit') >= '5' && $object->getValue('r_version_label') <> 'OLD')	{$cell['content'] .= '<jm:option value="MINOR_VERSION">'.$this->getString('MINOR_VERSION').'</jm:option>';}
						if (!$isFolder && $object->getValue('r_accessor_permit') >= '5' && $object->getValue('r_version_label') <> 'OLD')	{$cell['content'] .= '<jm:option value="MAJOR_VERSION">'.$this->getString('MAJOR_VERSION').'</jm:option>';}
						if (!$isFolder && $object->getValue('r_version_label') == 'OLD')	{$cell['content'] .= '<jm:option value="BRANCH_VERSION">'.$this->getString('BRANCH_VERSION').'</jm:option>';}
						$cell['content'] .= '</jm:dropdownlist>';
						$cell['content'] .='</span>';
						break;
					// Delete
					case ($value == 'delete'):
						$cell['content'] = '<span>';
						$cell['content'] .= '<jm:dropdownlist name="action['.$objectId.']" id="action" style="width: 200px; font-size: 11px;">';
						if ($component == 'clipboard')
						{
							$cell['content'] .= '<jm:option value="3">'.$this->getString('REMOVE_OBJECT_FROM_CLIPBOARD').'</jm:option>';
						}
						elseif ($component == 'favorites')
						{
							$cell['content'] .= '<jm:option value="4">'.$this->getString('REMOVE_OBJECT_FROM_FAVORITES').'</jm:option>';
						}
						else
						{
							$cell['content'] .= '<jm:option value="0">'.$this->getString('REMOVE_OBJECT').'</jm:option>';
							if ($object->getValue('_version_count') > 1)	{$cell['content'] .= '<jm:option value="1">'.$this->getString('REMOVE_ALL_VERSIONS').'</jm:option>';}
							if ($object->getValue('_folder_count') > 1 && $object->getValue('r_immutable_flag') == 0)	{$cell['content'] .= '<jm:option value="2">'.$this->getString('UNLINK_FROM_CURRENT_FOLDER').'</jm:option>';}
						}
						$cell['content'] .= '</jm:dropdownlist>';
						$cell['content'] .='</span>';
						break;
					// Delete Group
					case ($value == 'deletegroup'):
						$cell['content'] = '<span>';
						$cell['content'] .= '<jm:dropdownlist name="action['.$objectId.']" id="action" style="width: 150px; font-size: 11px;">';
						$cell['content'] .= '<jm:option value="0">'.$this->getString('REMOVE_OBJECT').'</jm:option>';
						if ($object->getValue('_group_count_') > 1)	{$cell['content'] .= '<jm:option value="2">'.$this->getString('UNLINK').'</jm:option>';}
						$cell['content'] .= '</jm:dropdownlist>';
						$cell['content'] .='</span>';
						break;
					// Paste
					case ($value == 'paste'):
						$cell['content'] = '<span>';
						$cell['content'] .= '<jm:dropdownlist name="action['.$objectId.']" id="action" style="width: 200px; font-size: 11px;">';
						$cell['content'] .= '<jm:option value="0">'.$this->getString('PLEASE_SELECT').'</jm:option>';
						$cell['content'] .= '<jm:option value="1">'.$this->getString('REMOVE_FROM_CLIPBOARD').'</jm:option>';
						if ($operation == 'copy')
						{
							$cell['content'] .= '<jm:option value="2">'.$this->getString('COPY').'</jm:option>';
							$cell['content'] .= '<jm:option value="3">'.$this->getString('LINK').'</jm:option>';
						}
						else if ($operation == 'copygroup' && in_array($objectId, $containerIds) === false)
						{
							$cell['content'] .= '<jm:option value="3">'.$this->getString('LINK').'</jm:option>';
						}
						else if (($operation == 'cut' || $operation == 'cutgroup') && in_array($objectId, $containerIds) === false)
						{
							$cell['content'] .= '<jm:option value="4">'.$this->getString('MOVE').'</jm:option>';
						}
						$cell['content'] .= '</jm:dropdownlist>';
						$cell['content'] .='</span>';
						break;
					// Restore
					case ($value == 'restore'):
						$cell['content'] = '<span>';
						$cell['content'] .= '<jm:dropdownlist name="action['.$objectId.']" id="action" style="width: 150px; font-size: 11px;">';
						$cell['content'] .= '<jm:option value="0">'.$this->getString('RESTORE_OBJECT').'</jm:option>';
						if ($object->getValue('_version_count') > 1)	{$cell['content'] .= '<jm:option value="1">'.$this->getString('RESTORE_ALL_VERSIONS').'</jm:option>';}
						$cell['content'] .= '</jm:dropdownlist>';
						$cell['content'] .='</span>';
						break;
					default:
						$cell['content'] = '<span>'.$this->getString($object->getValue($value)).'</span>';
				}
				// Display the cell
				$this->showCell($cell);
			}

			echo '</ul>';
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Set the object grid content
	 *
	 * @access	public
	 * @param	boolean		is the grid modal
	 */
	public function setObjectGridContent($objectgridcontent)
	{
		$this->objectgridcontent = $objectgridcontent;
	}

	/**
	 * Set the grid columns
	 *
	 * @access	public
	 * @param	array	the columns
	 */
	public function setColumns($columns)
	{
		$this->columns = $columns;
	}

	/**
	 * Set the grid columns properties
	 *
	 * @access	public
	 * @param	array	the columns properties
	 */
	public function setColumnsProperties($columnsProps)
	{
		$this->columnsProps = $columnsProps;
	}

	/**
	 * Set the empty message
	 *
	 * @access	public
	 * @param	boolean		is the grid modal
	 */
	public function setEmptyMessage($empty_message)
	{
		$this->empty_message = $empty_message;
	}

	/**
	 * Set the fast search mode
	 *
	 * @access	public
	 * @param	boolean		the fast search mode
	 */
	public function setFastSearch($fastsearch)
	{
		$this->fastsearch = $fastsearch;
	}

	/**
	 * Set the sort mode
	 *
	 * @access	public
	 * @param	String		the order
	 * @param	String		the sort
	 */
	public function setSort($sort, $order)
	{
		$this->sort = $sort;
		$this->order = $order;
	}

	/**
	 * Set the sort view link
	 *
	 * @access	public
	 * @param	String	the sort
	 */
	public function setSortView()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		foreach ($this->columns as $key => $value)
		{
			// Init the current component
			$component = $this->getComponent();
			// Init the current page
			$page = $this->getPageNumber();
			$page = (($page == 1) ? '' : 'page='.$this->getPageNumber().';');
			// Change the order
			$order = (($this->order == 'ASC') ? 'DESC' : 'ASC');
			// Re init the sort and order
			if ($value <> $this->sort)	{$order = 'ASC';}
			// Get the search string
			$search = (($this->strSearch <> '') ? ';search='.$this->strSearch.';' : '');
			// Set the link
			$jscript = "'objectlist', 'jump', '".$component."', null, '".$page."sort=".$value.";order=".$order.$search."'";
			$jscript = 'javascript:postServerEvent('.$jscript.');';
			// Add the link to the the sortView array
			$this->sortView[$value] = $jscript;
		}
	}

	/**
	 * Show the content of a cell
	 * $param = array('width' => '24', 'content' => '&nbsp;');
	 *
	 * @param	array	param	an array of details to display
	 * @access	private
	 */
	private function showCell($param)
	{
		$width = ((isset($param['width'])) ? 'width: '.$param['width'].';' : '');
		$textalign = ((isset($param['text-align'])) ? ' text-align: '.$param['text-align'].';' : '');
		$style = trim($width.$textalign);
		$name = ((isset($param['name'])) ? ' name="'.$param['name'].'"' : '');
		echo '<li class="clli" style="'.$style.'"'.$name.'>';
		echo $param['content'];
		echo '</li>';
	}

	/**
	 * Show details empty message
	 *
	 * @access	private
	 */
	private function showDetailsEmpty()
	{
		echo '<div id="objectgridcontentdetail" style="height: 24px; line-height:24px; border-top:2px solid #e5e5e5;">';
		echo '<div style="float: left; text-align: left; width: 24px;">&nbsp;</div>';
		echo '<div style="float: left; text-align: left; width: 24px;"><img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/warning_16.png" style="width: 16px; height: 16px; margin: 4px 0 0 4px;"></div>';
		echo '<div style="float: left; text-align: left; font-weight: bold;">'.$this->getString($this->empty_message).'</div>';
		echo '</div>';
		echo '<div style="clear: both;"></div>';
	}

	/**
	 * Show details title
	 *
	 * @access	private
	 */
	private function showDetailsTitle()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Create the sort arrows
//		$sortArr = array('empty' => '', 'event' => '', 'sent_by' => '', 'date_sent' => '');
		$sortArr = $this->array_fill_keys($this->columns, '');

		if (trim ($this->sort) <> '')
		{
			if ($this->order == 'ASC')	{$sortArr[$this->sort] = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/arrow_up.png" style="width: 12px; height: 12px; margin-top: 10px;">';}
			else						{$sortArr[$this->sort] = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/arrow_down.png" style="width: 12px; height: 12px; margin-top: 10px;">';}
		}
		// Title
		echo '<div style="height: 32px; line-height:32px; margin-top: 0px; border-bottom:2px solid #e5e5e5; margin-right: 16px;">';
		// 'Name', 'Properties', 'Size', 'Info' (icon), 'Type' and 'Modified' headers.
//		$this->columns = array('empty', 'empty', 'event', 'sent_by', 'date_sent');
		foreach ($this->columns as $key => $value)
		{
			// $value = 'empty', 'empty', 'event', 'sent_by' or 'date_sent'
			$this->cellInfo[$key] = $this->columnsProps[strtoupper($value)];
			$link = isset($this->cellInfo[$key]['link']) ? $this->cellInfo[$key]['link'] : '';
			$size = isset($this->cellInfo[$key]['size']) ? $this->cellInfo[$key]['size'] : '';
			$title = isset($this->cellInfo[$key]['title']) ? $this->cellInfo[$key]['title'] : '';
			$icon = isset($this->cellInfo[$key]['icon']) ? $this->cellInfo[$key]['icon'] : '';
			if ($size <> '')	{$size = ' style="width: '.$size.';"';}
			if ($link == 'false')				{$title = $this->getString($title);}
			else if ($this->fastsearch == true)	{$title = $this->getString($title);}
			else if ($title <> '')				{$title = '<a href="'.$this->getSortView($value).'">'.$this->getString($title).'</a>'.$sortArr[$value];}
			else if ($icon <> '')				{$title = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/'.$icon.'">';}
			else								{$title = '&nbsp;';}
			echo '<div class="columntitle"'.$size.'>'.$title.'</div>';
		}
		echo '</div>';
	}
}
?>