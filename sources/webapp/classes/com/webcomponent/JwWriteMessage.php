<?php
/**
 * JwWriteMessage webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwWriteMessage extends JwComponent
{
	/**
	* Subject
	*
	* @access	private
	* @var		String
	*/
	private $subject = '';

	/**
	* Request
	*
	* @access	private
	* @var		JcHttpServletRequest
	*/
	private $request;

	/**
	* Recipients
	*
	* @access	private
	* @var		array
	*/
	private $userArr = array();

	/**
	 * Get the object name
	 *
	 * @access	public
	 * @return	String	the object name
	 */
	public function doAddUser()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$strMessage = $this->getModal('INFO', 'GUEST_RESTRICTION', 'info');
		echo "	<script>
					function postProcessingEvent()	{
						$('#ajax').html('".$strMessage."');
						nest();
					}
				</script>";
	}

	/**
	 * Get a list of recipients
	 *
	 * @access	private
	 */
	private function getRecipients($request, $type)
	{
		foreach ($request->getParameter('writemessage_'.$type) as $key => $value)
		{
			$this->userArr[] = $value;
		}
	}

	/**
	 * Get the object name
	 *
	 * @access	public
	 * @return	String	the object name
	 */
	public function doSend()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the request object
		$request = new JcHttpServletRequest();
		// Get the session and the user's details
		$session = $this->session;
		$user = $this->user;
		// Get the parameters
		// JcLogger::info('Subject : '.$request->getParameter('subject'));
		// JcLogger::info('Body : '.$request->getParameter('writebody'));
		// Set the recipient (self) - @todo change later
//		$this->userArr[] = 'World';
		$this->getRecipients($request, 'TO');
		$this->getRecipients($request, 'CC');
		$this->getRecipients($request, 'BCC');
		// Init the success boolean variable
		$success = false;
		// Send the message
		try
		{
//			throw new JfException('EMPTY_MESSAGE');
			if (trim($request->getParameter('subject')) == '' && trim($request->getParameter('writebody')) == '')	{throw new JfException('EMPTY_MESSAGE');}
			$session->sendToDistributionListEx($this->userArr, null, $request->getParameter('subject'), $request->getParameter('writebody'), null, null, false);
			$success = true;
		}
		catch (JfException $exception)	{JcLogger::info('Error : '.$exception->getMessage());}

		// Success : message sent
		if ($success)
		{
			echo "	<script>
					$('.current').each(function(event) {
						tabNumber = this.id;
						closeTab($('#' + tabNumber.substr(4)), event);
					});
					</script>";
			$strMessage = $this->getModal('INFO', 'MESSAGE_SENT', 'info');
			echo "	<script>
						function postProcessingEvent()	{
							$('#ajax').html('".$strMessage."');
							nest();
						}
					</script>";
		}
		// Fail : message could not be sent
		else
		{
			$strMessage = $this->getModal('ERROR', 'MESSAGE_COULD_NOT_BE_SENT', 'warning');
			echo "	<script>
						function postProcessingEvent()	{
							$('#ajax').html('".$strMessage."');
							nest();
						}
					</script>";
		}
	}

	/**
	 * Get the modal window
	 *
	 * @access	public
	 * @return	String	the modal HTML code
	 */
	public function getModal($nlsId, $nlsMessage, $icon)
	{
		$strMessage = '<div id="'.$nlsId.'">';
		$strMessage .= '<div class="modal" style="width: 400px;">';
		$strMessage .= '<div style="background-color: #3A5A86;" class="modal-header drag">';
		$strMessage .= '<img src="/estancia/webapp/themes/default/images/icons/'.$icon.'.png" class="imgheader">';
		$strMessage .= '<span class="txtheader">'.$this->getString($nlsId).'</span>';
		$strMessage .= '</div>';
		$strMessage .= '<div class="modal-content">';
		$strMessage .= '<div class="content">'.$this->getString($nlsMessage).'</div>';
		$strMessage .= '<div class="buttons">';
		$strMessage .= '<div class=" right"><a class="button" onclick="this.blur();" href="javascript:closeWindow();"><img src="/estancia/webapp/themes/default/images/icons/cancel_16.png"><span style="padding-left: 32px;">'.$this->getString('CLOSE').'</span></a></div>';
		$strMessage .= '</div>';
		$strMessage .= '</div>';
		$strMessage .= '</div>';
		$strMessage .= '</div>';
		return $strMessage;
	}

	/**
	 * Get the recipient
	 *
	 * @access	public
	 * @return	String	the recipient HTML code
	 */
	public function getRecipient()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$recipient = '';
		$user = $this->user;
		// if ($user->getValue('user_name') == 'guest')
		// {
			$recipient = '<div class="maillabel" style="display: inline-block;"><span>'.$this->getString('TO').' :&nbsp;</span></div>';
			$recipient .= '<div class="maillink" style="display: inline-block;"><img src="/estancia/webapp/themes/default/images/icons/jm_group_16.png"><span>'.$this->getString('WORLD').'</span></div>';
		// }
		// else
		// {
			// $recipient = '<div class="maillabel"><jm:datatextvalue nlsid="'.$this->getString('TO').'" value="" name="recipient" width="450px" readonly="false"/></div>';
		// }
		return $recipient;
	}

	/**
	 * Get the recipient
	 *
	 * @access	public
	 * @return	String	the recipient HTML code
	 */
	public function getRecipientCopy($nlsid)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$recipient = '';
		$user = $this->user;
		// if ($user->getValue('user_name') == 'guest')
		// {
			$recipient = '<div class="maillabel"><span>'.$this->getString($nlsid).' :&nbsp;</span></div>';
		// }
		// else
		// {
			// $recipient = '<div class="maillabel"><jm:datatextvalue nlsid="'.$this->getString($nlsid).'" value="" name="recipient" width="450px" readonly="false"/></div>';
		// }
		return $recipient;
	}

	/**
	 * Get the object name
	 *
	 * @access	public
	 * @return	String	the object name
	 */
	public function getSubject()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$request = $this->request;
		$prefix = '';
		if ($request->getParameter('type') == 'reply')			{$prefix = $this->getString('RE').': ';}
		else if ($request->getParameter('type') == 'forward')	{$prefix = $this->getString('FW').': ';}
		return $prefix.$this->subject;
	}

	/**
	 * Get the object name
	 *
	 * @access	public
	 * @return	String	the object name
	 */
	public function getTitle()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get title
		$request = $this->request;
		$strTitle = $request->getParameter('type');
		if ($strTitle == '')	{$strTitle = $this->getString('NEW_MESSAGE');}
		else					{$strTitle = $this->getString(strtoupper($strTitle));}
		return $strTitle;
	}

	/**
	 * Get the content of an object
	 *
	 * @access	public
	 */
	public function init()
	{
		$this->request = new JcHttpServletRequest();
		$request = $this->request;
		// Get the session
		// $sessionmanager = new JfSessionManager();
		// $session = $sessionmanager->getSession('www_jmroy');
		// Get the user details
		// $user = $session->getLoginInfo();
		// Get the session and the user's details
		$session = $this->session;
		$user = $this->user;
		// Get the message specified by its ID
		// For security reasons, only retrieve the message specified by its ID AND recipient name (equals to the current user name)
		// If someone is trying to view a message not sent to him, the query won't return anything
		$query = new JfQuery();
		// Get all the groups the current user belongs to
		$sqlGroup = "SELECT group_name FROM jm_group_s WHERE r_object_id IN (SELECT i_group_id FROM `v_users_groups` WHERE r_object_id = '".$user->getValue('r_object_id')."')";
		// Get the notification specified by its ID and recipient
		$sql = "SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp, read_flag FROM jmi_queue_item_s WHERE r_object_id = '".$request->getParameter('messageId')."' AND (name = '".$user->getValue('user_name')."' OR name IN (".$sqlGroup."))";
		// Run the query
		$query->setSQL($sql);
		$results = $query->execute($session);
		while ($results->next())	{$mail = $results->getResult();}
		// If the email was sent and not received, try again :
		if (isset($mail['r_object_id']) && $mail['r_object_id'] == '0000000000000000')
		{
			// Get the notification specified by its ID and recipient
			$sql = "SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp, read_flag FROM jmi_queue_item_s WHERE r_object_id = '".$request->getParameter('messageId')."' AND (sent_by = '".$user->getValue('user_name')."' OR sent_by IN (".$sqlGroup."))";
			// Run the query
			$query->setSQL($sql);
			$results = $query->execute($session);
			while ($results->next())	{$mail = $results->getResult();}
		}
		// Set the subject (event), sender and recipient
		if (isset($mail['event']))	$this->subject = $mail['event'];
		// Get tab title
		$strTitle = $this->getSubject();
		if ($strTitle == '')	{$strTitle = $this->getString('NEW_MESSAGE');}
		// Change the tab name
		echo "	<script>
				function postProcessingEvent()	{
					$('.current').each(function() {
						var link = $(this).html();
						link = '".$strTitle."' + link.substr(12);
						$(this).html(link);
					});
				}
				</script>";
	}
}
?>