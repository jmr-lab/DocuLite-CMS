<?php
/**
 * Import webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwImport extends JwComponent
{
	/**
	 * Get the user's name
	 *
	 * @access	public
	 */
	public function getUserName()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the user login info
		$user = $this->user;
		return $user->getValue('user_name');
	}

	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function onOk()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Get the request and session objects
		$request = new JcHttpServletRequest();
		$httpSession = $request->getSession();
		// Get the data sent to the server
		$object_name = $request->getParameter('object_name');
		$format = $request->getParameter('formats');
		$aclId = $request->getParameter('permissions');
		$file = $request->getParameter('file');
		// foreach ($file as $key => $value)	{JcLogger::info('file['.$key.'] : '.$value);}
		// Get current folder ID
		$pathSO = $httpSession->getAttribute('path');
		$containerId = (sizeof($pathSO) > 0 ? $pathSO[sizeof($pathSO) - 1] :'0000000000000000');
		// Show the parameters
		$object_name = basename($object_name);
		// JcLogger::info('$object_name : '.$object_name);
		// JcLogger::info('$formatId : '.$formatId);
		JcLogger::info('$containerId : '.$containerId);
		// JcLogger::info('$aclId : '.$aclId);
		// If the current component is 'home', then use the user default folder
		$component = current($httpSession->getAttribute('component'));
		if ($component == 'home')	{$user = $this->user; $containerId = $user->getValue('default_folder');}
		// Use of JfImportOperation
		$importOperation = new JfImportOperation();
		$session = $this->session;
		$strFileName = "/temp/".$session->getLoginUserId()."/".basename($file[1]);
		$importOperation->setFormat($format);
		$importOperation->setACL($aclId);
		$importOperation->setFolderId($containerId);
		// Add the node
		$importOperation->add(array('path' => $strFileName, 'name' => $object_name));
		// Run the import operation
		$importOperation->execute();
		return '';
	}

	/**
	 * Method called when an return event is called on the current component.
	 *
	 * @access	public
	 */
	public function selectFormat()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
//		echo "<script>if ($('#object_name').val() == '')	$('#object_name').val($('#file').val());</script>";
		echo "<script>$('#object_name').val($('#file').val());</script>";
		// Get the request and session objects
		$request = new JcHttpServletRequest();
		// Get the data sent to the server
		$arrFileName = $request->getParameter('file');
		$strFileName = $arrFileName[0];
		// JcLogger::info('strFileName : '.$strFileName);
		if (strripos(basename($strFileName), '.') >= 0)
		{
			echo "<script>";
			$formats = '';
			$dos = substr($strFileName, strripos($strFileName, '.') + 1);
			// JcLogger::info('dos : '.$dos);

			$query = new JfQuery();
			$sql = "SELECT r_object_id, name, description,
							(SELECT COUNT(r_object_id) FROM jm_sysobject_s WHERE a_content_type = name) AS number
					FROM jm_format_sp
					WHERE jm_format_sp.dos_extension = '".$dos."'
					ORDER BY number DESC, description ASC";
			$query->setSQL($sql);
			$result = $query->execute($this->session);
			while ($result->next())	{$formats .= '<OPTION VALUE="'.$result->getValue('name').'">'.$result->getValue('description').'</OPTION>';}
			if (strlen($formats) > 0)	echo "$('#formats').html('".$formats."');";
			echo "</script>";
		}
	}
}
?>