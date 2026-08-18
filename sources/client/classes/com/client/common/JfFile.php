<?php
/**
 * The file manager class.
 *
 * @package		com.core.common
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JfFile
{
	/**
	 * Move a file to a specified target
	 *
	 * @access		public
	 * @param		String			the source pathname
	 * @param		String			the target pathname
	 * @throws		JfException		if a server error occurs
	 */
	public function move($source, $target)
	{
		if (!copy($source, $target))	{throw new Exception('OBJECT_SAVE_FILE_COPY_ERROR');}
	}
}
?>