<?php
/**
 * An Estancia Queue Item (mail) object.
 *
 * This interface contains the functionality to retrieve information from a jmi_queue_item object. 
 *
 * @package		com.core.object
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfQueueItem extends JfPersistentObject
{
	/**
	* Permissions
	*
	* @access	public
	* @var		int
	*/
	public static $JF_PERMIT_NONE = 1;
	public static $JF_PERMIT_BROWSE = 2;
	public static $JF_PERMIT_READ = 3;
	public static $JF_PERMIT_RELATE = 4;
	public static $JF_PERMIT_VERSION = 5;
	public static $JF_PERMIT_WRITE = 6;
	public static $JF_PERMIT_DELETE = 7;

	/**
	 * Adds a new rendition to the object. This operation is not committed until a save or checkin is performed.
	 *
	 * The following code example demonstrates how to add a Word Perfect rendition to the sysobject :
	 *
	 * $sysObj = $sess->getObjectByQualification("dm_document where r_object_id='0900d5bb8001f900'");
	 * $sysObj = JfUtils::cast($sysObj, 'JfSysObject');
	 * $sysObj->addRendition("chap_1.wp7", "wp7");
	 * $sysObj->save();
	 *
	 *
	 * @access	public
	 * @param	String		fileName - specifies the file that contains the content.
	 * @param	String		formatName - specifies the content's file format.
	 * @throws	JfException	if a server error occurs
	 */
	public function addRendition($fileName, $formatName)
	{
		try
		{
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__.'('.__FILE__.':'.__LINE__.')');
			throw $exception;
		}
	}
}
?>