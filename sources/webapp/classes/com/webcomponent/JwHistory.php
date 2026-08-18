<?php
/**
 * JwHistory webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwHistory extends JwModalList
{
	/**
	 * List of columns
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $columns = array('icon', 'empty', 'event_name', 'user_id', 'time_stamp');

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
	 * Get the page link
	 *
	 * @access	public
	 * @return	String	the page link
	 */
	public function getPageLink($target)
	{
		$icon = '';
		$page = 0;
		switch ($target)
		{
			case 'first' :	
						if ($this->getPageNumber() == 1)	{$icon = 'player_rew_grey';}
						else	{$icon = 'player_rew';$page = 1;}
						break;
			case 'prev' :
						if ($this->getPageNumber() == 1)	{$icon = 'player_back_grey';}
						else	{$icon = 'player_back';$page = $this->getPageNumber() - 1;}
						break;
			case 'next' :
						if ($this->getPageNumber() == $this->getPageCount())	{$icon = 'player_play_grey';}
						else	{$icon = 'player_play';$page = $this->getPageNumber() + 1;}
						break;
			case 'last' :
						if ($this->getPageNumber() == $this->getPageCount())	{$icon = 'player_fwd_grey';}
						else	{$icon = 'player_fwd';$page = $this->getPageCount();}
						break;
		}
		$link = '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/'.$icon.'.png">';
		if ($page > 0)
		{
			// Get the request info
			$request = new JcHttpServletRequest();
			// Init the current folder ID
			$objectId = $request->getParameter('objectId');
			// Get the search string
			$search = (($this->strSearch <> '') ? ';search='.$this->strSearch.';' : '');
			// Get the sort string
			$sort = '';
			if ($this->sort <> '')	{$sort = ';sort='.$this->sort.';order='.$this->order.';';}

			$jscript = "'objectlist', 'jump', ";
			$jscript .= "'history', null, 'folderId=".$objectId.";page=".$page.$sort.$search."'";

			$link = '<a href="javascript:postServerEvent('.$jscript.');">'.$link;
			$link .= '</a>';
		}
		return $link;
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
		// Get the request info
		$request = new JcHttpServletRequest();
		// Init the current folder ID
		$objectId = $request->getParameter('objectId');

		$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, event_name, user_id, time_stamp, 'event' AS r_object_type
				FROM jm_audittrail_s
				WHERE audited_obj_id = '".$objectId."'";

		$queryObj = new JcQuery($sql);
		$this->setSQL($queryObj);
		// Force the 'details' view to be used
		$this->view = 'details';
		$this->objectgridcontent = 'nestedobjectgridcontent';
		parent::init();
		// Init current component to 'History'
//		$this->component = 'history';
	}
}
?>