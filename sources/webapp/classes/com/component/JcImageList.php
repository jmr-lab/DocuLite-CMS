<?php
/**
 * The JcImageList class.
 * Usage :
 *
 * $images = new JcImageList($objects, $size);
 * ...
 * $images->getIcon($object);
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcImageList
{
	/**
	 * List of images
	 *
	 * @access	private
	 * @var		JcImage
	 */
	private $images = array();

	/**
	 * Constructor
	 *
	 * @param	array	List of objects
	 * @param	int		the size
	 */
	public function __construct($objects, $size)
	{
		foreach ($objects as $key => $object)
		{
			// Get the signature
			$index = $this->getSignature($object, $size);
			// Now we have an index, create the link to the icon
			if (!isset($this->images[$index]))	{$this->addImage($object, $size);}
		}
	}

	/**
	 * Add an image to the image list
	 *
	 * @access	private
	 * @param	array	an object
	 * @param	int		the size
	 */
	private function addImage($object, $size)
	{
	}

	/**
	 * Get the icon
	 *
	 * @access	public
	 * @param	array	an object
	 * @param	int		the size
	 * @return	String	the path to the icon and its name and extension
	 */
	public function getIcon($object, $size)
	{
		return $this->images[$this->getSignature($object, $size)];
	}

	/**
	 * Get the signature of the object
	 *
	 * @access	private
	 * @param	array	an object
	 * @param	int		the size
	 * @return	String	the signature of the object
	 */
	private function getSignature($object, $size = 64)
	{
		// Init variables ($dos = 'png' or 'doc'...)
		$type = trim($object->getValue('r_object_type'));
		$dos = trim($object->getValue('a_content_type'));
		$content = trim($object->getValue('i_contents_id'));
		$ext_list = array('png', 'jpg', 'jpeg');
		$size = ($size == 16 ? '_16' : '');
		// Create the signature
		$signature = $type.$size.'.'.$dos;
		// If the object is a sysobject and an image (png, jpg, jpeg)
		if (in_array($dos, $ext_list) && $content <> '')	{$signature = $content;}
		return $signature;
	}
}
?>