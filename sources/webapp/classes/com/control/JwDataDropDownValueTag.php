<?php
/**
 * An Estancia JwDataDropDownValueTag tag.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDataDropDownValueTag extends JwTag
{
	/**
	 * Render the custom tag :
	 * <jm:datadropdownvalue nlsid="User State" name="user_state" value="Active" width="450px" readonly="true"/>
	 *
	 * If the attribute is read-only :
	 * 	<span>User State :&nbsp;</span>
	 * 	<span class="label">Active</span>
	 *
	 * Otherwise :
	 * 	<span>User State :&nbsp;</span>
	 * 	<span>
	 * 		<select name="user_state" id="user_state">
	 * 			<option value="jm_group">Group</option>
	 * 			<option value="jm_user">User</option>
	 * 		</select>
	 * 	</span>
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
		// Set the default value
		$attrValue = '';
		// Get localized messages
		if (isset($tag['properties']))	{$nlsProperties = $tag['properties'];}
		// Init the arrays
		$user_state = array(0 => JcUtils::getString($nlsProperties, 'USER_ACTIVE'),
							1 => JcUtils::getString($nlsProperties, 'USER_INACTIVE'),
							2 => JcUtils::getString($nlsProperties, 'USER_LOCKED'),
							3 => JcUtils::getString($nlsProperties, 'USER_LOCKED_INACTIVE'));
		$state = array(0 => '', 1 => '', 2 => '', 3 => '');
		$client_capability = array(	0 => JcUtils::getString($nlsProperties, 'USER_CONSUMER'),
									2 => JcUtils::getString($nlsProperties, 'USER_CONTRIBUTOR'),
									4 => JcUtils::getString($nlsProperties, 'USER_COORDINATOR'),
									8 => JcUtils::getString($nlsProperties, 'USER_SYSADMIN'));
		$capability = array(0 => '', 2 => '', 4 => '', 8 => '');
		// Render the tag
		echo '<span>'.$attributes['nlsid'].' :&nbsp;</span>';
		if (isset($attributes['readonly']) && $attributes['readonly'] == 'false' && $attributes['value'] <> '')
		{
			echo '<select';
			if (isset($attributes['width']))	{echo ' style="width: '.$attributes['width'].';"';}
			if (isset($attributes['id']))	{echo ' id="'.$attributes['id'].'"';}
			if (isset($attributes['name']))	{echo ' name="'.$attributes['name'].'"';}
			echo '>';
			if ($attributes['name'] == 'user_state')
			{
				$state[$attributes['value']] = 'selected';
				for ($i = 0; $i < 4; $i++)	{echo '<option value="'.$i.'" '.$state[$i].'>'.$user_state[$i];}
			}
			else if ($attributes['name'] == 'client_capability')
			{
				$capability[$attributes['value']] = 'selected';
				foreach ($capability as $key => $value)	{echo '<option value="'.$key.'" '.$capability[$key].'>'.$client_capability[$key];}
			}
			echo '</select>';
		}
		else
		{
			if ($attributes['name'] == 'user_state')	{$attrValue = (isset($user_state[$attributes['value']]) ? $user_state[$attributes['value']] : '');}
			else if ($attributes['name'] == 'client_capability')	{$attrValue = (isset($client_capability[$attributes['value']]) ? $client_capability[$attributes['value']] : '');}
			echo '<span ';
			if (isset($attributes['width']))	{echo 'style="width: '.$attributes['width'].';" ';}
			echo 'class="label">'.$attrValue.'</span>';
		}
		// Return the tag
		$output = ob_get_clean();
		return $output;
	}
}
?>