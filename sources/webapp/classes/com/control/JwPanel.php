<?php
/**
 * An Estancia Panel.
 *
 * @package		com.control
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwPanel extends JwTag
{
	/**
	 * Returns the class name of a component :
	 * $this->getClass('doclist') = 'JwDocList'
	 *
	 * @access		private
	 * @param		String			the component name
	 * @return		String			the class name
	 * @throws		JcException		if a server error occurs
	 */
	private static function getClass($properties, $component)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// $component = 'doclist';
			// strtoupper($component) = 'DOCLIST';
			// Get the class name
			$className = JcUtils::getPropertyValue($properties, strtoupper($component), 'class');
			if ($className == '')	{throw new JcException('COMPONENT_NOT_FOUND');}
			return $className;
		}
		catch (JcException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Returns the page name of a component :
	 * $this->getPage('doclist') = 'jpdoclist'
	 *
	 * @access		private
	 * @param		String			the component name
	 * @return		String			the page name
	 * @throws		JcException		if a server error occurs
	 */
	private static function getPage($properties, $component)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// $component = 'doclist';
			// strtoupper($component) = 'DOCLIST';
			// Get the class name
			$pageName = JcUtils::getPropertyValue($properties, strtoupper($component), 'page');
//			if ($pageName == '')	{throw new JcException('COMPONENT_NOT_FOUND');}
			return $pageName;
		}
		catch (JcException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Render the control.
	 * $tag = array (
	 * 				'begin' => '7',
	 * 				'end' => '23',
	 * 				'tagname' => 'button',
	 * 				'attributes' => array('value' => 'BtnContent', 'name' => 'myButton'),
	 * 				'content' => 'This is a button'
	 * 				 );
	 *
	 * @access	public
	 * @param	array	the array to modify
	 * @return	String	the string to display
	 */
	public static function render($tag)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Starts the output buffer
			ob_start();
			// Initialize some variables
			$component = '';
			$attributes = $tag['attributes'];
			// Open the tag
			echo '<div';
			if (isset($attributes['cssclass']))	{echo ' class="'.$attributes['cssclass'].'"';}
			if (isset($attributes['enabled']))	{echo ' enabled="'.$attributes['enabled'].'"';}
			if (isset($attributes['focus']))	{echo ' focus="'.$attributes['focus'].'"';}
			if (isset($attributes['id']))	{echo ' id="'.$attributes['id'].'"';}
			if (isset($attributes['name']))	{echo ' name="'.$attributes['name'].'"';}
			if (isset($attributes['style']))	{echo ' style="'.$attributes['style'].'"';}
			if (isset($attributes['visible']))	{echo ' visible="'.$attributes['visible'].'"';}
			// Get the object if defined
			if (isset($attributes['object']))	{$perobj = unserialize(html_entity_decode($attributes['object']));}
			// Close the opening tag
			echo '>';
			// Get the content
			if (isset($attributes['component']))
			{
				$component = $attributes['component'];
				// Read the ini file
				$properties = JcUtils::getProperties(JcUtils::getIniFile('components'));
				$className = self::getClass($properties, $component);
				$pageName = self::getPage($properties, $component);
				$classFile = _SERVER_ROOT_.'/webapp/classes/com/webcomponent/'.$className.'.php';
				$pageFile = _SERVER_ROOT_.'/webapp/library/'.$pageName.'.php';
				// If the files doesn't exist then throw an exception
//				if (!file_exists($classFile) || !file_exists($pageFile))	{throw new JcException('COMPONENT_NOT_FOUND');}
				if ($className <> '' && !file_exists($classFile))	{throw new JcException('COMPONENT_NOT_FOUND');}
				if ($pageName <> '' && !file_exists($pageFile))	{throw new JcException('COMPONENT_NOT_FOUND');}
				require_once $classFile;
				$class = new $className();
				if (isset($perobj))	{$class->setObject($perobj);}
				if (file_exists($pageFile))	{include $pageFile;}
			}
			// Eventually add a content inside
			// if ($content <> '')	{echo  $content;}
			// Close the tag itself
			echo '</div>';
			// Return the tag
			$output = ob_get_clean();
			// $output = ob_get_contents();
			// ob_end_flush();
			return $output;
		}
		catch (JcException $exception)
		{
			// Throw an exception
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			// Clean the output buffer
			ob_clean();
			// @todo - Manage the error
			throw $exception;
		}
	}
}
?>