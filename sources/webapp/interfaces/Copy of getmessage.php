<?php
/**
 * Display a notification
 *
 * This interface displays a notification / email
 * specified by its ID.
 *
* @author Jean-Marie Roy
* @version 3.0
*/

include 'interface.php';
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
 * Get the notification (main)
 */
try
{
	// Get the session
	$sessionmanager = new JfSessionManager();
	$session = $sessionmanager->getSession('www_jmroy');
	// Get the user details
	$user = $session->getLoginInfo();
	// Get the message specified by its ID
	// For security reasons, only retrieve the message specified by its ID AND recipient name (equals to the current user name)
	// If someone is trying to view a message not sent to him, the query won't return anything
	$query = new JfQuery();
	// Get all the groups the current user belongs to
	$sql = 'SELECT group_name FROM jm_group_s WHERE r_object_id IN (SELECT i_group_id FROM `v_users_groups` WHERE r_object_id = \''.$user->getValue('r_object_id').'\')';
	// Get the notification specified by its ID and recipient
	$sql = 'SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp, read_flag FROM jmi_queue_item_s WHERE r_object_id = \''.$request->getParameter('messageId').'\' AND (name = \''.$user->getValue('user_name').'\' OR name IN ('.$sql.'))';
//	$sql = 'SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp FROM jmi_queue_item_s WHERE r_object_id = \''.$request->getParameter('messageId').'\' AND (name = \''.$user->getValue('user_name').'\' OR name IN (SELECT group_name FROM jm_group_s WHERE r_object_id IN (SELECT i_group_id FROM `v_users_groups` WHERE r_object_id = \''.$user->getValue('r_object_id').'\')))';
	// Run the query
	$query->setSQL($sql);
	$results = $query->execute($session);
	while ($results->next())	{$mail = $results->getResult();}
	// If there is an unread notification to display
	// Set the read_flag, dequeued_by and dequeued_date attributes
	if ($mail['r_object_id'] <> '' && $mail['read_flag'] <> '1')
	{
		$query = new JfQuery();
		$sql = 'UPDATE jmi_queue_item_s OBJECTS SET read_flag = 1, dequeued_by = \''.$user->getValue('user_name').'\', dequeued_date = \''.date("Y-m-d H:i:s").'\' WHERE r_object_id = \''.$request->getParameter('messageId').'\' AND dequeued_by = \'\'';
		$query->setSQL($sql);
		$results = $query->execute($session);
	}

	// Set the subject (event), sender and recipient
	$subject = $mail['event'];
	$sender = htmlentities($mail['sent_by']);
	$recipient = $mail['name'];

	// Get the message itself
	// First get the message from the queue Item object
	// from the 'message' or 'event_details' fields
	// and then try to get the message from a content file
	// associated with a package linked to this queue item.
	$message = ($mail['message'] == '') ? $mail['event_detail'] : $mail['message'];
	$message = getMessage($session, $mail['r_object_id'], $message);

	// If the message is a reply to another message
	// display the other message after the current one
	// and so on and so forth
	if (trim($mail['stamp']) <> '' && $mail['stamp'] <> '0000000000000000')
	{
		$parentId = trim($mail['stamp']);
		while ($parentId <> '' && $parentId <> '0000000000000000')
		{
			$query = new JfQuery();
			$sql = 'SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp FROM jmi_queue_item_s WHERE r_object_id = \''.$parentId.'\' AND (name = \''.$user->getValue('user_name').'\' OR name IN (SELECT group_name FROM jm_group_s WHERE r_object_id IN (SELECT i_group_id FROM `v_users_groups` WHERE r_object_id = \''.$user->getValue('r_object_id').'\')))';
			$query->setSQL($sql);
			$results = $query->execute($session);
			while ($results->next())	{$mail = $results->getResult();}

			$parentId = (trim($mail['stamp']) <> '' && $mail['stamp'] <> '0000000000000000') ? $mail['stamp'] : '';
			$message .= '<div class="line" style="margin: 16px 0 16px 0; background-color:#628DC7;"><!----></div>';
			$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">From : </span><span class="td_status" style="height: 15px;">'.$mail['sent_by'].'</span></div>';
			$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">To : </span><span class="td_status" style="height: 15px;">'.$mail['name'].'</span></div>';
			$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">Date : </span><span class="td_status" style="height: 15px;">'.$mail['date_sent'].'</span></div>';
			$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">Subject : </span><span class="td_status" style="height: 15px;">'.$mail['event'].'</span></div><br>';
			$tmpmessage = ($mail['message'] == '') ? $mail['event_detail'] : $mail['message'];
			$message .= getMessage($session, $mail['r_object_id'], $tmpmessage);
		}
	}
}
catch (Exception $e)
{
	// TODO - Display the message page with the error message
	$message = '<div style="font-weight: bold;">An error occured while retrieving this message.<br><br>'.$e->getMessage().'<br><br>Please contact the administrator for more details.<br><br>'.$message.'</div>';
}
?>

<!-- Headers of the iFrame -->
<html>
<head>
<script type="text/javascript" src="<?php echo _APP_ROOT_; ?>/webapp/javascript/jquery-1.4.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo _APP_ROOT_; ?>/webapp/themes/default/css/common.css">
<!--[if IE]><link rel="stylesheet" type="text/css" href="<?php echo _APP_ROOT_; ?>/webapp/themes/default/css/common_ie.css"><![endif]-->
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="<?php echo _APP_ROOT_; ?>/webapp/themes/default/css/common_ie_6.css"><![endif]-->
</head>

<!-- Body of the iFrame -->
<body style="width: 100%; height: 100%; overflow: hidden;">
<div style="background-color: #F3F3F3; padding: 8px;">
<div style="width: 100%;"><span id="subject" style="font-size:175%; font-family: Verdana;"><?php echo $mail['event']; ?></span></div>
<div style="display: inline;"><span style="display: inline-block; width: 60px; color: #5F5F5F; font-family: Verdana,Arial; font-size: 12px; font-weight: bold; letter-spacing: 0; height: 15px;">From : </span><span style="display: inline-block; width: 200px; color: #8F8F8F; font-family: Verdana,Arial; font-size: 11px; padding-left: 4px; height: 15px;"><?php echo $mail['sent_by']; ?></span></div>
<div style="display: inline; float: right; margin-right: 12px;"><span style="display: inline-block; width: 60px; color: #5F5F5F; font-family: Verdana,Arial; font-size: 12px; font-weight: bold; letter-spacing: 0; height: 15px;">Sent : </span><span style="display: inline-block; color: #8F8F8F; font-family: Verdana,Arial; font-size: 11px; padding-left: 4px; height: 15px;"><?php echo $mail['date_sent']; ?></span></div>
<div><span style="display: inline-block; width: 60px; color: #5F5F5F; font-family: Verdana,Arial; font-size: 12px; font-weight: bold; letter-spacing: 0; height: 15px;">To : </span><span style="display: inline-block; width: 200px; color: #8F8F8F; font-family: Verdana,Arial; font-size: 11px; padding-left: 4px; height: 15px;"><?php echo $mail['name']; ?></span></div>
</div>
<!--div style="text-align: right; position: relative; top: -28px; padding-right: 12px;"><span style="color: #5F5F5F; font-family: Verdana,Arial; font-size: 12px; font-weight: bold; letter-spacing: 0; padding: 0 5px; height: 15px;">Sent : </span><span style="color: #8F8F8F; font-family: Verdana,Arial; font-size: 11px; padding-left: 4px; height: 15px;"><?php echo $mail['date_sent']; ?></span></div-->
<div id="mailbody" style="width: 100%; text-align: center; margin: 0 auto 0 auto; border-top: 1px solid #E5E5E5; overflow-y: scroll; display: none;">
<div style="margin: 16px 8px 8px 8px; color: #8F8F8F; font-family: Verdana,Arial; padding-left: 4px; text-align: justify; font-size: 12px;"><?php echo $message; ?></div>
</div>

<!-- Close the tags -->
</body>
</html>
