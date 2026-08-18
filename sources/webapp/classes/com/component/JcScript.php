<?php
/**
 * The JcScript class.
 * Usage :
 *
 * $script = new JcScript($request);
 * ...
 * $script->printJavaScript();
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcScript
{
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
	}

	/**
	 * JavaScript
	 *
	 */
	public function printJavaScript($error)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Jump to anchor (should be replaced with a postprocessing javascript function)
		echo '<script>anchor();</script>';
		// First replace all XML occurences of the string returned by its Estancia HTML equivalent
		// echo "	<script>
				// var strAjax = $('#ajax').html();
				// strAjax = strAjax.replace(/<unlocked><\/unlocked>/gi, '<li style=\"width: 24px; text-align: center;\">&nbsp;</li>');
				// strAjax = strAjax.replace(/<icon>/gi, '<li style=\"width: 24px; text-align: center;\"><img src=\"/estancia/webapp/themes/default/images/icons/');
				// strAjax = strAjax.replace(/<\/icon>/gi, '\"></li>');
				// strAjax = strAjax.replace(/<description>/gi, '<li style=\"float: left; width: 20%;\"><div class=\"celltitle\"><div style=\" text-align: left;\" class=\"cellelement\"><div class=\"element\"><span>');
				// strAjax = strAjax.replace(/<\/description>/gi, '</span></div></div></div></li>');
				// $('#ajax').html(strAjax);
				// </script>";

		// Then print the other Javascript
		$request = $this->request;
		$httpsession = new JcHttpSession();
		$event = (($request == null) ? 'jump' : $request->getParameter('event'));
		$page = (($request == null) ? '1' : $request->getParameter('page'));
		$page = (($page == null) ? '1' : $page);
		$componentList = $httpsession->getAttribute('component');
		if ($error)	{$event = 'nest';}
		switch ($event)
		{
			case 'jump':	// onComponentJump
				$length = ((sizeof($componentList) == 0) ? 1 : sizeof($componentList));
				$source = (($length > 1) ? 'sheet_'.$length : 'objectlist');
				// if ($length == 1)	echo "<script>objects.length = 0;actions.length = 0;</script>";
				$this->printJsJump($length, $source, $page);
				break;
			case 'nest':	// onComponentNest
				$length = ((sizeof($componentList) == 0) ? 1 : sizeof($componentList));
				// $display = (($length > 1) ? 'inline' : 'none');
				// if ($error)	{$display = 'inline';$length = 9999;}
				if ($error)	{$length = 9999;}
				$this->printJsNest($length);
				break;
			case 'return':	// onComponentReturn
				$length = ((sizeof($componentList) == 0) ? 1 : sizeof($componentList));
				$source = (($length > 1) ? 'sheet_'.$length : 'objectlist');
				$this->printJsReturn($length, $source);
				break;
			case 'open':	// onComponentOpen
				$length = ((sizeof($componentList) == 0) ? 1 : sizeof($componentList));
				$source = $request->getParameter('component');
				$this->printJsOpen($length, $source);
				break;
			case 'close':	// onComponentClose
				$length = ((sizeof($componentList) == 0) ? 1 : sizeof($componentList));
				$source = $request->getParameter('component');
				$this->printJsClose($length, $source);
				break;
			default:
				echo "	<script>
						$('#message').html($('#ajaxmessage').html());
						$('#ajaxmessage').remove();
						$('#ajax').html('');
						</script>";
				break;
		}
	}

	/**
	 * Print the javascript code for a nest event
	 *
	 * @access	public
	 * @param	int		the number of component in the stack
	 * @param	String	the source caller id
	 * @param	int		the page number
	 */
	public function printJsJump($length, $source, $page)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		JcLogger::info('$page : '.$page);
		echo "	<script>
				var nbcomponent = ".$length.";
				$('#message').html($('#ajaxmessage').html());
				$('#ajaxmessage').remove();
				if (jQuery.trim($('#ajax').html()) != '')	$('#".$source."').html($('#ajax').html());
				$('#ajax').html('');
				</script>";
		if ($page == 1)	{echo "	<script>jump1();</script>";}
		else			{echo "	<script>jump2();</script>";}
	}

	/**
	 * Print the javascript code for a nest event
	 *
	 * @access	public
	 * @param	int		the number of component in the stack
	 * @param	String	the source caller id
	 * @param	int		the page number
	 */
	public function printJsOpen($length, $source)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$tabId = rand(100, 999);
		echo "	<script>
				$('#tabs').children().removeClass('current');
				var newtab = '<span id=\"tab_".$tabId."\" class=\"current tabtitle\">".$source."<a class=\"tabclose\" id=\"".$tabId."\">x</a></span>';
				$('#tabs').append(newtab);
				var newdiv = '<div id=\"div_".$tabId."\"></div>';
				$('#mainpanel').prepend(newdiv);
				$('#div_".$tabId."').html($('#ajax').html());
				$('#ajax').html('');
				$('#mainpanel').children().css({display: 'none'});
				$('#tabs').css({display: ''});
				$('#div_".$tabId."').css({
					position: 'absolute',
					top: '0px',
					bottom: '28px',
					left: '0px',
					right: '0px',
					'overflow': 'auto',
					display: 'inline'
				});	
				$('#objectlist').css({
					display: 'none'
				});	
				</script>";
	}

	/**
	 * Print the javascript code for a nest event
	 *
	 * @access	public
	 * @param	int		the number of component in the stack
	 * @param	String	the source caller id
	 * @param	int		the page number
	 */
	public function printJsClose($length, $source)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		echo "	<script>
				$('.current').each(function(event) {
					tabNumber = this.id;
					closeTab($('#' + tabNumber.substr(4)), event);
				});
				$('#ajax').html('');
				</script>";
	}

	/**
	 * Print the javascript code for a nest event
	 *
	 * @access	public
	 * @param	int		the number of component in the stack
	 */
	public function printJsNest($length)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		echo "	<script>
				var nbcomponent = ".$length.";
				nest();
				</script>";
	}
	/**
	 * Print the javascript code for a return event
	 *
	 * @access	public
	 * @param	int		the number of component in the stack
	 * @param	String	the source caller id
	 */
	public function printJsReturn($length, $source)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		echo "	<script>
				var nbcomponent = ".$length.";
				if (nbcomponent == 1)	$('#overlay').css('display', 'none');
				var sheetno = nbcomponent + 1;
				$('#overlay').css({'zIndex': (2 * nbcomponent)});
				$('#sheet_' + sheetno).remove();
				$('#message').html($('#ajaxmessage').html());
				if (jQuery.trim($('.phperror').html()) != '')
				{
					$('#tabs').children().removeClass('current');
					var newtab = '<span id=\"tab_errors\" class=\"current tabtitle\">Errors<a class=\"tabclose\" id=\"errors\">x</a></span>';
					if (!$('#tab_errors').length)	$('#tabs').append(newtab);
					else							$('#tab_errors').addClass('current');
					var newdiv = '<div id=\"div_errors\"></div>';
					if (!$('#div_errors').length)	$('#mainpanel').prepend(newdiv);
					else							$('#div_errors').css({display: ''});
					$('#div_errors').html($('.phperror').html());
					sheight = $(window).height() - 58;
					swidth = $(window).width() - 200 - 4;
					if ($.browser.msie)	sheight = sheight - 2;
					$('#objectlist').css({
						display: 'none'
					});
					$('#div_errors').css({
						height: sheight + 'px',
						width: swidth + 'px',
						'overflow-y': 'scroll'
					});
					$('.phperror').remove();
				}
				if (jQuery.trim($('#ajax').html()) != '')	$('#".$source."').html($('#ajax').html());
				$('#ajax').html('');
				</script>";
	}
}
?>