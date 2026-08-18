<?php
/**
 * The JcImage class.
 * Usage :
 *
 * $image = new JcImage();
 * $image->getIcon();
 *
 * @package		com.component
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JcImage
{
	/**
	 * Name of the image
	 *
	 * @access	private
	 * @var		String
	 */
	private $name;

	/**
	 * Path of the image
	 *
	 * @access	private
	 * @var		String
	 */
	private $path;

	/**
	 * Size of the image
	 *
	 * @access	private
	 * @var		int
	 */
	private $size;

	/**
	 * Constructor
	 *
	 */
	public function __construct()	{}

	/**
	 * Get the icon
	 *
	 * @access	public
	 * @return	String	the icon
	 */
	public function getIcon()
	{
		return $this->path.'/'.$this->name.'_'.$this->size.'.'.$this->type;
	}

	/**
	 * Get the path
	 *
	 * @access	public
	 * @return	String	the path
	 */
	public function getPath()
	{
		return $this->path;
	}


	/**
	 * Get the size
	 *
	 * @access	public
	 * @return	int	the size
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * Set the path
	 *
	 * @access	public
	 * @param	String	the path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * Set the size
	 *
	 * @access	public
	 * @param	int	the size
	 */
	public function getSize($size)
	{
		$this->size = $size;
	}
}
?>