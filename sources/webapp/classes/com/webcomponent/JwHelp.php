<?php
/**
 * Help webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwHelp extends JwComponent
{
	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function render()
	{
		// JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// return false;
		$context = new JcContext();
		$components = $context->getComponents();
		// JcLogger::info('First component : '.$components->getFirstComponent());
		// JcLogger::info('Last component : '.$components->getLastComponent());
		// JcLogger::info('Number of components : '.$components->getComponentsSize());
		// JcLogger::info('Language : '.$context->getLanguage());
		// Get the help content
		echo JcUtils::getHelpFile($context->getLanguage());
		echo '<script>function anchor()	{window.location.hash="'.$components->getFirstComponent().'";}</script>';
	}

	/**
	 * Method called when the current component is closed
	 *
	 * @access	public
	 */
	public function onClose()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		echo '<script>function anchor()	{}</script>';
		return false;
	}
}
?>