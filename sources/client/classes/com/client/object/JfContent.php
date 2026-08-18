<?php
/**
 * An Estancia content object.
 *
 * This is the base type for manipulating the content of all objects stored in the repository.
 *
 * $contentObj = new JfContent($session);
 * $contentObj->setContent('/usr/tmp/Word Document.doc');
 * $strObjectId = $contentObj->save();
 *
 * @package		com.core.object
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfContent
{
	/**
	* Path to the content file
	*
	* @todo		Check if it is really needed in this class
	* @access	private
	* @var		String
	*/
	private $content;

	/**
	* Session through which the object was originally requested.
	*
	* @todo		Check if it is really needed in this class
	* @access	private
	* @var		JfSession
	*/
	private $session;

	/**
	 * Constructor
	 *
	 * @param	JfSession	The session object that called this class.
	 * @todo	Check if input is a valid array for this class in case it is not a string
	 * @throws	JfException - if a server error occurs
	 */
	function __construct($session)
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		// Set the session
		$this->session = $session;
	}

	/**
	 * Get the content type.
	 *
	 * This method is called from the main save method.
	 *
	 * @access	public
	 * @return	String			The content type ('msw8'...)
	 * @throws	JfException		if a server error occurs
	 */
	public function getContentType()
	{
		// Logger
		JfLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		try
		{
			// Set up the content type
			$strContentType = '';
			// We can't get the content type if no content has been set
			if ($this->content == null)	{throw new JfException('CONTENT_FILE_MISSING');}
			// Retrieve the content type
			// Get the DOS extension of the current object
			$dos_extension = JfUtils::getDOSExtension($this->content);
			$query = new JfQuery();
			$sql = "SELECT name, (SELECT COUNT(r_object_id) FROM jm_sysobject_s WHERE a_content_type = name) AS number 
					FROM jm_format_sp 
					WHERE jm_format_sp.dos_extension = '".$dos_extension."' 
					ORDER BY number ASC";
			$query->setSQL($sql);
			$result = $query->execute($this->session);
			while ($result->next())	{$strContentType = $result->getValue('name');}
			// If no type was found then return the DOS extension
			if ($strContentType == '')	{$strContentType = $dos_extension;}
			return $strContentType;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Set the content.
	 *
	 * @access	public
	 * @param	content	the name of the content
	 * @throws JfException - if a server error occurs
	 */
	public function setContent($content)
	{
		try
		{
			// Set the content
			$this->content = $content;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

	/**
	 * Save the content to the repository.
	 *
	 * @access	public
	 * @throws JfException - if a server error occurs
	 */
	public function save()
	{
		try
		{
			// The new content is stored in the protected 'content' variable
			// $this->content = '/usr/tmp/Word Document.doc';
			if (!is_file(_SERVER_ROOT_.$this->content))	{throw new JfException ('OBJECT_SAVE_FILE_DOESNT_EXIST');}
			// Try to find a content file with the same hash (to avoid having the same file twice in the repository)
			$hash = md5_file(_SERVER_ROOT_.$this->content);
			$query = new JfQuery();
			$sql = "SELECT r_object_id FROM jmr_content_s WHERE r_content_hash = '".$hash."'";
			$query->setSQL($sql);
			$result = $query->execute($this->session);
			$contentId = $result->getValue('r_object_id');
			// No content file found. Create a new content object
			if ($contentId == '' || $contentId == null)
			{
				$contentId = JfUtils::getNewId($this->session, 'jmr_content');
				$sql = "INSERT INTO jmr_content_s (r_object_id, r_content_hash) VALUES ('".$contentId."', '".$hash."')";
				$query->setSQL($sql);
				$result = $query->execute($this->session);
				// Copy the content file to the repository
				$target = _DOCUMENT_ROOT_.'/data/content_storage_01/'.$contentId.JfUtils::getDOSExtension($this->content);
				// @todo - JfFile
				$filemgr = new JfFile();
				$filemgr->move(_SERVER_ROOT_.$this->content, $target);
			}
			return $contentId;
		}
		catch (JfException $exception)
		{
			$exception->append(__CLASS__.'.'.__FUNCTION__."(".__FILE__.":".__LINE__.")");
			throw $exception;
		}
	}

}

?>