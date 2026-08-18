<?php
/**
 * The JwModalList webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwModalList extends JwDocList
{
	/**
	 * Set the link to the target
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
							'objectgridcontent' => $this->objectgridcontent,
							'columns' => $this->columns,
							'columnsProps' => $this->columnsProps,
							'component' => $this->component,
							'empty_message' => $this->empty_message,
							'fastsearch' => $this->fastsearch,
							'pageNumber' => $this->pageNumber,
							'strSearch' => $this->strSearch,
							'sort' => $this->sort,
							'order' => $this->order,
							'name' => $this->name,
							'modal' => 'true'
						);
		return $values;
	}
}
?>