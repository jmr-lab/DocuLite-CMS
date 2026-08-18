<?php
/**
 * An Estancia Action Link.
 *
 * @package		com.control
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwActionLink extends JwTag
{
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
			// Get localized messages
			if (isset($tag['properties']))	{$nlsProperties = $tag['properties'];}
			// Open the tag
			echo '<a href="';
			if (isset($attributes['action']))	{echo 'javascript:'.$attributes['action'];}
			else								{echo '#';}
			echo '">';
			echo '<div';
			if (isset($attributes['cssclass']))	{echo ' class="'.$attributes['cssclass'].'"';}
			if (isset($attributes['enabled']))	{echo ' enabled="'.$attributes['enabled'].'"';}
			if (isset($attributes['focus']))	{echo ' focus="'.$attributes['focus'].'"';}
			if (isset($attributes['id']))	{echo ' id="'.$attributes['id'].'"';}
			if (isset($attributes['name']))	{echo ' name="'.$attributes['name'].'"';}
			if (isset($attributes['style']))	{echo ' style="'.$attributes['style'].'"';}
			if (isset($attributes['visible']))	{echo ' visible="'.$attributes['visible'].'"';}
			// Close the opening tag
			echo '>';
			// Add the image
			if (isset($attributes['src']))
			{
//				echo '<img src="'.$attributes['src'].'">';
				echo '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/'.$attributes['src'].'">';
			}
			// And the label
			if (isset($attributes['nlsid']))	{echo '<span>'.JcUtils::getString($nlsProperties, $attributes['nlsid']).'</span>';}
			// Close the tag itself
			echo '</div>';
			echo '</a>';
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