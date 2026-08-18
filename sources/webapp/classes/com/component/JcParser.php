<?php
/**
 * An Estancia tag parser.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcParser
{
	/**
	* Current component
	*
	* @access	private
	* @var		array
	*/
	private $component = 'EMPTY';

	/**
	* Properties array
	*
	* @access	protected
	* @var		array
	*/
	protected $nlsProperties = array();

	/**
	* Existing tags array
	*
	* @access	private
	* @var		array
	*/
	private $tags;

	/**
	* Equal
	*
	* @access	private
	* @var		String
	*/
	private static $EQUAL = '=';

	/**
	* Space
	*
	* @access	private
	* @var		String
	*/
	private static $SPACE = ' ';

	/**
	* Tag open
	*
	* @access	private
	* @var		String
	*/
	private static $TAG_OPEN = '<jm:';

	/**
	* Tag close
	*
	* @access	private
	* @var		String
	*/
	private static $TAG_CLOSE = '>';

	/**
	* Tag end
	*
	* @access	private
	* @var		String
	*/
	private static $TAG_END = '</jm:';

	/**
	* Tag close end
	*
	* @access	private
	* @var		String
	*/
	private static $TAG_CLOSE_END = '/>';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct($component)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Set the component
		if ($component <> '')	$this->component = $component;
		// Get the file name
//		$lang = 'fr';
		$httpsession = new JcHttpSession();
		$lang = $httpsession->getAttribute('lang');
		$this->nlsProperties = JcUtils::getNLSProperties($lang);
		// Scan the control folder to get all existing tag class files
		foreach ( scandir(_SERVER_ROOT_.'/webapp/classes/com/control') as $key => $value )	{$this->tags[$value] = '';}
	}

	/**
	 * Returns the class name of a tag :
	 * $this->getClass('button') = 'JwButtonTag'
	 *
	 * @access	private
	 * @return	String	the class name
	 */
	private function getClass($properties, $tagname)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the class name
		$className = JcUtils::getPropertyValue($properties, 'TAGS', $tagname);
		if ($className == '')	{$className = 'JwTag';}
		return $className;
	}

	/**
	 * Returns the first tag as an array :
	 * <jm:button value="testbutton" name="mybutton">This is the content of the button</jm:button>
	 * $tag = array (
	 * 				'source' => '<jm:button>...</jm:button>',
	 * 				'tagname' => 'button',
	 * 				'attributes' => array('value' => 'testbutton', 'name' => 'mybutton'),
	 * 				'content' => 'This is the content of the button'
	 * 				 );
	 *
	 * @access	private
	 * @return	array	the first tag found, false if no custom tag found
	 */
	private function getTag($source)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Initialize the tag array
			$tag = array();
			$tag['begin'] = strpos($source, self::$TAG_OPEN);
			// The source can be 'This is a text<br><jm:button>...</jm:button><br>This is another text'
			// We need to get only the tag string : '<jm:button>...</jm:button>'
//			JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'($source1 : '.$source.')');
			$source = $this->getTagString($source);
//			JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'($source2 : '.$source.')');
			if ($source == '')	{throw new Exception('NO_TAG_FOUND');}
			$tag['source'] = $source;
			// Initialize the attributes array
			$attributes = array();
			// Get the content of this tag
			// $tagname = 'button';
			// $strAttributes = 'value="testbutton" name="mybutton"';
			$space = strpos($source, self::$SPACE);
			$close = strpos($source, self::$TAG_CLOSE);
			$end = 0;
			if ($space === false)	{$end = $close;}
			else if ($close === false)	{$end = $space;}
			else	{$end = min($space, $close);}
			$tag['tagname'] = substr($source, strlen(self::$TAG_OPEN), $end - strlen(self::$TAG_OPEN));
//			$tag['tagname'] = substr($source, strlen(self::$TAG_OPEN), strpos($source, self::$SPACE) - strlen(self::$TAG_OPEN));
			$strAttribute = substr($source, strpos($source, self::$SPACE) + 1, strpos($source, self::$TAG_CLOSE) - strpos($source, self::$SPACE) - 1);
			// Remove the last character if it is '/'
			if (substr($strAttribute, -1) == '/')	{$strAttribute = trim(substr($strAttribute, 0, -1));}
			// $strAttribute = 'value=" testbutton" name="mybutton"';
			while (strpos($strAttribute, '=" ') > 0)	{$strAttribute = preg_replace('/=" /', '="', $strAttribute);}
			// $pairs = array(0 => 'value="testbutton"', 1 => 'name="mybutton"');
			if (strpos($strAttribute, '" ') > 0)	{$pairs = explode('" ', $strAttribute);}
			else	{$pairs = array(0 => $strAttribute);}
			foreach ($pairs as $pair)
			{
				// $pair = 'value="testbutton"';
				// $attributes = array(0 => 'value', 1 => '"testbutton"');
				$attribute = explode('="', $pair);
				// $attributes = array('value' => 'testbutton', 'name' => 'mybutton');
				// Remove ' or " from the beginning and the end of the string
				// $attributes[1] = '"Ok"';
				// $attributes[1] = 'Ok';
				$quote = array('\'', '"');
				if (in_array(substr($attribute[1], -1), $quote))	{$attribute[1] = substr($attribute[1], 0, -1);}
				if (in_array(substr($attribute[1], 0, 1), $quote))	{$attribute[1] = substr($attribute[1], 1);}
				$attributes[$attribute[0]] = $attribute[1];
			}
			$tag['attributes'] = $attributes;
			// Set the content of the tag
			$tag['content'] = substr($source, strpos($source, self::$TAG_CLOSE) + 1, strrpos($source, '<') - strpos($source, self::$TAG_CLOSE) - 1);
			// Add the localized messages to the tag
			$tag['properties'] = $this->nlsProperties;
			// Add an Id to the tag
			$tag['component'] = $this->component;
			return $tag;
		}
		catch (Exception $exception)
		{
			return false;
		}
	}

	/**
	 * Returns the first tag as a string :
	 * The source can be 'Text 2<jm:button name="BtnContent">Text 3<jm:button value="BtnAnotherContent">Text 4</jm:button></jm:button>Text 5...<jm:button>...'
	 * We need to get only the tag string : '<jm:button name="BtnContent">Text 3<jm:button value="BtnAnotherContent">Text 4</jm:button></jm:button>'
	 *
	 * @access	private
	 * @return	String	the first tag found, false if no custom tag found
	 */
	private function getTagString($source)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// The source can be 'Text 2<jm:button name="BtnContent">Text 3<jm:button value="BtnAnotherContent">Text 4</jm:button></jm:button>Text 5...<jm:button>...'
			// We need to get only the tag string : '<jm:button name="BtnContent">Text 3<jm:button value="BtnAnotherContent">Text 4</jm:button></jm:button>'
			// Initialize the end of the tag
			$length = 0;
			// Position of the first occurence of '<jm:' in the source string
			$pos = strpos($source, self::$TAG_OPEN);
			// If no occurence found
			if ($pos === false)	{throw new Exception('NO_TAG_FOUND');}
			// Assume an occurence was found
			// $source = '<jm:button name="BtnContent">Text <br>3<jm:button value="BtnAnotherContent">Text 4</jm:button></jm:button>Text 5...<jm:button>...';
			$source = substr($source, $pos);
			
			$_offset = 0;
			$_index = 0;
			$_security = 1;
			while (($_index += $this->getTagType(substr($source, $_offset))) > 0)
			{
				if (($_security += 1) > 1000)	{throw new Exception('INFINITE_LOOP');}
				$open = strpos($source, '<jm:', $_offset + 1);
				$close = strpos($source, '</jm:', $_offset + 1);
				if ($open === false)	{$open = 999999;}
				if ($close === false)	{$close = 999999;}
				$_offset = min($open, $close);
			}
			// 'Trim' the source string
			$source = substr($source, 0, strpos($source, '>', $_offset) + 1);
			// Return the new source string
			return $source;
		}
		catch (Exception $exception)
		{
			if ($exception->getMessage() <> 'NO_TAG_FOUND')	{JcLogger::info('ERROR : '.$exception);}
			return '';
		}
	}

	/**
	 * Returns the type of the tag :
	 * $strTag = '<jm:button name="BtnContent">Text 3<jm:button value="BtnAnotherContent">Text 4</jm:button></jm:button>Text 5...<jm:button>...'
	 * If $strTag begins with '<jm:.../>' the tag is an opening-closing tag : $type = 0
	 * if $strTag begins with '<jm:...>' the tag is an opening tag : $type = 1
	 * if $strTag begins with '</jm:' the tag is a closing tag : $type = -1
	 *
	 * @access	private
	 * @return	int	the tag type
	 */
	private function getTagType($strTag)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Initialize the type (default = 0)
		$type = 0;
		try
		{
			// if $strTag begins with '</jm:' the tag is a closing tag : $type = -1
			if (strpos($strTag, '</jm:') === 0)	{$type = -1;}
			elseif (strpos($strTag, '<jm:') == 0)
			{
				if (($close = strpos($strTag, '/>')) === false)	{$close = 999999;}
				if (($end = strpos($strTag, '>')) === false)	{$end = 999999;}
				// If $strTag begins with '<jm:.../>' the tag is an opening-closing tag : $type = 0
				if ($close < $end)	{$type = 0;}
				// if $strTag begins with '<jm:...>' the tag is an opening tag : $type = 1
				else	{$type = 1;}
			}
			else	{throw new JcException('NO_TAG_FOUND');}
			// Return the new strTag string
			return $type;
		}
		catch (Exception $exception)
		{
			return 0;
		}
	}

	/**
	 * Returns the modified string
	 *
	 * @access	public
	 * @return	String	the new parsed string.
	 */
	public function parseTags($source)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// While there is at least one tag do the following :
		// replace the tag using the custom function in the tag class
		// While loops can be dangerous, add a safety net
		$security = 1;
		// Read the ini file
		$tags = JcUtils::getProperties(JcUtils::getIniFile('tags'));
		while ($tag = $this->getTag($source))
		{
			if (($security += 1) > 100)	{return $source;}
			// $className = 'JwButtonTag';
			$className = $this->getClass($tags, $tag['tagname']);
			// Check the existence of the tag
//			if (!file_exists(_SERVER_ROOT_.'/webapp/classes/com/control/'.$className.'.php'))	{$className = 'JwTag';}
			if (!isset($this->tags[$className.'.php']))	{$className = 'JwTag';}
			$classFile = _SERVER_ROOT_.'/webapp/classes/com/control/'.$className.'.php';
			// Include the associated tag class
			require_once $classFile;
//			$class = new $className();
			// Replace '<jm:button value="BtnAnotherContent">This is another text</jm:button>'
			// with JwButtonTag->render($tag) : '<div id="BtnAnotherContent">This is another text</div>'
//			$source = str_replace($tag['source'], call_user_func($className.'::render', $tag), $source);
			$source = str_replace($tag['source'], call_user_func_array(array($className, 'render'), array($tag)), $source);
			
		}
		return $source;
	}
}
?>