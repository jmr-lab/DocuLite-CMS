<?php
/**
 * The JcLanguage class.
 * Usage :
 *
 * $language = new JcLanguage();
 * $language->init($request);
 *
 * This class will set the language if needed.
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcLanguage
{
	/**
	* Default language
	*
	* @access	private
	* @var		String
	*/
	private $language = 'fr';

	/**
	 * Constructor
	 *
	 */
	public function __construct()	{}

	/**
	 * Initialize the language
	 *
	 * @param	JcHttpRequest	The request
	 */
	public function init($request)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get session object
		$httpsession = new JcHttpSession();
		// Init the language
		if ($httpsession->getAttribute('lang') == null)
		{
			$lang = 'fr';
			// Get the language from the browser if possible
			$http_lang = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
			if ($http_lang == 'fr')			{$lang = 'fr';}
			else if ($http_lang == 'en')	{$lang = 'en';}
			else if ($http_lang == 'es')	{$lang = 'es';}
			else if ($http_lang == 'de')	{$lang = 'de';}
			// Set the language in the session object
			$httpsession->setAttribute('lang', $lang);
		}
	}
}
?>