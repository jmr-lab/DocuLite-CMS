<?php
/**
 * Create a new mail in the system
 *
 * This interface creates a new mail in the system
 * when a user fill the contact form.
 *
* @author	Jean-Marie Roy
* @version	3.0
*/

include 'interface.php';
$request = new JcHttpServletRequest();
$servereventmgr = new JcServerEventManager($request);

JcLogger::info('newmail::newmail::Begin');
JcLogger::info('newmail::newmail::Trace - Name : '.$request->getParameter('name'));
JcLogger::info('newmail::newmail::Trace - Email : '.$request->getParameter('email'));
JcLogger::info('newmail::newmail::Trace - Subject : '.$request->getParameter('subject'));
JcLogger::info('newmail::newmail::Trace - Message : '.substr($request->getParameter('message'), 0, 50));
//JcLogger::info();
//header('Content-Type: text/html; charset=iso-8859-1');

/**
 * Create the new message (main)
 */
try
{
	// Init all fields
	$name = trim($request->getParameter('name'));
	$email = trim($request->getParameter('email'));
	$type = trim($request->getParameter('type'));
	$subject = trim($request->getParameter('subject'));
	$message = trim($request->getParameter('message'));
	$recipient = ($type == 1 ? 'World' : 'Administrators');
	$sender = ($type == 1 ? $name : $name.' <'.$email.'>');

	// ------------------------
	// Begin with verifications
	// ------------------------

	// Check the validationUID
	if (!isset($_SESSION['validationUID']))	{throw new JfException('Invalid UID');}
	if ($_SESSION['validationUID'] <> trim($request->getParameter('validationUID')))	{throw new JfException('Invalid UID');}

	// All fields are mandatory
	if ($email == '' && $type <> 0)	{throw new JfException('The email field is mandatory');}
	if ($subject == '')	{throw new JfException('The subject field is mandatory');}
	if ($message == '')	{throw new JfException('The message field is mandatory');}
	JcLogger::info('newmail::newmail::All fields contain something');
	// Extra verification : the email address must contain an '@' and a '.' after it
	//	$email = 'john.smith@myemail.com.au'
	//	$emailArr = ['john.smith', 'myemail.com.au']	There must be only one '@' symbol
	//	$extension = ['myemail', 'com', 'au']			There must be at least one '.' symbol
	$emailArr = explode("@", $email);
	$extension = explode(".", $emailArr[1]);
	if (sizeof($emailArr) <> 2 && $type <> 0)	{throw new JfException('The email is not valid');}
	if (sizeof($extension) < 1 && $type <> 0)	{throw new JfException('The email is not valid');}
	JcLogger::info('newmail::newmail::Email address is valid');
	// TODO - Optional : check that there is no 'stupid' word in the message

	// All fields have been validated
	// Check that someone is not flooding the system
	$date1 = time();							// Get current time
	$date2 = mktime(0,0,0,01,01,2000);			// Get the timestamp of the 1st of January 2000
	if (isset($_SESSION['date_last_email']))	{$date2 = $_SESSION['date_last_email'];}
	$dateDiff = $date1 - $date2;				// $dateDiff = N seconds
	$_SESSION['date_last_email'] = $date1;
	if ($dateDiff <= 30)	{throw new JfException('You have sent an email recently. Please wait a couple of minutes before re-trying.');}

	// ------------------------
	// End of verifications
	// ------------------------

	// // Send the email to the 'Administrators' group
	JcLogger::info('newmail::newmail::Sending mail');
	// Get the session
	$sessionmanager = new JfSessionManager();
	try
	{
		$session = $sessionmanager->getSession('www_jmroy');
	}
	catch (JfException $e)
	{
		$identity = array('repository' => 'www_jmroy', 'username' => 'guest', 'password' => '');
		$sessionmanager->setIdentity('www_jmroy', $identity);
		$sessionmanager->authenticate('www_jmroy');
		$session = $sessionmanager->getSession('www_jmroy');
	}

	// Send a message
//	$session->sendToDistributionListEx(array($recipient), null, $subject, $message, null, null, false);

	// Create a queue item object
	$queueItem = $session->newObject('jmi_queue_item');
	$queueItem->setValue('name', $recipient); 
	$queueItem->setValue('event', $subject); 
	$queueItem->setValue('sent_by', $name);
	if (strlen($message) < 255)	{$queueItem->setValue('message', $message);}
	$queueItem->setValue('supervisor_name', $email);
	$queueItem->setValue('date_sent', date("Y-m-d H:i:s"));
	$queueItem->save();
	$queueId = $queueItem->getValue('r_object_id');

	// Only create a new workflow if the message size if more than 255
	JcLogger::info('newmail::newmail::strlen(message) : '.strlen($message));
	if (strlen($message) >= 255)
	{
		// Create a new Document and its associated content object
		$perObj = $session->newObject('jm_document');
		$document = JfUtils::cast($perObj, 'JfSysObject');
		$document->setObjectName($subject);
		// TODO - Set an ACL / owner / location to the document
//		$document->setACL($_POST['acl']);
//		$document->setValue('a_content_type', $this->getFormat($_POST['format']));
//		$document->setRepeatingValue('i_folder_id', '0', $this->getFolderId());
		// Create a temporary file : 20120510084523012.eml
		$milli = floor(1000 * microtime());
		while (strlen($milli) < 3)	{$milli = '0'.$milli;}
		$fileName = '/temp/'.date("YmdHis").$milli.'.eml';
		$file = fopen(_SERVER_ROOT_.$fileName, "w");
		fputs($file, $message);
		fclose($file);
		$document->setFile($fileName);
		$document->setValue('owner_name', 'admin');
		$document->save();
		$documentId = $document->getValue('r_object_id');
		JcLogger::info('documentId : '.$documentId);
		
		// Get the current user name (default is 'guest')
		$user = $session->getLoginInfo();
		JcLogger::info('user : '.$user->getValue('user_name'));
		// // Create a new Workflow and its associated woritem and package
		$workflow = $session->newObject('jm_workflow');
		$workflow->setValue('object_name', date("d/m/Y H:i:s").' QuickFlow'); 
		$workflow->setValue('supervisor_name', $user->getValue('user_name')); 
		$workflow->setValue('r_creator_name', $user->getValue('user_name')); 
		$workflow->setValue('r_start_date', date("Y-m-d H:i:s")); 
		$workflow->save();
		$workflowId = $workflow->getValue('r_object_id');
		JcLogger::info('workflowId : '.$workflowId);
		$workItem = $session->newObject('jmi_workitem');
		$workItem->setValue('r_creation_date', date("Y-m-d H:i:s"));
		$workItem->setValue('r_workflow_id', $workflowId); 
		$workItem->setValue('r_queue_item_id', $queueId); 
		$workItem->save();
		$workItemId = $workItem->getValue('r_object_id');
		JcLogger::info('workItemId : '.$workItemId);
		$package = $session->newObject('jmi_package');
		$package->setValue('r_workflow_id', $workflowId); 
		$package->setRepeatingValue('r_component_id', 0, $documentId); 
		$package->save();
		$packageId = $package->getValue('r_object_id');
		JcLogger::info('packageId : '.$packageId);
		// Unlink (delete) the temporary file
		unlink(_SERVER_ROOT_.$fileName);
	}

	// Successful message (log)
	JcLogger::info('newmail::newmail::Mail sent');

	// Unset the validationUID
	unset($_SESSION['validationUID']);

	// Set the success message
	$strMessage = '<div class="label"><label>Name :</label>'.$name.'</div>';
	$strMessage .= '<div class="label"><label>Email :</label>'.$email.'</div>';
	$strMessage .= '<div class="label"><label>Type :</label>'.(($type == '0') ? 'Public' : 'Private').'</div>';
	$strMessage .= '<div class="label"><label>Subject :</label>'.$subject.'</div>';
	$strMessage .= '<div class="label"><label>Comments :</label>'.substr($request->getParameter('message'), 0, 255).'</div>';
	$strMessage .= '<div class="label">Thank you for your contribution. I will try to reply to you shortly. Have a nice day.</div>';

	// If in Prod, send an email
	if ($_SERVER['SERVER_NAME'] <> 'localhost')
	{
		$ret = mail("jean-marie.roy@hotmail.fr", "Estancia - $subject", "$message", "from : $email");
		if ($ret)	{$strMessage .= '<div style="display: none;">Message sent successfully.</div>';}
		else		{$strMessage .= '<div style="display: none;">Message could not be sent.</div>';}
	}
}
catch (JfException $e)
{
	// TODO - Display the contact page with the error message
	$strMessage = '<div class="label"><label>Name :</label>'.$name.'</div>';
	$strMessage .= '<div class="label"><label>Email :</label>'.$email.'</div>';
	$strMessage .= '<div class="label"><label>Type :</label>'.(($type == '0') ? 'Public' : 'Private').'</div>';
	$strMessage .= '<div class="label"><label>Subject :</label>'.$subject.'</div>';
	$strMessage .= '<div class="label"><label>Comments :</label>'.substr($request->getParameter('message'), 0, 50).'</div>';
	$strMessage .= '<div class="warning">'.$e->getMessage().'</div>';
	JcLogger::info('newmail::newmail::JfError : '.$e->getMessage());
}
JcLogger::info('newmail::newmail::End');
?>
<?php
// Turn on output buffering with zlib compression
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
?>
<span><?php echo $strMessage; ?></span>
