<?php
/**
 * Display an error message
 *
 * This interface displays a generic error message
 *
* @author Jean-Marie Roy
* @version 3.0
*/

/**
 * Constants definitions
 *
 * _APP_ROOT_ = /localhost/webapp
 * _SERVER_ROOT_ = C:/website/localhost/webapp
 */
$path = substr($_SERVER['PHP_SELF'], 1);
$_APP_ROOT_ = '/'.substr($path, 0, strpos($path, '/'));
$_SERVER_ROOT_ = $_SERVER["DOCUMENT_ROOT"].$_APP_ROOT_;
// Remove the last character if it is a slash ('/')
if (substr($_APP_ROOT_, -1) == '/')	{$_APP_ROOT_ = trim(substr($_APP_ROOT_, 0, -1));}
define("path", $path);
define("_APP_ROOT_", $_APP_ROOT_);
define("_SERVER_ROOT_", $_SERVER_ROOT_);

/**
 * Classes required
 *
 */
require _SERVER_ROOT_.'/webapp/classes/com/component/JcHttpSession.php';
require _SERVER_ROOT_.'/webapp/classes/com/component/JcUtils.php';
require _SERVER_ROOT_.'/webapp/classes/com/component/JcLogger.php';

/**
 * Properties (language) definitions
 *
 * $lang = 'fr'
 */
$httpsession = new JcHttpSession();
// Init the language
$lang = (($httpsession->getAttribute('lang') == null) ? 'en' : $httpsession->getAttribute('lang'));
$nlsProperties = JcUtils::getNLSProperties($lang);
define("_PROPERTIES_", serialize($nlsProperties));


/**
 * Get the message.
 *
 * This function retrieves the localized version of a string.
 *
 * @param	String		the message
 * @return	String		the localized value of the message
 */
function getString($message)
{
	return JcUtils::getString(unserialize(_PROPERTIES_), strtoupper($message));
}
?>
<html>
	<head>
		<title>Estancia 3.0</title>
		<META NAME="Description" CONTENT="Estancia is a Content Management System written in PHP and using a MySQL database. It's using the same tables as Documentum, but intend to be faster and better than Documentum.">
		<META NAME="Keywords" CONTENT="Documentum, documentum, Expert, expert, job, Jean-Marie, jean-marie, dmadmin, freelance, Freelance, 4i, dctm, Rightsite, RightSite, rightsite, webtop, da, docapp, workflow, search, Verity, verity, full-text">
		<META NAME="Author" CONTENT="Jean-Marie">
		<META NAME="copyright" CONTENT="Jean-Marie">
		<META NAME="Identifier-URL" CONTENT="http://www.jmroy.free.fr">
		<META NAME="Date-Creation-yyyymmdd" content="20060930">
		<META NAME="Date-Revision-yyyymmdd" content="20060930">
		<META NAME="Category" CONTENT="Internet">
		<META NAME="robots" CONTENT="index, nofollow">
		<META NAME="Generator" CONTENT="Notepad, Documentum, Microsoft Paint, Microsoft Photo Editor">
		<link rel="stylesheet" type="text/css" href="/estancia/webapp/themes/default/css/common.css">
		<!--[if IE]><link rel="stylesheet" type="text/css" href="/estancia/webapp/themes/default/css/common_ie.css"><![endif]-->
		<!--[if IE 6]><link rel="stylesheet" type="text/css" href="/estancia/webapp/themes/default/css/common_ie_6.css"><![endif]-->
		<link rel="shortcut icon" type="image/png" href="/estancia/webapp/themes/default/images/icons/estancia.ico">
		<script type="text/javascript" src="/estancia/webapp/javascript/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="/estancia/webapp/javascript/jquery-ui-1.8.12.min.js"></script>
		<script type="text/javascript" src="/estancia/webapp/javascript/estancia.js"></script>
		<script type="text/javascript" src="/estancia/webapp/javascript/ajaxfileupload.js"></script>
	</head>
	<div id="ajax" style="display: none;"></div>
	<div id="overlay" class="overlay" style="display: none;"></div>
	<div class="freezer" style="display: none;" id="please_wait"><div class="please_wait"><?php echo getString('PLEASE_WAIT');?></div></div>
	<body>
		<div id="objectlist"></div>
		<div id="sheet_2" class="sheet" style="width: 600px;">
			<div id="login">
				<div class="modal" style="width: 600px;">
					<div class="modal-header">
						<img src="/estancia/webapp/themes/default/images/icons/error.png" class="imgheader">
						<span class="drag txtheader"><?php echo getString('ERROR');?></span>
						<img class="drag" src="/estancia/webapp/themes/default/images/background/toolbar.png" width="100%" height="24px">
					</div>
					<div class="modal-content">
						<div class="attribute">
							<span style="width: 480px;"><?php echo getString('ERROR_MESSAGE');?></span>
						</div>
						<div class="attribute" id="message"></div>
						<div class="buttons">
							<div class=" right">
								<a class="button" href="http://localhost/estancia">
									<img src="/estancia/webapp/themes/default/images/icons/ok_16.png">
									<span style="padding-left: 32px;">Ok</span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">displayLogin();</script>
	</body>
</html>
<script>
	var nbcomponent = 1;
	$('#message').html($('#ajaxmessage').html());
	$('#ajaxmessage').remove();
	if (jQuery.trim($('#ajax').html()) != '')	$('#objectlist').html($('#ajax').html());
	$('#ajax').html('');
	</script>	<script>
		var nbcomponent = 1;
		// Grid Height :
		sheight = $('#contentgrid').height();
		if (sheight > 0.6 * $(window).height())
		{
			gheight = 0.6 * $(window).height();
			$('#contentgrid').css({
				height: gheight + 'px',
				'overflow-y' : 'scroll'
			});
		}
		else
		{
			$('#contentgrid').css({
				'overflow-y' : 'hidden'
			});
		}
		shtop = ($(window).height() - $('#sheet_' + nbcomponent).height()) / 2;
		$('#sheet_' + nbcomponent).css({
			top: shtop + 'px'
		});
</script>
<div style="display: none;" id="contextmenu"></div>
<script>redimWindows();</script>