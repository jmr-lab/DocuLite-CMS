<?php
/**
 * An audittrail manager.
 *
 * @package com.core.common
 * @author Jean-Marie Roy
 * @copyright Jean-Marie Roy 2011
 * @version 3.0
 */
class JfAuditTrailManager
{
	/**
	* Session
	*
	* @access private
	* @var JfSession
	*/
	private $session;

	/**
	 * Constructor
	 *
	 * This function initialize the audit trail manager
	 *
	 * @throws JfException if a server error occurs
	 */
	public function __construct($session)
	{
		$this->session = $session;
	}

	/**
	 * Create an audit.
	 *
	 * Creates an audit trail entry for application events.
	 * Use this method in an application to create an audit trail entry of type jm_audittrail for application events.
	 * Using this method to create a jm_audittrail object automatically sets many of the attributes in the object and saves the object when the method completes.
	 * (If you used JfSession->newObject to create the audittrail object, you have to set each attribute individually and call save().)
	 * This method does not require a save method to save the audittrail entry.
	 * However, if you call createAudit in an explicit transaction, the audit trail object is not created until the transaction is explicitly committed.
	 * Anyone can use createAudit. No special permissions or privileges are needed. 
	 *
	 * @access public
	 * @param JfId objectId - Identifies the object of the audited event. Use the object's object ID.
	 * @param String event - Identities the audited event. Use the event's name.
	 * @param array stringArgs - Array of up to 5 additional string arguments
	 * @param JdIf idArgs - Array containing the user name, ID and IP address
	 * @return JfId ID of jm_audittrail object.
	 * @throws JfException if a server error occurs.
	 */
	public function createAudit($objectId, $event, $stringArgs, $idArgs)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'('.$objectId.', '.$event.', $stringArgs, $idArgs)');
		try
		{
			// Check that there is an event name
			if ($event == '')	{throw new JfException('AUDIT_INVALID_EVENT');}
			// Prepare the arguments
			$strArgument = array();
			$strArgumentName = array();
			foreach ($stringArgs as $key=>$value)
			{
				if ($key <> 'userName' && $key <> 'userIP')	{$strArgumentName[] = $key;	$strArgument[] = $value;}
			}
			// Initialize the attributes/values arrays
			$args = array(0 => '', 1 => '', 2 => '', 3 => '', 4 => '');
			$strArgument = $strArgument + $args;
//			$strArgumentName = array(0 => 'browser', 1 => 'os', 2 => 'referer', 3 => 'language', 4 => 'details');
			// Get the time and the user details
			$userName = $stringArgs['userName'];
			$userIP = $stringArgs['userIP'];
			// Get a new ID
			$session = $this->session;
//			$newId = JfUtils::getNewId($this->session, 'jm_audittrail');
			$docbaseId = $session->getDocbaseId();
			$newId = '0000000000000000';
			$extension = '5f';
			// Get the latest object ID
			$sql  = 'SELECT r_object_id FROM jm_audittrail_s ORDER BY r_object_id DESC LIMIT 0, 1;';
			$query = new JfQuery();
			$query->setSQL($sql);
			$results = $query->execute($session);
			$extension = substr($results->getValue('r_object_id'), 0, 2);
			$newId = substr($results->getValue('r_object_id'), 9, 7);
			$newShortId = 1 + JfUtils::getDecimal($newId);
			if ($extension == '00')	{throw new JfException('OBJECT_INVALID_EXTENSION');}
			$newId = $extension.JfUtils::getHexaDecimal($docbaseId).JfUtils::getHexaDecimal($newShortId);
			// Audit the event
			$query = new JfQuery();
			// $insert = "r_object_id, audited_obj_id, event_name, time_stamp, user_id, session_id";
			// $values = "'".$newId."', '".$objectId."', '".$event."', '".date("Y-m-d H:i:s")."', '".$userName."', '".$userIP."'";
			// for($i = 0; $i < 5; $i++)
			// {
				// if ($strArgument[$i] <> '')
				// {
					// $insert .= ", string_".($i + 1);
					// $values .= ", '".$strArgument[$i]."'";
				// }
			// }
			$insert = "r_object_id, audited_obj_id, event_name, time_stamp, user_id, user_ip";
			$values = "'".$newId."', '".$objectId."', '".$event."', '".date("Y-m-d H:i:s")."', '".$userName."', '".$userIP."'";
			for($i = 0; $i < 5; $i++)
			{
				if ($strArgument[$i] <> '')
				{
					$insert .= ", attr_".($i + 1);
					$values .= ", '".$strArgumentName[$i]."'";
					$insert .= ", value_".($i + 1);
					$values .= ", '".$strArgument[$i]."'";
				}
			}

			$sql = "INSERT INTO jm_audittrail_s (".$insert.") VALUES (".$values.")";
			$query->setSQL($sql);
			try	{$result = $query->execute($session);}
			catch (JfException $exception)	{}
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}
}
?>