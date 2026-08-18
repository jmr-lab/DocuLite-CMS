<?php
/**
 * The JcIconList class.
 * Usage :
 *
 * $images = new JcIconList($user);
 * $images->getIcon($object, $size);
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcIconList
{
	/**
	 * List of icons
	 *
	 * @access	private
	 * @var		array
	 */
	private $icon = array(	'acad_16.png' => 'true', 'acad.png' => 'true',
							'amipro_16.png' => 'true', 'amipro.png' => 'true',
							'crtext_16.png' => 'true', 'crtext.png' => 'true',
							'dwf_16.png' => 'true', 'dwf.png' => 'true',
							'dxf_16.png' => 'true', 'dxf.png' => 'true',
							'eml_16.png' => 'true', 'eml.png' => 'true',
							'excel8template_16.png' => 'true', 'excel8template.png' => 'true',
							'excel5book_16.png' => 'true', 'excel5book.png' => 'true',
							'excel8book_16.png' => 'true', 'excel8book.png' => 'true',
							'home_16.png' => 'true', 'home.png' => 'true',
							'htm_16.png' => 'true', 'htm.png' => 'true',
							'jm_document_16.png' => 'true', 'jm_document.png' => 'true',
							'jm_folder_16.png' => 'true', 'jm_folder.png' => 'true',
							'jpeg_16.png' => 'true', 'jpeg.png' => 'true',
							'jpg_16.png' => 'true', 'jpg.png' => 'true',
							'maker55_16.png' => 'true', 'maker55.png' => 'true',
							'mdoc55_16.png' => 'true', 'mdoc55.png' => 'true',
							'mpeg-4v_16.png' => 'true', 'mpeg-4v.png' => 'true',
							'ms_access7_16.png' => 'true', 'ms_access7.png' => 'true',
							'ms_access8_16.png' => 'true', 'ms_access8.png' => 'true',
							'ms_access8_mde_16.png' => 'true', 'ms_access8_mde.png' => 'true',
							'msw6_16.png' => 'true', 'msw6.png' => 'true',
							'msw8_16.png' => 'true', 'msw8.png' => 'true',
							'msw8template_16.png' => 'true', 'msw8template.png' => 'true',
							'pdf_16.png' => 'true', 'pdf.png' => 'true',
							'php_16.png' => 'true', 'php.png' => 'true',
							'png_16.png' => 'true', 'png.png' => 'true',
							'powerpoint_16.png' => 'true', 'powerpoint.png' => 'true',
							'ppt8_16.png' => 'true', 'ppt8.png' => 'true',
							'ppt8_template_16.png' => 'true', 'ppt8_template.png' => 'true',
							'sql_16.png' => 'true', 'sql.png' => 'true',
							'ustn_16.png' => 'true', 'ustn.png' => 'true',
							'wp6_16.png' => 'true', 'wp6.png' => 'true',
							'wp7_16.png' => 'true', 'wp7.png' => 'true',
							'wp8_16.png' => 'true', 'wp8.png' => 'true',
							'zip_16.png' => 'true', 'zip.png' => 'true'
						);

						/**
	 * List of thumbnails
	 *
	 * @access	private
	 * @var		array
	 */
	private $thumbnails = array();

	/**
	 * User connected to the current session
	 *
	 * @access	protected
	 * @var		JcLoginInfo
	 */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param	JfUser	the current user
	 */
	public function __construct($user)
	{
		$this->user = $user;
	}

	/**
	 * Get the icon associated with an object
	 * $object = array(	'r_object_id' => '0b001e2400786532',
	 * 					'r_object_type' => 'jm_document',
	 * 					'a_content_type' = > 'png',
	 * 					'i_contents_id' = > '06001e2400786532');
	 * $size = 16;
	 * will return '/estancia/webapp/themes/default/images/icons/msw8_16.png'	 
	 *
	 * @access	public
	 * @param	array	object			an array representing the object
	 * @param	int		size			the icon size (64)
	 * @return	String	the icon path and name
	 */
	public function getIcon($object, $size = 64)
	{
		// Init variables ($dos = 'png' or 'doc'...)
		$type = trim($object->getValue('r_object_type'));
		$dos = trim($object->getValue('a_content_type'));
		$content = trim($object->getValue('i_contents_id'));
		$ext_list = array('png', 'jpg', 'jpeg');
//		// Create shortened object
//		$shobject = md5($size.$type.$dos.$content);
		// Init icon
		$path = _APP_ROOT_.'/webapp/themes/default/images/icons/';
		$icon = 'unknown';
		$png = '.png';
		// Get the user login info
		$user = $this->user;

		// Case 1 : The object is a sysobject and an image (png, jpg, jpeg)
		if (in_array($dos, $ext_list) && $content <> '')	{list($icon, $dos) = $this->getThumbnail($path, $content, $dos);}
		// Case 2 : The object is a sysobject (word, text, ...)
		else if ($dos <> '')	{$icon = $dos;}
		// Case 3 : The object is the home folder of the user
		else if ($object->getValue('r_object_id') == $user->getValue('default_folder'))	{$icon = 'home';}
		// Case 4 : The object is not a sysobject or doesn't have a content associated
		else if ($type <> '')	{$icon = $type;}

		// Add '_16' at the end if the size equals 16
		$append = ($size == 16) ? '_16' : '';

		// Change the path if icon is a thumbnail (starts with 'tn_'
		if (substr($icon, 0, 3) == 'tn_')	{$path = _APP_ROOT_.'/temp/'.$user->getValue('r_object_id').'/.thumbnails/';$png = '.'.$dos;$append = '';}
		// if (substr($icon, 0, 3) == 'tn_')	{$path = '/data/thumbnail_storage_01/';$png = '.'.$dos;}
		// JcLogger::info('Thumbnail : '._DOCUMENT_ROOT_.$path.$icon.$append.$png.' - '.$dos);
		// JcLogger::info('Icon : '.$icon.' - '.$png);

		// If icon has already been looked for then return the previous value found
		if (isset($this->icon[$icon.$append.$png]) && $this->icon[$icon.$append.$png] == 'true')	{return $path.$icon.$append.$png;}
		else if (isset($this->icon[$icon.'_16'.$png]) && isset($this->icon[$icon.$png]) && $this->icon[$icon.'_16'.$png] == 'false' && $this->icon[$icon.$png] == 'true')	{return $path.$icon.$png;}
		// Else If icon has already been looked for AND doesn't exist
		else if (isset($this->icon[$icon.$append.$png]) && $this->icon[$icon.$append.$png] == 'false')	{return $path.'unknown'.$append.'.png';}
		
		// Check if file exists
		if (!file_exists(_DOCUMENT_ROOT_.$path.$icon.$append.$png))
		{
			JcLogger::warning(__CLASS__.'.'.__FUNCTION__.'( File '.$icon.$append.$png.' doesnt exist)');
			// Store the icon in the array
			$this->icon[$icon.$append.$png] = 'false';
			$this->icon[$icon.$png] = 'true';
			$oldicon = $icon;
			$append = '';
			// Check if the same icon with bigger size exists
			if ($size == 16)
			{
				if (!file_exists(_DOCUMENT_ROOT_.$path.$icon.$png))
				{
					JcLogger::warning(__CLASS__.'.'.__FUNCTION__.'( File '.$icon.' doesnt exist)');
					$icon = 'unknown';
				}
			}
			else	{$icon = 'unknown';}
			// Store the icon in the array
			if ($icon == 'unknown')	{$this->icon[$oldicon.$png] = 'false';}
		}
		// Store the icon in the array
		else	{$this->icon[$icon.$append.$png] = 'true';}

		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'( '.$icon.$append.$png.' )');
		// Return the icon
		return $path.$icon.$append.$png;
	}

	/**
	 * Get a thumbnail
	 *
	 * @access	private
	 * @return	array	the thumnail icon
	 */
	private function getThumbnail($path, $content, $dos)
	{
		$thumbnail = 'tn_'.$content;
//		return array($dos, $dos);
		$user = $this->user;
		$userFolder = '/temp/'.$user->getValue('r_object_id');
		// Try to find an image in the user cache : if the thumbnails array has not been initialised yet, parse the user cache for all images
		if (sizeof($this->thumbnails) == 0)
		{
			if (!file_exists(_SERVER_ROOT_.$userFolder))	{mkdir(_SERVER_ROOT_.$userFolder);}
			if (!file_exists(_SERVER_ROOT_.$userFolder.'/.thumbnails'))	{mkdir(_SERVER_ROOT_.$userFolder.'/.thumbnails');}
//			foreach ( glob(_SERVER_ROOT_.$userFolder.'/.thumbnails'.'/tn_*.{jpg,jpeg,png,gif}', GLOB_BRACE) as $key => $value)
			foreach ( scandir(_SERVER_ROOT_.$userFolder.'/.thumbnails') as $key => $value)
			{
				// $strFileName = 'tn_06001e240c3d14c6.jpg'
				$strFileName = basename($value);
				// $strBaseName = 'tn_06001e240c3d14c6'
				$strBaseName = substr($strFileName, 0, strripos($strFileName, '.'));
				// $strExtension = 'jpg'
				$strExtension = substr($strFileName, strripos($strFileName, '.') + 1);
				// Add to the thumbnails array
				$this->thumbnails[$strBaseName] = $strExtension;
				// JcLogger::info('strBaseName : '.$strBaseName.' - strExtension : '.$strExtension);
			}
		}
		// There is a key corresponding to 'tn_06001e240c9313e6' in the thumbnails array :
		if (isset($this->thumbnails[$thumbnail]))	{return array($thumbnail, $this->thumbnails[$thumbnail]);}
		// Otherwise, try to find the thumbnail in the repository
//		foreach ( glob(_DOCUMENT_ROOT_.'/data/thumbnail_storage_01/'.$thumbnail.'.*') as $key => $value)
		foreach ( scandir(_DOCUMENT_ROOT_.'/data/thumbnail_storage_01') as $key => $value)
		{
			// $strFileName = 'tn_06001e240c3d14c6.jpg'
			$strFileName = basename($value);
			// $strBaseName = 'tn_06001e240c3d14c6'
			$strBaseName = substr($strFileName, 0, strripos($strFileName, '.'));
			// $strExtension = 'jpg'
			$strExtension = substr($strFileName, strripos($strFileName, '.') + 1);
			// If the file exists
			if ($strBaseName == $thumbnail)	{return $this->getThumbnailImage($thumbnail, $strExtension);}
			// JcLogger::info('strBaseName : '.$strBaseName.' - strExtension : '.$strExtension);
		}
		// No thumbnail found in the repository or user cache, return default image :
		$thumbnail = $dos;
		return array($thumbnail, $dos);
	}

	/**
	 * Get a thumbnail
	 *
	 * @access	private
	 * @return	array	the thumnail icon
	 */
	private function getThumbnailImage($thumbnail, $dos)
	{
		// Get the user info, the source and target folders :
		$user = $this->user;
		$userFolder = '/temp/'.$user->getValue('r_object_id');
		$source = _DOCUMENT_ROOT_.'/data/thumbnail_storage_01/'.$thumbnail.'.'.$dos;
		$target = _SERVER_ROOT_.$userFolder.'/.thumbnails/'.$thumbnail.'.'.$dos;
		if (!file_exists(_SERVER_ROOT_.$userFolder.'/.thumbnails'))	{mkdir(_SERVER_ROOT_.$userFolder.'/.thumbnails');}
		if (!copy($source, $target))	{$thumbnail = $dos;}
		return array($thumbnail, $dos);
	}
}
?>