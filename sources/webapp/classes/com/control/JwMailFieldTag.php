<?php
/**
 * An Estancia JwMailFieldTag tag.
 *
 * @package		com.control
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwMailFieldTag extends JwTag
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
			// Get the content
			if (isset($tag['content']))	{$content = $tag['content'];}
			// Get the tag Id
			$n = rand(10e16, 10e20);
			$tagId = base_convert($n, 10, 36);
			// Open the tag
			echo '<div class="mailline" id="'.$tagId.'" name="'.$attributes['nlsid'].'">';
			echo '<span class="mailline">'.JcUtils::getString($nlsProperties, $attributes['nlsid']).' :&nbsp;</span>';
			echo '<div class="maillinediv">';
			// Eventually add a content inside
			if ($content <> '')	{echo $content;}
			echo '</div>';
			if (isset($attributes['action']))
			{
				echo '<div class="maillinebutton">';
				echo '<a class="actionbutton" onclick="javascript:'.$attributes['action'].'" style="margin-top: 4px; margin-left: 4px;">';
				echo '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/add_16.png" style="border-left: 4px; border-right: 4px;">';
				echo '</a>';
				echo '</div>';
			}
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
			return '<div class="error">'.$exception->getMessage().'</div>';
		}
	}
}
?>