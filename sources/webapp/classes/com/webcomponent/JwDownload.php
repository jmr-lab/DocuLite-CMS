<?php
/**
 * Download webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDownload extends JwComponent
{
	/**
	 * Path to the content file
	 *
	 * @access	protected
	 * @var		String
	 */
	protected $content;

	/**
	 * JfSysObject
	 *
	 * @access	private
	 * @var		JfSysObject
	 */
	private $sysObj;

	/**
	 * Get the link to the content of the object
	 *
	 * @access	public
	 * @return	String	the link
	 */
	public function getLink()
	{
		$link = '<a href="'._APP_ROOT_.$this->content.'" style="text-decoration:none;">';
		$link .= '<img src="'._APP_ROOT_.'/webapp/themes/default/images/icons/floppy_16.png" style="width: 16px; height: 16px; margin: 4px; vertical-align: middle;">';
		$link .= '<span style="display: inline-block; width: 260px; cursor: pointer; cursor: hand; margin-top: 0px; font-weight: bold; vertical-align: middle;">'.$this->getString('DOWNLOAD').'</span>';
		$link .= '</a>';
		if ($this->isCorrupted())	{$link = '<div class="errormessage">'.$this->getString('CONTENT_INACCESSIBLE').'</div>';}
		return $link;
	}

	/**
	 * Get the icon associated with an object
	 * $object = array('r_object_id' => '0b001e2400786532', 'r_object_type' => 'jm_document', 'a_content_type' = > 'png', ...)
	 *
	 * @access	public
	 * @param	boolean	path	whether we need the path or not
	 * @return	String	the icon name
	 */
	public function getObjectIcon($path = false)
	{
		if ($this->isCorrupted())	{echo 'lock.png'; return;}
		$perObj = $this->perObj;
		$propArr = array(	'r_object_id' => $perObj->getValue('r_object_id'),
							'r_object_type' => $perObj->getValue('r_object_type'),
							'a_content_type' => $perObj->getValue('a_content_type'),
							'i_contents_id' => $perObj->getValue('i_contents_id')
						);
		$images = new JcIconList($this->user);
		$icon = $images->getIcon(new JfTypedObject($this->session, $propArr));
		// $icon = '/estancia3.0/webapp/themes/default/images/icons/php.png';
		// We just want to get the name of the icon : 'php.png'
		$bPath = ($path) ? 'true' : 'false';
		if (!$path)	{$icon = substr($icon, strrpos($icon, '/') + 1);}
		if (substr($icon, 0, 3) == 'tn_')	{$icon = $perObj->getValue('a_content_type').'.png';}
		echo $icon;
	}

	/**
	 * Get the object name
	 *
	 * @access	public
	 * @return	String	the object name
	 */
	public function getObjectName()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$sysObj = $this->sysObj;
		$name = ($sysObj->getValue('object_name') <> '') ? $sysObj->getValue('object_name') : 'View';
		if ($this->isCorrupted())	{$name = $this->getString('WARNING');}
		echo $name;
	}

	/**
	 * Get the current date time
	 * This function returns the current date time including milliseconds.
	 *
	 * @access	public
	 * @return	String	the date time
	 */
	public function getSize($format)
	{
		$perObj = $this->perObj;
		$size = $perObj->getValue('r_content_size');
		$formatted_size = number_format($size, 0, ' ', ' ');
		$byte = $this->getString('BYTE_SYMBOL');
		if ($size == 0)	{$formatted_size = '';}
		else if ($size == 1)	{$formatted_size = $size.' '.$this->getString('BYTE');}
		else					{$formatted_size = $formatted_size.' '.$this->getString('BYTES');}
		return $formatted_size;
	}


	/**
	 * Get the attribute value
	 *
	 * @access	public
	 * @return	String	the attribute value
	 */
	public function getValue($attribute, $isName = false)
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$perObj = $this->perObj;
		$value = $perObj->getValue($attribute);
		$value = ($isName) ? ucfirst($value) : $value;
		return $value;
	}

	/**
	 * Init the webcomponent.
	 *
	 * @access	public
	 */
	public function init()
	{
		// Get the object info
		$perObj = $this->perObj;
		$this->sysObj = JfUtils::cast($this->perObj, 'JfSysObject');
		$sysObj = $this->sysObj;
		$user = $this->user;
		try
		{
			$this->content = $sysObj->getFile();
		}
		catch (JfException $ex)
		{
			$this->content = 'RESTRICTED_ACCESS';
			JcLogger::info(__CLASS__.'.'.__FUNCTION__.'('.$ex->getMessage().')');
		}
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'(end)');
	}

	/**
	 * Checks if the content exists in the repository.
	 *
	 * @access	public
	 */
	public function isCorrupted()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$flag = true;
		if (file_exists(_SERVER_ROOT_.$this->content))	{$flag = false;}
		return $flag;
	}
}
?>