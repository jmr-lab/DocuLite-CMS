<?php
/**
 * SelectUser webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwSelectUser extends JwDocList
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
		// Get the request info
		$request = new JcHttpServletRequest();
		// Init the current folder ID
		$objectId = $request->getParameter('objectId');

		$sql = "SELECT SQL_CALC_FOUND_ROWS r_object_id, event_name, user_id, time_stamp
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

	/**
	 * Method called when the current component is closed.
	 *
	 * @access	public
	 */
	public function onClose()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Add date to the string
		$milli = floor(1000 * microtime());
		while (strlen($milli) < 3)	{$milli = '0'.$milli;}
		echo "	<script>
				var returnflag = '".date("Y-m-d H:i:s").",".$milli."';
				_callback(returnflag);
				var _callback = function(value)	{};
				</script>";
		return false;
	}
}
?>