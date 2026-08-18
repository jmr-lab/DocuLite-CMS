<?php
/**
 * The Configuration webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwConfiguration extends JwComponent
{
	/**
	 * Display
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $display;

	/**
	 * Home
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $home;

	/**
	 * Language
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $language;

	/**
	 * Number of results
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $results;

	/**
	 * Get the default selected value of a select tag
	 *
	 * @access	public
	 * @param	String		selectName	the name of the drop down list
	 * @param	String		value		the value
	 * @return	String		'selected' or empty
	 */
	public function getDefault($selectName, $value)
	{
		$default = (isset($this->$selectName)) ? $this->$selectName : '';
		$selected = '';
		if ($value == $default)	{$selected = ' selected="selected"';}
		return $selected;
	}

	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function init()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get session object
		$httpsession = new JcHttpSession();
		// Get the display ('details' or 'thumbnails')
		$this->display = (($httpsession->getAttribute('view') == null) ? 'thumbnails' : $httpsession->getAttribute('view'));
		// Get the home folder
		$this->home = (($httpsession->getAttribute('home') == null) ? 'home' : $httpsession->getAttribute('home'));
		// Get the language ('fr', 'en', ...)
		$this->language = (($httpsession->getAttribute('lang') == null) ? 'fr' : $httpsession->getAttribute('lang'));
		// Get the number of results ('5', '10', ...)
		$this->results = (($httpsession->getAttribute('results') == null) ? '30' : $httpsession->getAttribute('results'));
		// Logs
		// JcLogger::info('display : '.$this->display);
		// JcLogger::info('home : '.$this->home);
		// JcLogger::info('language : '.$this->language);
		// JcLogger::info('results : '.$this->results);
	}

	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function onOk()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
//		echo '<div id="ajaxmessage"><div class="errormessage" style="margin-top: 8px;">Error</div></div>';
		// Get the request object
		$request = new JcHttpServletRequest();
		// Get session object
		$httpsession = new JcHttpSession();
		// Set one month time (cookie)
		$oneMonth = 30 * 24 * 60 * 60 + time(); 
		// Get the languages list
//		$languages = JcUtils::getProperties(JcUtils::getIniFile('languages'));
		// Get the language ('French', 'English', ...)
		$lang = $request->getParameter('language');
//		$lang_code = JcUtils::getPropertyValue($languages, 'LANGUAGE', strtolower($lang));
		// Get the number of results ('5', '10', ...)
		$results = $request->getParameter('results');
		// Get the display ('details' or 'thumbnails')
		$display = $request->getParameter('display');
		// Get the home folder
		$home = $request->getParameter('home');
		// Set the language in the session object
		$httpsession->setAttribute('lang', $lang);
		setcookie("user[lang]", $lang, $oneMonth, "/");
		// Check if the number of results has been changed
		$oldResults = $httpsession->getAttribute('results');
		if ($oldResults <> $results)	{$httpsession->setAttribute('page', '1');}
		// Set the number of results
		$httpsession->setAttribute('results', $results);
		setcookie("user[results]", $results, $oneMonth, "/");
		// Set the display
		$httpsession->setAttribute('view', $display);
		setcookie("user[view]", $display, $oneMonth, "/");
		// Set the home folder
		$httpsession->setAttribute('home', $home);
		setcookie("user[home]", $home, $oneMonth, "/");
		return '';
	}
}
?>