<?php
/**
 * The Component Manager class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcComponentManager
{
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
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Set the request
		$this->request = $request;
	}

	/**
	 * Get the latest component name for an Ajax call.
	 *
	 * @access	public
	 * @return	String	the component name
	 */
	public function getAjax($component)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the request
		$request = $this->request;
		$event = (($request == null) ? '' : $request->getParameter('event'));
		if (is_null($event) || $event == 'null')	{$component = '';}
		else
		{
			try
			{
				// Authentication to the repository
				$sessionmanager = new JfSessionManager();
				$session = $sessionmanager->getSession('www_jmroy');
				// Init the component returned (default is empty)
				$reload = 'true';
				if ($event == 'return')	{$reload = $request->getParameter('reload');}
				// If unnecessary don't display the component
				if ($reload <> 'true' || $event == '')	{$component = '';}
			}
			catch (JfException $exception)
			{
				JcLogger::info('Exception : '.$exception->getMessage());
				// We couldn't connect to the repository : assume a timeout occured
				$request->setAttribute('event', 'nest');
				$request->setAttribute('component', 'timeout');
				$this->request = $request;
				$this->setComponent($request);
				$component = 'timeout';
			}
		}
		return $component;
	}

	/**
	 * Get the latest component name for an Ajax call.
	 *
	 * @access	public
	 * @return	String	the component name
	 */
	public function getPage($component)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Authentication to the repository using session credentials
			$sessionmanager = new JfSessionManager();
			$session = $sessionmanager->getSession('www_jmroy');
		}
		catch (JfException $exception)
		{
			try
			{
				// Get the Http Session
				$httpsession = new JcHttpSession();
				// Authentication to the repository as guest
				$user = array('username' => 'guest', 'password' => '');
				$repository = array('repository' => 'www_jmroy');
				// Authentication to the repository using cookie
				$cookie = new JcCookie();
				if (!is_null($cookie->getCookie('user')))	{$user = $cookie->getCookie('user');}
				// No cookie set, try to log in as user guest
				else	{$httpsession->setAttribute('welcome', true);}
				// Set the identity array
				$identity = array_merge($repository, $user);
				// @todo - understand how sessionmanager could not be set
				if (!isset($sessionmanager))	{throw new JfException('USER_NOT_LOGGED_IN');}
				$sessionmanager->setIdentity('www_jmroy', $identity);
				$sessionmanager->authenticate('www_jmroy');
				if (isset($user['home']))	{$component = $user['home'];}
				// Init the language
				if (isset($user['lang']))	{$httpsession->setAttribute('lang', $user['lang']);}
				// Init the results
				if (isset($user['results']))	{$httpsession->setAttribute('results', $user['results']);}
				// Init the view
				if (isset($user['view']))	{$httpsession->setAttribute('view', $user['view']);}
				// Set a favorite folder
				// $favorites = new JcBookMark();
				// $bookmarkObject = new JcBookMarkObject();
				// $bookmarkObject->setObjectId('0b001e24085218d8');
				// $favorites->addObject($bookmarkObject);
				// $favorites->save();
			}
			catch (JfException $exception)
			{
				// We couldn't connect to the repository : assume user is not connected (need to login)
				$component = '';
			}
		}
		return $component;
	}

	/**
	 * Get the request.
	 *
	 * @access	public
	 * @return	JcHttpServletRequest	the request
	 */
	public function getRequest()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return $this->request;
	}
	
	/**
	 * Update the component list.
	 *
	 * @access	public
	 */
	public function setComponent($request)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the session
		$httpsession = new JcHttpSession();
		// Init the params
		$home = (($httpsession->getAttribute('home') == null) ? 'home' : $httpsession->getAttribute('home'));
		$event = (($request == null) ? '' : $request->getParameter('event'));
		$component = (($request == null) ? $home : $request->getParameter('component'));
		$componentList = $httpsession->getAttribute('component');
		$lastComponent = '';

		// Init the component list
		if ($request == null)	{$componentList = (isset($_SESSION['webapp']['component'][0]) ? array('0' => $_SESSION['webapp']['component'][0]) : array('0' => $home));}

		switch ($event)
		{
			case 'jump':	// onComponentJump
				$length = ((sizeof($componentList) == 0) ? 1 : sizeof($componentList));
				$componentList[$length - 1] = $component;
				$lastComponent = $component;
				break;
			case 'nest':	// onComponentNest
				$length = ((sizeof($componentList) == 0) ? 1 : sizeof($componentList));
				$componentList[] = $component;
				$lastComponent = $component;
				break;
			case 'open':	// onComponentOpen
				$length = ((sizeof($componentList) == 0) ? 1 : sizeof($componentList));
//				$componentList[] = $component;
				$lastComponent = $component;
				break;
			case 'return':	// onComponentReturn
				$lastComponent = array_pop($componentList);
				// Get the returned component
				$component = (($request == null) ? null : $request->getParameter('component'));
				// If the component is not null then replace the last component of the list with this one
				if (!is_null($component) && $component <> 'null')	{$componentList[sizeof($componentList) - 1] = $component;}
				$lastComponent = end($componentList);
				break;
			default:
				break;
		}
		// Set the component in the session
		$httpsession->setAttribute('component', $componentList);
		// $message = '';
		// foreach ($componentList as $key => $value)	{$message .= '"'.$key.'" => "'.$value.'", ';}
		// $message = substr($message, 0, -2);
		// JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(components : '.$message.')');
		return $lastComponent;
	}
}
?>