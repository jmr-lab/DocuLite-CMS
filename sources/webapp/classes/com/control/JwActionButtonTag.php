<?php
/**
 * An Estancia action button Link.
 *
 * @package		com.control
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwActionButtonTag extends JwButtonTag
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
			// Get the class
			$cssclass = 'actionbutton';
			if (isset($attributes['cssclass']))	{$cssclass = $attributes['cssclass'];}
			// Get the tag Id
			$n = rand(10e16, 10e20);
			$tagId = base_convert($n, 10, 36);
			//Open the tag
			echo '<p style="display: inline;" id="'.$tagId.'">';
			echo '<a class="'.$cssclass.'"';
			if (isset($attributes['delete']))	{echo ' onclick="javascript:$(&quot;#'.$tagId.'&quot;).remove();"';}
			else if (isset($attributes['action']))	{echo ' onclick="javascript:'.$attributes['action'].'"';}
			echo '>';
			echo '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/'.$attributes['src'].'">';
			// And the label
			$label = '';
			if (isset($attributes['nlsid']))	{$label = $attributes['nlsid'];}
			elseif (isset($attributes['label']))	{$label = $attributes['label'];}
			if ($label <> '')	{echo '<span>'.JcUtils::getString($nlsProperties, $label).'</span>';}
			// Add the close button
			if (isset($attributes['delete']))	{echo '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/delete_16.png" style="border-left-width: 4px; border-right-width: 0px;">';}
			echo '</a>';
			// Add a hidden field
			if (isset($attributes['id']))	{echo '<input type="hidden" name="'.$tag['component'].'_'.$attributes['target'].'['.$attributes['id'].']" value="'.$label.'">';}
			// if (isset($attributes['id']))	{echo '<input type="hidden" name="'.$attributes['target'].'['.$attributes['id'].']" value="'.$label.'">';}
			// Close the tag itself
			echo '</p>';
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