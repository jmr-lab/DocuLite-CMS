<?php
/**
 * An Estancia JwTitleTag tag.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwTitleTag extends JwTag
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
		// Initialize the attributes
		$attributes = $tag['attributes'];
		// Get localized messages
		if (isset($tag['properties']))	{$nlsProperties = $tag['properties'];}
		// Render the tag
		echo '<div class="header">';
		// Get the icon
		if (isset($attributes['icon']))
		{
			echo '<div class="header-image"><img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/'.$attributes['icon'].'"></div>';
		}
		// Get the title
		if (isset($attributes['title']))
		{
			echo '<div class="header-title"><span>'.html_entity_decode($attributes['title']).'</span></div>';
		}
		// Get the path
		if (isset($attributes['path']))
		{
			$path = unserialize(html_entity_decode($attributes['path']));
			echo '<div class="header-path">'.$path.'</div>';
		}
		// Get the configuration button
		$jscript = 	"'objectlist', 'nest', 'configuration', null, null";
		$jscript = 'javascript:postServerEvent('.$jscript.');';
		if (isset($attributes['configuration']) && $attributes['configuration'] == 'true')
		{
			echo '<a href="'.$jscript.'"><div class="configuration"><img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/configuration_16.png"></div></a>';
		}
//		echo '<div class="header-actions"></div>';
		// Get the content
		if (isset($tag['content']))	{$content = $tag['content'];}
		// Eventually add a content inside
		if ($content <> '')	{echo $content;}
		// Close the tag
		echo '</div>';
		// Return the tag
		$output = ob_get_clean();
		return $output;
	}
}
?>