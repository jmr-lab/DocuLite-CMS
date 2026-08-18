<?php
/**
 * An Estancia JwDataTextValueTag tag.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDataTextValueTag extends JwTag
{
	/**
	 * Render the custom tag :
	 * <jm:datatextvalue name="Name" value="Test Document" width="450px" readonly="true"/>
	 *
	 * If the attribute is read-only :
	 * 	<span>Name :&nbsp;</span>
	 * 	<span style="width: 450px;" class="label">Test Document</span>
	 *
	 * Otherwise :
	 * 	<span>Name :&nbsp;</span>
	 * 	<span style="width: 450px;" class="label">Test Document</span>
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
		// Initialize the attributes
		$attributes = $tag['attributes'];
		if (isset($attributes['nlsid']))	{echo '<span>'.$attributes['nlsid'].' :&nbsp;</span>';}
		if (isset($attributes['readonly']) && $attributes['readonly'] == 'false')
		{
			echo '<input type="text" ';
			if (isset($attributes['id']))	{echo 'id="'.$attributes['id'].'" ';}
			if (isset($attributes['name']))	{echo 'name="'.$attributes['name'].'" ';}
			if (isset($attributes['width']))	{echo 'style="width: '.$attributes['width'].';" ';}
			echo 'class="label" value="'.$attributes['value'].'">';
		}
		else
		{
			echo '<span ';
			if (isset($attributes['width']))	{echo 'style="width: '.$attributes['width'].';" ';}
			echo 'class="label">'.$attributes['value'].'</span>';
		}
		// Return the tag
		$output = ob_get_clean();
		return $output;
	}
}
?>