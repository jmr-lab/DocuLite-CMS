<?php
/**
 * View webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwView extends JwComponent
{
	/**
	 * Object ID
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $objectId;

	/**
	 * Object
	 *
	 * @access	protected
	 * @var		JfPersistentObject
	 */
	protected $perObj;

	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function __construct()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Run the parent constructor first
		parent::__construct();
		// Get the object info
		$request = new JcHttpServletRequest();
		// Get the object
		$session = $this->session;
		$this->perObj = $session->getObject(new JfId($request->getParameter('objectId')));
		// Get the corresponding component
		if ($this->isViewable())				{$component = 'display';}
		else									{$component = 'download';}
		// Redirect to the component
		echo '<jm:panel component="'.$component.'" id="'.$component.'" object="'.htmlentities(serialize($this->perObj)).'"/>';
	}

	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 * @return	boolean whether the file can be displayed in the browser or has to be downloaded
	 */
	private function isViewable()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Default : the file is not viewable
		$flag = false;
		// Get the file object as a sysobject
		$sysObj = JfUtils::cast($this->perObj, 'JfSysObject');
		// List of viewable types
		$viewableTypes = array('php', 'crtext', 'jpg', 'jpeg', 'sql');
		if (in_array($sysObj->getValue('a_content_type'), $viewableTypes))	{$flag = true;}
		if ($sysObj->getValue('r_content_size') > 1024*1024)	{$flag = false;}
		return $flag;
	}
}
?>