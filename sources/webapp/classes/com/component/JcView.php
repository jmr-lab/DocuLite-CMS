<?php
/**
 * The JcView class.
 * Usage :
 *
 * $view = new JcView($request);
 * ...
 * $view->printErrorMessage($exception);
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcView
{
	/**
	* Whether an error occured
	*
	* @access	private
	* @var		boolean
	*/
	private $error = false;

	/**
	* JcHttpServletRequest
	*
	* @access	private
	* @var		JcHttpServletRequest
	*/
	private $request;

	/**
	 * Constructor
	 *
	 * @param	JcHttpRequest	The request
	 */
	public function __construct($request)
	{
		$this->request = $request;
		// Get session object
		$httpsession = new JcHttpSession();
		// Init the view
		if ($httpsession->getAttribute('view') == null || ($request <> null && $request->getParameter('view') <> null))	{$this->initView();}
		// Init the order
		if ($httpsession->getAttribute('order') == null || ($request <> null && $request->getParameter('order') <> null))	{$this->initOrder();}
	}

	/**
	 * Return whether an error occured.
	 *
	 * @access	public
	 * @return	boolean	whether an error occured
	 */
	public function hasError()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return $this->error;
	}

	/**
	 * Initialize the order
	 *
	 * @access	public
	 */
	public function initOrder()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$request = $this->request;
		// Initialize the order
		$order = 'ASC';
		// Get the order from the request (if applicable)
		if ($request <> null && $request->getParameter('order') <> null)	{$order = $request->getParameter('order');}
		// Get session object
		$httpsession = new JcHttpSession();
		// Set the order in the session object
		$httpsession->setAttribute('order', $order);
	}

	/**
	 * Initialize the view
	 *
	 * @access	public
	 */
	public function initView()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$request = $this->request;
		// Initialize the view
		$view = 'thumbnails';
		// Get the view from the request (if applicable)
		if ($request <> null && $request->getParameter('view') <> null)	{$view = $request->getParameter('view');}
		// Get session object
		$httpsession = new JcHttpSession();
		// Set the view in the session object
		$httpsession->setAttribute('view', $view);
	}

	/**
	 * HTML Inclusions
	 *
	 */
	public function printAjax()
	{
		echo '<div id="ajax" style="display: none;"></div>';
		echo '<div id="overlay" class="overlay" style="display: none;"></div>';
	}

	/**
	 * Error message
	 *
	 */
	public function printErrorMessage($exception)
	{
//		$errMsg = '<div id="error" class="error">'.$message.'</div>';
		$iconError = 'warning';
//		$link = "javascript:postServerEvent('error', 'return', null, null, null);";
		$link = 'javascript:window.location.reload();';
		if ($exception->getCode() < 2)
		{
			$httpsession = new JcHttpSession();
			$httpsession->removeAttribute('path');
			$httpsession->removeAttribute('component');
			$iconError = 'error';
			$link = 'javascript:closeWindow(9999);';
		}
		$httpsession = new JcHttpSession();
		$lang = $httpsession->getAttribute('lang');
		$nlsProperties = JcUtils::getNLSProperties($lang);
		$strError = JcUtils::getString($nlsProperties, strtoupper('ERROR'));
		$strClose = JcUtils::getString($nlsProperties, strtoupper('CLOSE'));

		$this->error = true;
		$errMsg = '<div id="error">';
		$errMsg .= '<div class="modal" style="width: 600px;">';
		$errMsg .= '<div class="modal-header">';
		$errMsg .= '<img src="/estancia/webapp/themes/default/images/icons/'.$iconError.'.png" class="imgheader">';
		$errMsg .= '<span class="drag txtheader">'.$strError.'</span>';
		$errMsg .= '<img class="drag" src="/estancia/webapp/themes/default/images/background/toolbar.png" width="100%" height="24px">';
		$errMsg .= '</div>';
		$errMsg .= '<div class="modal-content">';
		$errMsg .= '<div class="content">'.$exception->getMessage().'</div>';
		$errMsg .= '<div class="buttons">';
		$errMsg .= '<div class=" right"><a class="button" onclick="this.blur();" href="'.$link.'"><img src="/estancia/webapp/themes/default/images/icons/cancel_16.png"><span style="padding-left: 32px;">'.$strClose.'</span></a></div>';
		$errMsg .= '</div>';
		$errMsg .= '</div>';
		$errMsg .= '</div>';
		$errMsg .= '</div>';
		return $errMsg;
		// javascript:postServerEvent(\'objectlist\', \'return\', null, null, null);
	}

	/**
	 * HTML Inclusions
	 *
	 */
	public function printHeaders()
	{
		/**
		 * Header inclusions
		 *
		 */
		echo '<!DOCTYPE html>';
//		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
		echo '<html>';
		echo '<head>';
		echo '<title>Estancia 3.1</title>';
		echo '<META NAME="Description" CONTENT="Jean-Marie Roy, a Documentum expert, has created Estancia, a Content Management System written in PHP and using a MySQL database. It\'s using the same tables as Documentum, but intend to be faster and better than Documentum.">';
		echo '<META NAME="Keywords" CONTENT="Documentum, documentum, Expert, expert, job, Jean-Marie, jean-marie, dmadmin, freelance, Freelance, 4i, dctm, Rightsite, RightSite, rightsite, webtop, da, docapp, workflow, search, Verity, verity, full-text">';
		echo '<META NAME="Author" CONTENT="Jean-Marie">';
		echo '<META NAME="copyright" CONTENT="Jean-Marie">';
		echo '<META NAME="Identifier-URL" CONTENT="http://www.jmroy.free.fr">';
		echo '<META NAME="Date-Creation-yyyymmdd" content="20060930">';
		echo '<META NAME="Date-Revision-yyyymmdd" content="20060930">';
		echo '<META NAME="Category" CONTENT="Internet">';
		echo '<META NAME="robots" CONTENT="index, nofollow">';
		echo '<META NAME="Generator" CONTENT="Notepad, Documentum, Microsoft Paint, Microsoft Photo Editor">';

		 /**
		 * CSS inclusions
		 *
		 */
		echo '<link rel="stylesheet" type="text/css" href="'._APP_ROOT_.'/webapp/themes/default/css/common.css">';
		// echo '<!--[if IE]><link rel="stylesheet" type="text/css" href="'._APP_ROOT_.'/webapp/themes/default/css/common_ie.css"><![endif]-->';
		// echo '<!--[if IE 6]><link rel="stylesheet" type="text/css" href="'._APP_ROOT_.'/webapp/themes/default/css/common_ie6.css"><![endif]-->';
		echo '<!--[if IE 7]><link rel="stylesheet" type="text/css" href="'._APP_ROOT_.'/webapp/themes/default/css/common_ie7.css"><![endif]-->';
		echo '<!--[if IE 8]><link rel="stylesheet" type="text/css" href="'._APP_ROOT_.'/webapp/themes/default/css/common_ie8.css"><![endif]-->';
		echo '<link rel="shortcut icon" type="image/x-icon" href="'._APP_ROOT_.'/webapp/themes/default/images/icons/estancia.ico">';

		/**
		 * Javascript inclusions
		 *
		 */
		echo '<!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="'._APP_ROOT_.'/webapp/javascript/html5.js"><![endif]-->';
		echo '<script type="text/javascript" src="'._APP_ROOT_.'/webapp/javascript/jquery-1.4.2.min.js"></script>';
		echo '<script type="text/javascript" src="'._APP_ROOT_.'/webapp/javascript/jquery-ui-1.8.12.min.js"></script>';
		echo '<script type="text/javascript" src="'._APP_ROOT_.'/webapp/javascript/context-menu.js"></script>';
		echo '<script type="text/javascript" src="'._APP_ROOT_.'/webapp/javascript/estancia.js"></script>';
		echo '<script type="text/javascript" src="'._APP_ROOT_.'/webapp/javascript/ajaxfileupload.js"></script>';

		echo '</head>';
	}

	/**
	 * Context menu
	 *
	 */
	public function printContextMenu()
	{
		echo '<div style="display: none;" id="contextmenu">';
		echo '</div>';
	}

	/**
	 * HTML Inclusions
	 *
	 */
	public function printPHPErrors()
	{
		if (!isset($_SESSION['phperror']))	{return;}
		$request = $this->request;
		if ($request <> null && $request->getParameter('event') == 'nest')	{unset($_SESSION['phperror']); return;}
		echo '<div class="phperror">';
		$bgColour = '#f3f3f3';
		foreach ($_SESSION['phperror'] as $msg)
		{
			if ($bgColour == '#f3f3f3')	{$bgColour = 'white';}
			else						{$bgColour = '#f3f3f3';}
			echo '<div style="background-color: '.$bgColour.';"><div style="height: 48px; padding: 6px; display: table-cell; vertical-align: middle;">'.$msg.'</div></div>';
		}
		echo '</div>';
		unset($_SESSION['phperror']);
	}

	/**
	 * 'Please Wait' message
	 *
	 */
	public function printWaitMessage()
	{
		$httpsession = new JcHttpSession();
		$lang = $httpsession->getAttribute('lang');
		$nlsProperties = JcUtils::getNLSProperties($lang);
		$please_wait = JcUtils::getString($nlsProperties, strtoupper('PLEASE_WAIT'));
		echo '<div style="display: none;" id="please_wait"><div class="freezer"></div><div class="please_wait"><img src="/estancia/webapp/themes/default/images/icons/please_wait.gif"><span>'.$please_wait.'</span></div></div>';
	}

	/**
	 * HTML Inclusions
	 *
	 */
	public function printXML($strOutput)
	{
		// if ($this->headers === false)
		// {
			// JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(strOutput : '.$strOutput.')');
			// $strOutput = str_replace("<li style=\"width: 24px; text-align: center;\">&nbsp;</li>", "<unlocked/>", $strOutput);
			// $strOutput = str_replace("<li style=\"width: 24px; text-align: center;\"><img src=\"/estancia/webapp/themes/default/images/icons/", "<icon>", $strOutput);
			// $strOutput = str_replace("\"></li>", "</icon>", $strOutput);
			// // $strOutput = str_replace("<li style=\"width: 40%; text-align: left;\">", "<name>", $strOutput);
			// // $strOutput = str_replace("</li>", "</name>", $strOutput);
			// // $strOutput = str_replace("<li style=\"width: 10%; text-align: center;\">", "<properties>", $strOutput);
			// // $strOutput = str_replace("<img src=\"/estancia/webapp/themes/default/images/icons/info_16.png\"></a></li>", "</properties>", $strOutput);
			// // $strOutput = str_replace("<li style=\"width: 10%; text-align: left;\"><span>", "<size>", $strOutput);
			// // $strOutput = str_replace("</span></li>", "</size>", $strOutput);
// //			$strOutput = str_replace("<li style=\"float: left; width: 20%;\"><div class=\"celltitle\"><div style=\" text-align: left;\" class=\"cellelement\"><div class=\"element\"><span>", "<description>", $strOutput);
			// $strOutput = str_replace("<li style=\"float: left; width: 20%;\"><div class=\"celltitle\"><div class=\"cellelement\" style=\" text-align: left;\"><div class=\"element\"><span>", "<description>", $strOutput);
			// $strOutput = str_replace("</span></div></div></div></li>", "</description>", $strOutput);
			// // $strOutput = str_replace("<li style=\"text-align: left;\"><span>", "<modified>", $strOutput);
			// // $strOutput = str_replace("</span></li>", "</modified>", $strOutput);
			// JcLogger::info(__CLASS__.'.'.__FUNCTION__.'(strOutput : '.$strOutput.')');
		// }
		return $strOutput;
	}

	/**
	 * Display the welcome message
	 *
	 * @access	public
	 */
	public function showWelcomeMessage($parser)
	{
		// Get the Http Session
		$httpsession = new JcHttpSession();
		// Check whether the welcome message will be displayed
		if ($httpsession->getAttribute('welcome') <> true || sizeof($httpsession->getAttribute('component')) <> 1)	{return;}
		// If a cookie has been set (user don't want to see this message, then return
		if (isset($_COOKIE['welcome']))	{return;}
		// Remove the welcome page
		$httpsession->removeAttribute('welcome');
		// Show the page (the following line has been commented because calling an ajax function is quite slow in this situation (reload)
//		echo "<script>javascript:postServerEvent('objectlist', 'nest', 'welcome', null, null);</script>";
		// Parse the tags
		echo $parser->parseTags('<div id="div_welcome" style="display: none;"><jm:panel component="welcome" id="welcome"/></div>');
		echo "	<script>
					$('#ajax').html($('#div_welcome').html());
					$('#div_welcome').remove();
				</script>";
		// Run javascript to display correctly the welcome component
		$script = new JcScript(null);
		$script->printJsNest(1);
		// @todo - change the component list stack
		// $componentList = $httpsession->getAttribute('component');
		// $componentList[] = 'welcome';
		// $httpsession->setAttribute('component', $componentList);
	}
}
?>