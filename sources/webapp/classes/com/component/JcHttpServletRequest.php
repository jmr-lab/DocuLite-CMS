<?php
/**
 * PHP implementation of the HttpServletRequest class.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcHttpServletRequest
{
	/**
	 * Array of parameters sent by the request (either GET or POST)
	 *
	 * @access	private
	 * @var		array
	 */
	private $parameters = null;

	/**
	 * HTTP Session
	 *
	 * @access	private
	 * @var		JcHttpSession
	 */
	private $httpsession = null;

	/**
	 * Constructor
	 *
	 * This function initialize the current request object.
	 *
	 * @access	public
	 */
	public function __construct()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$this->parameters = ((sizeof($_POST) > 0) ? $_POST : $_GET);

		// Set additionnal parameters
		$strArguments = '';
		if (isset($this->parameters['arguments']))	{$strArguments = $this->parameters['arguments'];}
		// $strArguments = 'arg1=val1;arg2=val2';
//		JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(strArguments : '.$strArguments.')');
		$argument_list = explode (';', $strArguments);
		// $argument_list[0]='arg1=val1', $argument_list[1]='arg2=val2', ...
		foreach ($argument_list as $key=>$value)
		{
			$args = explode ('=', $value);
			// $args[0]='arg1', $args[1]='val1', ...
			if (sizeof($args) == 2)	{$this->parameters[$args[0]] = $args[1];}
			// $arguments['arg1']='val1', $arguments['arg2']='val2', ...
		}
		if (isset($this->parameters['arguments']))	{unset($this->parameters['arguments']);}

		// Set the session
		$this->httpsession = new JcHttpSession();
		
		// List all the paramaters
		// JcLogger::info('Parameters - Begin');
		// foreach ($this->parameters as $key=>$value)	{JcLogger::info('Parameter['.$key.'] : '.$value);}
		// JcLogger::info('Parameters - End');
//		$this->parameters = array_map('decode', $this->parameters);
		$this->parameters = $this->decode($this->parameters);
	}

	/**
	 * Decode the parameters
	 *
	 * @access	private
	 * @param	String	name	the name of the parameter
	 * @return	String	the value of the parameter
	 */
	private function decode($input)
	{
		if (is_array($input))
		{
			$output = array();
			foreach ($input as $key => $value)	{$output[$key] = $this->decode($value);}
			return $output;
		}
		else	{return utf8_decode($input);}
	}

	/**
	 * Get the value of the parameter.
	 *
	 * If it doesn't exist, null will be returned. If it exists but is empty, it will be a string.
	 *
	 * @access	public
	 * @param	String	name	the name of the parameter
	 * @return	String	the value of the parameter
	 */
	public function getParameter($name)
	{
		$value = null;
		if (isset($this->parameters[$name]))	{$value = $this->parameters[$name];}
		return $value;
	}

	/**
	 * Returns an array of strings containing the names of the parameters contained in this request.
	 *
	 * If the request has no parameters, the method returns an empty array.
	 *
	 * @access	public
	 * @return	String	the names of the parameters contained in this request.
	 */
	public static function getParameterNames()
	{
		$names = array();
		foreach ($this->parameters as $name=>$value)	{$names[] = $name;}
		return $names;
	}

	/**
	 * Returns the current session associated with this request, or if the request does not have a session, creates one.
	 *
	 * @access	public
	 * @return	JcHttpSession	the session associated with this request
	 */
	public function getSession()
	{
		return $this->httpsession;
	}

	/**
	 * Stores an attribute in this request.
	 *
	 * @access	public
	 * @param	String	name	the name of the parameter
	 * @param	String	value	the value of the parameter
	 */
	public function setAttribute($name, $value)
	{
		$this->parameters[$name] = $value;
	}
}
?>