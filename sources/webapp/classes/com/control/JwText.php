<?php
/**
 * An Estancia Text input.
 *
 * @package		com.control
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwText extends JwTag
{
	/**
	 * Returns the string value of the nls Id
	 *
	 * @access		private
	 * @param		String			the Nls Id
	 * @return		String			the String value
	 */
	private static function getString($nlsId)
	{
		// Logger
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return ucfirst(strtolower(str_replace('_', ' ', $nlsId)));
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
			echo '<input type="text"';
			if (isset($attributes['cssclass']))	{echo ' class="'.$attributes['cssclass'].'"';}
			if (isset($attributes['enabled']))	{echo ' enabled="'.$attributes['enabled'].'"';}
			if (isset($attributes['focus']))	{echo ' focus="'.$attributes['focus'].'"';}
			if (isset($attributes['id']))	{echo ' id="'.$attributes['id'].'"';}
			if (isset($attributes['name']))	{echo ' name="'.$attributes['name'].'"';}
			if (isset($attributes['style']))	{echo ' style="'.$attributes['style'].'"';}
			if (isset($attributes['visible']))	{echo ' visible="'.$attributes['visible'].'"';}
			// And the label
			if (isset($attributes['nlsid']))	{echo ' value="'.self::getString($attributes['nlsid']).'"';}
			if (isset($attributes['onkeypress']))	{echo ' onkeypress="'.$attributes['onkeypress'].'"';}
			// Close the tag itself
			echo '>';
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
			return '<div class="error">'.$exception->getMessage().'</div>';
		}
	}
}
?>