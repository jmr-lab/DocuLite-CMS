<?php
/**
 * Upload a file to the server
 *
 * This interface upload a file to the server
 *
* @author	Jean-Marie Roy
* @version	3.0
*/
include 'interface.php';

/**
 * Get the message of a notification
 *
 * This function retrieves the message of a notification
 * by looking first for a content file associated with
 * a package linked to this notification.
 *
 * The status can be :
 * 0 if nothing was done
 * 1 if no file was uploaded (it may be normal)
 * 2 if a file was uploaded correctly
 * 3 if an error occured
 *
 * @param ID the object ID of the notification (queue item object)
 * @param String the message to change
 * @return String the message itself
 */
$strStatus = '0'; $strErrorMessage = ''; $strMessage = '';
JcLogger::debug('Call to fileimportmgr');
try
{
	// As we can upload several files at the same time, parse the files :
	foreach ($_FILES['file'] as $index => $files)
	{
		foreach ($files as $key => $value)
		{
			JcLogger::debug('FILE['.$index.']['.$key.'] : '.$value);
			if ($index == 'name')	$arrFiles[] = $key;
		}
	}
	// For each file uploaded
	foreach ($arrFiles as $index => $value)
	{
		JcLogger::debug('arrFile['.$index.'] : '.$value);
		// If no file was uploaded set the strErrorMessage
		if (	!empty($_FILES['file']['error'][$value])
			||	empty($_FILES['file']['tmp_name'][$value])
			||	$_FILES['file']['tmp_name'][$value] == 'none'	)
		{
			$strErrorMessage = 'No file was uploaded...';
			$strStatus = '1';
		}
		else
		{
			// Otherwise create a folder to store the uploaded file
			// Such as /estancia/temp/<user_id>/file.doc
			$sessionmanager = new JfSessionManager();
			$session = $sessionmanager->getSession('www_jmroy');
			// Create a temporary area associated with the user Id
			$userFolder = '/temp/'.$session->getLoginUserId();
			if (!file_exists(_SERVER_ROOT_.$userFolder))	{mkdir(_SERVER_ROOT_.$userFolder);}
			// $target_path = '/temp/11001e240200108d/mydocument.docx'
			$target_path = _SERVER_ROOT_.$userFolder.'/'.basename($_FILES['file']['name'][$value]);
			!@move_uploaded_file($_FILES['file']['tmp_name'][$value], $target_path);
			$strMessage = 'File Name : '.$_FILES['file']['name'][$value].', ';
			$strMessage .= 'File Size : '.@filesize($target_path);
			JcLogger::debug('Uploaded file to target : '.$target_path);
			$strStatus = '2';
		}
	}
}
catch (Exception $exception )
{
	JcLogger::error('Exception : '.$exception);
	$strErrorMessage = $exception;
	$strStatus = '3';
}
// Send the message and eventually the error message to the output
echo "	{
			error: '".$strErrorMessage."',
			status: '".$strStatus."',
			msg: '" . $strMessage . "'
		}";
// Log the messages (TODO : remove)
JcLogger::debug('Error : '.$strErrorMessage);
JcLogger::debug('Status : '.$strStatus);
JcLogger::debug('Message : '.$strMessage);
?>