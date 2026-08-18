<?php
/**
 * An Estancia basic tag.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwTag
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
	 * @access public
	 * @param array - the array to modify
	 * @return String - the string to display
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
		echo '<div';
		if (isset($attributes['cssclass']))	{echo  ' class="'.$attributes['cssclass'].'"';}
		if (isset($attributes['enabled']))	{echo  ' enabled="'.$attributes['enabled'].'"';}
		if (isset($attributes['focus']))	{echo  ' focus="'.$attributes['focus'].'"';}
		if (isset($attributes['id']))	{echo  ' id="'.$attributes['id'].'"';}
		if (isset($attributes['label']))	{echo  ' label="'.$attributes['label'].'"';}
		if (isset($attributes['name']))	{echo  ' name="'.$attributes['name'].'"';}
		if (isset($attributes['style']))	{echo  ' style="'.$attributes['style'].'"';}
		if (isset($attributes['visible']))	{echo  ' visible="'.$attributes['visible'].'"';}
		// Get the content
		if (isset($tag['content']))	{$content = $tag['content'];}
		// Close the opening tag
		echo  '>';
		// Eventually add a content inside
		if ($content <> '')	{echo  $content;}
		// Close the tag itself
		echo  '</div>';
		// Return the tag
		$output = ob_get_clean();
		// $output = ob_get_contents();
		// ob_end_flush();
		return $output;
	}
}
?>