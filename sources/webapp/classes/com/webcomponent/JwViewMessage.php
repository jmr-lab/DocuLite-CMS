<?php
/**
 * JwViewMessage webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwViewMessage extends JwComponent
{
	/**
	* Message to display
	*
	* @access	private
	* @var		String
	*/
	private $content;

	/**
	* Subject
	*
	* @access	private
	* @var		String
	*/
	private $datesent;

	/**
	* Message ID
	*
	* @access	private
	* @var		String
	*/
	private $messageId;

	/**
	* Subject
	*
	* @access	private
	* @var		String
	*/
	private $recipient;

	/**
	* Subject
	*
	* @access	private
	* @var		String
	*/
	private $sender;

	/**
	* Subject
	*
	* @access	private
	* @var		String
	*/
	private $subject;

	/**
	 * Get the message of a notification
	 *
	 * This function retrieves the message of a notification
	 * by looking first for a content file associated with
	 * a package linked to this notification.
	 *
	 * @param	JfSession	the current session
	 * @param	ID			the object ID of the notification (queue item object)
	 * @param	String		the message to change
	 * @return	String		the message itself
	 */
	function getMessage($session, $queueId, $message)
	{
		// Get the message specified by its queue item ID
		$tmpmessage = '';
		// Init the mail array
		$mail = array(	'r_object_id' => '0000000000000000',
						'message' => 'This is the content of the mail',
						'event' => 'Mail Subject',
						'event_detail' => 'Mail Detail',
						'sent_by' => 'Jean-Marie',
						'name' => 'Guest',
						'read_flag' => '0',
						'stamp' => '0000000000000000',
						'r_component_id' => '0000000000000000'
						);
		// From a queue item we can get an associated workitem,
		// from a work item we can get a workflow,
		// and from a workflow we can get a package.
		// The content message is linked to the first document of the package.
		// The correct way to do this should be :
		// $workItem = $session->getObjectByQualification('jmi_workitem where r_queue_item_id = \''.$queueId.'\'');
		// $package = $session->getObjectByQualification('jmi_package where r_workflow_id = \''.$workItem->getValue('r_workflow_id').'\'');
		// $document = $session->getdocument($package->getRepeatingValue('r_component_id', 0));
		// but it will cause the system to run 6 queries and create 3 objects
		// We can reduce the load by running a first query which will get the document ID
		// and then get the document itself.
		$query = new JfQuery();
		// Get the workflow ID from the workItem object defined by $queueId
		$sql = 'SELECT r_workflow_id FROM jmi_workitem_sp WHERE r_queue_item_id = \''.$queueId.'\'';
		// Get the package ID defined by the workflow ID
		$sql = 'SELECT r_object_id FROM jmi_package_sp WHERE r_workflow_id IN ('.$sql.')';
		// Get the document ID from the package
		$sql = 'SELECT r_component_id FROM jmi_package_rp WHERE i_position = -1 AND r_object_id IN ('.$sql.')';
	//	$sql = 'SELECT r_component_id FROM jmi_package_rp WHERE i_position = -1 AND r_object_id IN (SELECT r_object_id FROM jmi_package_sp WHERE r_workflow_id IN (SELECT r_workflow_id FROM jmi_workitem_sp WHERE r_queue_item_id = \''.$queueId.'\'))';
		// Run the query
		$query->setSQL($sql);
		$results = $query->execute($session);
		while ($results->next())	{$mail = $results->getResult();}

		// Read the content of the document
		// and send the output to the $tmpmessage string
		$documentId = trim($mail['r_component_id']);
		if ($documentId <> '' && $documentId <> '0000000000000000')
		{
			$tmpmessage = 'this is the content of the message specified by the number <strong>'.$documentId.'</strong>';
			$document = $session->getObject(new JfId($documentId));
			$document = JfUtils::cast($document, 'JfSysObject');
			// $document = $session->getDocument($documentId);
			$tmpmessage = file_get_contents(_SERVER_ROOT_.$document->getFile());
		}

		return ($tmpmessage == '') ? $message : $tmpmessage;
	}

	/**
	 * Get the content of an object
	 *
	 * @access	public
	 */
	public function getContent()
	{
//		echo $this->content;
		$content = preg_replace('/<script(.*)<\/script>/', ' <span style="color: red; font-weight: bold; font-style: italic;">'.$this->getString('SCRIPT_REMOVED').'</span> ', $this->content);
		echo $content;
	}
	
	/**
	 * Get the content of an object
	 *
	 * @access	public
	 */
	public function getDateSent()
	{
//		echo date('l, j F Y G:i:s', strtotime($this->datesent));
		setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.ISO8859-1');
		echo ucfirst(strftime('%A, %d %B %Y %H:%M:%S', strtotime($this->datesent)));
//		echo $this->datesent;
	}

	/**
	 * Get the message ID
	 *
	 * @access	public
	 */
	public function getMessageId()
	{
		echo $this->messageId;
	}
	
	/**
	 * Get the content of an object
	 *
	 * @access	public
	 */
	public function init()
	{
		/**
		 * Get the notification (main)
		 */
		$request = new JcHttpServletRequest();

		$mail = array(	'r_object_id' => '0000000000000000',
						'message' => 'This is the content of the mail',
						'event' => 'Mail Subject',
						'event_detail' => 'Mail Detail',
						'sent_by' => 'Jean-Marie',
						'name' => 'Guest',
						'read_flag' => '0',
						'stamp' => '0000000000000000'
						);
						
		$message = '';
		
		try
		{
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
			$sql = "SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp, read_flag, '1' AS received FROM jmi_queue_item_s WHERE r_object_id = '".$request->getParameter('messageId')."' AND (name = '".$user->getValue('user_name')."' OR name IN (".$sqlGroup."))";
			// Run the query
			$query->setSQL($sql);
			$results = $query->execute($session);
			while ($results->next())	{$mail = $results->getResult();}
			// If the email was sent and not received, try again :
			if ($mail['r_object_id'] == '0000000000000000')
			{
				// Get the notification specified by its ID and recipient
				$sql = "SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp, read_flag, '0' AS received FROM jmi_queue_item_s WHERE r_object_id = '".$request->getParameter('messageId')."' AND (sent_by = '".$user->getValue('user_name')."' OR sent_by IN (".$sqlGroup."))";
				// Run the query
				$query->setSQL($sql);
				$results = $query->execute($session);
				while ($results->next())	{$mail = $results->getResult();}
			}
			// If there is an unread notification to display
			// Set the read_flag, dequeued_by and dequeued_date attributes
			if ($mail['r_object_id'] <> '0000000000000000' && $mail['read_flag'] <> '1' && $mail['received'] <> '0')
			{
				$query = new JfQuery();
				$sql = 'UPDATE jmi_queue_item_s OBJECTS SET read_flag = 1, dequeued_by = \''.$user->getValue('user_name').'\', dequeued_date = \''.date("Y-m-d H:i:s").'\' WHERE r_object_id = \''.$request->getParameter('messageId').'\' AND read_flag = \'0\'';
				$query->setSQL($sql);
				$results = $query->execute($session);
				echo "	<script>parent.markAsRead('".$mail['r_object_id']."');</script>";
			}

			// Set the subject (event), sender and recipient
			$this->messageId = $request->getParameter('messageId');
			$this->subject = $mail['event'];
			$this->sender = htmlentities($mail['sent_by']);
			$this->recipient = $mail['name'];
			$this->datesent = $mail['date_sent'];

			// Get the message itself
			// First get the message from the queue Item object
			// from the 'message' or 'event_details' fields
			// and then try to get the message from a content file
			// associated with a package linked to this queue item.
			$message = ($mail['message'] == '') ? $mail['event_detail'] : $mail['message'];
			$message = $this->getMessage($session, $mail['r_object_id'], $message);

			// If the message is a reply to another message
			// display the other message after the current one
			// and so on and so forth
			if (trim($mail['stamp']) <> '' && $mail['stamp'] <> '0000000000000000')
			{
				$objectId = $mail['r_object_id'];
				$parentId = trim($mail['stamp']);
				while ($parentId <> '' && $parentId <> '0000000000000000' && $parentId <> $objectId)
				{
					$query = new JfQuery();
					$sql = 'SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp FROM jmi_queue_item_s WHERE r_object_id = \''.$parentId.'\' AND (name = \''.$user->getValue('user_name').'\' OR name IN (SELECT group_name FROM jm_group_s WHERE r_object_id IN (SELECT i_group_id FROM `v_users_groups` WHERE r_object_id = \''.$user->getValue('r_object_id').'\')))';
					$query->setSQL($sql);
					$results = $query->execute($session);
					while ($results->next())	{$mail = $results->getTypedObject();}

					$parentId = (trim($mail['stamp']) <> '' && $mail['stamp'] <> '0000000000000000') ? $mail['stamp'] : '';
					$message .= '<div class="line" style="margin: 16px 0 16px 0; background-color:#628DC7;"><!----></div>';
					$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">From : </span><span class="td_status" style="height: 15px;">'.$mail['sent_by'].'</span></div>';
					$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">To : </span><span class="td_status" style="height: 15px;">'.$mail['name'].'</span></div>';
					$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">Date : </span><span class="td_status" style="height: 15px;">'.$mail['date_sent'].'</span></div>';
					$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">Subject : </span><span class="td_status" style="height: 15px;">'.$mail['event'].'</span></div><br>';
					$tmpmessage = ($mail['message'] == '') ? $mail['event_detail'] : $mail['message'];
					$message .= $this->getMessage($session, $mail['r_object_id'], $tmpmessage);
				}
			}
		}
		catch (Exception $e)
		{
			// TODO - Display the message page with the error message
			$message = '<div style="font-weight: bold;">An error occured while retrieving this message.<br><br>'.$e->getMessage().'<br><br>Please contact the administrator for more details.<br><br>'.$message.'</div>';
		}
		$this->content = $message;
	}

	/**
	 * Get the object name
	 *
	 * @access	public
	 * @return	String	the object name
	 */
	public function getRecipient()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return $this->recipient;
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
		$subject = ($this->subject == '') ? '...' : $this->subject;
		return $subject;
	}

	/**
	 * Get the object name
	 *
	 * @access	public
	 * @return	String	the object name
	 */
	public function getSender()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		return $this->sender;
	}
}
?>