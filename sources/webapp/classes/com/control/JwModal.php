<?php
/**
 * An Estancia Modal window.
 *
 * @package		com.control
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwModal extends JwTag
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
			$title = '';
			$icon = '';
			$rticon = '';
			$attributes = $tag['attributes'];
			// Open the tag
			echo '<div class="modal';
			if (isset($attributes['cssclass']))	{echo ' '.$attributes['cssclass'];}
			echo '"';
			if (isset($attributes['enabled']))	{echo ' enabled="'.$attributes['enabled'].'"';}
			if (isset($attributes['focus']))	{echo ' focus="'.$attributes['focus'].'"';}
			if (isset($attributes['id']))	{echo ' id="'.$attributes['id'].'"';}
			if (isset($attributes['name']))	{echo ' name="'.$attributes['name'].'"';}
			if (isset($attributes['style']))	{echo ' style="'.$attributes['style'].'"';}
			else								{echo ' style="width: 800px;"';}
			if (isset($attributes['visible']))	{echo ' visible="'.$attributes['visible'].'"';}
			// Get the content
			if (isset($tag['content']))	{$content = $tag['content'];}
			// Get the header
			if (isset($attributes['title']))	{$title = $attributes['title'];}
			if (isset($attributes['icon']))	{$icon = $attributes['icon'];}
			// Get the right icon
			if (isset($attributes['rticon']))	{$rticon = $attributes['rticon'];}
			// Close the opening tag
			echo '>';
			// Displays the header
			echo '<div class="modal-header drag" style="background-color: #3A5A86;">';
			echo '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/'.$icon.'" class="imgheader">';
			echo '<span class="txtheader">'.$title.'</span>';
			echo '<!--img class="drag" src="'._APP_ROOT_.'/webapp/themes/default/images/background/toolbar.png" width="100%" height="24px"-->';
			if ($rticon <> '')	{echo '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/'.$rticon.'" class="righticon">';}
			echo '</div>';
			// Eventually add a content inside
			if ($content <> '')	{echo '<div class="modal-content">'.$content.'</div>';}
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