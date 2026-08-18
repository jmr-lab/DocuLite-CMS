<?php
/**
 * The Server Event Manager class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcServerEventManager
{
	/**
	* Begin time of the call
	*
	* @access	private
	* @var		String
	*/
	private $begin;

	/**
	* End time of the call
	*
	* @access	private
	* @var		int
	*/
	private $end;

	/**
	* Last component called. Used when a return is done.
	*
	* @access	private
	* @var		String
	*/
	private $component = '';

	/**
	* Is the user logged in
	*
	* @access	private
	* @var		boolean
	*/
	private $isLoggedIn = true;

	/**
	* JcHttpServletRequest
	*
	* @access	private
	* @var		JcHttpServletRequest
	*/
	private $request;

	/**
	 * Constructor
	 *
	 * This function initialize the current class.
	 */
	public function __construct($request)
	{
		// Logger
		JcLogger::info(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the time (milliseconds)
		$this->begin = floor(1000 * microtime());
		// Set the request
		$this->request = $request;
		// Run the function (optional)
		$function = new JcFunction($request);
		$function->execute();
		// Update the folder list (path)
		$path = new JcPath();
		$path->init($request);
		// Update the component list
		$componentmgr = new JcComponentManager($request);
		$this->component = $componentmgr->setComponent($request);
		// Update the current page number
		$page = new JcPage();
		$page->init($request);
		// Update the language (default is 'fr')
		$language = new JcLanguage();
		$language->init($request);
	}

	/**
	 * Destructor
	 *
	 * This function parse the tags and output the new buffer
	 *
	 * @throws JfException - if a server error occurs
	 */
	public function __destruct()
	{
		$source = ob_get_clean();
		// If the user is not logged in redirect to the login page :
		if (!$this->isLoggedIn)	{$source = $this->redirectToLoginPage();}
		// Get the Http Session
		$httpsession = new JcHttpSession();
		// Initialize the view
		$view = new JcView($this->request);
		// Parse the tags
		$parser = new JcParser($this->component);
//		try	{$output = $parser->parseTags($source); throw new Exception('AN ERROR OCCURED');}
		try	{$output = $parser->parseTags($source);}
		catch (Exception $exception)	{$output = $view->printErrorMessage($exception);}
		if (ob_get_length() > 0)	{ob_end_clean();}
		// Turn on output buffering with zlib compression
		if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
		// Print the headers
		if ($this->request == null)	{$view->printHeaders();}
		if (sizeof($httpsession->getAttribute('component')) == 1)	{$view->printPHPErrors();}
		// Add the Ajax element and the overlay
		if ($this->request == null)	{$view->printAjax();}
		// Print the wait page
		if ($this->request == null)	{$view->printWaitMessage();}
		// and print the XML of the output
		echo $view->printXML($output);
		// Print the JavaScript
		$script = new JcScript($this->request);
		$script->printJavaScript($view->hasError());
		// Print the welcome message
		$view->showWelcomeMessage($parser);
		// Print the context menu
		if ($this->request == null)	{$view->printContextMenu();}
		// And send the buffer
		ob_end_flush();
		// Get the time (milliseconds)
		$this->end = floor(1000 * microtime());
		// Get the duration of the quey
		$duration = $this->end - $this->begin;
		while ($duration < 0)	{$duration = 1000 + $duration;}
		// Logger
		JcLogger::info(__CLASS__.'.'.__FUNCTION__.'('.$duration.' ms)');
		JfLogger::dump();
		JcLogger::dump();
//		sleep(5);
	}

	/**
	 * Get the latest component name.
	 *
	 * @access	public
	 * @return	String	the latest component name
	 */
	public function getComponent()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// return $component;
		$httpsession = new JcHttpSession();
		$componentList = $httpsession->getAttribute('component');
		$component = (is_array($componentList)) ? end($componentList) : '';
		$component = ($this->component <> '') ? $this->component : $component;
		// JcLogger::info('$component : '.$component);
		// JcLogger::info('$this->component : '.$this->component);
		// Get the request
		$request = $this->request;
		// TODO - This should be placed in a component class
		$componentmgr = new JcComponentManager($request);
		if ($this->request == null)
		{
			$component = $componentmgr->getPage($component);
			if ($component == '')	$this->isLoggedIn = false;
		}
		else
		{
			$component = $componentmgr->getAjax($component);
			if ($component == 'timeout')	$this->request = $componentmgr->getRequest();
		}
		return $component;
	}

	/**
	 * Redirect to the login page
	 *
	 * @access	private
	 */
	private function redirectToLoginPage()
	{
		$strLoginPage = '<body>';
		$strLoginPage .= '<div id="objectlist"></div>';
		$strLoginPage .= '<div id="sheet_2" class="sheet" style="width: 600px;"><jm:panel component="login" id="login"/></div>';
		$strLoginPage .= '<script type="text/javascript">displayLogin();</script>';
		$strLoginPage .= '</body>';
		return $strLoginPage;
	}
}
?>