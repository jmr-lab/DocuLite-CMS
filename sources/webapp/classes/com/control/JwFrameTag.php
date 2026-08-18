<?php
/**
 * An Estancia JwFrameTag tag.
 *
 * @package		com.control
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwFrameTag extends JwTag
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
			// Get the content
			if (isset($tag['content']))	{$content = $tag['content'];}

			// Open the tag
			echo '<html>';
			echo '<head>';
			echo '<script type="text/javascript" src="'._APP_ROOT_.'/webapp/javascript/jquery-1.4.2.min.js"></script>';
			echo '<link rel="stylesheet" type="text/css" href="'._APP_ROOT_.'/webapp/themes/default/css/common.css">';
			echo '<!--[if IE]><link rel="stylesheet" type="text/css" href="'._APP_ROOT_.'/webapp/themes/default/css/common_ie.css"><![endif]-->';
			echo '<!--[if IE 6]><link rel="stylesheet" type="text/css" href="'._APP_ROOT_.'/webapp/themes/default/css/common_ie_6.css"><![endif]-->';
			echo '<script>function anchor()	{}</script>';
			echo '</head>';
			echo '<!-- Body of the iFrame -->';
			echo '<body style="width: 100%; height: 100%; overflow: hidden;">';

			// Eventually add a content inside
			if ($content <> '')	{echo $content;}
			
			// Close the tag itself
			echo '</body>';
			echo '</html>';


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