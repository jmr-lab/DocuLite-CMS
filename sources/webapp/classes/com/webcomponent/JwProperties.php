<?php
/**
 * The Properties webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwProperties extends JwComponent
{
	/**
	 * Component (properties) to display
	 *
	 * @access	private
	 * @var		String
	 */
	private $component = 'sysobjproperties';

	/**
	 * Is the object editable (modifiable)
	 *
	 * @access	private
	 * @var		String
	 */
	protected $bReadOnly = 'true';

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
	 * Get the component (default is sysobject properties)
	 *
	 * @access	public
	 * @return	String	the attribute value
	 */
	public function getComponent()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return $this->component;
	}

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
		// Build the object extension list ('09' => 'document'...)
		// @TODO - Update this list later
		$arrComponent = array(	'03' => 'type',		'11' => 'user',		'1b' => 'mail',		'12' => 'group',	'27' => 'format',	'45' => 'acl',
								'4d' => 'workflow',	'08' => 'sysobj',	'09' => 'sysobj',	'0b' => 'sysobj',	'0c' => 'sysobj',	'10' => 'sysobj',
								'17' => 'perobj',	'19' => 'sysobj',	'3a' => 'perobj',	'3c' => 'sysobj',	'3d' => 'sysobj',	'3e' => 'perobj',
								'46' => 'sysobj',	'4b' => 'sysobj',	'4c' => 'sysobj',	'58' => 'sysobj',	'67' => 'perobj');
		// Get the object info
		$request = new JcHttpServletRequest();
		$this->objectId = $request->getParameter('objectId');
		$extension = substr($this->objectId, 0, 2);
		// Get the corresponding component
		if (isset($arrComponent[$extension]))	{$this->component = $arrComponent[$extension].'properties';}
		else									{$this->component = 'perobjproperties';}
		echo '<jm:panel component="'.$this->getComponent().'" id="'.$this->getComponent().'"/>';
	}

	/**
	 * Is the object editable (modifiable)
	 *
	 * @access	public
	 * @return	boolean	whether the object editable
	 */
	public function isReadOnly()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return $this->bReadOnly;
	}
}
?>