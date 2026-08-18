<?php
/**
 * An Estancia DropDownList tag.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDropDownListTag extends JwTag
{
	/**
	 * Render the webcomponent.
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
		// Starts the output buffer
		ob_start();
		// Initialize some variables
		$content = '';
		$attributes = $tag['attributes'];
		// Open the tag
		echo '<select';
		if (isset($attributes['cssclass']))	{echo ' class="'.$attributes['cssclass'].'"';}
		if (isset($attributes['enabled']))	{echo ' enabled="'.$attributes['enabled'].'"';}
		if (isset($attributes['focus']))	{echo ' focus="'.$attributes['focus'].'"';}
		if (isset($attributes['id']))	{echo ' id="'.$attributes['id'].'"';}
		if (isset($attributes['name']))	{echo ' name="'.$attributes['name'].'"';}
		if (isset($attributes['style']))	{echo ' style="'.$attributes['style'].'"';}
		if (isset($attributes['visible']))	{echo ' visible="'.$attributes['visible'].'"';}
		// Get the content
		if (isset($tag['content']))	{$content = $tag['content'];}
		// Close the opening tag
		echo '>';
		// Eventually add a content inside
		if ($content <> '')	{echo $content;}
		// Close the tag itself
		echo '</select>';
		// Return the tag
		$output = ob_get_clean();
//		$output = ob_get_contents();
//		ob_end_flush();
		return $output;
	}
}
?>