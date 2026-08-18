<?php
/**
 * The JcActions class.
 * Usage :
 *
 * $actions = new JcActions($session, $folderObj, $objects);
 * $actions->showActions();
 *
 * This class will display all available actions.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcActions
{
	/**
	* NLS Properties
	*
	* @access	private
	* @var		array
	*/
	private $nlsProperties;

	/**
	* Container
	*
	* @access	private
	* @var		JfPersistentObject
	*/
	private $folderObj;

	/**
	* Objects
	*
	* @access	private
	* @var		JfCollection
	*/
	private $objects;

	/**
	* Session
	*
	* @access	private
	* @var		JfSession
	*/
	private $session;

	/**
	 * Constructor
	 *
	 */
	public function __construct($session, $folderObj, $objects)
	{
		// Set the NLS Properties
		$httpsession = new JcHttpSession();
		$lang = $httpsession->getAttribute('lang');
		$this->nlsProperties = JcUtils::getNLSProperties($lang);
		// Set the session...
		$this->session = $session;
		// ...the container object
		$this->folderObj = $folderObj;
		// and all the objects
		$this->objects = $objects;
	}

	/**
	 * Get a local form of a string
	 *
	 * @param	String	The message to display
	 * @return	String	The localized message
	 */
	public function getString($message)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return JcUtils::getString($this->nlsProperties, strtoupper($message));
	}

	/**
	 * Show actions
	 *
	 * @access	private
	 */
	public function showActions()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		echo '<script>';
		// First empty the actions array
		echo "	actions.length = 0;";
		// Get the actions from the ini file
		$actions = JcUtils::getProperties(JcUtils::getIniFile('actions'));
		// Get the container object
		$folder = $this->folderObj;
		// Process the actions
		foreach ($actions as $key=>$value)
		{
			$listIds = '';
			$flag = false;
			if (sizeof($value) == 0)
			{
				echo "	actionObj = new Object();";
				echo "	actionObj.isMulti = 5;";
				echo "	actionObj.name = '".$this->getString($key)."';";
				echo "	actionObj.target = '';";
				echo "	actionObj.type = '';";
				echo "	actionObj.icon = '';";
				echo "	actionObj.listIds = '';";
				echo "	actions.push(actionObj);";
				continue;
			}
			$class = new $value['class']();
			if ($value['isMulti'] == 0 && isset($folder))
			{
				if ($class->queryExecute($this->session, $folder))	{$flag = true;}
			}
			else if ($value['isMulti'] > 0)
			{
				// For each object
				if (sizeof($this->objects) > 0)
				{
					foreach ($this->objects as $object)	{	if ($class->queryExecute($this->session, $object))	{$listIds .= $object->getValue('r_object_id').',';}	}
				}
				if ($listIds <> '')	{$listIds = substr($listIds, 0, -1);$flag = true;}
			}
			if ($flag)
			{
				echo "	actionObj = new Object();";
				echo "	actionObj.isMulti = ".$value['isMulti'].";";
				echo "	actionObj.name = '".$this->getString($key)."';";
				echo "	actionObj.target = '".$value['target']."';";
				echo "	actionObj.type = '".$value['type']."';";
				echo "	actionObj.icon = '".$value['icon']."';";
				echo "	actionObj.listIds = '".$listIds."';";
				echo "	actions.push(actionObj);";
			}
		}
		echo 'messages["noaction"] = "'.$this->getString('NO_ACTION_ALLOWED').'";';
		echo '</script>';
	}
}
?>