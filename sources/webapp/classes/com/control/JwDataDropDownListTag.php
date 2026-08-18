<?php
/**
 * An Estancia DataDropDownList tag.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDataDropDownListTag extends JwTag
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
		$strQuery = '';
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
		// Query
		if (isset($attributes['query']))	{$strQuery = $attributes['query'];}
		// Close the opening tag
		echo '>';
		// Eventually add a content inside
		if ($strQuery <> '')
		{
			// The query string must be parsed before we can get the options :
			// $query = 'select r_object_id, user_name from jm_user where user_name like "a%"';
			// $names = array(0 => 'r_object_id', 1 => 'user_name');
			$names = explode(',', substr($strQuery, strlen('select '), stripos($strQuery, ' from ') - strlen(' from ') - 1));
			// Query the attributes table
			// try
			// {
				$query = new JfQuery();
				$query->setSQL($strQuery);
				$sessionmanager = new JfSessionManager();
				$session = $sessionmanager->getSession('www_jmroy');
				$options = $query->execute($session);
				while ($options->next())
				{
					echo '<option value="'.$options->getValue(trim($names[0])).'">'.$options->getValue(trim($names[1])).'</option>';
				}
			// }
			// catch (JfException $exception)	{echo 'error : '.$exception;}
		}
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