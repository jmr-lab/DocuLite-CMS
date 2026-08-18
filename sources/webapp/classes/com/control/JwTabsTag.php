<?php
/**
 * An Estancia JwTabsTag tag.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwTabsTag extends JwTag
{
	/**
	 * Render the custom tag :
	 * <jm:title icon="..." title="..." path="..."/>
	 *
	 * <div class="header">
	 * 	<div class="header-image"><img src="<?php echo $class->getTitleImage();?>"></div>
	 * 	<div class="header-title"><span><?php echo $class->getTitleName();?></span></div>
	 * 	<div class="header-path"><?php echo $class->getTitlePath();?></div>
	 * </div>
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
		// Render the tag
		echo '<div class="tabs"';
		$attributes = $tag['attributes'];
		if (isset($attributes['id']))	{echo ' id="'.$attributes['id'].'"';}
		echo '>';
		echo '<span class="current" id="tab_estancia" onclick="selectTab(this);">Estancia</span>';
		// Close the tag
		echo '</div>';
		// Return the tag
		$output = ob_get_clean();
		return $output;
	}
}
?>