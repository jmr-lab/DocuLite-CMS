<?php
/**
 * Welcome webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwWelcome extends JwComponent
{
	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function render()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$context = new JcContext();
		// Get the help content
		echo JcUtils::getFile('welcome', $context->getLanguage());
	}

	/**
	 * Method called when the current component is closed
	 *
	 * @access	public
	 */
	public function onClose()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the request object
		$request = new JcHttpServletRequest();
		$showmessage = $request->getParameter('showmessage');
		if ($showmessage <> 'true')
		{
			$oneMonth = 30 * 24 * 60 * 60 + time(); 
			setcookie("welcome", "false", $oneMonth, "/");
		}
		return false;
	}
}
?>