<?php
/**
 * Display webcomponent.
 *
 * @package		com.webcomponent
 * @author		Jean-Marie Roy
 * @copyright	Jean-Marie Roy 2011
 * @version		3.0
 */
class JwDisplay extends JwComponent
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
	 * Get the content of an object
	 *
	 * @access	public
	 */
	public function getContent()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$sysObj = $this->sysObj;
		if ($this->isCorrupted())	{throw new JcException('<span class="errormessage">'.$this->getString('CONTENT_INACCESSIBLE').'</span>', JcException::$JC_EXCEPTION_WARNING);}
		else if ($sysObj->getValue('a_content_type') == 'pdf')
		{
			echo '<iframe src="'._APP_ROOT_.$this->content.'" id="frame" style="border: 0; height: 80%; width: 100%;" frameborder=0 marginwidth=0 marginheight=0></iframe>';
			echo "<script>";
			echo "$('.modal').css({";
			echo "	width: '1000px'";
			echo "});";
			echo "fheight = 0.8 * $(window).height();";
			echo "$('#frame').css({";
			echo "	height: fheight + 'px'";
			echo "});";
			echo "</script>";
		}
//		else if ($sysObj->getValue('a_content_type') == 'jpg')
		else if (in_array($sysObj->getValue('a_content_type'), array('png', 'jpg', 'jpeg')))
		{
			list($width, $height) = getimagesize(_SERVER_ROOT_.$this->content);
			echo '<img src="'._APP_ROOT_.$this->content.'" id="imgcontent" style="border: 0;">';
			echo "<script>";
			echo "img_width = ".$width.";";
			echo "img_height = ".$height.";";
			echo "window_width = 0.8 * $(window).width();";
			echo "window_height = 0.8 * $(window).height();";
			echo "ratio_width = img_width / window_width;";
			echo "ratio_height = img_height / window_height;";
			echo "ratio_min = Math.min(ratio_width, ratio_height);";
			echo "ratio_max = Math.max(ratio_width, ratio_height);";
			echo "if (ratio_max > 0.80)";
			echo "{";
			echo "	img_width = 0.80 * img_width / ratio_max;";
			echo "	img_height = 0.80 * img_height / ratio_max;";
			echo "}";
			echo "modal_width = img_width + 4;";
			echo "modal_height = img_height + 4;";
//			echo "modal_top = (window_height - modal_height)/2;";
			echo "$('.modal').css({";
//			echo "	position: 'absolute',";
//			echo "	top: modal_top + 'px',";
			echo "	height: modal_height + 'px',";
			echo "	width: modal_width + 'px'";
			echo "});";
			echo "$('#imgcontent').css({";
			echo "	height: img_height + 'px',";
			echo "	width: img_width + 'px',";
			echo "	'border': '4px solid black'";
			echo "});";
			echo "</script>";
		}
		else
		{
			echo '<div class="drag"></div>';
			echo '<div style="padding: 4px; background-color: black;"><div id="content" style="background-color: white;">'.$this->viewContent().'</div></div>';
			echo "<script>";
			echo "$('.modal').css({";
			echo "	width: '800px'";
			echo "});";
			echo "</script>";
		}
	}

	/**
	 * Get the link to the content of the object
	 *
	 * @access	public
	 * @return	String	the link
	 */
	public function getLink()
	{
		$link = '<jm:actionlink action="location.href='._APP_ROOT_.$this->content.';" cssclass="viewbutton" src="floppy_16.png" nlsid="DOWNLOAD"/>';
		if ($this->isCorrupted())	{$link = '';}
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
		if ($this->isCorrupted())	{$name = 'Warning';}
		echo $name;
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

	/**
	 * Get the content of an object
	 *
	 * @access	private
	 */
	private function viewContent()
	{
		JcLogger::debug(__CLASS__.'.'.__FUNCTION__.'()');
		$sysObj = $this->sysObj;
		$strContent = htmlspecialchars($sysObj->getContent());
		$strContent = str_replace('<?php', '\<?php', $strContent);
		$strContent = str_replace('?>', '\?>', $strContent);
		$order   = array("\r\n", "\n", "\r");
		$strContent = str_replace($order, '<br />', $strContent);
		if ($sysObj->getValue('a_content_type') == 'sql' && $sysObj->getValue('r_content_size') < 100*1024)
		{
			$SQLStatement   = array("SELECT", "UPDATE", "INSERT", "DELETE", "CREATE TABLE", "CREATE VIEW", "DROP VIEW",
									" FROM ", " SET ", " IN ", " WHERE ", " AND ", " ORDER BY ",
									" VARCHAR(", " FLOAT(", " INT(", " NOT ", "NULL");
			$SQLReplace   = array(	"<span style='font-weight: bold; color: blue; padding-bottom: 4px;'>SELECT</span>",
									"<span style='font-weight: bold; color: blue; padding-bottom: 4px;'>UPDATE</span>",
									"<span style='font-weight: bold; color: blue; padding-bottom: 4px;'>INSERT</span>",
									"<span style='font-weight: bold; color: blue; padding-bottom: 4px;'>DELETE</span>",
									"<span style='font-weight: bold; color: blue; padding-bottom: 4px;'>CREATE TABLE</span>",
									"<span style='font-weight: bold; color: blue; padding-bottom: 4px;'>CREATE VIEW</span>",
									"<span style='font-weight: bold; color: blue; padding-bottom: 4px;'>DROP VIEW</span>",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'>FROM</span> ",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'>SET</span> ",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'>IN</span> ",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'>WHERE</span> ",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'>AND</span> ",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'>ORDER BY</span> ",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'> VARCHAR</span>(",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'> FLOAT</span>(",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'> INT</span>(",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'> NOT </span>",
									" <span style='font-weight: bold; color: blue; padding-bottom: 4px;'>NULL</span>"
								);
			$strContent = str_ireplace($SQLStatement, $SQLReplace, $strContent);
			$strContent = preg_replace("/\/\/(.*?)<br \/>/", "<span style='color: green;'>//$1<br /></span>", $strContent);
			$strContent = preg_replace("/\/\*(.*?)<br \/>/", "<span style='color: green;'>/*$1<br /></span>", $strContent);
			$strContent = preg_replace("/\*(.*?)<br \/>/", "<span style='color: green;'>*$1<br /></span>", $strContent);
			$strContent = preg_replace("/\*\/(.*?)<br \/>/", "<span style='color: green;'>*/$1<br /></span>", $strContent);
		}
		// JcLogger::info('head : '.$head);
		return $strContent.'<BR>';
	}
}
?>