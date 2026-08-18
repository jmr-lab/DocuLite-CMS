<?php
/**
 * An Estancia button Link.
 *
 * @package		com.control
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwButtonTag extends JwTag
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
			// If button is hidden
			if (isset($attributes['hidden']) && $attributes['hidden'] == 'true')	{return '';}
			// Open the tag
			echo '<div class="';
			if (isset($attributes['cssclass']))	{echo ' '.$attributes['cssclass'];}
			echo '"';
			if (isset($attributes['enabled']))	{echo ' enabled="'.$attributes['enabled'].'"';}
			if (isset($attributes['focus']))	{echo ' focus="'.$attributes['focus'].'"';}
			if (isset($attributes['id']))	{echo ' id="'.$attributes['id'].'"';}
			if (isset($attributes['name']))	{echo ' name="'.$attributes['name'].'"';}
			if (isset($attributes['style']))	{echo ' style="'.$attributes['style'].'"';}
			if (isset($attributes['visible']))	{echo ' visible="'.$attributes['visible'].'"';}
			// Close the opening tag
			echo '>';
			// Get the colour if any
			$color = (isset($attributes['color'])) ? ' '.$attributes['color'] : '';
			echo '<a class="button'.$color.'" onclick="this.blur();" href="';
			if (isset($attributes['action']))	{echo 'javascript:'.$attributes['action'];}
			else								{echo '#';}
			echo '">';
			// Add the image
			$padding = '';
			if (isset($attributes['src']))
			{
				echo '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/'.$attributes['src'].'">';
				$padding = ' style="padding-left: 32px;"';
			}
			// And the label
			$label = '';
			if (isset($attributes['nlsid']))	{$label = $attributes['nlsid'];}
			elseif (isset($attributes['label']))	{$label = $attributes['label'];}
			if ($label <> '')	{echo '<span'.$padding.'>'.JcUtils::getString($nlsProperties, $label).'</span>';}
			echo '</a>';
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