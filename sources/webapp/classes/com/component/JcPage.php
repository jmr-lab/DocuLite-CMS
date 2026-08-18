<?php
/**
 * The JcPage class.
 * Usage :
 *
 * $page = new JcPage();
 * $page->init($request);
 *
 * This class will set the page if needed.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcPage
{
	/**
	* Default page
	*
	* @access	private
	* @var		array
	*/
	private $page;

	/**
	 * Constructor
	 *
	 */
	public function __construct()	{}

	/**
	 * Initialize the page
	 *
	 * @access	public
	 * @param	JcHttpRequest	The request
	 */
	public function init($request)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get session object
		$httpsession = new JcHttpSession();
		// Get the requested page : can be null (refresh), empty (no page set), or contains a number
		$page = (($request == null) ? null : $request->getParameter('page'));
		// Get the current event
		$event = (($request == null) ? '' : $request->getParameter('event'));
		// Only works on the base component
		$componentList = $httpsession->getAttribute('component');
		// Don't do anything if request is null (refresh)
		if ($request == null)	{}
		// page contains an integer
		else if (sizeof($componentList) == 1 && $event == 'jump')
		{
			if ($page == '')	{$page = '1';}
			$httpsession->setAttribute('page', $page);
		}
	}

	/**
	 * Get the current page
	 *
	 * @access	public
	 * @param	JcHttpRequest	The request
	 * @return	integer			The page number
	 */
	public function getPage($request)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$pageNumber = 1;
		$httpsession = new JcHttpSession();
		// Init the current component
		$component = (($httpsession->getAttribute('component') <> null) ? $httpsession->getAttribute('component') : array(0 => 'doclist'));
		if ($request <> null && $request->getParameter('page') <> '')	{$pageNumber = $request->getParameter('page');}
		else if (sizeof($component) > 1)	{$pageNumber = 1;}
		else	{$pageNumber = (($httpsession->getAttribute('page') <> '') ? $httpsession->getAttribute('page') : '1');}
		return $pageNumber;
	}
}
?>