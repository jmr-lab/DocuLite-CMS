<?php
/**
 * Display a notification
 *
 * This interface displays a notification / email
 * specified by its ID.
 *
* @author Jean-Marie Roy
* @version 1.0
*/

$path = substr($_SERVER['PHP_SELF'], 1);
$_APP_ROOT_ = '/'.substr($path, 0, strpos($path, '/'));
$_SERVER_ROOT_ = $_SERVER["DOCUMENT_ROOT"].$_APP_ROOT_;
define('_APP_ROOT_', '_APP_ROOT_');
define('_SERVER_ROOT_', '_SERVER_ROOT_');
require_once $GLOBALS[_SERVER_ROOT_].'/webapp/classes/com/core/object/ACL.php';
require_once $GLOBALS[_SERVER_ROOT_].'/webapp/classes/com/core/object/Document.php';
require_once $GLOBALS[_SERVER_ROOT_].'/webapp/classes/com/core/object/Group.php';
require_once $GLOBALS[_SERVER_ROOT_].'/webapp/classes/com/core/object/SysObject.php';
require_once $GLOBALS[_SERVER_ROOT_].'/webapp/classes/com/core/object/User.php';
require_once $GLOBALS[_SERVER_ROOT_].'/webapp/classes/com/core/logs/Log.php';
require_once $GLOBALS[_SERVER_ROOT_].'/webapp/classes/com/webtop/config/Context.php';
require_once $GLOBALS[_SERVER_ROOT_].'/webapp/classes/com/webtop/config/Component.php';
require_once $GLOBALS[_SERVER_ROOT_].'/webapp/classes/com/core/logs/Log.php';
require_once $GLOBALS[_SERVER_ROOT_].'/webapp/classes/com/core/Session.php';
@session_start();
error_reporting(0);

Log::write('showmail::showmail::Begin', 5);
header('Content-Type: text/html; charset=iso-8859-1');
Log::write('showmail::showmail::Trace - objectID : '.$_POST['objectId'], 5);

/**
 * Get the message of a notification
 *
 * This function retrieves the message of a notification
 * by looking first for a content file associated with
 * a package linked to this notification.
 *
 * @param ID the object ID of the notification (queue item object)
 * @param String the message to change
 * @return String the message itself
 */
function getMessage($queueId, $message)
{
	// Get the message specified by its queue item ID
	$tmpmessage = '';

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
	$query = new myQuery();
	// Get the workflow ID from the workItem object defined by $queueId
	$sql = 'SELECT r_workflow_id FROM jmi_workitem_sp WHERE r_queue_item_id = \''.$queueId.'\'';
	// Get the package ID defined by the workflow ID
	$sql = 'SELECT r_object_id FROM jmi_package_sp WHERE r_workflow_id IN ('.$sql.')';
	// Get the document ID from the package
	$sql = 'SELECT r_component_id FROM jmi_package_rp WHERE i_position = -1 AND r_object_id IN ('.$sql.')';
//	$sql = 'SELECT r_component_id FROM jmi_package_rp WHERE i_position = -1 AND r_object_id IN (SELECT r_object_id FROM jmi_package_sp WHERE r_workflow_id IN (SELECT r_workflow_id FROM jmi_workitem_sp WHERE r_queue_item_id = \''.$queueId.'\'))';
	// Run the query
	$req = $query->setSQL($sql);
	$data = $query->execute();

	// Read the content of the document
	// and send the output to the $tmpmessage string
	$documentId = trim($data['r_component_id']);
	if ($documentId <> '' && $documentId <> '0000000000000000')
	{
		$session = new Session();
		$document = $session->getDocument($documentId);
		$tmpmessage = file_get_contents($document->getFile());
	}

	return ($tmpmessage == '') ? $message : $tmpmessage;
}

/**
 * Get the notification (main)
 */
try
{
	// Get the message specified by its ID
	// For security reasons, only retrieve the message specified by its ID AND recipient name (equals to the current user name)
	// If someone is trying to view a message not sent to him, the query won't return anything
	$query = new myQuery();
	// Get all the groups the current user belongs to
	$sql = 'SELECT group_name FROM jm_group_s WHERE r_object_id IN (SELECT i_group_id FROM `v_users_groups` WHERE r_object_id = \''.$_SESSION['userId'].'\')';
	// Get the notification specified by its ID and recipient
	$sql = 'SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp, read_flag FROM jmi_queue_item_s WHERE r_object_id = \''.$_POST['objectId'].'\' AND (name = \''.$_SESSION['user_name'].'\' OR name IN ('.$sql.'))';
//	$sql = 'SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp FROM jmi_queue_item_s WHERE r_object_id = \''.$_POST['objectId'].'\' AND (name = \''.$_SESSION['user_name'].'\' OR name IN (SELECT group_name FROM jm_group_s WHERE r_object_id IN (SELECT i_group_id FROM `v_users_groups` WHERE r_object_id = \''.$_SESSION['userId'].'\')))';
	// Run the query
	$req = $query->setSQL($sql);
	$data = $query->execute();

	// If there is an unread notification to display
	// Set the read_flag, dequeued_by and dequeued_date attributes
	if ($data['r_object_id'] <> '' && $data['read_flag'] <> '1')
	{
		$query = new myQuery();
		$sql = 'UPDATE jmi_queue_item_s OBJECTS SET read_flag = 1, dequeued_by = \''.$_SESSION['user_name'].'\', dequeued_date = \''.date("Y-m-d H:i:s").'\' WHERE r_object_id = \''.$_POST['objectId'].'\' AND dequeued_by = \'\'';
		$req = $query->setSQL($sql);
	}

	// Set the subject (event), sender and recipient
	$subject = $data['event'];
	$sender = htmlentities($data['sent_by']);
	$recipient = $data['name'];

	// Get the message itself
	// First get the message from the queue Item object
	// from the 'message' or 'event_details' fields
	// and then try to get the message from a content file
	// associated with a package linked to this queue item.
	$message = ($data['message'] == '') ? $data['event_detail'] : $data['message'];
	$message = getMessage($data['r_object_id'], $message);

	// If the message is a reply to another message
	// display the other message after the current one
	// and so on and so forth
	if (trim($data['stamp']) <> '' && $data['stamp'] <> '0000000000000000')
	{
		$parentId = trim($data['stamp']);
		while ($parentId <> '' && $parentId <> '0000000000000000')
		{
			$query = new myQuery();
			$sql = 'SELECT r_object_id, event, item_name, date_sent, sent_by, message, event_detail, name, stamp FROM jmi_queue_item_s WHERE r_object_id = \''.$parentId.'\' AND (name = \''.$_SESSION['user_name'].'\' OR name IN (SELECT group_name FROM jm_group_s WHERE r_object_id IN (SELECT i_group_id FROM `v_users_groups` WHERE r_object_id = \''.$_SESSION['userId'].'\')))';
			$req = $query->setSQL($sql);
			$data = $query->execute();

			$parentId = (trim($data['stamp']) <> '' && $data['stamp'] <> '0000000000000000') ? $data['stamp'] : '';
			$message .= '<div class="line" style="margin: 16px 0 16px 0; background-color:#628DC7;"><!----></div>';
			$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">From : </span><span class="td_status" style="height: 15px;">'.$data['sent_by'].'</span></div>';
			$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">To : </span><span class="td_status" style="height: 15px;">'.$data['name'].'</span></div>';
			$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">Date : </span><span class="td_status" style="height: 15px;">'.$data['date_sent'].'</span></div>';
			$message .= '<div style="margin-left: -4px;"><span class="td_file_name" style="height: 15px;">Subject : </span><span class="td_status" style="height: 15px;">'.$data['event'].'</span></div><br>';
			$tmpmessage = ($data['message'] == '') ? $data['event_detail'] : $data['message'];
			$message .= getMessage($data['r_object_id'], $tmpmessage);
		}
	}
}
catch (Exception $e)
{
	// TODO - Display the message page with the error message
	$message = '<div style="font-weight: bold;">An error occured while retrieving this message. Please contact the administrator for more details.</div>';
	Log::write('showmail::showmail::Error : '.$e->getMessage(), 5);
}
Log::write('showmail::showmail::End', 5);
?>

<div id="mail">
	<div id="mailpage" style="padding: 8px; background-color: white; border: 1px solid black; border-width: 1px 3px 3px 1px;margin-bottom: 16px;">
		<div style="width: 100%"><span style="font-size:175%;"><?php echo $subject; ?></span></div>
		<div><span class="td_file_name" style="height: 15px;">From : </span><span class="td_status" style="height: 15px;"><?php echo $sender; ?></span></div>
		<div><span class="td_file_name" style="height: 15px;">To : </span><span class="td_status" style="height: 15px;"><?php echo $recipient; ?></span></div>
		<div id="mailbody" style="width: 100%; text-align: center; margin: 8px auto 0 auto; border-top: 1px solid #628DC7; overflow-y: scroll;">
			<div class="td_status" style="text-align: justify; margin: 16px 8px 0 0; font-size: 12px;"><?php echo $message; ?></div>
		</div>
	</div>
</div>

<script>
function redimmail()
{
	sheight = ($(window).height() + 50) / 2;
	$('#mail').css({
		height: sheight + 'px'
	});
	sheight = ($(window).height() - 200) / 2;
	$('#mailpage').css({
		height: sheight + 'px'
	});
	sheight = 8 + ($(window).height() - 350) / 2;
	$('#mailbody').css({
		height: sheight + 'px'
	});
}
</script>
