<?php
/**
 * The JwToolBar webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwToolBar extends JwComponent
{
	/**
	 * Constructor
	 *
	 * This function initialize the current user0
	 *
	 */
	public function __construct()
	{
		// Call to parent method
		parent::__construct();
		// Change search string
		echo "<script>txtsearch = '".$this->getString('SEARCH')."'</script>";
	}
}
?>