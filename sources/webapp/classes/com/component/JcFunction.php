<?php
/**
 * The Function class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcFunction
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
	 *
	 * @var		JcHttpServletRequest
	 */
	public function __construct($request)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Set the request
		$this->request = $request;
	}

	/**
	 * Run a class method
	 *
	 * @access	private
	 * @return	String	the result message
	 */
	public function execute()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$request = $this->request;
		// Run the function (optional)
		$function = (($request == null) ? null : $request->getParameter('function_name'));
		$source = (($request == null) ? '' : $request->getParameter('source'));
		// JcLogger::info('function : '.$function);
		if (!is_null($function) && $function <> 'null')
		{
			try
			{
				$message = $this->run($source, $function);
				if ($message === true)	{JcLogger::debug('message : true');}
				else if ($message === false)	{JcLogger::debug('message : false');}
				else if ($message <> '')	{$message = '<div class="successmessage">'.$message.'</div>';}
				else	{$request->setAttribute('reload', true);echo '<script>objRemoveAllObjects();</script>';}
			}
			catch (Exception $exception)
			{
				$request->setAttribute('event', 'null');
				$request->setAttribute('source', $request->getParameter('target'));
				$message = '<div class="errormessage">'.$exception->getMessage().'</div>';
			}
			if ($message <> '')	{echo '<div id="ajaxmessage">'.$message.'</div>';}
		}
	}

	/**
	 * Run a class method
	 *
	 * @access	private
	 * @param	String	component	the component name
	 * @param	String	method		the method name
	 * @return	String	the result message
	 */
	private function run($component, $method)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Read the ini file
		$properties = JcUtils::getProperties(JcUtils::getIniFile('components'));
		$className = JcUtils::getPropertyValue($properties, strtoupper($component), 'class');
		$classFile = _SERVER_ROOT_.'/webapp/classes/com/webcomponent/'.$className.'.php';
		// If the files doesn't exist then throw an exception
		require_once $classFile;
		$class = new $className();
		$result = $class->$method();
		return $result;
	}
}
?>